<?php

namespace Tests\Unit\Domain\Order;

use App\Domain\Order\Entities\Order as DomainOrder;
use App\Models\Order as EloquentOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class OrderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_transition_according_to_valid_transitions(): void
    {
        $eloquent = EloquentOrder::factory()->create([
            'status' => EloquentOrder::STATUS_NEW,
        ]);

        $domain = (new DomainOrder())->setEloquentModel($eloquent);

        $this->assertTrue($domain->canTransitionTo(EloquentOrder::STATUS_CONFIRMED));
        $this->assertTrue($domain->canTransitionTo(EloquentOrder::STATUS_CANCELLED));
        $this->assertFalse($domain->canTransitionTo(EloquentOrder::STATUS_COMPLETED));
    }

    public function test_change_status_updates_timestamps_and_validates_transition(): void
    {
        $eloquent = EloquentOrder::factory()->create([
            'status' => EloquentOrder::STATUS_NEW,
            'confirmed_at' => null,
        ]);

        $domain = (new DomainOrder())->setEloquentModel($eloquent);

        $domain->changeStatus(EloquentOrder::STATUS_CONFIRMED);

        $this->assertSame(EloquentOrder::STATUS_CONFIRMED, $eloquent->status);
        $this->assertNotNull($eloquent->confirmed_at);
    }

    public function test_change_status_throws_on_invalid_transition(): void
    {
        $eloquent = EloquentOrder::factory()->create([
            'status' => EloquentOrder::STATUS_COMPLETED,
        ]);

        $domain = (new DomainOrder())->setEloquentModel($eloquent);

        $this->expectException(InvalidArgumentException::class);

        $domain->changeStatus(EloquentOrder::STATUS_NEW);
    }
}

