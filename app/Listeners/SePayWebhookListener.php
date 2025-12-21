<?php

namespace App\Listeners;

use App\Models\Order;
use App\Mail\OrderStatusUpdateMail;
use Illuminate\Support\Facades\Mail;
use SePay\SePay\Events\SePayWebhookEvent;

class SePayWebhookListener
{
    /**
     * Handle the event.
     */
    public function handle(SePayWebhookEvent $event): void
    {
        $data = $event->sePayWebhookData;

        // Chỉ xử lý tiền vào
        if ($data->transferType !== 'in') {
            return;
        }

        // Tìm mã đơn trong nội dung chuyển khoản, dạng ORDER_123
        $content = $data->content ?? $data->description ?? '';
        if (preg_match('/ORDER_(\d+)/', $content, $matches)) {
            $orderId = (int) $matches[1];
            $order = Order::find($orderId);

            if ($order && $order->status === 'pending_payment') {
                $order->update([
                    'status' => 'paid',
                ]);

                // Gửi email thông báo đã thanh toán
                try {
                    $email = $order->shipping_email ?? $order->user->email ?? null;
                    if ($email) {
                        Mail::to($email)->queue(new OrderStatusUpdateMail($order, 'paid'));
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send payment confirmed email (SePay webhook): ' . $e->getMessage());
                }
            }
        }
    }
}


