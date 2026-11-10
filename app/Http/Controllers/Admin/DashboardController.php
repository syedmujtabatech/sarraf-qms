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
        $user = auth()->user();
        $isBranchManager = $user && $user->role === 'branch_manager';

        if ($isBranchManager && $user->branch_id) {
            $selectedBranchId = (int) $user->branch_id;
            $branches = Branch::where('id', $selectedBranchId)->get();
        } else {
            $selectedBranchId = (int) $request->get('branch_id', Branch::first()->id ?? 1);
            $branches = Branch::where('is_active', true)->get();
        }

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

        // Priority categories distribution today
        $priorityStats = PriorityCategory::withCount(['tokens' => function ($q) use ($selectedBranchId) {
            $q->where('branch_id', $selectedBranchId)->whereDate('created_at', Carbon::today());
        }])->get();

        // Average feedback rating
        $avgFeedback = CustomerFeedback::where('branch_id', $selectedBranchId)->avg('rating') ?: 4.8;
        $totalFeedbacks = CustomerFeedback::where('branch_id', $selectedBranchId)->count();

        // Services & Priorities for quick ticket issuance
        $services = ServiceCategory::where('is_active', true)->get();
        $priorities = PriorityCategory::where('is_active', true)->get();

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
            'totalFeedbacks',
            'isBranchManager',
            'services',
            'priorities'
        ));
    }

    public function quickTicket(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'service_category_id' => 'required|exists:service_categories,id',
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'priority_category_id' => 'nullable|exists:priority_categories,id',
        ]);

        $defaultPriority = PriorityCategory::where('code', 'REGULAR')->first()->id 
            ?? PriorityCategory::first()->id;

        $token = $this->queueService->generateToken(
            (int) $validated['branch_id'],
            (int) $validated['service_category_id'],
            (int) ($validated['priority_category_id'] ?? $defaultPriority),
            [
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
            ],
            'kiosk'
        );

        return redirect()->route('kiosk.printSlip', $token->id)
            ->with('success', "Ticket {$token->token_number} generated for {$token->customer_name}.");
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
