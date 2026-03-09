<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Products', description: 'Product catalog API')]
#[OA\Get(
    path: '/api/v1/products',
    summary: 'List products',
    tags: ['Products'],
    parameters: [
        new OA\Parameter(name: 'category_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Search by name or SKU', schema: new OA\Schema(type: 'string')),
        new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
        new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
    ],
    responses: [new OA\Response(response: 200, description: 'Paginated list of products')]
)]
class ProductApi
{
}
