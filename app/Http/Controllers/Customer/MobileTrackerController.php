<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Counter;
use App\Models\Token;
use App\Services\QueueService;
use Illuminate\Http\Request;

class MobileTrackerController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    public function show(string $tokenNumber)
    {
        $token = Token::with(['branch', 'serviceCategory', 'priorityCategory', 'counter', 'servedBy'])
            ->where('token_number', $tokenNumber)
            ->latest('id')
            ->firstOrFail();

        $waitStats = $this->queueService->getEstimatedWait($token);

        return view('customer.mobile_track', compact('token', 'waitStats'));
    }

    public function status(string $tokenNumber)
    {
        $token = Token::with(['branch', 'serviceCategory', 'priorityCategory', 'counter', 'servedBy'])
            ->where('token_number', $tokenNumber)
            ->latest('id')
            ->firstOrFail();

        $waitStats = $this->queueService->getEstimatedWait($token);

        return response()->json([
            'status' => $token->status,
            'status_label' => ucfirst($token->status),
            'counter_number' => $token->counter->counter_number ?? null,
            'counter_name' => $token->counter->name ?? null,
            'teller_name' => $token->servedBy->name ?? null,
            'tokens_ahead' => $waitStats['tokens_ahead'],
            'estimated_minutes' => $waitStats['estimated_minutes'],
            'called_at' => $token->called_at ? $token->called_at->format('h:i:s A') : null,
            'recall_count' => $token->recall_count,
        ]);
    }

    public function joinForm(Request $request)
    {
        $branchParam = $request->get('branch', $request->get('branch_id'));
        $selectedBranchId = $branchParam ? (int) $branchParam : (\App\Models\Branch::first()->id ?? 1);
        $branches = \App\Models\Branch::where('is_active', true)->get();
        $currentBranch = \App\Models\Branch::find($selectedBranchId) ?? $branches->first();

        $services = \App\Models\ServiceCategory::where('is_active', true)->orderBy('sort_order')->get();
        $priorities = \App\Models\PriorityCategory::where('is_active', true)->orderBy('weight_multiplier', 'desc')->get();

        return view('customer.join', compact('branches', 'currentBranch', 'services', 'priorities', 'selectedBranchId'));
    }

    public function processJoin(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'service_category_id' => 'required|exists:service_categories,id',
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'priority_category_id' => 'nullable|exists:priority_categories,id',
        ]);

        $defaultPriority = \App\Models\PriorityCategory::where('code', 'REGULAR')->first()->id 
            ?? \App\Models\PriorityCategory::first()->id;

        $token = $this->queueService->generateToken(
            (int) $validated['branch_id'],
            (int) $validated['service_category_id'],
            (int) ($validated['priority_category_id'] ?? $defaultPriority),
            [
                'salutation' => 'Mr.',
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
            ],
            'mobile'
        );

        return redirect()->route('mobile.track', $token->token_number)
            ->with('success', "Welcome {$token->customer_name}! Your live queue pass has been generated.");
    }
}
