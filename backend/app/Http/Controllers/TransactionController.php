<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Transaction;
use App\Models\TransactionSchedule;
use App\Rules\ValidScheduleIntervals;
use App\Services\AnalyticService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $transactions = Transaction::with('animal')
            ->with(['schedules.user', 'patient', 'barangay', 'brand']);

        if ($request->filled('search')) {
            $name = strtolower($request->input('search'));
            $transactions->whereHas('patient', function ($query) use ($name) {
                $query->whereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ['%' . $name . '%']);
            });
        }

        if ($request->filled('dateFrom') && $request->filled('dateTo')) {
            $dateFrom = Carbon::parse($request->input('dateFrom'))->startOfDay();
            $dateTo = Carbon::parse($request->input('dateTo'))->endOfDay();
            $transactions->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
        if ($request->filled('animal')) {
            $transactions->where('animal_id', $request->input('animal'));
        }
        if ($request->filled('barangay')) {;
            $transactions->where('barangay_id', $request->input('barangay'));
        }
        if ($request->filled('brand')) {;
            $transactions->where('brand_id', $request->input('brand'));
        }

        return response()->json($transactions->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $patient = $request->validate([
            'patient' => ['required', 'array'],
            'patient.id' => ['nullable', 'integer'],
            'patient.first_name' => ['required', 'string', 'max:255'],
            'patient.middle_name' => ['required', 'string', 'max:255'],
            'patient.last_name' => ['required', 'string', 'max:255'],
            'patient.barangay_id' => ['required', 'int'],
            'patient.birth_date' => ['required', 'date', 'before_or_equal:today'],
            'patient.gender' => ['required', 'string', 'in:male,female'],
        ]);

        $transaction = $request->validate([
            'transaction' => ['required', 'array'],
            'transaction.barangay_id' => ['required', 'int', 'exists:barangays,id'],
            'transaction.place' => ['required', 'string', 'max:255'],
            'transaction.vaccination_status' => ['required', 'string', 'in:optional_opted_in,optional_opted_out,required'],
            'transaction.body_part' => ['required', 'string', 'max:255'],
            'transaction.category' => ['required', 'integer', 'min:1'],
            'transaction.wash_bite' => ['required', 'boolean'],
            'transaction.rig_given' => ['required', 'boolean'],
            'transaction.booster_given' => ['required', 'boolean'],
            'transaction.animal_status' => ['required', 'string', 'max:255'],
            'transaction.animal_id' => ['required', 'integer', 'exists:animals,id'],
            'transaction.brand_id' => ['required', 'integer', 'exists:brands,id'],
        ]);

        $schedules = [];
        // Only validate if patient need/wants vaccination
        if ($transaction['transaction']['vaccination_status'] !== "optional_opted_out") {
            $schedules = $request->validate([
                'transaction_schedules' => ['required', 'array'],
                'transaction_schedules.*.name' => ['required', 'string', 'regex:/^Day \d+$/'],
                'transaction_schedules.*.schedule' => ['required', 'date'],
                'transaction_schedules.*.remarks' => ['nullable', 'string'],
            ])['transaction_schedules'];
        }

        $checkIfPatientExist = Patient::where('first_name', $patient['patient']['first_name'])
            ->where('last_name', $patient['patient']['last_name'])
            ->where('middle_name', $patient['patient']['middle_name'])->first();

        if ($checkIfPatientExist) {
            $patient['patient']['id'] = $checkIfPatientExist->id;
        }

        // Check if ID exists — Update or Create
        $patientRecord = Patient::updateOrCreate(
            ['id' => $patient['patient']['id'] ?? null],
            $patient['patient']
        );

        $age = Carbon::parse($patient['patient']['birth_date'])->age;
        $transaction['transaction']['age'] = $age;

        $transactionRecord = Transaction::create(array_merge(
            $transaction['transaction'],
            ['patient_id' => $patientRecord->id]
        ));

        // Only the required and patients who opt-in for vaccination will save schedules
        if (!empty($schedules)) {
            function formatToDate($date)
            {
                return Carbon::parse($date)->format('Y-m-d');
            }

            foreach ($schedules as $schedule) {
                TransactionSchedule::create([
                    'transaction_id' => $transactionRecord->id,
                    'name' => $schedule['name'],
                    'schedule' => formatToDate($schedule['schedule']),
                    'visited_date' => $schedule['name'] === "Day 0" ? formatToDate($schedule['schedule']) : null,
                    'user_id' => $schedule['name'] === "Day 0" ? $request->user()->id : null,
                    'remarks' => $schedule['remarks'] ?? null,
                ]);
            }
        }
        return response()->json(['message' => 'Transaction Successful', 'transaction_id' => $transactionRecord->id,]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaction = Transaction::with(['animal', 'schedules.user', 'patient', 'patient.barangay', 'barangay', 'brand'])->find($id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
        return response()->json($transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $transaction = $request->validate([
            'schedules' => ['required', 'array'],
            'schedules.*.name' => ['required', 'string', 'regex:/^Day \d+$/'],
            'schedules.*.schedule' => ['required', 'date'],

            'barangay_id' => ['required', 'int'],
            'place' => ['required', 'string', 'max:255'],
            //'vaccination_status' => ['required', 'string', 'in:optional_opted_in,optional_opted_out,required'],
            'body_part' => ['required', 'string', 'max:255'],
            'category' => ['required', 'integer', 'min:1'],
            'wash_bite' => ['required', 'boolean'],
            'rig_given' => ['required', 'boolean'],
            'animal_status' => ['required', 'string', 'max:255'],
            'animal_id' => ['required', 'integer', 'exists:animals,id'],
        ]);
        // Find the Transaction record
        $transactionRecord = Transaction::findOrFail($id);

        // Update the transaction record
        $transactionRecord->update([
            'barangay_id' => $transaction['barangay_id'],
            'place' => $transaction['place'],
            'body_part' => $transaction['body_part'],
            'category' => $transaction['category'],
            'wash_bite' => $transaction['wash_bite'],
            'rig_given' => $transaction['rig_given'],
            'animal_status' => $transaction['animal_status'],
            'animal_id' => $transaction['animal_id'],
        ]);

        function formatToDate($date)
        {
            return Carbon::parse($date)->format('Y-m-d');
        }

        // Get all existing schedule names and IDs
        $existingSchedulesData = TransactionSchedule::where('transaction_id', $id)
            ->get(['id', 'name'])
            ->keyBy('name')
            ->toArray();

        // Create a list of validated schedule names for comparison
        $validatedScheduleNames = array_column($transaction['schedules'], 'name');

        // Delete schedules that don't exist in the validated data
        foreach ($existingSchedulesData as $name => $data) {
            if (!in_array($name, $validatedScheduleNames)) {
                TransactionSchedule::destroy($data['id']);
            }
        }

        foreach ($transaction['schedules'] as $schedule) {
            // Only create new schedules if they don't already exist
            if (!isset($existingSchedulesData[$schedule['name']])) {
                // Create new schedule
                TransactionSchedule::create([
                    'transaction_id' => $id,
                    'name' => $schedule['name'],
                    'schedule' => formatToDate($schedule['schedule']),
                    'remarks' => $schedule['remarks'] ?? null,
                ]);
            }
        }

        $transaction = $transactionRecord->fresh(['animal', 'schedules.user', 'patient.barangay', 'barangay', 'brand']);
        return response()->json(['transaction' => $transaction, 'message' => 'Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }

    public function exportExcel(Request $request)
    {
        $fileName = 'transactions.xlsx';
        $transactions = Transaction::with('animal', 'schedules', 'patient', 'barangay');

        if ($request->filled('search')) {
            $name = strtolower($request->input('search'));
            $transactions->whereHas('patient', function ($query) use ($name) {
                $query->whereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ['%' . $name . '%']);
            });
        }

        if ($request->filled('dateFrom') && $request->filled('dateTo')) {
            $dateFrom = Carbon::parse($request->input('dateFrom'))->startOfDay();
            $dateTo = Carbon::parse($request->input('dateTo'))->endOfDay();
            $transactions->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
        if ($request->filled('animal')) {
            $transactions->where('animal_id', $request->input('animal'));
        }
        if ($request->filled('barangay')) {;
            $transactions->where('barangay_id', $request->input('barangay'));
        }
        if ($request->filled('brand')) {;
            $transactions->where('brand_id', $request->input('brand'));
        }

        $transactions = $transactions->get();

        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Define headers
        $headers = ['Transaction ID', 'Patient', 'Place', 'Barangay', 'Animal', 'Animal Status', 'Body Part', 'Brand', 'Category', 'Washed Bite', 'Rig Given', 'Booster Given', 'Date Created'];
        $sheet->fromArray([$headers], null, 'A1');

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'], // White text
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4CAF50'], // Green background
            ],
        ];

        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);

        $sheet->setAutoFilter('A1:M1');

        // Add data
        $row = 2;
        foreach ($transactions as $transaction) {
            $sheet->fromArray([
                $transaction->id,
                optional($transaction->patient)->full_name ?? 'N/A',
                $transaction->place ?? 'N/A',
                optional($transaction->barangay)->name ?? 'N/A',
                optional($transaction->animal)->name ?? 'N/A',
                $transaction->animal_status ?? 'N/A',
                $transaction->body_part ?? 'N/A',
                $transaction->brand->name ?? 'N/A',
                $transaction->category ?? 'N/A',
                $transaction->wash_bite ? 'Washed Bite' : 'Not Washed',
                $transaction->rig_given ? 'Yes' : 'No',
                $transaction->booster_given ? 'Yes' : 'No',
                $transaction->created_at->format('Y-m-d H:i:s') ?? 'N/A',
            ], null, "A{$row}");
            $row++;
        }

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Stream file to response
        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }

    public function predictCase(Request $request): JsonResponse
    {
        $monthYear = $request->input("monthYear");
        $monthYear = explode('-', $monthYear);
        $analyticService = new AnalyticService();
        return response()->json($analyticService->predictedCasesPerBarangay($monthYear[0], $monthYear[1]));
    }

    public function month6Cases(): JsonResponse
    {
        $analyticService = new AnalyticService();
        return response()->json($analyticService->get6MonthsCases());
    }

    public function getCounts(): JsonResponse
    {
        $analyticService = new AnalyticService();
        return response()->json($analyticService->getCounts());
    }

    public function getOnlyAnimalsCount(): JsonResponse
    {
        $analyticService = new AnalyticService();
        return response()->json($analyticService->getOnlyAnimalsCount());
    }

    public function getTop10BrangayBase6Month(): JsonResponse
    {
        $analyticService = new AnalyticService();
        return response()->json($analyticService->getTop10BrangayBase6Month());
    }
}
