<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Transaction;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Patient::with('barangay');
        $limit = 50;
        if ($request->filled('limit')) {
            $limit = $request->input('limit');
        }

        if ($request->filled('name')) {
            $name = strtolower($request->input('name'));
            $query->whereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ['%' . $name . '%']);
        }
        if ($request->filled('transactions')) {
            $query->withCount('transactions');
        }
        if ($request->filled('barangay')) {
            $query->where('barangay_id', $request->input('barangay'));
        }

        $patients = $query->limit($limit)->get();
        // Include middlename if no patients
        if ($patients->isEmpty() && $request->filled('name')) {
            $query = Patient::query();
            $name = strtolower($request->input('name'));
            $query->whereRaw('LOWER(CONCAT(first_name, " ", middle_name, " ", last_name)) LIKE ?', ['%' . $name . '%']);
            $patients = $query->limit($limit)->get();
        }
        return response()->json($patients);
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
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'barangay_id' => ['required', 'int'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            // 'age' => ['required', 'int'],
            'gender' => ['required', 'string', 'in:male,female'],
        ]);

        Patient::create($validated);

        return response()->json(['message' => 'Patient created successfully', 'patient' => $validated]);
    }

    /**
     * Display the specified resource. GET{id}
     */
    public function show($id)
    {
        $patient = Patient::with([
            'barangay',
            'transactions',
            'transactions.barangay',
            'transactions.animal',
            'transactions.brand',
            'transactions.schedules.user'
        ])->find($id);

        if (!$patient) {
            return response()->json(['error' => 'Patient not found'], 404);
        }

        return response()->json($patient);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage. PUT
     */
    public function update(Request $request, Patient $patient)
    {
        //
    }

    /**
     * Remove the specified resource from storage. DELETE
     */
    public function destroy(Patient $patient)
    {
        //
    }

    public function exportExcel(Request $request)
    {
        $fileName = 'patients.xlsx';
        $patient = Patient::with('barangay')->withCount('transactions');

        if ($request->filled('name')) {
            $name = strtolower($request->input('name'));
            $patient->whereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ['%' . $name . '%']);
        }

        if ($request->filled('barangay')) {;
            $patient->where('barangay_id', $request->input('barangay'));
        }


        $patients = $patient->get();

        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Define headers
        $headers = ['Patient ID', 'First Name', 'Middle Name', 'Last Name', 'Birth Date', 'Gender', 'Barangay', 'Transactions Count', 'Date Created'];
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

        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        $sheet->setAutoFilter('A1:I1');

        // Add data
        $row = 2;
        foreach ($patients as $patient) {
            $sheet->fromArray([
                $patient->id,
                $patient->first_name ?? 'N/A',
                $patient->middle_name ?? 'N/A',
                $patient->last_name ?? 'N/A',
                $patient->birth_date ?? 'N/A',
                // $patient->age ?? 'N/A',
                $patient->gender ?? 'N/A',
                $patient->barangay->name ?? 'N/A',
                $patient->transactions_count ?? 'N/A',
                $patient->created_at->format('Y-m-d H:i:s') ?? 'N/A',
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
}
