<?php

namespace App\Application\Services\Product\Product;

use App\Domain\Product\Contracts\ProductServiceContract;
use App\Services\ProductCacheService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use JsonException;

readonly class ProductService implements ProductServiceContract
{
    public function __construct(
        private ProductCacheService $cacheService,
    ) {}

    /**
     * @throws JsonException
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        $filters = [
            'category_id' => $filters['category_id'] ?? null,
            'search' => $filters['search'] ?? null,
            'per_page' => $filters['per_page'] ?? 15,
            'page' => $filters['page'] ?? 1,
        ];

        return $this->cacheService->getCachedList($filters);
    }
}

