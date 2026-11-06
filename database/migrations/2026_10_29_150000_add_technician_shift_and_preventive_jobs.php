<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('technicians', function (Blueprint $table) {
            $table->unsignedTinyInteger('shift')->nullable();
        });

        Schema::create('technician_preventive_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('machine_number')->nullable();
            $table->string('category', 20);
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technician_preventive_jobs');

        Schema::table('technicians', function (Blueprint $table) {
            $table->dropColumn('shift');
        });
    }
};
