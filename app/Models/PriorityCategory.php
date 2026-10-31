<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriorityCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ur',
        'code',
        'weight_multiplier',
        'badge_color',
        'icon',
        'regulatory_note',
        'is_active',
    ];

    protected $casts = [
        'weight_multiplier' => 'integer',
        'is_active' => 'boolean',
    ];

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class);
    }
}
