<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->string('status')->default('close_by_technician')->after('result');
            $table->index('status');
        });

        DB::table('preventive_reports')->update(['status' => 'close']);
    }

    public function down(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }
};
