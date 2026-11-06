<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class PreventiveCategoryRecord extends Model
{
    protected $table = 'preventive_categories';

    protected $fillable = [
        'code',
        'label',
        'sort_order',
    ];

    /** @var array<string, string>|null */
    private static ?array $labelCache = null;

    protected static function booted(): void
    {
        static::saved(fn () => self::flushLabelCache());
        static::deleted(fn () => self::flushLabelCache());
    }

    public static function flushLabelCache(): void
    {
        self::$labelCache = null;
    }

    public static function storedLabel(?string $code): ?string
    {
        if ($code === null || $code === '' || ! Schema::hasTable('preventive_categories')) {
            return null;
        }

        self::$labelCache ??= self::query()->pluck('label', 'code')->all();

        return self::$labelCache[$code] ?? null;
    }

    public static function ensureBuiltin(): void
    {
        if (! Schema::hasTable('preventive_categories')) {
            return;
        }

        $builtins = [
            'PM1' => 'PM1',
            'PM2' => 'PM2',
            'PM3' => 'PM3',
            'PM4' => 'PM4',
            'P1E' => 'P1E',
            'MTC' => 'Mold Temperature Control',
            'HOPPER' => 'Hopper',
            'DEHUM' => 'Dehum',
            'ROBOT' => 'Robot',
            'HOT' => 'Hot Runner',
            'JSWEL' => 'JSW EL',
            'CONVEYOR' => 'Preventive Conveyor',
        ];

        if (self::query()->exists()) {
            return;
        }

        foreach (array_values($builtins) as $order => $label) {
            $code = array_keys($builtins)[$order];
            self::query()->firstOrCreate(
                ['code' => $code],
                ['label' => $label, 'sort_order' => $order],
            );
        }
    }
};
