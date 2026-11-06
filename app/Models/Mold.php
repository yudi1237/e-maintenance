<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mold extends Model
{
    protected $fillable = [
        'name',
        'customer',
        'dimension',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function machines(): HasMany
    {
        return $this->hasMany(MachineName::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function optionLabel(): string
    {
        return $this->name.' — '.$this->customer.' — '.$this->dimension.' mm';
    }
}
