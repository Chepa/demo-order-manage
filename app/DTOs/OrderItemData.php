<?php

namespace App\DTOs;

readonly class OrderItemData
{
    public function __construct(
        public int $productId,
        public int $quantity,
    ) {}
}
