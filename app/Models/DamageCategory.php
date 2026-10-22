<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DamageCategory extends Model
{
    protected $fillable = [
        'name',
        'allows_other',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'allows_other' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function maintenanceReports(): HasMany
    {
        return $this->hasMany(MaintenanceReport::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function displayLabel(): string
    {
        return $this->name;
    }
}
