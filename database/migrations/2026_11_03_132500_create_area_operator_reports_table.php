<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_operator_reports', function (Blueprint $table) {
            $table->id();
            $table->string('area');
            $table->string('reporter_name');
            $table->string('part_name')->nullable();
            $table->string('equipment_name')->nullable();
            $table->date('stop_date');
            $table->time('stop_time');
            $table->text('problem');
            $table->text('chronology')->nullable();
            $table->foreignId('claimed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('claimed_technician_id')->nullable()->constrained('technicians')->nullOnDelete();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();

            $table->index(['area', 'stop_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_operator_reports');
    }
};
