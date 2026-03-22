<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TickTickProject extends Model
{
    /** @use HasFactory<\Database\Factories\TickTickProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'ticktick_id',
        'name',
        'color',
        'view_mode',
        'kind',
        'is_closed',
        'sort_order',
        'synced_at',
    ];

    public function casts(): array
    {
        return [
            'is_closed' => 'boolean',
            'sort_order' => 'integer',
            'synced_at' => 'datetime',
        ];
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(TickTickTask::class, 'project_id');
    }
}
