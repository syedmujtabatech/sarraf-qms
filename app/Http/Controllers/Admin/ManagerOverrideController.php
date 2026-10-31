<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PriorityCategory;
use App\Models\ServiceCategory;
use App\Models\Token;
use App\Services\QueueService;
use Illuminate\Http\Request;

class ManagerOverrideController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    public function injectEmergencyToken(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'service_category_id' => 'required|exists:service_categories,id',
            'customer_name' => 'required|string|max:100',
            'override_reason' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_cnic' => 'nullable|string|max:25',
        ]);

        // Get VIP priority or highest priority tier
        $vipPriority = PriorityCategory::where('code', 'VIP')->first() ?? PriorityCategory::orderBy('weight_multiplier', 'desc')->first();

        $token = $this->queueService->generateToken(
            $validated['branch_id'],
            $validated['service_category_id'],
            $vipPriority->id,
            [
                'customer_name' => $validated['customer_name'] . ' (Manager Override)',
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_cnic' => $validated['customer_cnic'] ?? null,
            ],
            'teller_manual'
        );

        // Boost priority score higher than standard and flag manager_override
        $token->update([
            'manager_override' => true,
            'override_reason' => $validated['override_reason'],
            'priority_score' => 999.00, // Maximum precedence
        ]);

        return back()->with('success', "Emergency Token {$token->token_number} injected by Manager Override!");
    }

    public function escalateToken(Token $token)
    {
        $token->update([
            'is_escalated' => true,
            'priority_score' => $token->priority_score + 200,
        ]);

        return back()->with('warning', "Token {$token->token_number} escalated for immediate counter summon.");
    }

    public function broadcastAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'voice_text_en' => 'required|string|max:300',
            'voice_text_ur' => 'required|string|max:300',
        ]);

        \App\Models\Announcement::create([
            'branch_id' => $validated['branch_id'],
            'voice_text_en' => $validated['voice_text_en'],
            'voice_text_ur' => $validated['voice_text_ur'],
            'token_id' => null,
            'token_number' => 'ALL',
            'counter_number' => 'FLOOR',
            'type' => 'manager_broadcast',
            'is_played' => false,
        ]);

        return back()->with('success', 'Floor Audio Announcement successfully queued for broadcast across Lobby TV loudspeakers!');
    }
}
