<?php

namespace App\Application\Services\Order\Order;

use App\Domain\Order\Contracts\OrderRepository;
use App\Domain\Order\Contracts\OrderServiceContract;
use App\Domain\Order\Entities\Order as DomainOrder;
use App\Domain\Order\Mappers\OrderMapper;
use App\Domain\Order\Providers\Order\CreateOrderProviderContract;
use App\Domain\Order\Providers\Order\OrderStatusUpdateProviderContract;
use App\DTOs\CreateOrderData;
use App\DTOs\OrderStatusUpdateData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Throwable;

readonly class OrderService implements OrderServiceContract
{
    public function __construct(
        private CreateOrderHandler $createOrderHandler,
        private ChangeOrderStatusHandler $changeOrderStatusHandler,
        private OrderMapper $orderMapper,
        private OrderRepository $orders,
    ) {}

    /**
     * @param CreateOrderData $provider
     * @return DomainOrder
     * @throws Throwable
     */
    public function create(CreateOrderProviderContract $provider): DomainOrder
    {
        return $this->createOrderHandler->handle($provider);
    }

    /**
     * @param DomainOrder $order
     * @param OrderStatusUpdateData $provider
     * @return DomainOrder
     * @throws Throwable
     */
    public function changeStatus(DomainOrder $order, OrderStatusUpdateProviderContract $provider): DomainOrder
    {
        return $this->changeOrderStatusHandler->handle($order, $provider);
    }

    public function index(array $filters = []): LengthAwarePaginator
    {
        return $this->orders->paginate($filters);
    }

    public function show(int $id): DomainOrder
    {
        return $this->orderMapper->toDomain(
            $this->orders->findById($id)
        );
    }
}

