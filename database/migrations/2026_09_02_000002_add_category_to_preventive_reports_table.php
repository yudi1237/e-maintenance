<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->string('category')->nullable()->after('machine_number');
            $table->index(['machine_number', 'category']);
        });
    }

    public function down(): void
    {
        Schema::table('preventive_reports', function (Blueprint $table) {
            $table->dropIndex(['machine_number', 'category']);
            $table->dropColumn('category');
        });
    }
};
