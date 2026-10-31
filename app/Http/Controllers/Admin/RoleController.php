<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with(['permissions', 'users'])->withCount('users')->get();
        $permissionsByModule = Permission::all()->groupBy('module');

        return view('admin.roles.index', compact('roles', 'permissionsByModule'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name|max:50',
            'display_name' => 'required|string|max:100',
            'badge_color' => 'required|string|max:30',
            'description' => 'nullable|string|max:255',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => strtolower(str_replace(' ', '_', $validated['name'])),
            'display_name' => $validated['display_name'],
            'badge_color' => $validated['badge_color'],
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        if (!empty($validated['permission_ids'])) {
            $role->permissions()->sync($validated['permission_ids']);
        }

        return redirect()->route('admin.roles.index')->with('success', "Role '{$role->display_name}' created with custom permissions.");
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:100',
            'badge_color' => 'required|string|max:30',
            'description' => 'nullable|string|max:255',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'display_name' => $validated['display_name'],
            'badge_color' => $validated['badge_color'],
            'description' => $validated['description'] ?? null,
        ]);

        $role->permissions()->sync($validated['permission_ids'] ?? []);

        return redirect()->route('admin.roles.index')->with('success', "Role '{$role->display_name}' permissions updated successfully.");
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'Core banking system roles cannot be deleted.');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }
}
