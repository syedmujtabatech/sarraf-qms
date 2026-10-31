<?php

namespace App\Http\Controllers\Display;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Branch;
use App\Models\Counter;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    public function index(Request $request)
    {
        $selectedBranchId = $request->get('branch_id', Branch::first()->id ?? 1);
        $branches = Branch::where('is_active', true)->get();
        $currentBranch = Branch::find($selectedBranchId) ?? $branches->first();

        // Currently serving and called tokens
        $activeCounters = Counter::with(['currentToken.serviceCategory', 'currentToken.priorityCategory'])
            ->where('branch_id', $selectedBranchId)
            ->whereIn('status', ['serving', 'online'])
            ->orderBy('counter_number')
            ->get();

        // Recently called tokens today
        $calledTokens = Token::with(['serviceCategory', 'counter', 'priorityCategory'])
            ->where('branch_id', $selectedBranchId)
            ->whereIn('status', ['called', 'serving'])
            ->orderBy('called_at', 'desc')
            ->take(8)
            ->get();

        // Upcoming in queue
        $waitingTokens = Token::with(['serviceCategory', 'priorityCategory'])
            ->where('branch_id', $selectedBranchId)
            ->where('status', 'waiting')
            ->orderBy('priority_score', 'desc')
            ->orderBy('id', 'asc')
            ->take(6)
            ->get();

        return view('display.index', compact(
            'branches',
            'currentBranch',
            'selectedBranchId',
            'activeCounters',
            'calledTokens',
            'waitingTokens'
        ));
    }

    public function pollUpdates(int $branchId)
    {
        // 1. Fetch pending announcements for audio playback (SBP BSC Spec)
        $pendingAnnouncements = Announcement::where('branch_id', $branchId)
            ->where('is_played', false)
            ->where('created_at', '>=', Carbon::now()->subMinutes(2))
            ->orderBy('id', 'asc')
            ->get();

        // Mark announcements as played
        if ($pendingAnnouncements->isNotEmpty()) {
            Announcement::whereIn('id', $pendingAnnouncements->pluck('id'))->update(['is_played' => true]);
        }

        // 2. Active counters status
        $activeCounters = Counter::with(['currentToken.serviceCategory', 'currentToken.priorityCategory'])
            ->where('branch_id', $branchId)
            ->orderBy('counter_number')
            ->get();

        // 3. Recently called tokens
        $calledTokens = Token::with(['serviceCategory', 'counter', 'priorityCategory'])
            ->where('branch_id', $branchId)
            ->whereIn('status', ['called', 'serving'])
            ->orderBy('called_at', 'desc')
            ->take(8)
            ->get();

        // 4. Waiting tokens
        $waitingTokens = Token::with(['serviceCategory', 'priorityCategory'])
            ->where('branch_id', $branchId)
            ->where('status', 'waiting')
            ->orderBy('priority_score', 'desc')
            ->orderBy('id', 'asc')
            ->take(6)
            ->get();

        return response()->json([
            'announcements' => $pendingAnnouncements,
            'active_counters' => $activeCounters,
            'called_tokens' => $calledTokens,
            'waiting_tokens' => $waitingTokens,
            'server_time' => Carbon::now()->format('h:i:s A'),
        ]);
    }

    public function pole(Counter $counter)
    {
        $counter->load(['branch', 'currentToken.serviceCategory', 'currentToken.priorityCategory', 'currentUser']);
        return view('display.pole', compact('counter'));
    }

    public function poleApi(Counter $counter)
    {
        $counter->load(['currentToken.serviceCategory', 'currentToken.priorityCategory', 'currentUser']);
        
        return response()->json([
            'counter_id' => $counter->id,
            'counter_number' => $counter->counter_number,
            'counter_name' => $counter->name,
            'status' => $counter->status,
            'break_reason' => $counter->break_reason,
            'current_token' => $counter->currentToken ? [
                'token_number' => $counter->currentToken->token_number,
                'customer_name' => ($counter->currentToken->salutation ? $counter->currentToken->salutation . ' ' : '') . $counter->currentToken->customer_name,
                'service' => $counter->currentToken->serviceCategory?->name,
                'priority' => $counter->currentToken->priorityCategory?->name,
                'status' => $counter->currentToken->status,
            ] : null,
            'teller_name' => $counter->currentUser?->name ?? 'Open Station',
            'server_time' => Carbon::now()->format('h:i:s A'),
        ]);
    }
}
