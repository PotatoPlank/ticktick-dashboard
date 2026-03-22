<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TickTickTag extends Model
{
    /** @use HasFactory<\Database\Factories\TickTickTagFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'sort_order',
        'synced_at',
    ];

    public function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'synced_at' => 'datetime',
        ];
    }
}
