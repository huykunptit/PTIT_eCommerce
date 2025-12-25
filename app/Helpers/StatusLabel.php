<?php

namespace App\Helpers;

class StatusLabel
{
    public static function orderStatus(?string $status): string
    {
        $status = (string) ($status ?? '');

        $map = [
            // Current orders.status enum values
            'pending' => 'Chờ xác nhận',
            'pending_payment' => 'Chờ thanh toán',
            'paid' => 'Đã thanh toán',
            'shipped' => 'Đã đóng gói',
            'completed' => 'Hoàn tất',
            'canceled' => 'Đã hủy',

            // Legacy/other code paths
            'new' => 'Mới',
            'process' => 'Đang xử lý',
            'processing' => 'Đang xử lý',
            'delivered' => 'Đã giao',
            'cancel' => 'Đã hủy',
            'cancelled' => 'Đã hủy',
        ];

        if ($status === '') {
            return '';
        }

        return $map[$status] ?? self::humanize($status);
    }

    public static function shippingStatus(?string $status): string
    {
        $status = (string) ($status ?? '');

        $map = [
            'pending_confirmation' => 'Chờ xác nhận',
            'pending_pickup' => 'Chờ lấy hàng',
            'in_transit' => 'Đang giao',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy',
            'returned' => 'Hoàn trả',
        ];

        if ($status === '') {
            return '';
        }

        return $map[$status] ?? self::humanize($status);
    }

    public static function returnStatus(?string $status): string
    {
        $status = (string) ($status ?? '');

        $map = [
            'pending' => 'Chờ xử lý',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn tất',
        ];

        if ($status === '') {
            return '';
        }

        return $map[$status] ?? self::humanize($status);
    }

    private static function humanize(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $value = str_replace(['_', '-'], ' ', $value);
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }
}
