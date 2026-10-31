<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ur',
        'prefix',
        'description',
        'avg_service_time_mins',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'avg_service_time_mins' => 'integer',
        'sort_order' => 'integer',
    ];

    public function counters(): BelongsToMany
    {
        return $this->belongsToMany(Counter::class, 'counter_service');
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class);
    }
}
