<?php

namespace App\Infrastructure\Laravel\Jobs;

use App\Models\OrderExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExportOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public OrderExport $export
    ) {}

    public function handle(): void
    {
        $this->export->increment('attempts');

        try {
            $response = Http::timeout(30)->post(
                config('services.order_export.url'),
                $this->export->payload
            );

            if (! $response->successful()) {
                throw new \RuntimeException("Export failed with status: {$response->status()}");
            }

            $this->export->update([
                'status' => OrderExport::STATUS_COMPLETED,
                'exported_at' => now(),
                'error_message' => null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Order export failed', [
                'export_id' => $this->export->id,
                'order_id' => $this->export->order_id,
                'attempt' => $this->export->attempts,
                'error' => $e->getMessage(),
            ]);

            if ($this->export->attempts >= $this->tries) {
                $this->export->update([
                    'status' => OrderExport::STATUS_FAILED,
                    'error_message' => $e->getMessage(),
                ]);
            }

            throw $e;
        }
    }
}
