<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected readonly OrderService $orderService
    ) {}

    // =========================================================================
    // GET /orders
    // =========================================================================
    public function getAllOrders(): JsonResponse
    {
        $orders = $this->orderService->getAllOrders();

        return $this->successResponse('Orders fetched successfully', $orders);
    }

    // =========================================================================
    // GET /orders/{id}
    // =========================================================================
    public function getOrderById(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);

            return $this->successResponse('Order details fetched successfully', $order);
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Order not found', 404);
        }
    }

    // =========================================================================
    // GET /orders/number/{orderNumber}
    // =========================================================================
    public function getOrderByOrderNumber(string $orderNumber): JsonResponse
    {
        $order = $this->orderService->getOrderByOrderNumber($orderNumber);

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        return $this->successResponse('Order fetched successfully', $order);
    }

    // =========================================================================
    // POST /orders
    // =========================================================================
    public function createOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer.name'         => 'required|string|max:255',
            'customer.phone'        => 'required|string|max:15',
            'customer.email'        => 'nullable|email',
            // 'customer.address'      => 'nullable|string',
            'customer.city'         => 'required|string',
            'customer.state'        => 'required|string',
            'customer.postcode'     => 'required|string',
            'items'                 => 'required|array|min:1',
            'items.*.id'            => 'required|integer|exists:products,id',
            'items.*.name'          => 'required|string',
            'items.*.discount_rate' => 'required|numeric|min:0',
            'items.*.qty'           => 'required|integer|min:1',
            'total'                 => 'required|numeric|min:0',
        ]);

        $order = $this->orderService->createOrder($validated);

        return $this->successResponse(
            'Order placed successfully',
            [
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'total'        => $order->total_amount,
            ],
            201
        );
    }

    // =========================================================================
    // PUT /orders/{id}/status
    // =========================================================================
    public function updateOrderStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'order_status'   => 'sometimes|string',
            'payment_status' => 'sometimes|string',
        ]);

        try {
            $order = $this->orderService->updateOrderStatus($id, $validated);

            return $this->successResponse('Order updated successfully', $order);
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Order not found', 404);
        }
    }

    // =========================================================================
    // GET /orders/{id}/whatsapp-link
    // =========================================================================
    public function getWhatsAppLink(int $id): JsonResponse
    {
        try {
            $link = $this->orderService->getWhatsAppLink($id);

            return $this->successResponse('WhatsApp link generated', ['whatsapp_link' => $link]);
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Order not found', 404);
        }
    }

    // =========================================================================
    // PATCH /orders/{id}/whatsapp-sent
    // =========================================================================
    public function markWhatsAppSent(int $id): JsonResponse
    {
        try {
            $this->orderService->markWhatsAppSent($id);

            return $this->successResponse('WhatsApp marked as sent');
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Order not found', 404);
        }
    }

    public function deleteOrder(int $id): JsonResponse
    {
        try {
            $this->orderService->deleteOrder($id);

            return $this->successResponse('Order deleted successfully');
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Order not found', 404);
        }
    }
    
}