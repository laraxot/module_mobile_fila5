<?php

declare(strict_types=1);

namespace Modules\Mobile\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Modules\Xot\Models\XotBaseModel;

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

    public function user()
    {
        return $this->belongsTo(
            config('auth.providers.user.model', 'App\Models\User'),
            'user_id'
        );
    }

    public function shift()
    {
        return $this->belongsTo(\Modules\Restaurant\Models\StaffShift::class, 'shift_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDevice($query, string $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }
}
