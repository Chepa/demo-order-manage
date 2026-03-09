<?php

namespace App\Infrastructure\Laravel\Order;

use App\Domain\Order\Contracts\OrderRepository;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentOrderRepository implements OrderRepository
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = Order::query()->with(['customer', 'items.product']);

        if (! empty($filters['status'])) {
            $query->status($filters['status']);
        }

        if (! empty($filters['customer_id'])) {
            $query->customer((int) $filters['customer_id']);
        }

        $query->dateRange($filters['date_from'] ?? null, $filters['date_to'] ?? null);

        return $query->paginate((int) ($filters['per_page'] ?? 15));
    }

    public function findById(int $id): Order
    {
        /** @var Order $order */
        $order = Order::query()
            ->with(['customer', 'items.product'])
            ->findOrFail($id);

        return $order;
    }
}

