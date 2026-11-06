<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->string('specification', 200)->nullable()->after('type');
            $table->string('area', 40)->nullable()->after('specification');
        });

        DB::table('equipments')->delete();
        DB::table('robots')->delete();
    }

    public function down(): void
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->dropColumn(['specification', 'area']);
        });
    }
};
