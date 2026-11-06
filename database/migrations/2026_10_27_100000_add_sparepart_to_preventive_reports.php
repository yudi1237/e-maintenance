<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->text('penggunaan_sparepart')->nullable()->after('notes');
            $table->json('sparepart_items')->nullable()->after('penggunaan_sparepart');
        });

        Schema::table('preventive_conveyor_reports', function (Blueprint $table) {
            $table->text('penggunaan_sparepart')->nullable()->after('notes');
            $table->json('sparepart_items')->nullable()->after('penggunaan_sparepart');
        });
    }

    public function down(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->dropColumn(['penggunaan_sparepart', 'sparepart_items']);
        });

        Schema::table('preventive_conveyor_reports', function (Blueprint $table) {
            $table->dropColumn(['penggunaan_sparepart', 'sparepart_items']);
        });
    }
};
