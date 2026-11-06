<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preventive_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('label', 150);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        $rows = [
            ['PM1', 'PM1'],
            ['PM2', 'PM2'],
            ['PM3', 'PM3'],
            ['PM4', 'PM4'],
            ['P1E', 'P1E'],
            ['MTC', 'Mold Temperature Control'],
            ['HOPPER', 'Hopper'],
            ['DEHUM', 'Dehum'],
            ['ROBOT', 'Robot'],
            ['HOT', 'Hot Runner'],
            ['JSWEL', 'JSW EL'],
            ['CONVEYOR', 'Preventive Conveyor'],
        ];

        DB::table('preventive_categories')->insert(array_map(
            fn (array $row, int $order): array => [
                'code' => $row[0],
                'label' => $row[1],
                'sort_order' => $order,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $rows,
            array_keys($rows),
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('preventive_categories');
    }
};
