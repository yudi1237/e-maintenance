<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Robot extends Model
{
    protected $fillable = [
        'robot_number',
        'robot_type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function preventiveReports(): HasMany
    {
        return $this->hasMany(PreventiveReport::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getLabelAttribute(): string
    {
        return $this->robot_number.' — '.$this->robot_type;
    }
}
