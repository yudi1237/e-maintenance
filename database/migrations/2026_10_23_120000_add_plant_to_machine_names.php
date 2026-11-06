<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machine_names', function (Blueprint $table) {
            $table->string('plant', 40)->default('plant_1_cikarang')->after('machine_number');
        });
    }

    public function down(): void
    {
        Schema::table('machine_names', function (Blueprint $table) {
            $table->dropColumn('plant');
        });
    }
};
