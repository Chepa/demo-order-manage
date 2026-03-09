<?php

namespace App\Domain\Product\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepository
{
    public function paginate(array $filters = []): LengthAwarePaginator;
}

