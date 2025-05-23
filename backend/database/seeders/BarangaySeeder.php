<?php

namespace Database\Seeders;

use App\Models\Barangay;
use Illuminate\Database\Seeder;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barangays = [
            ["code" => "045608002", "name" => "Buenavista East"],
            ["code" => "045608003", "name" => "Buenavista West"],
            ["code" => "045608004", "name" => "Bukal Norte"],
            ["code" => "045608005", "name" => "Bukal Sur"],
            ["code" => "045608006", "name" => "Kinatihan I"],
            ["code" => "045608007", "name" => "Kinatihan II"],
            ["code" => "045608008", "name" => "Malabanban Norte"],
            ["code" => "045608009", "name" => "Malabanban Sur"],
            ["code" => "045608010", "name" => "Mangilag Norte"],
            ["code" => "045608011", "name" => "Mangilag Sur"],
            ["code" => "045608012", "name" => "Masalukot I"],
            ["code" => "045608013", "name" => "Masalukot II"],
            ["code" => "045608014", "name" => "Masalukot III"],
            ["code" => "045608015", "name" => "Masalukot IV"],
            ["code" => "045608026", "name" => "Masalukot V"],
            ["code" => "045608016", "name" => "Masin Norte"],
            ["code" => "045608017", "name" => "Masin Sur"],
            ["code" => "045608018", "name" => "Mayabobo"],
            ["code" => "045608019", "name" => "Pahinga Norte"],
            ["code" => "045608020", "name" => "Pahinga Sur"],
            ["code" => "045608001", "name" => "Poblacion"],
            ["code" => "045608022", "name" => "San Andres"],
            ["code" => "045608023", "name" => "San Isidro"],
            ["code" => "045608024", "name" => "Santa Catalina Norte"],
            ["code" => "045608025", "name" => "Santa Catalina Sur"]
        ];

        foreach ($barangays as $barangay) {
            Barangay::create($barangay);
        }
    }
}
