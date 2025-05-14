<?php

namespace App\Http\Controllers;

use App\Models\TransactionSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionSchedule $transactionSchedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $schedule = $request->validate([
            'visited_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string'],
        ]);

        $schedule['user_id'] = $request->user()->id;
        $schedule['visited_date'] = Carbon::parse($schedule['visited_date'])->format('Y-m-d');
        $transactionSchedule = TransactionSchedule::findOrFail($id);
        $transactionSchedule->update($schedule);

        return response()->json([
            'message' => 'Transaction schedule updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionSchedule $transactionSchedule)
    {
        //
    }
}
