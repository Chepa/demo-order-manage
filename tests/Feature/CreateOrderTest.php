<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_order_creation_decrements_stock_and_calculates_total(): void
    {
        $customer = Customer::factory()->create();
        $product1 = Product::factory()->create([
            'price' => 100,
            'stock_quantity' => 10,
        ]);
        $product2 = Product::factory()->create([
            'price' => 50,
            'stock_quantity' => 5,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product1->id, 'quantity' => 2],
                ['product_id' => $product2->id, 'quantity' => 3],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'customer_id',
                    'status',
                    'total_amount',
                    'items' => [
                        '*' => ['id', 'product_id', 'quantity', 'unit_price', 'total_price'],
                    ],
                ],
            ])
            ->assertJsonPath('data.status', 'new');
        $this->assertEqualsWithDelta(350, $response->json('data.total_amount'), 0.01);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'total_amount' => 350,
        ]);

        $product1->refresh();
        $product2->refresh();
        $this->assertSame(8, $product1->stock_quantity);
        $this->assertSame(2, $product2->stock_quantity);
    }

    public function test_order_creation_fails_when_insufficient_stock(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create([
            'stock_quantity' => 2,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ]);

        $response->assertStatus(422);
        $this->assertSame(2, $product->fresh()->stock_quantity);
    }
}
