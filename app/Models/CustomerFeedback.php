<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerFeedback extends Model
{
    use HasFactory;

    protected $table = 'customer_feedbacks';

    protected $fillable = [
        'token_id',
        'branch_id',
        'counter_id',
        'rating',
        'feedback_tags',
        'comments',
        'customer_phone',
    ];

    protected $casts = [
        'rating' => 'integer',
        'feedback_tags' => 'array',
    ];

    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function counter(): BelongsTo
    {
        return $this->belongsTo(Counter::class);
    }
}
