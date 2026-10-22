<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('damage_categories', 'code')) {
            return;
        }

        Schema::table('damage_categories', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }

    public function down(): void
    {
        Schema::table('damage_categories', function (Blueprint $table) {
            $table->string('code', 5)->after('id');
        });
    }
};
