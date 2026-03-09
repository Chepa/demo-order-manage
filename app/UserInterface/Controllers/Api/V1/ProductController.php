<?php

namespace App\UserInterface\Controllers\Api\V1;

use App\Domain\Product\Contracts\ProductServiceContract;
use App\UserInterface\Controllers\Controller;
use App\UserInterface\Requests\ProductIndexRequest;
use App\UserInterface\Transformers\ProductResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductServiceContract $productService,
    ) {}

    public function index(ProductIndexRequest $request): AnonymousResourceCollection
    {
        $filters = $request->toFilters();

        $products = $this->productService->list($filters);

        return ProductResource::collection($products);
    }
}
