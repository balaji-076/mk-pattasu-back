<?php

namespace App\Observers;

use App\Models\Orders;
use Illuminate\Support\Facades\Auth;

class OrderObserver
{
    /**
     * Handle the Orders "created" event.
     * Order create ஆகும்போதே initial status history entry insert பண்ணுவோம்.
     */
    public function created(Orders $order): void
    {
        $order->statusHistories()->create([
            'status'     => $order->order_status,
            'note'       => 'Order placed',
            'changed_by' => Auth::id(), // customer order pannuna null/customer id, admin panna admin id
            'changed_at' => now(),
        ]);
    }

    /**
     * Handle the Orders "updated" event.
     * order_status column change ஆனா மட்டும் history-ல insert பண்ணுவோம்.
     */
    public function updated(Orders $order): void
    {
        if ($order->isDirty('order_status')) {
            $order->statusHistories()->create([
                'status'     => $order->order_status,
                'note'       => $this->defaultNoteFor($order->order_status),
                'changed_by' => Auth::id(),
                'changed_at' => now(),
            ]);
        }
    }

    /**
     * Handle the Orders "deleted" event.
     */
    public function deleted(Orders $order): void
    {
        //
    }

    /**
     * Optional: status change ஆகும்போது default note auto-generate பண்ண.
     */
    private function defaultNoteFor(string $status): ?string
    {
        return match ($status) {
            'confirmed' => 'Your Order is confirmed',
            'packed'    => 'Order packed and ready to ship',
            'shipped'   => 'Order shipped',
            'delivered' => 'Order delivered successfully',
            'cancelled' => 'Order cancelled',
            default     => null,
        };
    }
}