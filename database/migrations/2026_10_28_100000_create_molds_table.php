<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('molds', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('customer', 150);
            $table->string('dimension', 80);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('machine_names', function (Blueprint $table) {
            $table->foreignId('mold_id')->nullable()->constrained('molds')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('machine_names', function (Blueprint $table) {
            $table->dropConstrainedForeignId('mold_id');
        });

        Schema::dropIfExists('molds');
    }
};
