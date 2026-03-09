<?php

namespace App\Infrastructure\Laravel\Product;

use App\Domain\Product\Contracts\ProductRepository;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentProductRepository implements ProductRepository
{
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = Product::query();

        if (! empty($filters['category_id'])) {
            $query->categoryId((int) $filters['category_id']);
        }

        if (! empty($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query->paginate(
            $filters['per_page'] ?? 15,
            ['*'],
            'page',
            $filters['page'] ?? 1
        );
    }
}

