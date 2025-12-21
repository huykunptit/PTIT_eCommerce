<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Đơn hàng mới #' . $this->order->id)
                    ->line('Bạn có một đơn hàng mới cần xử lý.')
                    ->action('Xem đơn hàng', route('admin.orders.show', $this->order->id))
                    ->line('Cảm ơn bạn đã sử dụng hệ thống!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Đơn hàng mới #' . $this->order->id,
            'message' => 'Khách hàng ' . ($this->order->user->name ?? $this->order->shipping_name) . ' đã đặt đơn hàng với tổng tiền ' . number_format($this->order->total_amount, 0, ',', '.') . '₫',
            'type' => 'order',
            'url' => route('admin.orders.show', $this->order->id),
        ];
    }
}

