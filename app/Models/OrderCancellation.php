<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCancellation extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'reason',
        'reason_detail',
        'status',
        'admin_note',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getReasonTextAttribute()
    {
        $reasons = [
            'changed_mind' => 'Thay đổi ý định',
            'found_cheaper' => 'Tìm thấy sản phẩm rẻ hơn',
            'wrong_item' => 'Đặt nhầm sản phẩm',
            'delivery_too_long' => 'Thời gian giao hàng quá lâu',
            'payment_issue' => 'Vấn đề thanh toán',
            'other' => 'Lý do khác',
        ];

        return $reasons[$this->reason] ?? $this->reason;
    }
}
