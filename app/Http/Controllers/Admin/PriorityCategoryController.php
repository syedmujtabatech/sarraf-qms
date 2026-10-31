<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriorityCategory;
use Illuminate\Http\Request;

class PriorityCategoryController extends Controller
{
    public function index()
    {
        $priorities = PriorityCategory::withCount('tokens')->orderBy('weight_multiplier', 'desc')->get();
        return view('admin.priorities.index', compact('priorities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ur' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:priority_categories,code',
            'weight_multiplier' => 'required|integer|min:1|max:10',
            'badge_color' => 'required|string|max:30',
            'icon' => 'required|string|max:50',
            'regulatory_note' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');
        PriorityCategory::create($validated);

        return redirect()->route('admin.priorities.index')->with('success', 'Priority category created.');
    }

    public function update(Request $request, PriorityCategory $priority)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ur' => 'required|string|max:255',
            'weight_multiplier' => 'required|integer|min:1|max:10',
            'badge_color' => 'required|string|max:30',
            'icon' => 'required|string|max:50',
            'regulatory_note' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $priority->update($validated);

        return redirect()->route('admin.priorities.index')->with('success', 'Priority category updated.');
    }
}
