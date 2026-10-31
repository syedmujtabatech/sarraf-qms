<?php

namespace App\Http\Controllers\Teller;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Counter;
use App\Models\Token;
use App\Services\QueueService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TellerConsoleController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $counterId = $request->get('counter_id', $user->assigned_counter_id);

        $branches = Branch::where('is_active', true)->get();
        $selectedBranchId = $request->get('branch_id', $user->branch_id ?? Branch::first()->id);

        $counters = Counter::where('branch_id', $selectedBranchId)->get();
        $activeCounter = null;

        if ($counterId) {
            $activeCounter = Counter::with(['services', 'currentToken.serviceCategory', 'currentToken.priorityCategory'])
                ->where('id', $counterId)
                ->first();
        }

        if (!$activeCounter && $counters->isNotEmpty()) {
            $activeCounter = $counters->first();
            $counterId = $activeCounter->id;
        }

        // Current serving/called token
        $currentToken = $activeCounter?->currentToken;

        // Waiting queue for this counter
        $assignedServiceIds = $activeCounter ? $activeCounter->services()->pluck('service_categories.id')->toArray() : [];

        $waitingTokens = Token::with(['serviceCategory', 'priorityCategory'])
            ->where('branch_id', $selectedBranchId)
            ->where('status', 'waiting')
            ->when(!empty($assignedServiceIds), fn($q) => $q->whereIn('service_category_id', $assignedServiceIds))
            ->orderBy('priority_score', 'desc')
            ->orderBy('id', 'asc')
            ->get();

        // On hold tokens
        $heldTokens = Token::with(['serviceCategory', 'priorityCategory'])
            ->where('branch_id', $selectedBranchId)
            ->where('status', 'on_hold')
            ->when(!empty($assignedServiceIds), fn($q) => $q->whereIn('service_category_id', $assignedServiceIds))
            ->get();

        // Today's completed by this teller
        $myCompletedTokens = Token::with(['serviceCategory', 'priorityCategory'])
            ->where('served_by_user_id', $user->id)
            ->whereDate('completed_at', Carbon::today())
            ->orderBy('completed_at', 'desc')
            ->take(10)
            ->get();

        return view('teller.console', compact(
            'branches',
            'counters',
            'selectedBranchId',
            'activeCounter',
            'currentToken',
            'waitingTokens',
            'heldTokens',
            'myCompletedTokens'
        ));
    }

    public function callNext(Request $request)
    {
        $request->validate(['counter_id' => 'required|exists:counters,id']);
        $counter = Counter::findOrFail($request->counter_id);
        
        $token = $this->queueService->callNextToken($counter, Auth::id());

        if (!$token) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No customers waiting in this queue.']);
            }
            return back()->with('info', 'No customers are currently waiting in this service queue.');
        }

        $voiceUr = $this->queueService->formatUrduVoice($token->token_number, (string) $counter->counter_number, $token->customer_name, $token->salutation);
        $voiceEn = $this->queueService->formatEnglishVoice($token->token_number, (string) $counter->counter_number, $token->customer_name, $token->salutation);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Token {$token->token_number} called!",
                'token' => $token->load(['serviceCategory', 'priorityCategory']),
                'voice_en' => $voiceEn,
                'voice_ur' => $voiceUr,
                'counter_number' => $counter->counter_number,
            ]);
        }

        return back()
            ->with('success', "Called Token {$token->token_number} to Counter {$counter->counter_number}")
            ->with('announcement_en', $voiceEn)
            ->with('announcement_ur', $voiceUr)
            ->with('announcement_token', $token->token_number)
            ->with('announcement_counter', $counter->counter_number);
    }

    public function callSpecific(Request $request)
    {
        $request->validate([
            'counter_id' => 'required|exists:counters,id',
            'token_number' => 'required|string|max:10',
        ]);

        $counter = Counter::findOrFail($request->counter_id);
        $token = $this->queueService->callSpecificToken($counter, Auth::id(), $request->token_number);

        if (!$token) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => "Token '{$request->token_number}' not found in waiting queue."]);
            }
            return back()->with('error', "Token '{$request->token_number}' was not found in waiting or on-hold status.");
        }

        $voiceUr = $this->queueService->formatUrduVoice($token->token_number, (string) $counter->counter_number, $token->customer_name, $token->salutation);
        $voiceEn = $this->queueService->formatEnglishVoice($token->token_number, (string) $counter->counter_number, $token->customer_name, $token->salutation);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Token {$token->token_number} directly called!",
                'token' => $token->load(['serviceCategory', 'priorityCategory']),
                'voice_en' => $voiceEn,
                'voice_ur' => $voiceUr,
                'counter_number' => $counter->counter_number,
            ]);
        }

        return back()
            ->with('success', "Directly called Token {$token->token_number} to Counter {$counter->counter_number}")
            ->with('announcement_en', $voiceEn)
            ->with('announcement_ur', $voiceUr)
            ->with('announcement_token', $token->token_number)
            ->with('announcement_counter', $counter->counter_number);
    }

    public function recall(Token $token)
    {
        $counter = $token->counter ?? Counter::find(request('counter_id'));
        if (!$counter) {
            return back()->with('error', 'Counter not assigned.');
        }

        $this->queueService->recallToken($token, $counter);
        $voiceUr = $this->queueService->formatUrduVoice($token->token_number, (string) $counter->counter_number, $token->customer_name, $token->salutation);
        $voiceEn = $this->queueService->formatEnglishVoice($token->token_number, (string) $counter->counter_number, $token->customer_name, $token->salutation);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Token {$token->token_number} recalled.",
                'voice_en' => $voiceEn,
                'voice_ur' => $voiceUr,
                'counter_number' => $counter->counter_number,
            ]);
        }

        return back()
            ->with('info', "Recalled Token {$token->token_number} (Chime & Announcement queued)")
            ->with('announcement_en', $voiceEn)
            ->with('announcement_ur', $voiceUr)
            ->with('announcement_token', $token->token_number)
            ->with('announcement_counter', $counter->counter_number);
    }

    public function startServing(Token $token)
    {
        $this->queueService->startServing($token);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Service started for {$token->token_number}"]);
        }

        return back()->with('success', "Service started for {$token->token_number}");
    }

    public function complete(Request $request, Token $token)
    {
        $validated = $request->validate([
            'transaction_amount' => 'nullable|numeric|min:0',
            'transaction_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $this->queueService->completeToken($token, $validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Token {$token->token_number} completed."]);
        }

        return back()->with('success', "Token {$token->token_number} marked as completed.");
    }

    public function hold(Request $request, Token $token)
    {
        $reason = $request->get('hold_reason', 'Awaiting documents / customer signature');
        $this->queueService->holdToken($token, $reason);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Token {$token->token_number} put on hold."]);
        }

        return back()->with('warning', "Token {$token->token_number} put on hold.");
    }

    public function transfer(Request $request, Token $token)
    {
        $request->validate([
            'to_counter_id' => 'required|exists:counters,id',
            'transfer_notes' => 'nullable|string|max:255',
        ]);

        $this->queueService->transferToken($token, $request->to_counter_id, $request->transfer_notes);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Token {$token->token_number} transferred."]);
        }

        return back()->with('success', "Token {$token->token_number} transferred to selected counter.");
    }

    public function noShow(Token $token)
    {
        $this->queueService->markNoShow($token);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Token {$token->token_number} marked as No-Show."]);
        }

        return back()->with('warning', "Token {$token->token_number} marked as No-Show.");
    }

    public function takeBreak(Request $request, Counter $counter)
    {
        $reason = $request->get('break_reason', 'Official Break');
        $counter->update([
            'status' => 'break',
            'break_reason' => $reason,
        ]);

        return back()->with('warning', "Counter #{$counter->counter_number} paused for: {$reason}");
    }

    public function resumeWork(Counter $counter)
    {
        $counter->update([
            'status' => 'online',
            'break_reason' => null,
            'last_active_at' => now(),
        ]);

        return back()->with('success', "Counter #{$counter->counter_number} resumed. Station is online.");
    }

    public function updateCashDrawer(Request $request, Counter $counter)
    {
        $validated = $request->validate([
            'current_cash_drawer' => 'required|numeric|min:0',
        ]);

        $counter->update([
            'current_cash_drawer' => $validated['current_cash_drawer'],
        ]);

        return back()->with('success', "Counter #{$counter->counter_number} cash drawer updated: PKR " . number_format($counter->current_cash_drawer, 2));
    }

    public function liveQueueApi(int $counterId)
    {
        $counter = Counter::with(['currentToken.serviceCategory', 'currentToken.priorityCategory'])->findOrFail($counterId);
        $assignedServiceIds = $counter->services()->pluck('service_categories.id')->toArray();

        $waitingTokens = Token::with(['serviceCategory', 'priorityCategory'])
            ->where('branch_id', $counter->branch_id)
            ->where('status', 'waiting')
            ->when(!empty($assignedServiceIds), fn($q) => $q->whereIn('service_category_id', $assignedServiceIds))
            ->orderBy('priority_score', 'desc')
            ->orderBy('id', 'asc')
            ->take(15)
            ->get();

        return response()->json([
            'current_token' => $counter->currentToken,
            'waiting_tokens' => $waitingTokens,
            'waiting_count' => $waitingTokens->count(),
            'counter_status' => $counter->status,
        ]);
    }
}
