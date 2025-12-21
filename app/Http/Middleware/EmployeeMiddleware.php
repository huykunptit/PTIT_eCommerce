<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $roleCode = null)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }

        $user = Auth::user();
        $userRole = $user->getRole;

        if (!$userRole) {
            return redirect()->route('home')->with('error', 'Bạn chưa được phân quyền.');
        }

        // Nếu có role_code cụ thể, kiểm tra role đó
        if ($roleCode) {
            $allowedRoles = explode('|', $roleCode);
            if (!in_array($userRole->role_code, $allowedRoles)) {
                return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này.');
            }
        } else {
            // Kiểm tra nếu là nhân viên (không phải admin và user thường)
            $employeeRoles = ['sales', 'shipper', 'auditor', 'packer'];
            if (!in_array($userRole->role_code, $employeeRoles)) {
                return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập.');
            }
        }

        return $next($request);
    }
}

