<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('barangay');
            $table->string('place');
            $table->foreignId('animal_id')->constrained('animals')->restrictOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->restrictOnDelete();
            $table->string('body_part');
            $table->tinyInteger('category');
            $table->enum('vaccination_status', [
                'required',
                'optional_opted_in',
                'optional_opted_out'
            ]);
            $table->boolean('wash_bite');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
