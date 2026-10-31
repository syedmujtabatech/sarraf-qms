<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_code',
        'branch_id',
        'service_category_id',
        'priority_category_id',
        'customer_name',
        'customer_phone',
        'customer_cnic',
        'customer_email',
        'appointment_date',
        'time_slot',
        'status',
        'token_id',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'date',
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

    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class);
    }
}
