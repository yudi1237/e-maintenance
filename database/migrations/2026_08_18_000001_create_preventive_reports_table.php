<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preventive_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('pic_name');
            $table->foreignId('machine_name_id')->nullable()->constrained('machine_names')->nullOnDelete();
            $table->unsignedTinyInteger('machine_number');
            $table->date('work_date');
            $table->enum('work_shift', ['shift_1', 'shift_2', 'shift_3']);
            $table->json('items');
            $table->text('notes')->nullable();
            $table->string('result')->default('ok');
            $table->timestamps();

            $table->index(['machine_number', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preventive_reports');
    }
};
