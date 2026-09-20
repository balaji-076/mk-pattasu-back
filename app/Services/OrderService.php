<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Orders;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected readonly OrderRepositoryInterface $orderRepository,
        protected readonly BrevoMailService $brevoMailService,
        protected readonly TelegramNotificationService $telegramNotificationService,
    ) {}

    // -------------------------------------------------------------------------
    // Queries
    // -------------------------------------------------------------------------

    public function getAllOrders(): Collection
    {
        return $this->orderRepository->getAllOrders();
    }

    public function getOrderById(int $id): Orders
    {
        return $this->orderRepository->getOrderById($id);
    }

    public function getOrderByOrderNumber(string $orderNumber): ?Orders
    {
        return $this->orderRepository->getOrderByOrderNumber($orderNumber);
    }

    public function getWhatsAppLink(int $id): string
    {
        $order = $this->orderRepository->getOrderById($id);

        return 'https://wa.me/91' . $order->customer->mobile
            . '?text=' . $this->buildWhatsAppMessage($order, $order->customer);
    }

    // -------------------------------------------------------------------------
    // Commands
    // -------------------------------------------------------------------------

    public function createOrder(array $validated): Orders
    {
        return DB::transaction(function () use ($validated) {
            $cust = $validated['customer'];

            $customer = Customer::updateOrCreate(
                ['mobile' => $cust['phone']],
                [
                    'name'    => $cust['name'],
                    'email'   => $cust['email'] ?? null,
                    // 'address' => $cust['address'],
                    'city'    => $cust['city'],
                    'state'   => $cust['state'],
                    'pincode' => $cust['postcode'],
                ]
            );

            $order = $this->orderRepository->createOrder([
                'order_number'     => 'ORD-' . date('Y') . '-' . strtoupper(Str::random(6)),
                'customer_id'      => $customer->id,
                // 'shipping_address' => $cust['address'],
                'shipping_city'    => $cust['city'],
                'shipping_state'   => $cust['state'],
                'shipping_pincode' => $cust['postcode'],
                'total_amount'     => $validated['total'],
                'payment_status'   => 'pending',
                'order_status'     => 'placed',
            ]);

            $orderItems = collect($validated['items'])->map(fn ($item) => [
                'order_id'      => $order->id,
                'product_id'    => $item['id'],
                'product_name'  => $item['name'],
                'product_price' => $item['discount_rate'],
                'quantity'      => $item['qty'],
                'subtotal'      => $item['discount_rate'] * $item['qty'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ])->toArray();

            OrderItem::insert($orderItems);

            // $this->sendOrderMail($order, $orderItems, $customer);
            $this->dispatchAdminTelegramAlert($order, $orderItems, $customer);
            return $order;
        });
    }

    public function updateOrderStatus(int $id, array $validated): Orders
    {
        $order = $this->orderRepository->getOrderById($id);

        return DB::transaction(function () use ($order, $validated) {
            $this->orderRepository->updateOrderStatus($order, $validated);

            return $order->fresh([
                'items',
                'customer',
                'statusHistories' => fn ($q) => $q->orderBy('changed_at'),
            ]);
        });
    }

    public function markWhatsAppSent(int $id): void
    {
        $order = $this->orderRepository->getOrderById($id);

        $this->orderRepository->updateOrderStatus($order, [
            'wa_status'  => 'sent',
            'wa_sent_at' => now(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function sendOrderMail(Orders $order, array $orderItems, Customer $customer): void
    {
        try {
            $this->brevoMailService->send(
                config('mail.admin_order_email'),
                'New Order Received',
                view('emails.new-order', [
                    'order'    => $order,
                    'items'    => $orderItems,
                    'customer' => $customer,
                ])->render()
            );

            Log::info('Order mail sent via Brevo', ['order_id' => $order->id]);
        } catch (\Throwable $e) {
            Log::error('Brevo mail failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    private function buildWhatsAppMessage(Orders $order, Customer $customer): string
    {
        $grandTotal = collect($order->items)->sum(fn ($item) => (float) $item->subtotal);

        $msg  = "Dear {$customer->name},\n\n";
        $msg .= "Thank you for shopping with KMV Traders & Fireworks.\n";
        $msg .= "Your order has been placed successfully.\n\n";
        $msg .= "Order No: {$order->order_number}\n";
        $msg .= "Amount: ₹{$grandTotal}\n\n";
        $msg .= "Track your order:\n";
        $msg .= "https://kmvfireworks.com/order-status/{$order->order_number}\n\n";
        $msg .= "Warm regards,\n";
        $msg .= "KMV Traders & Fireworks";

        return rawurlencode($msg);
    }
    private const TELEGRAM_MAX_LINE_ITEMS = 30;

    private function dispatchAdminTelegramAlert(Orders $order, array $orderItems, Customer $customer): void
    {
        try {
            $this->telegramNotificationService->notifyAdmin(
                $this->composeAdminTelegramAlert($order, $orderItems, $customer)
            );
        } catch (\Throwable $e) {
            // A notification failure must never fail the order
            Log::error('Admin Telegram alert failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    private function composeAdminTelegramAlert(Orders $order, array $orderItems, Customer $customer): string
    {
        $items = collect($orderItems);
        $lineItems = $items
            ->take(self::TELEGRAM_MAX_LINE_ITEMS)
            ->map(fn ($i) => '• ' . e($i['product_name']) . ' × ' . $i['quantity']
                . ' = ₹' . number_format($i['subtotal'], 2))
            ->implode("\n");

        $remaining = $items->count() - self::TELEGRAM_MAX_LINE_ITEMS;
        if ($remaining > 0) {
            $lineItems .= "\n…and {$remaining} more item(s). Check the admin panel.";
        }

        return "<b>New Order Received</b>\n\n"
            . "<b>Order No:</b> {$order->order_number}\n"
            . '<b>Customer:</b> ' . e($customer->name) . "\n"
            . "<b>Mobile:</b> {$customer->mobile}\n"
            . '<b>Location:</b> ' . e("{$customer->city}, {$customer->state} - {$customer->pincode}") . "\n\n"
            . "<b>Items</b>\n{$lineItems}\n\n"
            . '<b>Total: ₹' . number_format((float) $order->total_amount, 2) . '</b>';
    }

    public function deleteOrder(int $id): void
    {
        $order = $this->orderRepository->getOrderById($id);

        $this->orderRepository->deleteOrder($order);
    }

}