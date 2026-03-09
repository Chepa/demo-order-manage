<?php

namespace App\UserInterface\Controllers\Api\V1;

use App\Domain\Order\Contracts\OrderServiceContract;
use App\Domain\Order\Entities\Order as DomainOrder;
use App\UserInterface\Controllers\Controller;
use App\UserInterface\Requests\OrderIndexRequest;
use App\UserInterface\Requests\OrderStatusUpdateRequest;
use App\UserInterface\Requests\OrderStoreRequest;
use App\Models\Order as EloquentOrder;
use App\UserInterface\Transformers\OrderResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderServiceContract $orderService
    ) {}

    public function store(OrderStoreRequest $request): OrderResource
    {
        $order = $this->orderService->create($request->toDto());

        return new OrderResource($order->getEloquentModel());
    }

    public function index(OrderIndexRequest $request): AnonymousResourceCollection
    {
        $filters = $request->toFilters();

        $orders = $this->orderService->index($filters);

        return OrderResource::collection($orders);
    }

    public function show(int $order): OrderResource
    {
        $orderEntity = $this->orderService->show($order);

        return new OrderResource($orderEntity->getEloquentModel());
    }

    public function updateStatus(OrderStatusUpdateRequest $request, EloquentOrder $order): OrderResource
    {
        $domainOrder = (new DomainOrder())->setEloquentModel($order);

        $updated = $this->orderService->changeStatus($domainOrder, $request->toDto());

        return new OrderResource($updated->getEloquentModel());
    }
}
