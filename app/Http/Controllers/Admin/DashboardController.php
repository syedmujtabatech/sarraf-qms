<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Counter;
use App\Models\CustomerFeedback;
use App\Models\PriorityCategory;
use App\Models\ServiceCategory;
use App\Models\Token;
use App\Services\QueueService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    public function index(Request $request)
    {
        $selectedBranchId = $request->get('branch_id', Branch::first()->id ?? 1);
        $branches = Branch::where('is_active', true)->get();
        $currentBranch = Branch::with(['counters.services', 'counters.currentUser'])->find($selectedBranchId);

        if (!$currentBranch && $branches->isNotEmpty()) {
            $currentBranch = $branches->first();
            $selectedBranchId = $currentBranch->id;
        }

        $stats = $this->queueService->getBranchQueueStats($selectedBranchId);

        // Recent tokens today
        $recentTokens = Token::with(['serviceCategory', 'priorityCategory', 'counter', 'servedBy'])
            ->where('branch_id', $selectedBranchId)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        // Counters for this branch with active tokens
        $counters = Counter::with(['currentToken.serviceCategory', 'currentToken.priorityCategory', 'currentUser', 'services'])
            ->where('branch_id', $selectedBranchId)
            ->get();

        // Service categories distribution today
        $serviceStats = ServiceCategory::withCount(['tokens' => function ($q) use ($selectedBranchId) {
            $q->where('branch_id', $selectedBranchId)->whereDate('created_at', Carbon::today());
        }])->get();

        // Priority categories distribution today (SBP compliance)
        $priorityStats = PriorityCategory::withCount(['tokens' => function ($q) use ($selectedBranchId) {
            $q->where('branch_id', $selectedBranchId)->whereDate('created_at', Carbon::today());
        }])->get();

        // Average feedback rating
        $avgFeedback = CustomerFeedback::where('branch_id', $selectedBranchId)->avg('rating') ?: 4.8;
        $totalFeedbacks = CustomerFeedback::where('branch_id', $selectedBranchId)->count();

        return view('admin.dashboard', compact(
            'branches',
            'currentBranch',
            'selectedBranchId',
            'stats',
            'recentTokens',
            'counters',
            'serviceStats',
            'priorityStats',
            'avgFeedback',
            'totalFeedbacks'
        ));
    }

    public function liveApi(int $branchId)
    {
        $stats = $this->queueService->getBranchQueueStats($branchId);

        $activeCounters = Counter::with(['currentToken.serviceCategory', 'currentToken.priorityCategory', 'currentUser'])
            ->where('branch_id', $branchId)
            ->get();

        $waitingTokens = Token::with(['serviceCategory', 'priorityCategory'])
            ->where('branch_id', $branchId)
            ->where('status', 'waiting')
            ->orderBy('priority_score', 'desc')
            ->orderBy('id', 'asc')
            ->take(10)
            ->get();

        return response()->json([
            'stats' => $stats,
            'counters' => $activeCounters,
            'waiting_tokens' => $waitingTokens,
        ]);
    }
}
