<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Brand::withCount('transaction')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:1', 'unique:brands,name'],
        ]);
        Brand::create($validated);
        return response()->json(['message' => 'Created Brand Successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:1', 'unique:animals,name'],
        ]);
        Brand::findOrFail($id)->update($validated);
        return response()->json(['message' => 'Updated Brand Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Brand::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted Brand Successfully']);
    }
}
