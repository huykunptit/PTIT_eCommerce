<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
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
            'defective' => 'Sản phẩm bị lỗi',
            'wrong_item' => 'Nhận nhầm sản phẩm',
            'not_as_described' => 'Không đúng như mô tả',
            'damaged_during_shipping' => 'Bị hỏng trong quá trình vận chuyển',
            'size_issue' => 'Vấn đề về kích thước',
            'color_issue' => 'Vấn đề về màu sắc',
            'other' => 'Lý do khác',
        ];

        return $reasons[$this->reason] ?? $this->reason;
    }
}
