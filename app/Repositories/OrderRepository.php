<?php

namespace App\Repositories;

use App\Models\Orders;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        protected readonly Orders $model
    ) {}

    public function getAllOrders(): Collection
    {
        return $this->model
            ->with(['customer', 'items'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getOrderById(int $id): Orders
    {
        return $this->model
            ->with(['customer', 'items', 'statusHistories' => fn ($q) => $q->orderBy('changed_at') ])
            ->findOrFail($id);
    }

    public function getOrderByOrderNumber(string $orderNumber): ?Orders
    {
        return $this->model
            ->with(['customer', 'items', 'statusHistories' => fn ($q) => $q->orderBy('changed_at')])
            ->where('order_number', $orderNumber)
            ->first();
    }

    public function createOrder(array $data): Orders
    {
        return $this->model->create($data);
    }

    public function updateOrderStatus(Orders $order, array $validated): Orders
    {
        $order->update($validated);

        return $order;
    }

    public function deleteOrder(Orders $order): bool
    {
        return (bool) $order->delete();
    }

}