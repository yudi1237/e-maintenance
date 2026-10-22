<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operator_reports', function (Blueprint $table) {
            $table->foreignId('claimed_technician_id')
                ->nullable()
                ->after('claimed_by')
                ->constrained('technicians')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('operator_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('claimed_technician_id');
        });
    }
};
