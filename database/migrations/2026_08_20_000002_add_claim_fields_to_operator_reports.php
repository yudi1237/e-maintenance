<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operator_reports', function (Blueprint $table) {
            $table->foreignId('claimed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('claimed_at')->nullable();
        });

        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->foreignId('operator_report_id')->nullable()->unique()->constrained('operator_reports')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('operator_report_id');
        });

        Schema::table('operator_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('claimed_by');
            $table->dropColumn('claimed_at');
        });
    }
};
