<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $services = ServiceCategory::withCount('tokens')->orderBy('sort_order')->get();
        return view('admin.services.index', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ur' => 'required|string|max:255',
            'prefix' => 'required|string|max:5',
            'description' => 'nullable|string',
            'avg_service_time_mins' => 'required|integer|min:1',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:30',
        ]);

        $validated['is_active'] = $request->has('is_active');
        ServiceCategory::create($validated);

        return redirect()->route('admin.services.index')->with('success', 'Service category created successfully.');
    }

    public function update(Request $request, ServiceCategory $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ur' => 'required|string|max:255',
            'prefix' => 'required|string|max:5',
            'description' => 'nullable|string',
            'avg_service_time_mins' => 'required|integer|min:1',
            'icon' => 'required|string|max:50',
            'color' => 'required|string|max:30',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $service->update($validated);

        return redirect()->route('admin.services.index')->with('success', 'Service category updated successfully.');
    }

    public function destroy(ServiceCategory $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service category deleted.');
    }
}
