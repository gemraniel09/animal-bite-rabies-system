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
        Schema::table('transaction_schedules', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->after('transaction_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_schedules', function (Blueprint $table) {
            $table->dropColumndropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
