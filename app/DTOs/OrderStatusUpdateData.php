<?php

namespace App\DTOs;

use App\Domain\Order\Providers\Order\OrderStatusUpdateProviderContract;

readonly class OrderStatusUpdateData implements OrderStatusUpdateProviderContract
{
    public function __construct(
        public string $status,
    ) {}
}
