<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0',
    title: 'Order Management API',
    description: 'REST API for managing orders and products'
)]
#[OA\Server(url: '/', description: 'API Server')]
class OpenApiSpec
{
}
