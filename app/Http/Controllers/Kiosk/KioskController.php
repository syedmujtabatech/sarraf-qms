<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PriorityCategory;
use App\Models\ServiceCategory;
use App\Models\Token;
use App\Services\QueueService;
use Illuminate\Http\Request;

class KioskController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    public function index(Request $request)
    {
        $selectedBranchId = $request->get('branch_id', Branch::first()->id ?? 1);
        $branches = Branch::where('is_active', true)->get();
        $currentBranch = Branch::find($selectedBranchId) ?? $branches->first();

        $services = ServiceCategory::where('is_active', true)->orderBy('sort_order')->get();
        $priorities = PriorityCategory::where('is_active', true)->orderBy('weight_multiplier', 'desc')->get();

        return view('kiosk.index', compact('branches', 'currentBranch', 'services', 'priorities', 'selectedBranchId'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'service_category_id' => 'required|exists:service_categories,id',
            'priority_category_id' => 'required|exists:priority_categories,id',
            'salutation' => 'nullable|string|max:20',
            'customer_name' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
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
                'customer_name' => $validated['customer_name'] ?: 'Valued Bank Customer',
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_cnic' => $validated['customer_cnic'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'transaction_purpose' => $validated['transaction_purpose'] ?? null,
                'expected_amount' => $validated['expected_amount'] ?? null,
            ],
            $request->get('channel', 'kiosk')
        );

        $waitStats = $this->queueService->getEstimatedWait($token);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'token' => $token->load(['serviceCategory', 'priorityCategory', 'branch']),
                'wait_stats' => $waitStats,
            ]);
        }

        return redirect()->route('kiosk.printSlip', $token->id);
    }

    public function printSlip(Token $token)
    {
        $token->load(['serviceCategory', 'priorityCategory', 'branch']);
        $waitStats = $this->queueService->getEstimatedWait($token);

        return view('kiosk.slip', compact('token', 'waitStats'));
    }
}
