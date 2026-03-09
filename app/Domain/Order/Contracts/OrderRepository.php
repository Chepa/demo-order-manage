<?php

namespace App\Domain\Order\Contracts;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepository
{
    public function paginate(array $filters = []): LengthAwarePaginator;

    public function findById(int $id): Order;
}

