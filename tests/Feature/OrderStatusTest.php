<?php

namespace Tests\Feature;

use App\Infrastructure\Laravel\Jobs\ExportOrderJob;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_change_to_confirmed_dispatches_export_job(): void
    {
        Queue::fake();
        Http::fake();

        $order = Order::factory()->create([
            'status' => Order::STATUS_NEW,
        ]);

        $response = $this->patchJson("/api/v1/orders/{$order->id}/status", [
            'status' => Order::STATUS_CONFIRMED,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', Order::STATUS_CONFIRMED);

        Queue::assertPushed(ExportOrderJob::class);
    }

    public function test_invalid_status_transition_returns_422(): void
    {
        $order = Order::factory()->create([
            'status' => Order::STATUS_NEW,
        ]);

        $response = $this->patchJson("/api/v1/orders/{$order->id}/status", [
            'status' => Order::STATUS_SHIPPED,
        ]);

        $response->assertStatus(422);
    }
}
