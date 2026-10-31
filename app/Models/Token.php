<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Token extends Model
{
    use HasFactory;

    protected $fillable = [
        'token_number',
        'sequence_number',
        'branch_id',
        'service_category_id',
        'priority_category_id',
        'counter_id',
        'served_by_user_id',
        'salutation',
        'customer_name',
        'customer_phone',
        'customer_cnic',
        'customer_email',
        'account_number',
        'transaction_purpose',
        'expected_amount',
        'channel',
        'status',
        'is_escalated',
        'manager_override',
        'override_reason',
        'priority_score',
        'issued_at',
        'called_at',
        'serving_started_at',
        'completed_at',
        'waiting_time_seconds',
        'serving_time_seconds',
        'recall_count',
        'notes',
        'transaction_amount',
        'transaction_reference',
        'transferred_from_counter_id',
        'transferred_to_counter_id',
    ];

    protected $casts = [
        'is_escalated' => 'boolean',
        'manager_override' => 'boolean',
        'issued_at' => 'datetime',
        'called_at' => 'datetime',
        'serving_started_at' => 'datetime',
        'completed_at' => 'datetime',
        'waiting_time_seconds' => 'integer',
        'serving_time_seconds' => 'integer',
        'recall_count' => 'integer',
        'priority_score' => 'float',
        'transaction_amount' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function priorityCategory(): BelongsTo
    {
        return $this->belongsTo(PriorityCategory::class);
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class);
    }

    public function servedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'served_by_user_id');
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(CustomerFeedback::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_escalated && $this->status === 'waiting') {
            return '<span class="badge bg-danger pulse-dot"><i class="bx bx-alarm-exclamation me-1"></i> SLA Escalated</span>';
        }

        return match ($this->status) {
            'waiting' => '<span class="badge bg-label-warning"><i class="bx bx-time-five me-1"></i> Waiting</span>',
            'called' => '<span class="badge bg-label-primary"><i class="bx bx-bell me-1"></i> Called</span>',
            'serving' => '<span class="badge bg-label-info"><i class="bx bx-play-circle me-1"></i> Serving</span>',
            'completed' => '<span class="badge bg-label-success"><i class="bx bx-check-circle me-1"></i> Completed</span>',
            'on_hold' => '<span class="badge bg-label-secondary"><i class="bx bx-pause me-1"></i> On Hold</span>',
            'transferred' => '<span class="badge bg-label-dark"><i class="bx bx-transfer me-1"></i> Transferred</span>',
            'no_show' => '<span class="badge bg-label-danger"><i class="bx bx-user-x me-1"></i> No Show</span>',
            'cancelled' => '<span class="badge bg-label-danger"><i class="bx bx-x me-1"></i> Cancelled</span>',
            default => '<span class="badge bg-label-secondary">' . ucfirst($this->status) . '</span>',
        };
    }
}
