<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->string('damage_cause_category', 64)->nullable()->after('analisa_penyebab_kerusakan');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->dropColumn('damage_cause_category');
        });
    }
};
