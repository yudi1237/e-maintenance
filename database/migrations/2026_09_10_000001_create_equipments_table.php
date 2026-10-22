<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->string('category', 40);
            $table->string('number', 50);
            $table->string('type', 150);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['category', 'number']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};
