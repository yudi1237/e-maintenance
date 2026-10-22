<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operator_reports', function (Blueprint $table) {
            $table->id();
            $table->string('operator_name');
            $table->string('npk', 50);
            $table->string('item_name');
            $table->foreignId('machine_name_id')->nullable()->constrained('machine_names')->nullOnDelete();
            $table->unsignedTinyInteger('machine_number');
            $table->date('stop_date');
            $table->time('stop_time');
            $table->text('problem');
            $table->text('chronology')->nullable();
            $table->timestamps();

            $table->index(['machine_number', 'stop_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operator_reports');
    }
};
