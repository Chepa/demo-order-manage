<?php

namespace App\Domain\Order\Mappers;

use App\Domain\Order\Entities\Order as DomainOrder;
use App\Models\Order as EloquentOrder;

class OrderMapper
{
    public function toDomain(EloquentOrder $eloquentOrder): DomainOrder
    {
        return (new DomainOrder())->setEloquentModel($eloquentOrder);
    }

    public function toEloquent(DomainOrder $domainOrder): EloquentOrder
    {
        return $domainOrder->getEloquentModel();
    }
}

