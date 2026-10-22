<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_reports', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->unsignedTinyInteger('machine_number');
            $table->foreignId('machine_name_id')->nullable()->constrained('machine_names')->nullOnDelete();
            $table->string('machine_name_text')->nullable();
            $table->foreignId('damage_category_id')->constrained('damage_categories');
            $table->string('damage_category_other')->nullable();
            $table->string('mold_assy_utility')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('pic_name');
            $table->enum('work_shift', ['shift_1', 'shift_2', 'shift_3']);
            $table->dateTime('repair_started_at');
            $table->dateTime('repair_finished_at');
            $table->timestamps();

            $table->index(['machine_number', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_reports');
    }
};
