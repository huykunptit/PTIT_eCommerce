<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','admin']);
    }

    public function index()
    {
        $roles = Roles::orderBy('role_name')->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:255',
            'role_code' => 'required|string|max:100|unique:roles,role_code',
        ]);
        Roles::create($validated);
        return redirect()->route('admin.roles.index')->with('success', 'Tạo vai trò thành công');
    }

    public function edit(Roles $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Roles $role)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:255',
            'role_code' => 'required|string|max:100|unique:roles,role_code,'.$role->id,
        ]);
        $role->update($validated);
        return redirect()->route('admin.roles.index')->with('success', 'Cập nhật vai trò thành công');
    }

    public function destroy(Roles $role)
    {
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Xóa vai trò thành công');
    }
}


