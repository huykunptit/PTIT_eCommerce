<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Roles;
class UserController extends Controller
{
    //User Management


    // public function roles() {
    //     $roles = Roles::all();
    // }
    public function users()
    {
        $users = User::all();
        // dd($users);
        $roles = Roles::all();
        return view('admin.users.index', compact('users','roles'));
    }

    public function createUser()
    {   
        $roles = Roles::all();
        return view('admin.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'role_id' => 'required|exists:roles,id',
            'status' => 'nullable|in:active,inactive',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $datePrefix = now()->format('Ymd');
            $file = $request->file('photo');
            $filename = $datePrefix . '_' . $file->getClientOriginalName();
            $destination = public_path('uploads/img/user');
            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $photoPath = 'uploads/img/user/' . $filename;
        }
        // dd($request);
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'photo' => $photoPath,
            'phone_number' => $validated['phone_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'role_id' => $validated['role_id'],
            'status' => $request->input('status', 'active'),
            'permissions' => $request->input('permissions', []),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $roles = Roles::all();
        return view('admin.users.edit', compact('user','roles'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:4096',
            'role_id' => 'required|exists:roles,id',
            'status' => 'nullable|in:active,inactive',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'status' => $request->input('status', $user->status),
            'phone_number' => $validated['phone_number'] ?? $user->phone_number,
            'address' => $validated['address'] ?? $user->address,
            'permissions' => $request->input('permissions', $user->permissions ?? []),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        if ($request->hasFile('photo')) {
            $datePrefix = now()->format('Ymd');
            $file = $request->file('photo');
            $filename = $datePrefix . '_' . $file->getClientOriginalName();
            $destination = public_path('uploads/img/user');
            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $data['photo'] = 'uploads/img/user/' . $filename;
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng ' . $user->name . ' thành công!');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Xoá người dùng ' . $user->name . ' thành công!');
    }

}
