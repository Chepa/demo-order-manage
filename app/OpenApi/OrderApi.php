<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Orders', description: 'Order management API')]
#[OA\Post(
    path: '/api/v1/orders',
    summary: 'Create order',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['customer_id', 'items'],
            properties: [
                new OA\Property(property: 'customer_id', type: 'integer', example: 1),
                new OA\Property(
                    property: 'items',
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'product_id', type: 'integer', example: 1),
                            new OA\Property(property: 'quantity', type: 'integer', example: 2),
                        ]
                    )
                ),
            ]
        )
    ),
    tags: ['Orders'],
    responses: [
        new OA\Response(response: 201, description: 'Order created'),
        new OA\Response(response: 422, description: 'Validation error'),
        new OA\Response(response: 429, description: 'Rate limit exceeded'),
    ]
)]
#[OA\Get(
    path: '/api/v1/orders',
    summary: 'List orders',
    tags: ['Orders'],
    parameters: [
        new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['new', 'confirmed', 'processing', 'shipped', 'completed', 'cancelled'])),
        new OA\Parameter(name: 'customer_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        new OA\Parameter(name: 'date_from', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
        new OA\Parameter(name: 'date_to', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
        new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
        new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
    ],
    responses: [new OA\Response(response: 200, description: 'Paginated list of orders')]
)]
#[OA\Get(
    path: '/api/v1/orders/{id}',
    summary: 'Get order details',
    tags: ['Orders'],
    parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
    responses: [
        new OA\Response(response: 200, description: 'Order details'),
        new OA\Response(response: 404, description: 'Order not found'),
    ]
)]
#[OA\Patch(
    path: '/api/v1/orders/{id}/status',
    summary: 'Update order status',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['status'],
            properties: [new OA\Property(property: 'status', type: 'string', enum: ['new', 'confirmed', 'processing', 'shipped', 'completed', 'cancelled'])]
        )
    ),
    tags: ['Orders'],
    parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
    responses: [
        new OA\Response(response: 200, description: 'Status updated'),
        new OA\Response(response: 422, description: 'Invalid transition'),
        new OA\Response(response: 404, description: 'Order not found'),
    ]
)]
class OrderApi
{
}
