<?php

namespace App\Models;

use App\Support\FindingRecapStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FindingRecap extends Model
{
    protected $fillable = [
        'user_id',
        'machine_name_id',
        'machine_number',
        'machine_name',
        'equipment_name',
        'found_date',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'found_date' => 'date',
            'due_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function machineName(): BelongsTo
    {
        return $this->belongsTo(MachineName::class);
    }

    public function statusNotes(): HasMany
    {
        return $this->hasMany(FindingRecapStatusNote::class)->latest('id');
    }

    public function scopePriorityByDueDate(Builder $query): Builder
    {
        $today = now()->timezone('Asia/Jakarta')->toDateString();

        return $query
            ->orderByRaw(
                'CASE
                    WHEN status = ? THEN 2
                    WHEN due_date IS NOT NULL AND due_date < ? THEN 0
                    ELSE 1
                END',
                [FindingRecapStatus::CLOSE, $today]
            )
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->orderByDesc('id');
    }

    public function getStatusLabelAttribute(): string
    {
        return FindingRecapStatus::label($this->status);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return FindingRecapStatus::badgeClass($this->status);
    }

    public function getMachineNameDisplayAttribute(): string
    {
        $this->loadMissing('machineName');

        return $this->machineName?->viewLabel()
            ?: $this->machine_name
            ?: 'Mesin '.$this->machine_number;
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === FindingRecapStatus::CLOSE || $this->due_date === null) {
            return false;
        }

        return $this->due_date->lt(now()->timezone('Asia/Jakarta')->startOfDay());
    }

    public function getIsReminderAttribute(): bool
    {
        if ($this->is_overdue || $this->status === FindingRecapStatus::CLOSE || $this->due_date === null) {
            return false;
        }

        $today = now()->timezone('Asia/Jakarta')->startOfDay();
        $due = $this->due_date->copy()->timezone('Asia/Jakarta')->startOfDay();
        $daysUntilDue = $today->diffInDays($due, false);

        return $daysUntilDue >= 0 && $daysUntilDue <= 3;
    }

    /**
     * @return list<array{status: string, status_label: string, note: string, author: string, created_at: string}>
     */
    public function notesForView(): array
    {
        $this->loadMissing(['statusNotes.user']);

        return $this->statusNotes
            ->sortBy('id')
            ->values()
            ->map(fn (FindingRecapStatusNote $note): array => [
                'status' => $note->status,
                'status_label' => $note->status_label,
                'note' => $note->note,
                'author' => $note->author_name,
                'created_at' => $note->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-',
            ])
            ->all();
    }

    public function notesExportText(): string
    {
        return collect($this->notesForView())
            ->map(fn (array $note): string => $note['status_label'].': '.$note['note'])
            ->implode("\n");
    }

    public function addStatusNote(string $status, string $note, ?int $userId = null): FindingRecapStatusNote
    {
        return $this->statusNotes()->create([
            'status' => $status,
            'note' => $note,
            'user_id' => $userId,
        ]);
    }
}
