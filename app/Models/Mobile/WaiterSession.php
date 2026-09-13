<?php

declare(strict_types=1);

namespace Modules\Mobile\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Modules\Xot\Models\XotBaseModel;

/**
 * @method static \Illuminate\Database\Eloquent\Factories\Factory<static> factory($count = null, $state = [])
 * @property string $id
 * @property string $token
 * @property string|null $user_id
 * @property string|null $device_id
 * @property string|null $shift_id
 * @property float|null $location_lat
 * @property float|null $location_lng
 * @property \Illuminate\Support\Carbon|null $last_active_at
 */
=======
use Illuminate\Database\Eloquent\Model;
use Modules\Xot\Models\XotBaseModel;

>>>>>>> laraxot/dev
class WaiterSession extends XotBaseModel
{
    use HasUuids;

    protected $table = 'mobile_waiter_sessions';

    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'platform',
        'token',
        'biometric_enabled',
        'last_active_at',
        'is_active',
        'location_lat',
        'location_lng',
        'shift_id',
        'notes',
    ];

    protected $casts = [
        'biometric_enabled' => 'boolean',
        'is_active' => 'boolean',
        'last_active_at' => 'datetime',
        'location_lat' => 'float',
        'location_lng' => 'float',
    ];

<<<<<<< HEAD
    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
=======
    public function user()
    {
        return $this->belongsTo(
            config('auth.providers.user.model', 'App\Models\User'),
>>>>>>> laraxot/dev
            'user_id'
        );
    }

<<<<<<< HEAD
    /** @return BelongsTo<\Modules\Restaurant\Models\StaffShift, $this> */
    public function shift(): BelongsTo
=======
    public function shift()
>>>>>>> laraxot/dev
    {
        return $this->belongsTo(\Modules\Restaurant\Models\StaffShift::class, 'shift_id');
    }

<<<<<<< HEAD
    /**
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeActive(Builder $query): Builder
=======
    public function scopeActive($query)
>>>>>>> laraxot/dev
    {
        return $query->where('is_active', true);
    }

<<<<<<< HEAD
    /**
     * @param Builder<self> $query
     * @return Builder<self>
     */
    public function scopeForDevice(Builder $query, string $deviceId): Builder
=======
    public function scopeForDevice($query, string $deviceId)
>>>>>>> laraxot/dev
    {
        return $query->where('device_id', $deviceId);
    }
}
