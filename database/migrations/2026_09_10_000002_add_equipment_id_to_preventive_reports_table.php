<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->foreignId('equipment_id')->nullable()->after('equipment_number')->constrained('equipments')->nullOnDelete();
            $table->string('equipment_type', 150)->nullable()->after('equipment_id');
        });
    }

    public function down(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('equipment_id');
            $table->dropColumn('equipment_type');
        });
    }
};
