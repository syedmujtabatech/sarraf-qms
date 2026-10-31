<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Branch;
use App\Models\Counter;
use App\Models\PriorityCategory;
use App\Models\ServiceCategory;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QueueService
{
    /**
     * Generate a new Token (Walk-in Kiosk, Mobile or Appointment)
     */
    public function generateToken(
        int $branchId,
        int $serviceCategoryId,
        int $priorityCategoryId,
        array $customerData = [],
        string $channel = 'kiosk'
    ): Token {
        return DB::transaction(function () use ($branchId, $serviceCategoryId, $priorityCategoryId, $customerData, $channel) {
            $service = ServiceCategory::findOrFail($serviceCategoryId);
            $priority = PriorityCategory::findOrFail($priorityCategoryId);

            // Daily sequence number per branch and service category
            $today = Carbon::today();
            $lastToken = Token::where('branch_id', $branchId)
                ->where('service_category_id', $serviceCategoryId)
                ->whereDate('created_at', $today)
                ->orderBy('sequence_number', 'desc')
                ->lockForUpdate()
                ->first();

            $sequence = $lastToken ? ($lastToken->sequence_number + 1) : 101;
            $tokenNumber = $service->prefix . '-' . $sequence;

            // Priority Score calculation (SBP & SBI guidelines):
            // Multiplier gives immediate priority jump, timestamp keeps fairness within tier
            $weight = $priority->weight_multiplier ?? 1;
            $priorityScore = (float) ($weight * 100);

            $token = Token::create([
                'token_number' => $tokenNumber,
                'sequence_number' => $sequence,
                'branch_id' => $branchId,
                'service_category_id' => $serviceCategoryId,
                'priority_category_id' => $priorityCategoryId,
                'salutation' => $customerData['salutation'] ?? 'Mr.',
                'customer_name' => $customerData['customer_name'] ?? 'Walk-in Customer',
                'customer_phone' => $customerData['customer_phone'] ?? null,
                'customer_cnic' => $customerData['customer_cnic'] ?? null,
                'customer_email' => $customerData['customer_email'] ?? null,
                'account_number' => $customerData['account_number'] ?? null,
                'transaction_purpose' => $customerData['transaction_purpose'] ?? null,
                'expected_amount' => $customerData['expected_amount'] ?? null,
                'channel' => $channel,
                'status' => 'waiting',
                'priority_score' => $priorityScore,
                'issued_at' => now(),
            ]);

            return $token;
        });
    }

    /**
     * Calculate estimated waiting time & queue position for a token
     */
    public function getEstimatedWait(Token $token): array
    {
        // Count tokens waiting ahead with higher or equal priority
        $tokensAhead = Token::where('branch_id', $token->branch_id)
            ->where('status', 'waiting')
            ->where(function ($query) use ($token) {
                $query->where('priority_score', '>', $token->priority_score)
                    ->orWhere(function ($q) use ($token) {
                        $q->where('priority_score', '=', $token->priority_score)
                            ->where('id', '<', $token->id);
                    });
            })
            ->count();

        // Calculate rolling average service time for this service in past 20 tokens (or default)
        $avgServiceTimeSec = Token::where('branch_id', $token->branch_id)
            ->where('service_category_id', $token->service_category_id)
            ->where('status', 'completed')
            ->where('serving_time_seconds', '>', 60)
            ->latest('completed_at')
            ->take(20)
            ->avg('serving_time_seconds');

        $avgMins = $avgServiceTimeSec 
            ? round($avgServiceTimeSec / 60) 
            : ($token->serviceCategory->avg_service_time_mins ?? 8);

        // Count active serving counters for this service
        $activeCountersCount = Counter::where('branch_id', $token->branch_id)
            ->whereIn('status', ['online', 'serving'])
            ->whereHas('services', function ($q) use ($token) {
                $q->where('service_categories.id', $token->service_category_id);
            })
            ->count();

        $activeCountersCount = max(1, $activeCountersCount);

        $estimatedMins = ceil(($tokensAhead * $avgMins) / $activeCountersCount);

        return [
            'tokens_ahead' => $tokensAhead,
            'estimated_minutes' => max(2, $estimatedMins),
            'avg_service_time' => $avgMins,
            'active_counters' => $activeCountersCount,
        ];
    }

    public function formatUrduVoice(string $tokenNum, string $counterNum, ?string $customerName = null, ?string $salutation = null): string
    {
        $prefixMap = [
            'C' => 'سی',
            'W' => 'ڈبلیو',
            'A' => 'اے',
            'P' => 'پی',
            'R' => 'آر',
            'L' => 'ایل',
            'S' => 'ایس',
            'U' => 'یو',
        ];

        $salutationMap = [
            'Mr.' => 'جناب',
            'Mrs.' => 'محترمہ',
            'Ms.' => 'محترمہ',
            'Miss' => 'محترمہ',
            'Dr.' => 'ڈاکٹر',
            'Haji' => 'حاجی صاحب',
            'Syed' => 'سید صاحب',
            'Sheikh' => 'شیخ صاحب',
        ];

        $parts = explode('-', $tokenNum);
        $prefix = $parts[0] ?? '';
        $seq = $parts[1] ?? '';

        $prefixUrdu = $prefixMap[strtoupper($prefix)] ?? $prefix;

        $nameAnnouncement = '';
        if ($customerName && !in_array(strtolower(trim($customerName)), ['walk-in customer', 'valued bank customer', 'customer', 'walk-in'])) {
            $urduSalutation = $salutationMap[$salutation ?? 'Mr.'] ?? 'جناب';
            $nameAnnouncement = "، {$urduSalutation} {$customerName}";
        }

        return "ٹوکن نمبر {$prefixUrdu} {$seq}{$nameAnnouncement}، کاؤنٹر نمبر {$counterNum} پر تشریف لائیں";
    }

    public function formatEnglishVoice(string $tokenNum, string $counterNum, ?string $customerName = null, ?string $salutation = null): string
    {
        $parts = explode('-', $tokenNum);
        $prefix = $parts[0] ?? '';
        $seq = $parts[1] ?? '';

        $spacedSeq = implode(' ', str_split($seq));

        $nameAnnouncement = '';
        if ($customerName && !in_array(strtolower(trim($customerName)), ['walk-in customer', 'valued bank customer', 'customer', 'walk-in'])) {
            $salute = $salutation ? trim($salutation) . ' ' : '';
            $nameAnnouncement = ", {$salute}{$customerName}";
        }

        return "Token Number {$prefix} {$spacedSeq}{$nameAnnouncement}, please proceed to Counter {$counterNum}";
    }

    /**
     * Call Next Token for a specific Counter and Teller
     */
    public function callNextToken(Counter $counter, int $tellerUserId): ?Token
    {
        return DB::transaction(function () use ($counter, $tellerUserId) {
            // Get service IDs assigned to this counter
            $assignedServiceIds = $counter->services()->pluck('service_categories.id')->toArray();

            // Find next eligible token:
            // 1) Branch match
            // 2) Status is 'waiting'
            // 3) Service is served by this counter (or any if none restricted)
            // 4) Order by priority_score DESC, id ASC (SBP priority compliance)
            $query = Token::where('branch_id', $counter->branch_id)
                ->where('status', 'waiting');

            if (!empty($assignedServiceIds)) {
                $query->whereIn('service_category_id', $assignedServiceIds);
            }

            // If counter is marked as dedicated priority counter (SBP guideline), prioritize PWD/Senior/Pensioner
            if ($counter->type === 'priority_dedicated') {
                $query->whereHas('priorityCategory', function ($q) {
                    $q->whereIn('code', ['PWD', 'SENIOR', 'PENSIONER', 'PREGNANT']);
                });
            }

            $nextToken = $query->orderBy('priority_score', 'desc')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->first();

            // If priority counter had no priority customers, fallback to any customer
            if (!$nextToken && $counter->type === 'priority_dedicated') {
                $nextToken = Token::where('branch_id', $counter->branch_id)
                    ->where('status', 'waiting')
                    ->when(!empty($assignedServiceIds), fn($q) => $q->whereIn('service_category_id', $assignedServiceIds))
                    ->orderBy('priority_score', 'desc')
                    ->orderBy('id', 'asc')
                    ->lockForUpdate()
                    ->first();
            }

            if (!$nextToken) {
                return null;
            }

            $now = now();
            $waitTime = $nextToken->issued_at ? $now->diffInSeconds($nextToken->issued_at) : 0;

            $nextToken->update([
                'status' => 'called',
                'counter_id' => $counter->id,
                'served_by_user_id' => $tellerUserId,
                'called_at' => $now,
                'waiting_time_seconds' => $waitTime,
                'recall_count' => 0,
            ]);

            $counter->update([
                'current_token_id' => $nextToken->id,
                'current_user_id' => $tellerUserId,
                'status' => 'serving',
                'last_active_at' => $now,
            ]);

            // Create SBP BSC Bilingual Voice Announcement
            $counterNum = $counter->counter_number;
            $tokenNum = $nextToken->token_number;

            Announcement::create([
                'branch_id' => $counter->branch_id,
                'token_id' => $nextToken->id,
                'token_number' => $tokenNum,
                'counter_number' => $counterNum,
                'voice_text_ur' => $this->formatUrduVoice($tokenNum, $counterNum, $nextToken->customer_name, $nextToken->salutation),
                'voice_text_en' => $this->formatEnglishVoice($tokenNum, $counterNum, $nextToken->customer_name, $nextToken->salutation),
                'is_played' => false,
            ]);

            return $nextToken;
        });
    }

    /**
     * Call a Specific Token directly (Jump Call / Authorized Bypass)
     */
    public function callSpecificToken(Counter $counter, int $tellerUserId, string $tokenNumber): ?Token
    {
        return DB::transaction(function () use ($counter, $tellerUserId, $tokenNumber) {
            $token = Token::where('branch_id', $counter->branch_id)
                ->where('token_number', strtoupper(trim($tokenNumber)))
                ->whereIn('status', ['waiting', 'on_hold'])
                ->lockForUpdate()
                ->first();

            if (!$token) {
                return null;
            }

            $now = now();
            $waitTime = $token->issued_at ? $now->diffInSeconds($token->issued_at) : 0;

            $token->update([
                'status' => 'called',
                'counter_id' => $counter->id,
                'served_by_user_id' => $tellerUserId,
                'called_at' => $now,
                'waiting_time_seconds' => $waitTime,
                'recall_count' => 0,
            ]);

            $counter->update([
                'current_token_id' => $token->id,
                'current_user_id' => $tellerUserId,
                'status' => 'serving',
                'last_active_at' => $now,
            ]);

            $counterNum = $counter->counter_number;
            $tokenNum = $token->token_number;

            Announcement::create([
                'branch_id' => $counter->branch_id,
                'token_id' => $token->id,
                'token_number' => $tokenNum,
                'counter_number' => $counterNum,
                'voice_text_ur' => $this->formatUrduVoice($tokenNum, $counterNum, $token->customer_name, $token->salutation),
                'voice_text_en' => $this->formatEnglishVoice($tokenNum, $counterNum, $token->customer_name, $token->salutation),
                'is_played' => false,
            ]);

            return $token;
        });
    }

    /**
     * Recall a Token (Triggers chime + voice announcement replay)
     */
    public function recallToken(Token $token, Counter $counter): Token
    {
        $token->increment('recall_count');

        $counterNum = $counter->counter_number;
        $tokenNum = $token->token_number;

        Announcement::create([
            'branch_id' => $counter->branch_id,
            'token_id' => $token->id,
            'token_number' => $tokenNum,
            'counter_number' => $counterNum,
            'voice_text_ur' => "دوبارہ اعلان: " . $this->formatUrduVoice($tokenNum, $counterNum, $token->customer_name, $token->salutation),
            'voice_text_en' => "Recall: " . $this->formatEnglishVoice($tokenNum, $counterNum, $token->customer_name, $token->salutation),
            'is_played' => false,
        ]);

        return $token->fresh();
    }

    /**
     * Start Serving the customer
     */
    public function startServing(Token $token): Token
    {
        $token->update([
            'status' => 'serving',
            'serving_started_at' => now(),
        ]);

        return $token;
    }

    /**
     * Complete Service for Token
     */
    public function completeToken(Token $token, array $data = []): Token
    {
        $now = now();
        $servingStart = $token->serving_started_at ?? $token->called_at ?? $now;
        $servingSecs = $now->diffInSeconds($servingStart);

        $token->update([
            'status' => 'completed',
            'completed_at' => $now,
            'serving_time_seconds' => max(1, $servingSecs),
            'transaction_amount' => $data['transaction_amount'] ?? null,
            'transaction_reference' => $data['transaction_reference'] ?? null,
            'notes' => $data['notes'] ?? $token->notes,
        ]);

        if ($token->counter) {
            $token->counter->update([
                'current_token_id' => null,
                'status' => 'online',
                'last_active_at' => $now,
            ]);
        }

        return $token;
    }

    /**
     * Put Token on Hold
     */
    public function holdToken(Token $token, ?string $reason = null): Token
    {
        $token->update([
            'status' => 'on_hold',
            'notes' => $reason ? ($token->notes . "\n[On Hold Reason: " . $reason . "]") : $token->notes,
        ]);

        if ($token->counter) {
            $token->counter->update([
                'current_token_id' => null,
                'status' => 'online',
            ]);
        }

        return $token;
    }

    /**
     * Transfer Token to another counter or service queue
     */
    public function transferToken(Token $token, int $toCounterId, ?string $notes = null): Token
    {
        $targetCounter = Counter::findOrFail($toCounterId);

        $token->update([
            'status' => 'waiting',
            'transferred_from_counter_id' => $token->counter_id,
            'transferred_to_counter_id' => $targetCounter->id,
            'counter_id' => null,
            'priority_score' => $token->priority_score + 50, // Give high priority for transferred customers
            'notes' => $notes ? ($token->notes . "\n[Transferred to Counter " . $targetCounter->counter_number . ": " . $notes . "]") : $token->notes,
        ]);

        if ($token->counter) {
            $token->counter->update([
                'current_token_id' => null,
                'status' => 'online',
            ]);
        }

        return $token;
    }

    /**
     * Mark Token as No-Show / Customer Skipped
     */
    public function markNoShow(Token $token): Token
    {
        $token->update([
            'status' => 'no_show',
            'completed_at' => now(),
        ]);

        if ($token->counter) {
            $token->counter->update([
                'current_token_id' => null,
                'status' => 'online',
            ]);
        }

        return $token;
    }

    /**
     * Get Real-time Branch Dashboard Analytics
     */
    public function getBranchQueueStats(int $branchId): array
    {
        $today = Carbon::today();

        $todayTokens = Token::where('branch_id', $branchId)
            ->whereDate('created_at', $today);

        $totalToday = (clone $todayTokens)->count();
        $waitingCount = (clone $todayTokens)->where('status', 'waiting')->count();
        $servingCount = (clone $todayTokens)->whereIn('status', ['called', 'serving'])->count();
        $completedCount = (clone $todayTokens)->where('status', 'completed')->count();
        $noShowCount = (clone $todayTokens)->where('status', 'no_show')->count();
        $onHoldCount = (clone $todayTokens)->where('status', 'on_hold')->count();

        // Priority breakdown (SBP Regulatory Compliance SLA)
        $priorityCount = (clone $todayTokens)
            ->whereHas('priorityCategory', function ($q) {
                $q->whereIn('code', ['SENIOR', 'PENSIONER', 'PWD', 'PREGNANT']);
            })
            ->count();

        $priorityCompleted = (clone $todayTokens)
            ->where('status', 'completed')
            ->whereHas('priorityCategory', function ($q) {
                $q->whereIn('code', ['SENIOR', 'PENSIONER', 'PWD', 'PREGNANT']);
            })
            ->count();

        // Average Wait Time and Average Service Time in minutes
        $avgWaitSecs = (clone $todayTokens)->where('status', 'completed')->avg('waiting_time_seconds') ?: 0;
        $avgServingSecs = (clone $todayTokens)->where('status', 'completed')->avg('serving_time_seconds') ?: 0;

        // Active counters
        $onlineCounters = Counter::where('branch_id', $branchId)
            ->whereIn('status', ['online', 'serving', 'break'])
            ->count();

        return [
            'total_today' => $totalToday,
            'waiting' => $waitingCount,
            'serving' => $servingCount,
            'completed' => $completedCount,
            'no_show' => $noShowCount,
            'on_hold' => $onHoldCount,
            'priority_total' => $priorityCount,
            'priority_completed' => $priorityCompleted,
            'avg_wait_minutes' => round($avgWaitSecs / 60, 1),
            'avg_service_minutes' => round($avgServingSecs / 60, 1),
            'active_counters' => $onlineCounters,
        ];
    }
}
