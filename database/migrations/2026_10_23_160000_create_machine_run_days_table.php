<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_run_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('machine_number');
            $table->date('run_date');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['machine_number', 'run_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_run_days');
    }
};
