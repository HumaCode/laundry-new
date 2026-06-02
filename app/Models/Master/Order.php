<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

#[Fillable([
    'order_code',
    'customer_id',
    'outlet_id',
    'service_type',
    'weight',
    'price_per_unit',
    'total_price',
    'order_status',
    'payment_status',
    'payment_method',
    'finished_at',
    'notes',
])]
class Order extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'orders';

    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->order_code)) {
                $order->order_code = 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
            if (empty($order->total_price)) {
                $order->total_price = ($order->weight ?? 0) * ($order->price_per_unit ?? 0);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'price_per_unit' => 'integer',
            'total_price' => 'integer',
            'finished_at' => 'datetime',
        ];
    }

    /**
     * Get the customer that placed the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the outlet where the order was placed.
     */
    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class, 'outlet_id');
    }
}
