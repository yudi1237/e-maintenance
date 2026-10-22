<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preventive_schedules', function (Blueprint $table) {
            $table->string('pic_name')->nullable()->after('machine_type');
        });
    }

    public function down(): void
    {
        Schema::table('preventive_schedules', function (Blueprint $table) {
            $table->dropColumn('pic_name');
        });
    }
};
