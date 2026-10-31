<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // legacy string role fallback
        'role_id',
        'branch_id',
        'region_id',
        'assigned_counter_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function roleModel(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function assignedCounter(): BelongsTo
    {
        return $this->belongsTo(Counter::class, 'assigned_counter_id');
    }

    public function servedTokens(): HasMany
    {
        return $this->hasMany(Token::class, 'served_by_user_id');
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'super_admin' || $this->role === 'admin') {
            return true;
        }

        if ($this->roleModel) {
            return $this->roleModel->hasPermission($permission);
        }

        return false;
    }

    public function getRoleTitleAttribute(): string
    {
        if ($this->roleModel) {
            return $this->roleModel->display_name;
        }
        return ucwords(str_replace('_', ' ', $this->role));
    }
}
