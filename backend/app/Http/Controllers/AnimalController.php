<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use Illuminate\Http\Request;

class AnimalController extends Controller
{
    /**
     * Display a listing of the resource. GET
     */
    public function index()
    {
        return response()->json(Animal::all());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage. POST
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:1', 'unique:animals,name'],
        ]);
        $animal = Animal::create([
            'name' => $request->name,
        ]);
        return response()->json(['message' => 'Created animal successfully', 'animal' => $animal]);
    }

    /**
     * Display the specified resource. GET{ID}
     */
    public function show(Animal $animal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Animal $animal)
    {
        //
    }

    /**
     * Update the specified resource in storage. PUT
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:1', 'unique:animals,name'],
        ]);
        $animal = Animal::findOrfail($id)->update($validated);
        return response()->json(['animal' => $animal, 'message' => 'Animal updated successfully']);
    }

    /**
     * Remove the specified resource from storage. DELETE
     */
    public function destroy($id)
    {
        $animal = Animal::findOrfail($id)->delete();
        return response()->json(['animal' => $animal, 'message' => 'Animal deleted successfully']);
    }
}
