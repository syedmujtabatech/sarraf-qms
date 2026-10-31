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
        $selectedBranchId = $request->get('branch_id', \App\Models\Branch::first()->id ?? 1);
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
            'priority_category_id' => 'required|exists:priority_categories,id',
            'salutation' => 'nullable|string|max:20',
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'customer_cnic' => 'nullable|string|max:25',
            'account_number' => 'nullable|string|max:35',
            'transaction_purpose' => 'nullable|string|max:100',
            'expected_amount' => 'nullable|numeric|min:0',
        ]);

        $token = $this->queueService->generateToken(
            $validated['branch_id'],
            $validated['service_category_id'],
            $validated['priority_category_id'],
            [
                'salutation' => $validated['salutation'] ?? 'Mr.',
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_cnic' => $validated['customer_cnic'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'transaction_purpose' => $validated['transaction_purpose'] ?? null,
                'expected_amount' => $validated['expected_amount'] ?? null,
            ],
            'mobile'
        );

        return redirect()->route('mobile.track', $token->token_number)
            ->with('success', "Welcome {$token->salutation} {$token->customer_name}! Your live queue pass has been generated.");
    }
}
