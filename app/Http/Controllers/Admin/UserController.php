<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Counter;
use App\Models\Region;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $roleId = $request->get('role_id');
        $branchId = $request->get('branch_id');

        $users = User::with(['roleModel', 'branch.region', 'assignedCounter'])
            ->when($roleId, fn($q) => $q->where('role_id', $roleId))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('id', 'asc')
            ->get();

        $roles = Role::all();
        $branches = Branch::all();
        $regions = Region::all();
        $counters = Counter::all();

        return view('admin.users.index', compact('users', 'roles', 'branches', 'regions', 'counters', 'roleId', 'branchId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email|max:100',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'region_id' => 'nullable|exists:regions,id',
            'assigned_counter_id' => 'nullable|exists:counters,id',
        ]);

        $role = Role::findOrFail($validated['role_id']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $role->name,
            'role_id' => $role->id,
            'branch_id' => $validated['branch_id'] ?? null,
            'region_id' => $validated['region_id'] ?? null,
            'assigned_counter_id' => $validated['assigned_counter_id'] ?? null,
            'is_active' => true,
        ]);

        if (!empty($validated['assigned_counter_id'])) {
            Counter::where('id', $validated['assigned_counter_id'])->update(['current_user_id' => $user->id]);
        }

        return redirect()->route('admin.users.index')->with('success', "Staff user '{$user->name}' added successfully.");
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
            'region_id' => 'nullable|exists:regions,id',
            'assigned_counter_id' => 'nullable|exists:counters,id',
            'is_active' => 'boolean',
        ]);

        $role = Role::findOrFail($validated['role_id']);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $role->name,
            'role_id' => $role->id,
            'branch_id' => $validated['branch_id'] ?? null,
            'region_id' => $validated['region_id'] ?? null,
            'assigned_counter_id' => $validated['assigned_counter_id'] ?? null,
            'is_active' => $request->has('is_active'),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        if (!empty($validated['assigned_counter_id'])) {
            Counter::where('id', $validated['assigned_counter_id'])->update(['current_user_id' => $user->id]);
        }

        return redirect()->route('admin.users.index')->with('success', "Staff user '{$user->name}' updated successfully.");
    }

    public function destroy(User $user)
    {
        if ($user->id === 1) {
            return back()->with('error', 'Super Administrator account cannot be deleted.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Staff account deleted.');
    }
}
