<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->text('masalah')->nullable();
            $table->text('analisa_penyebab_kerusakan')->nullable();
            $table->text('tindakan_perbaikan')->nullable();
            $table->text('tindakan_perbaikan_2')->nullable();
            $table->string('evidence_path')->nullable();
            $table->text('penggunaan_sparepart')->nullable();
            $table->boolean('ada_op')->default(false);
            $table->string('status_perbaikan')->default('open');
            $table->timestamp('step2_completed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->dropColumn([
                'masalah',
                'analisa_penyebab_kerusakan',
                'tindakan_perbaikan',
                'tindakan_perbaikan_2',
                'evidence_path',
                'penggunaan_sparepart',
                'ada_op',
                'status_perbaikan',
                'step2_completed_at',
            ]);
        });
    }
};
