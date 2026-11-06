<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class MachineRunDay extends Model
{
    public const STATUS_RUNNING = 'running';

    public const STATUS_BREAKDOWN = 'breakdown';

    public const STATUS_STANDBY = 'standby';

    protected $fillable = [
        'machine_number',
        'run_date',
        'status',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'run_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function todayDate(): string
    {
        return now()->timezone('Asia/Jakarta')->toDateString();
    }

    /**
     * @return list<int>
     */
    public static function runningMachineNumbers(?Carbon $date = null): array
    {
        return self::numbersForStatus(self::STATUS_RUNNING, $date);
    }

    /**
     * @return list<int>
     */
    public static function breakdownMachineNumbers(?Carbon $date = null): array
    {
        return self::numbersForStatus(self::STATUS_BREAKDOWN, $date);
    }

    /**
     * @return list<int>
     */
    public static function standbyMachineNumbers(?Carbon $date = null): array
    {
        return self::numbersForStatus(self::STATUS_STANDBY, $date);
    }

    /**
     * @return list<int>
     */
    private static function numbersForStatus(string $status, ?Carbon $date = null): array
    {
        $day = ($date ?? now()->timezone('Asia/Jakarta'))->toDateString();

        return self::query()
            ->whereDate('run_date', $day)
            ->where('status', $status)
            ->orderBy('machine_number')
            ->pluck('machine_number')
            ->map(fn ($number) => (int) $number)
            ->all();
    }
}
