<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class TickTickHabit extends Model
{
    /** @use HasFactory<\Database\Factories\TickTickHabitFactory> */
    use HasFactory;

    protected $fillable = [
        'ticktick_id',
        'name',
        'color',
        'status',
        'type',
        'goal',
        'step',
        'unit',
        'repeat_rule',
        'encouragement',
        'reminders',
        'synced_at',
    ];

    protected $appends = [
        'completion_count',
        'completed',
        'remindAt',
    ];

    protected $with = ['checkIns'];

    public function casts(): array
    {
        return [
            'status' => 'integer',
            'goal' => 'integer',
            'step' => 'integer',
            'synced_at' => 'datetime',
        ];
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(TickTickHabitCheckIn::class, 'habit_id', 'ticktick_id');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', 0);
    }

    public function scopeArchived(Builder $query): void
    {
        $query->where(column: 'status', operator: 1);
    }

    public function getCompletedAttribute(): bool
    {
        return $this->getCompletionCountAttribute() === $this->goal;
    }

    public function getRemindAtAttribute(): Collection
    {
        if(empty($this->reminders)){
            return collect([]);
        }
        return collect(explode(';', $this->reminders))
            ->map(static fn ($reminder) => Carbon::parse($reminder, 'America/New_York'));
    }


    public function getCompletionCountAttribute(): int
    {
        return $this->checkIns->reduce(function ($carry, $checkIn) {
            if ($checkIn->status !== 2 || $checkIn->checkin_time->isToday() === false) {
                return $carry;
            }

            return $carry + $checkIn->value;
        }, 0);
    }
}
