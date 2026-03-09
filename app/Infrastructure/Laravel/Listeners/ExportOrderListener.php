<?php

namespace App\Infrastructure\Laravel\Listeners;

use App\Infrastructure\Laravel\Events\OrderStatusChanged;
use App\Infrastructure\Laravel\Jobs\ExportOrderJob;
use App\Models\Order;
use App\Models\OrderExport;

class ExportOrderListener
{
    public function handle(OrderStatusChanged $event): void
    {
        if ($event->newStatus !== Order::STATUS_CONFIRMED) {
            return;
        }

        $export = OrderExport::create([
            'order_id' => $event->order->id,
            'status' => OrderExport::STATUS_PENDING,
            'attempts' => 0,
            'payload' => $event->order->load(['customer', 'items.product'])->toArray(),
        ]);

        ExportOrderJob::dispatch($export);
    }
}
