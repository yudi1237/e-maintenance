<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->json('item_remarks')->nullable()->after('readings');
        });

        Schema::table('preventive_conveyor_reports', function (Blueprint $table) {
            $table->json('item_remarks')->nullable()->after('items');
        });
    }

    public function down(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->dropColumn('item_remarks');
        });

        Schema::table('preventive_conveyor_reports', function (Blueprint $table) {
            $table->dropColumn('item_remarks');
        });
    }
};
