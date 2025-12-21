<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assigned_to',
        'assigned_shipper',
        'assigned_packer',
        'total_amount',
        'status',
        'shipping_status',
        'shipping_name',
        'shipping_phone',
        'shipping_address',
        'shipping_email',
        'notes',
        'payment_method',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function cancellation()
    {
        return $this->hasOne(OrderCancellation::class);
    }

    public function return()
    {
        return $this->hasOne(OrderReturn::class);
    }

    public function assignedSales()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedShipper()
    {
        return $this->belongsTo(User::class, 'assigned_shipper');
    }

    public function assignedPacker()
    {
        return $this->belongsTo(User::class, 'assigned_packer');
    }
} 