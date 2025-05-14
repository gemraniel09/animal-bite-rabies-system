<?php

namespace App\Services;

use App\Models\Barangay;
use Illuminate\Support\Facades\DB;

class AnalyticService
{
    private $pythonScriptPath = 'predict.py'; // Ensure this is relative to your Laravel root or full path

    public function __construct()
    {
        if (!file_exists($this->pythonScriptPath)) {
            throw new \Exception("The Python script '{$this->pythonScriptPath}' does not exist.");
        }
    }

    public function predictedCasesPerBarangay($year, $month): array
    {
        $escapedYear = escapeshellarg($year);
        $escapedMonth = escapeshellarg($month);
        $command = "python3 {$this->pythonScriptPath} {$escapedYear} {$escapedMonth}";

        $output = [];
        $status = 0;
        exec($command, $output, $status);

        if ($status !== 0 || empty($output)) {
            return [];
        }

        $predictions = json_decode(implode("", $output), true);
        if (!$predictions) {
            return [];
        }

        // Get actual transaction counts from the database for the selected month/year
        $counts = DB::table('transactions')
            ->selectRaw('barangay_id, COUNT(*) as transaction_count')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('barangay_id')
            ->pluck('transaction_count', 'barangay_id');

        // Map barangay names to DB entries
        $barangayMap = Barangay::all()->keyBy('name');
        $result = [];

        foreach ($predictions as $entry) {
            $barangay = $barangayMap->get($entry['name']);
            if ($barangay) {
                $result[] = [
                    'id' => $barangay->id,
                    'name' => $barangay->name,
                    'predictedCase' => $entry['predictedCase'],
                    'risk_level' => $entry['risk_level'],
                    'transaction_count' => $counts->get($barangay->id, 0)
                ];
            }
        }

        // Sort by predicted cases
        usort($result, fn($a, $b) => $b['predictedCase'] <=> $a['predictedCase']);

        return $result;
    }
}
