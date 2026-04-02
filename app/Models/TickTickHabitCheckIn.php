<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TickTickHabitCheckIn extends Model
{
    /** @use HasFactory<\Database\Factories\TickTickHabitCheckInFactory> */
    use HasFactory;

    protected $fillable = [
        'ticktick_id',
        'habit_id',
        'checkin_stamp',
        'checkin_time',
        'op_time',
        'value',
        'goal',
        'status',
        'synced_at',
    ];

    public function casts(): array
    {
        return [
            'checkin_stamp' => 'integer',
            'checkin_time' => 'datetime',
            'op_time' => 'datetime',
            'value' => 'decimal:2',
            'goal' => 'decimal:2',
            'status' => 'integer',
            'synced_at' => 'datetime',
        ];
    }

    public function habit(): BelongsTo
    {
        return $this->belongsTo(TickTickHabit::class);
    }
}
