<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Region;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::with(['region'])->withCount(['counters', 'tokens' => function ($q) {
            $q->whereDate('created_at', today());
        }])->get();

        $regions = Region::where('is_active', true)->get();

        return view('admin.branches.index', compact('branches', 'regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'region_id' => 'nullable|exists:regions,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:branches,code|max:20',
            'city' => 'required|string|max:100',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'open_time' => 'required',
            'close_time' => 'required',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        Branch::create($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Branch created successfully.');
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'region_id' => 'nullable|exists:regions,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:branches,code,' . $branch->id,
            'city' => 'required|string|max:100',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'open_time' => 'required',
            'close_time' => 'required',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $branch->update($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('admin.branches.index')->with('success', 'Branch deleted successfully.');
    }
}
