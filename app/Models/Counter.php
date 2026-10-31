<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Counter extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'counter_number',
        'name',
        'type',
        'status',
        'break_reason',
        'opening_cash_balance',
        'current_cash_drawer',
        'current_user_id',
        'current_token_id',
        'last_active_at',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
        'opening_cash_balance' => 'decimal:2',
        'current_cash_drawer' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function currentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_user_id');
    }

    public function currentToken(): BelongsTo
    {
        return $this->belongsTo(Token::class, 'current_token_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(ServiceCategory::class, 'counter_service');
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class);
    }
}
