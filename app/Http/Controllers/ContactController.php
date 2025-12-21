<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        try {
            // Gửi email đến admin
            $adminEmail = config('mail.from.address', 'admin@ptit-ecommerce.com');
            
            Mail::send('emails.contact', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? 'N/A',
                'subject' => $validated['subject'] ?? 'Liên hệ từ website',
                'message' => $validated['message'],
            ], function ($mail) use ($adminEmail, $validated) {
                $mail->to($adminEmail)
                     ->subject('Liên hệ mới từ website - ' . ($validated['subject'] ?? 'Không có chủ đề'));
            });

            return redirect()->route('contact')->with('success', 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.');
        } catch (\Exception $e) {
            Log::error('Contact form submission error: ' . $e->getMessage());
            return redirect()->route('contact')
                ->with('error', 'Có lỗi xảy ra khi gửi tin nhắn. Vui lòng thử lại sau.')
                ->withInput();
        }
    }
}

