<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechnicianPreventiveJob extends Model
{
    protected $fillable = [
        'technician_id',
        'machine_number',
        'category',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'closed_at' => 'datetime',
        ];
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    public static function open(Technician $technician, ?int $machineNumber, string $category): void
    {
        $open = self::forJob($technician, $machineNumber, $category)
            ->whereNull('closed_at')
            ->exists();

        if ($open) {
            return;
        }

        self::query()->create([
            'technician_id' => $technician->id,
            'machine_number' => $machineNumber,
            'category' => $category,
        ]);
    }

    public static function close(Technician $technician, ?int $machineNumber, string $category): void
    {
        self::forJob($technician, $machineNumber, $category)
            ->whereNull('closed_at')
            ->update(['closed_at' => now()]);
    }

    private static function forJob(Technician $technician, ?int $machineNumber, string $category)
    {
        return self::query()
            ->where('technician_id', $technician->id)
            ->where('category', $category)
            ->when(
                $machineNumber === null,
                fn ($query) => $query->whereNull('machine_number'),
                fn ($query) => $query->where('machine_number', $machineNumber),
            );
    }

    public function detailLabel(): string
    {
        if ($this->category === 'CONVEYOR') {
            return 'Preventive Conveyor';
        }

        $label = \App\Support\PreventiveCategory::label($this->category);

        return 'Preventive '.$label.' · Mesin '.$this->machine_number;
    }
}
