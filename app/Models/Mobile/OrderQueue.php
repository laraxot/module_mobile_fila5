<?php

declare(strict_types=1);

namespace Modules\Mobile\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Modules\Xot\Models\XotBaseModel;

class OrderQueue extends XotBaseModel
{
    use HasUuids;

    protected $table = 'mobile_order_queue';

    protected $fillable = [
        'waiter_session_id',
        'table_id',
        'order_data',
        'status',
        'sync_attempts',
        'last_sync_at',
        'error_message',
    ];

    protected $casts = [
        'order_data' => 'array',
        'sync_attempts' => 'integer',
        'last_sync_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_SYNCING = 'syncing';
    public const STATUS_SYNCED = 'synced';
    public const STATUS_FAILED = 'failed';

    public function waiterSession()
    {
        return $this->belongsTo(WaiterSession::class, 'waiter_session_id');
    }

    public function table()
    {
        return $this->belongsTo(\Modules\Restaurant\Models\DiningTable::class, 'table_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }
}