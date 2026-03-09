<?php

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Services\ProductCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCacheServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_cached_list_returns_paginated_products(): void
    {
        Product::factory()->count(2)->create();

        $service = app(ProductCacheService::class);

        $filters = [
            'category_id' => null,
            'search' => null,
            'per_page' => 15,
            'page' => 1,
        ];

        $result = $service->getCachedList($filters);

        $this->assertCount(2, $result->items());
        $this->assertSame(2, $result->total());
    }

    public function test_invalidate_does_not_throw(): void
    {
        $service = app(ProductCacheService::class);

        $service->invalidate();

        $this->assertTrue(true);
    }
}

