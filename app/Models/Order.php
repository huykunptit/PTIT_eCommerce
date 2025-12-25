<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function getBuyerStatusAttribute(): string
    {
        $shippingStatus = (string) ($this->shipping_status ?? '');

        if ($shippingStatus === '') {
            // Fallback for legacy rows
            if (($this->status ?? null) === 'canceled') {
                return 'cancelled';
            }
            return 'pending_confirmation';
        }

        if (in_array($shippingStatus, ['cancelled', 'returned', 'pending_confirmation', 'pending_pickup', 'in_transit'], true)) {
            return $shippingStatus;
        }

        if ($shippingStatus === 'delivered') {
            return $this->hasAnyProductReviewForThisOrder() ? 'delivered' : 'awaiting_review';
        }

        return $shippingStatus;
    }

    public function getBuyerStatusLabelAttribute(): string
    {
        return self::buyerStatusLabel($this->buyer_status);
    }

    public static function buyerStatusLabel(string $buyerStatus): string
    {
        return match ($buyerStatus) {
            'pending_confirmation' => 'Chờ xác nhận',
            'pending_pickup' => 'Chờ lấy hàng',
            'in_transit' => 'Đang giao',
            'awaiting_review' => 'Đánh giá',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy',
            'returned' => 'Trả hàng',
            default => ucfirst(str_replace('_', ' ', $buyerStatus)),
        };
    }

    private function hasAnyProductReviewForThisOrder(): bool
    {
        $userId = $this->user_id;
        if (!$userId) {
            return false;
        }

        return DB::table('order_items as oi')
            ->join('product_reviews as pr', function ($join) use ($userId) {
                $join->on('pr.product_id', '=', 'oi.product_id')
                    ->where('pr.user_id', '=', $userId);
            })
            ->where('oi.order_id', '=', $this->id)
            ->exists();
    }
} 