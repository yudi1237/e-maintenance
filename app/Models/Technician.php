<?php

namespace App\Models;

use Database\Factories\TechnicianFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

class Technician extends Model
{
    /** @use HasFactory<TechnicianFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'npk',
        'pin',
        'is_active',
    ];

    protected $hidden = [
        'pin',
    ];

    protected function casts(): array
    {
        return [
            'pin' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function operatorReports(): HasMany
    {
        return $this->hasMany(OperatorReport::class, 'claimed_technician_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function pinMatches(string $pin): bool
    {
        return Hash::check($pin, (string) $this->getRawOriginal('pin'));
    }

    public function optionLabel(): string
    {
        return $this->name.' · NPK '.$this->npk;
    }
}
