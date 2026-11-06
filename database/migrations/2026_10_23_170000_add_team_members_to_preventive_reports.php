<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->json('team_members')->nullable()->after('work_shift');
        });

        Schema::table('preventive_conveyor_reports', function (Blueprint $table) {
            $table->json('team_members')->nullable()->after('work_shift');
        });
    }

    public function down(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->dropColumn('team_members');
        });

        Schema::table('preventive_conveyor_reports', function (Blueprint $table) {
            $table->dropColumn('team_members');
        });
    }
};
