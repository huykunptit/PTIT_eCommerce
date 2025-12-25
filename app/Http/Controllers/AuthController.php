<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
   
    public function showLoginForm()
    {
        return view('auth.login');
    }

   
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = $request->has('remember');
        $loginValue = $request->input('login');
        $password = $request->input('password');

        // Tìm user theo email hoặc phone
        $user = User::where('email', $loginValue)
                    ->orWhere('phone_number', $loginValue)
                    ->first();

        // Kiểm tra user và password
        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $remember);
            $request->session()->regenerate();
            
            // Kiểm tra role của user
            $userRole = $user->getRole;
            $roleCode = $userRole->role_code ?? '';
            
            if($roleCode === 'admin'){
                // Redirect đến admin dashboard
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Đăng nhập thành công!');
            }
            elseif(in_array($roleCode, ['sales', 'shipper', 'packer', 'auditor'])){
                // Redirect đến employee dashboard
                return redirect()->intended(route('employee.dashboard'))
                    ->with('success', 'Đăng nhập thành công!');
            }
            else {
                return redirect()->intended(route('home'))
                    ->with('success', 'Đăng nhập thành công!');
            }
        }

        return back()->withErrors([
            'login' => 'Email/Số điện thoại hoặc mật khẩu không chính xác.',
        ])->withInput($request->only('login'));
    }

    // Facebook Login
    public function facebookLogin()
    {
        // Placeholder for Facebook OAuth
        // In production, integrate with Laravel Socialite or Facebook SDK
        return redirect()->route('auth.login')
            ->with('error', 'Tính năng đăng nhập Facebook đang được phát triển. Vui lòng sử dụng email hoặc số điện thoại.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Đăng xuất thành công!');
    }


    public function showRegisterForm()
    {
        return view('auth.register');
    }


    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => 'user', // Mặc định là user
        ]);

        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Đăng ký tài khoản thành công!');
    }

 
    public function dashboard()
    {
        if(Auth::user()->role === 'admin'){
            return view('admin.index');
        }
    }

    // ========== API METHODS = =========
    
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Đăng nhập người dùng",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Đăng nhập thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="token", type="string", example="1|xxxxxxxxxxxx")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Thông tin đăng nhập không hợp lệ")
     * )
     */
    // API Login
    public function apiLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Đăng ký tài khoản mới",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *             @OA\Property(property="phone_number", type="string", nullable=true, example="0123456789"),
     *             @OA\Property(property="address", type="string", nullable=true, example="123 Đường ABC")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Đăng ký thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="token", type="string", example="1|xxxxxxxxxxxx")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Lỗi validation")
     * )
     */
    // API Register
    public function apiRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'role' => 'user',
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Đăng xuất",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Đăng xuất thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    // API Logout
    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/profile",
     *     summary="Lấy thông tin profile người dùng",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin người dùng",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    // API Profile
    public function apiProfile(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }
}
