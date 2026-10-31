<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Counter;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $request->get('branch_id', Branch::first()->id ?? null);
        $branches = Branch::all();
        
        $counters = Counter::with(['branch', 'currentUser', 'services', 'currentToken'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->orderBy('counter_number')
            ->get();

        $services = ServiceCategory::where('is_active', true)->get();
        $tellers = User::where('role', 'teller')->get();

        return view('admin.counters.index', compact('counters', 'branches', 'branchId', 'services', 'tellers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'counter_number' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'type' => 'required|in:general,priority_dedicated,cash,inquiries',
            'current_user_id' => 'nullable|exists:users,id',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:service_categories,id',
        ]);

        $counter = Counter::create([
            'branch_id' => $validated['branch_id'],
            'counter_number' => $validated['counter_number'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'status' => 'online',
            'current_user_id' => $validated['current_user_id'] ?? null,
        ]);

        if (!empty($validated['service_ids'])) {
            $counter->services()->sync($validated['service_ids']);
        }

        if (!empty($validated['current_user_id'])) {
            User::where('id', $validated['current_user_id'])->update(['assigned_counter_id' => $counter->id]);
        }

        return redirect()->route('admin.counters.index', ['branch_id' => $counter->branch_id])
            ->with('success', 'Counter created successfully.');
    }

    public function update(Request $request, Counter $counter)
    {
        $validated = $request->validate([
            'counter_number' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'type' => 'required|in:general,priority_dedicated,cash,inquiries',
            'status' => 'required|in:online,offline,break,serving',
            'current_user_id' => 'nullable|exists:users,id',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:service_categories,id',
        ]);

        $counter->update([
            'counter_number' => $validated['counter_number'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'status' => $validated['status'],
            'current_user_id' => $validated['current_user_id'] ?? null,
        ]);

        $counter->services()->sync($validated['service_ids'] ?? []);

        return redirect()->route('admin.counters.index', ['branch_id' => $counter->branch_id])
            ->with('success', 'Counter updated successfully.');
    }

    public function destroy(Counter $counter)
    {
        $branchId = $counter->branch_id;
        $counter->delete();
        return redirect()->route('admin.counters.index', ['branch_id' => $branchId])
            ->with('success', 'Counter deleted successfully.');
    }
}
