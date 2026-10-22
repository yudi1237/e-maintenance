<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machine_names', function (Blueprint $table) {
            $table->string('asset_number')->nullable()->unique()->after('machine_number');
            $table->unsignedSmallInteger('screw_diameter_mm')->nullable()->after('asset_number');
            $table->json('machine_photos')->nullable()->after('screw_diameter_mm');
        });
    }

    public function down(): void
    {
        Schema::table('machine_names', function (Blueprint $table) {
            $table->dropUnique(['asset_number']);
            $table->dropColumn(['asset_number', 'screw_diameter_mm', 'machine_photos']);
        });
    }
};

