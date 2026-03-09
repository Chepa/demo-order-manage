<?php

namespace App\Domain\Product\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductServiceContract
{
    public function list(array $filters = []): LengthAwarePaginator;
}

