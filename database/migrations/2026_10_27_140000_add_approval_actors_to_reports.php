<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->foreignId('injection_approved_by')->nullable()->after('status_perbaikan')->constrained('users')->nullOnDelete();
            $table->foreignId('quality_approved_by')->nullable()->after('injection_approved_by')->constrained('users')->nullOnDelete();
            $table->foreignId('maintenance_approved_by')->nullable()->after('quality_approved_by')->constrained('users')->nullOnDelete();
        });

        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->foreignId('injection_approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->foreignId('maintenance_approved_by')->nullable()->after('injection_approved_by')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('injection_approved_by');
            $table->dropConstrainedForeignId('quality_approved_by');
            $table->dropConstrainedForeignId('maintenance_approved_by');
        });

        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('injection_approved_by');
            $table->dropConstrainedForeignId('maintenance_approved_by');
        });
    }
};
