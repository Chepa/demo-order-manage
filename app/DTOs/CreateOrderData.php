<?php

namespace App\DTOs;

use App\Domain\Order\Providers\Order\CreateOrderProviderContract;

readonly class CreateOrderData implements CreateOrderProviderContract
{
    /**
     * @param  array<int, OrderItemData>  $items
     */
    public function __construct(
        public int $customerId,
        public array $items,
    ) {}
}
