<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class ProfileController extends Controller
{
    // Admin Profile
    public function showAdminProfile()
    {
        $user = Auth::user();
        return view('backend.profile.edit', compact('user'));
    }

    public function updateAdminProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone_number = $validated['phone_number'] ?? null;
        $user->address = $validated['address'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }
            
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = 'uploads/img/avatars';
            if (!file_exists(public_path($path))) {
                mkdir(public_path($path), 0755, true);
            }
            $file->move(public_path($path), $filename);
            $user->avatar = $path . '/' . $filename;
        }

        $user->save();

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
    }

    // User Profile
    /**
     * @OA\Get(
     *     path="/profile",
     *     tags={"User - Profile"},
     *     summary="Xem thông tin tài khoản (web)",
     *     description="Yêu cầu phiên đăng nhập web. Endpoint chủ yếu để mô tả luồng người dùng trên Swagger.",
     *     @OA\Response(response=200, description="Trang profile người dùng")
     * )
     */
    public function showUserProfile()
    {
        $user = Auth::user();
        return view('frontend.profile.edit', compact('user'));
    }

    /**
     * @OA\Put(
     *     path="/profile",
     *     tags={"User - Profile"},
     *     summary="Cập nhật thông tin người dùng",
     *     description="Cập nhật tên, email, số điện thoại, địa chỉ, mật khẩu và avatar cho người dùng hiện tại.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email"},
     *             @OA\Property(property="name", type="string", example="Nguyễn Văn A"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="phone_number", type="string", example="0123456789"),
     *             @OA\Property(property="address", type="string", example="Hà Nội"),
     *             @OA\Property(property="password", type="string", format="password", example="new-password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="new-password")
     *         )
     *     ),
     *     @OA\Response(response=302, description="Redirect sau khi cập nhật thành công"),
     *     @OA\Response(response=422, description="Dữ liệu không hợp lệ")
     * )
     */
    public function updateUserProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone_number = $validated['phone_number'] ?? null;
        $user->address = $validated['address'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }
            
            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = 'uploads/img/avatars';
            if (!file_exists(public_path($path))) {
                mkdir(public_path($path), 0755, true);
            }
            $file->move(public_path($path), $filename);
            $user->avatar = $path . '/' . $filename;
        }

        $user->save();

        return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
    }

    // User Orders
    public function showUserOrders(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['items.product', 'payments'])
            ->where('user_id', $user->id);
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Search by order ID
        if ($request->has('search') && $request->search != '') {
            $query->where('id', 'like', '%' . $request->search . '%');
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $orders = $query->paginate(10)->appends($request->all());
        
        return view('frontend.profile.orders', compact('orders'));
    }
}
