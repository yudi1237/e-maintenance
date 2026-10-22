<?php

namespace App\Models;

use App\Support\EquipmentCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipments';

    protected $fillable = [
        'category',
        'number',
        'type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function getLabelAttribute(): string
    {
        return $this->number.' — '.$this->type;
    }

    public function getCategoryLabelAttribute(): string
    {
        return EquipmentCategory::label($this->category);
    }
}
