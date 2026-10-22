<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->foreignId('robot_id')->nullable()->after('machine_number')->constrained('robots')->nullOnDelete();
            $table->string('robot_number')->nullable()->after('robot_id');
            $table->string('robot_type')->nullable()->after('robot_number');
        });
    }

    public function down(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('robot_id');
            $table->dropColumn(['robot_number', 'robot_type']);
        });
    }
};
