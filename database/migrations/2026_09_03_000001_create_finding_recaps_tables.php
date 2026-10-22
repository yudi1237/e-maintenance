<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finding_recaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('machine_name_id')->nullable()->constrained('machine_names')->nullOnDelete();
            $table->unsignedSmallInteger('machine_number');
            $table->string('machine_name');
            $table->string('equipment_name');
            $table->date('found_date');
            $table->date('due_date');
            $table->string('status');
            $table->timestamps();

            $table->index(['status', 'found_date']);
            $table->index(['machine_number', 'found_date']);
        });

        Schema::create('finding_recap_status_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finding_recap_id')->constrained('finding_recaps')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status');
            $table->text('note');
            $table->timestamps();

            $table->index(['finding_recap_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finding_recap_status_notes');
        Schema::dropIfExists('finding_recaps');
    }
};
