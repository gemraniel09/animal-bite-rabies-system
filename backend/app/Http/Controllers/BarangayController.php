<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use Illuminate\Http\JsonResponse;

class BarangayController extends Controller
{
    public function fetch(): JsonResponse
    {
        return response()->json(Barangay::all());
    }
}
