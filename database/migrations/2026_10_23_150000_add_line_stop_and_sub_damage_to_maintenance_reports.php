<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->boolean('line_stop_mesin')->nullable()->after('damage_cause_category');
            $table->foreignId('sub_damage_category_id')->nullable()->after('line_stop_mesin')->constrained('damage_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sub_damage_category_id');
            $table->dropColumn('line_stop_mesin');
        });
    }
};
