<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preventive_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->string('category', 30);
            $table->string('group_key', 80);
            $table->string('group_label', 150);
            $table->string('item_type', 30)->default('condition');
            $table->string('layout', 30)->nullable();
            $table->string('item_key', 80);
            $table->string('label', 255);
            $table->string('standard', 255)->nullable();
            $table->boolean('has_input')->default(false);
            $table->string('input_placeholder', 100)->nullable();
            $table->string('input_suffix', 30)->nullable();
            $table->unsignedInteger('group_sort_order')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['category', 'item_key']);
            $table->index(['category', 'is_active'], 'pci_category_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preventive_checklist_items');
    }
};
