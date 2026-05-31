<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\Employee;

class Outlet extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'outlets';

    protected $fillable = [
        'name',
        'code',
        'phone',
        'email',
        'city',
        'manager',
        'address',
        'is_active',
        'payment_type',
        'dp_percentage',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'dp_percentage' => 'integer',
    ];

    /**
     * Outlet memiliki banyak karyawan.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'outlet_id');
    }
}
