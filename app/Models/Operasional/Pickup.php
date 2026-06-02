<?php

namespace App\Models\Operasional;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Master\Outlet;
use App\Models\Master\Employee;

#[Fillable([
    'trip_code',
    'customer_name',
    'customer_phone',
    'customer_id',
    'outlet_id',
    'order_code',
    'address_from',
    'address_to',
    'service_type',
    'employee_id',
    'distance',
    'eta',
    'fee',
    'scheduled_at',
    'weight',
    'notes',
    'status',
    'avatar_color',
])]
class Pickup extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pickups';

    protected static function booted()
    {
        static::creating(function ($pickup) {
            if (empty($pickup->trip_code)) {
                $pickup->trip_code = 'TRP-' . date('Y') . '-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'distance' => 'decimal:2',
            'fee' => 'integer',
            'scheduled_at' => 'datetime',
        ];
    }

    /**
     * Get the customer associated with the pickup.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the outlet associated with the pickup.
     */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }

    /**
     * Get the driver (employee) associated with the pickup.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
