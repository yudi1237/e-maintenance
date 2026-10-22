<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machine_names', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('machine_number');
            $table->string('machine_model')->nullable()->after('brand');
            $table->unsignedSmallInteger('tonnage')->nullable()->after('machine_model');
            $table->string('clamping_type')->nullable()->after('tonnage');
            $table->string('machine_type')->nullable()->after('clamping_type');
            $table->string('tie_bar_distance_mm')->nullable()->after('machine_type');
            $table->string('platen_size_mm')->nullable()->after('tie_bar_distance_mm');
            $table->unsignedSmallInteger('mold_thickness_min_mm')->nullable()->after('platen_size_mm');
            $table->unsignedSmallInteger('mold_thickness_max_mm')->nullable()->after('mold_thickness_min_mm');
            $table->unsignedSmallInteger('clamp_stroke_mm')->nullable()->after('mold_thickness_max_mm');
            $table->decimal('power_pump_kw', 8, 2)->nullable()->after('clamp_stroke_mm');
            $table->decimal('power_heater_kw', 8, 2)->nullable()->after('power_pump_kw');
            $table->decimal('power_total_kw', 8, 2)->nullable()->after('power_heater_kw');
            $table->unsignedSmallInteger('shot_weight_small_gr')->nullable()->after('power_total_kw');
            $table->unsignedSmallInteger('shot_weight_med_gr')->nullable()->after('shot_weight_small_gr');
            $table->unsignedSmallInteger('manufacture_year')->nullable()->after('shot_weight_med_gr');
        });
    }

    public function down(): void
    {
        Schema::table('machine_names', function (Blueprint $table) {
            $table->dropColumn([
                'brand',
                'machine_model',
                'tonnage',
                'clamping_type',
                'machine_type',
                'tie_bar_distance_mm',
                'platen_size_mm',
                'mold_thickness_min_mm',
                'mold_thickness_max_mm',
                'clamp_stroke_mm',
                'power_pump_kw',
                'power_heater_kw',
                'power_total_kw',
                'shot_weight_small_gr',
                'shot_weight_med_gr',
                'manufacture_year',
            ]);
        });
    }
};

