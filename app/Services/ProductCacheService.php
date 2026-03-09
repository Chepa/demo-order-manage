<?php

namespace App\Services;

use App\Domain\Product\Contracts\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use JsonException;

class ProductCacheService
{
    private const CACHE_TTL = 300;

    private const CACHE_KEY_PREFIX = 'products:list:';

    public function __construct(
        private readonly ProductRepository $products,
    ) {}

    private const CACHE_VERSION_KEY = 'products:cache:version';

    /**
     * @throws JsonException
     */
    public function getCachedList(array $filters): LengthAwarePaginator
    {
        $normalizedFilters = $this->normalizeFilters($filters);
        $hash = md5(json_encode($normalizedFilters, JSON_THROW_ON_ERROR));
        $callback = fn () => $this->products->paginate($normalizedFilters);

        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            $key = self::CACHE_KEY_PREFIX . $hash;

            return Cache::tags(['products'])->remember($key, self::CACHE_TTL, $callback);
        }

        $key = $this->buildCacheKey($hash);

        return Cache::remember($key, self::CACHE_TTL, $callback);
    }

    private function buildCacheKey(string $hash): string
    {
        $version = Cache::get(self::CACHE_VERSION_KEY, 0);

        return self::CACHE_KEY_PREFIX . $version . ':' . $hash;
    }

    private function normalizeFilters(array $filters): array
    {
        ksort($filters);

        if (array_key_exists('category_id', $filters)) {
            $filters['category_id'] = $filters['category_id'] !== null
                ? (int) $filters['category_id']
                : null;
        }

        if (array_key_exists('per_page', $filters)) {
            $filters['per_page'] = (int) $filters['per_page'];
        }

        if (array_key_exists('page', $filters)) {
            $filters['page'] = (int) $filters['page'];
        }

        return $filters;
    }

    public function invalidate(): void
    {
        if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
            Cache::tags(['products'])->flush();

            return;
        }

        $version = Cache::get(self::CACHE_VERSION_KEY, 0);
        Cache::put(self::CACHE_VERSION_KEY, $version + 1);
    }
}
