<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TickTickTask extends Model
{
    /** @use HasFactory<\Database\Factories\TickTickTaskFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'ticktick_id',
        'title',
        'content',
        'description',
        'status',
        'priority',
        'start_date',
        'due_date',
        'completed_time',
        'timezone',
        'is_all_day',
        'sort_order',
        'tags',
        'items',
        'repeat_flag',
        'synced_at',
    ];

    public function casts(): array
    {
        return [
            'status' => 'integer',
            'priority' => 'integer',
            'start_date' => 'datetime',
            'due_date' => 'datetime',
            'completed_time' => 'datetime',
            'is_all_day' => 'boolean',
            'sort_order' => 'integer',
            'tags' => 'array',
            'items' => 'array',
            'synced_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(TickTickProject::class, 'project_id');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', 0);
    }

    public function scopeCompleted(Builder $query): void
    {
        $query->where('status', 2);
    }

    public function scopeDueToday(Builder $query): void
    {
        $query->whereDate('due_date', today());
    }

    public function scopeDueMorning(Builder $query): void
    {
        $query
            ->where('is_all_day', '!=', '1')
            ->where('due_date', '>=', today(config('app.user_timezone'))->setTime(0, 0)->utc())
            ->where('due_date', '<=', today(config('app.user_timezone'))->setTime(11, 59)->utc());
    }

    public function scopeDueAfterNoon(Builder $query): void
    {
        $query
            ->where('is_all_day', '!=', '1')
            ->where('due_date', '>=', today(config('app.user_timezone'))->setTime(12, 0)->utc())
            ->where('due_date', '<=', today(config('app.user_timezone'))->setTime(16, 59)->utc());
    }

    public function scopeDueEvening(Builder $query): void
    {
        $query
            ->where('is_all_day', '!=', '1')
            ->where('due_date', '>=', today(config('app.user_timezone'))->setTime(17, 0)->utc())
            ->where('due_date', '<=', today(config('app.user_timezone'))->setTime(23, 59)->utc());
    }

    public function scopeOverdue(Builder $query): void
    {
        $query->where('status', 0)->whereDate('due_date', '<', today());
    }

    public function scopeDueAllDay(Builder $query): void
    {
        $query->where('is_all_day', '1')->whereDate('due_date', '<', today());
    }
}
