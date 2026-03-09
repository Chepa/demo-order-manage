<?php

namespace App\Application\Services\Order\Order;

use App\Domain\Order\Entities\Order as DomainOrder;
use App\Domain\Order\Mappers\OrderMapper;
use App\DTOs\OrderStatusUpdateData;
use App\Infrastructure\Laravel\Events\OrderStatusChanged;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

readonly class ChangeOrderStatusHandler
{
    public function __construct(
        private OrderMapper $orderMapper,
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(DomainOrder $order, OrderStatusUpdateData $data): DomainOrder
    {
        $eloquent = $this->orderMapper->toEloquent($order);

        $updated = DB::transaction(function () use ($eloquent, $data) {
            $domainOrder = (new DomainOrder())->setEloquentModel($eloquent);

            $oldStatus = $eloquent->status;
            $newStatus = $data->status;

            if (! $domainOrder->canTransitionTo($newStatus)) {
                throw ValidationException::withMessages([
                    'status' => ["Transition from $oldStatus to $newStatus is not allowed."],
                ]);
            }

            $domainOrder->changeStatus($newStatus);

            $eloquent->save();

            event(new OrderStatusChanged($eloquent, $oldStatus, $newStatus));

            return $eloquent->fresh(['customer', 'items.product']);
        });

        return $this->orderMapper->toDomain($updated);
    }
}

