<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->json('evidence_paths')->nullable()->after('notes');
        });

        Schema::table('preventive_conveyor_reports', function (Blueprint $table) {
            $table->json('evidence_paths')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->dropColumn('evidence_paths');
        });

        Schema::table('preventive_conveyor_reports', function (Blueprint $table) {
            $table->dropColumn('evidence_paths');
        });
    }
};
