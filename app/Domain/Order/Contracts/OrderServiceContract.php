<?php

namespace App\Domain\Order\Contracts;

use App\Domain\Order\Entities\Order;
use App\Domain\Order\Providers\Order\CreateOrderProviderContract;
use App\Domain\Order\Providers\Order\OrderStatusUpdateProviderContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderServiceContract
{
    public function create(CreateOrderProviderContract $provider): Order;

    public function changeStatus(Order $order, OrderStatusUpdateProviderContract $provider): Order;

    public function index(array $filters = []): LengthAwarePaginator;

    public function show(int $id): Order;
}

