<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machine_run_days', function (Blueprint $table) {
            $table->string('status', 20)->default('running')->after('run_date');
        });
    }

    public function down(): void
    {
        Schema::table('machine_run_days', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
