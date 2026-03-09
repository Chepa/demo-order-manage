<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderExport extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'order_id',
        'status',
        'attempts',
        'payload',
        'exported_at',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'attempts' => 'integer',
            'payload' => 'array',
            'exported_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
