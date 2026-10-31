<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::withCount('branches')->get();
        return view('admin.regions.index', compact('regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|unique:regions,code|max:20',
            'city_headquarters' => 'required|string|max:50',
            'regional_manager_name' => 'nullable|string|max:100',
            'contact_email' => 'nullable|email|max:100',
            'contact_phone' => 'nullable|string|max:50',
        ]);

        Region::create($validated);

        return redirect()->route('admin.regions.index')->with('success', 'Banking region cluster created.');
    }

    public function update(Request $request, Region $region)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:regions,code,' . $region->id,
            'city_headquarters' => 'required|string|max:50',
            'regional_manager_name' => 'nullable|string|max:100',
            'contact_email' => 'nullable|email|max:100',
            'contact_phone' => 'nullable|string|max:50',
        ]);

        $region->update($validated);

        return redirect()->route('admin.regions.index')->with('success', 'Region updated successfully.');
    }

    public function destroy(Region $region)
    {
        $region->delete();
        return redirect()->route('admin.regions.index')->with('success', 'Region deleted.');
    }
}
