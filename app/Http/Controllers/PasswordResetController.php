<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $request->email]);

        try {
            Mail::send('emails.password-reset', ['resetUrl' => $resetUrl, 'token' => $token], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Đặt lại mật khẩu - PTIT eCommerce');
            });
        } catch (\Exception $e) {
            \Log::error('Email send failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Không thể gửi email. Vui lòng kiểm tra cấu hình SMTP.']);
        }

        return back()->with('success', 'Chúng tôi đã gửi link reset mật khẩu đến email của bạn!');
    }

    public function showResetPasswordForm(Request $request, $token = null)
    {
        $token = $token ?? $request->token;
        $email = $request->email;
        
        if (!$token || !$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Invalid reset link.']);
        }
        
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required'
        ]);

        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return back()->withErrors(['email' => 'Invalid reset token.'])->withInput();
        }

        if (!Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors(['email' => 'Invalid reset token.'])->withInput();
        }

        // Check if token is expired (24 hours)
        if (Carbon::parse($passwordReset->created_at)->addHours(24)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Reset token has expired. Please request a new one.'])->withInput();
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('auth.login')->with('success', 'Mật khẩu đã được đặt lại thành công! Vui lòng đăng nhập.');
    }
}
