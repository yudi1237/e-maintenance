<?php

use App\Support\RepairStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operator_reports', function (Blueprint $table) {
            $table->string('status')->default(RepairStatus::OPEN);
        });

        DB::table('operator_reports')
            ->whereNotNull('claimed_at')
            ->update(['status' => RepairStatus::ON_PROGRESS]);
    }

    public function down(): void
    {
        Schema::table('operator_reports', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
