<?php

namespace App\Repositories\Contracts;

use App\Models\Orders;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function getAllOrders(): Collection;

    public function getOrderById(int $id): Orders;

    public function getOrderByOrderNumber(string $orderNumber): ?Orders;

    public function createOrder(array $data): Orders;

    public function updateOrderStatus(Orders $order, array $data): Orders;

    public function deleteOrder(Orders $order): bool;
}