<?php

namespace App\Models;

use App\Support\WorkShift;
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
        'shift',
        'off_duty',
        'overtime',
    ];

    protected $hidden = [
        'pin',
    ];

    protected function casts(): array
    {
        return [
            'pin' => 'hashed',
            'is_active' => 'boolean',
            'shift' => 'integer',
            'off_duty' => 'boolean',
            'overtime' => 'boolean',
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

    public function scopeOnCurrentShift(Builder $query): Builder
    {
        $current = WorkShift::currentNumber();

        return $query->where('off_duty', false)->where(function (Builder $query) use ($current) {
            $query->where('shift', $current)
                ->orWhere(function (Builder $query) use ($current) {
                    $query->where('overtime', true)
                        ->where('shift', WorkShift::previousNumber($current));
                });
        });
    }

    public function isOnShift(): bool
    {
        if ($this->off_duty || ! in_array((int) $this->shift, [1, 2, 3], true)) {
            return false;
        }

        $current = WorkShift::currentNumber();

        return (int) $this->shift === $current
            || ($this->overtime && WorkShift::nextNumber((int) $this->shift) === $current);
    }

    public function pinMatches(string $pin): bool
    {
        return Hash::check($pin, (string) $this->getRawOriginal('pin'));
    }

    public function optionLabel(): string
    {
        return $this->name.' · NPK '.$this->npk;
    }

    /**
     * @param  list<int|string>  $ids
     * @return list<array{id: int, name: string}>
     */
    public static function teamSnapshot(array $ids): array
    {
        return self::query()
            ->active()
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->get()
            ->map(fn (self $technician) => [
                'id' => $technician->id,
                'name' => $technician->optionLabel(),
            ])
            ->values()
            ->all();
    }
}
