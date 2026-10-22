<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preventive_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('machine_name_id')->nullable()->constrained('machine_names')->nullOnDelete();
            $table->unsignedSmallInteger('machine_number');
            $table->string('machine_name');
            $table->string('machine_type')->nullable();
            $table->date('plan_date');
            $table->string('preventive_type');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('plan_date');
            $table->index(['machine_number', 'plan_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preventive_schedules');
    }
};
