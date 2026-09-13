<?php

declare(strict_types=1);

namespace Modules\Mobile\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Xot\Models\XotBaseModel;

/**
 * @method static \Illuminate\Database\Eloquent\Factories\Factory<static> factory($count = null, $state = [])
 * @property string $id
 * @property int $table_id
 * @property array<string, mixed> $order_data
 * @property int $sync_attempts
 */
=======
use Illuminate\Database\Eloquent\Model;
use Modules\Xot\Models\XotBaseModel;

>>>>>>> laraxot/dev
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

<<<<<<< HEAD
    /** @return BelongsTo<WaiterSession, $this> */
    public function waiterSession(): BelongsTo
=======
    public function waiterSession()
>>>>>>> laraxot/dev
    {
        return $this->belongsTo(WaiterSession::class, 'waiter_session_id');
    }

<<<<<<< HEAD
    /** @return BelongsTo<\Modules\Restaurant\Models\DiningTable, $this> */
    public function table(): BelongsTo
=======
    public function table()
>>>>>>> laraxot/dev
    {
        return $this->belongsTo(\Modules\Restaurant\Models\DiningTable::class, 'table_id');
    }

<<<<<<< HEAD
    /**
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopePending(Builder $query): Builder
=======
    public function scopePending($query)
>>>>>>> laraxot/dev
    {
        return $query->where('status', self::STATUS_PENDING);
    }

<<<<<<< HEAD
    /**
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FAILED);
    }
}
=======
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }
}
>>>>>>> laraxot/dev
