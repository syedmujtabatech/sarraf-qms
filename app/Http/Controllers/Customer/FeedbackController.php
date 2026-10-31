<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerFeedback;
use App\Models\Token;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $tokenId = $request->get('token_id');
        $token = null;

        if ($tokenId) {
            $token = Token::with(['branch', 'counter', 'serviceCategory'])->find($tokenId);
        }

        return view('customer.feedback', compact('token'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'token_id' => 'required|exists:tokens,id',
            'rating' => 'required|integer|min:1|max:5',
            'feedback_tags' => 'nullable|array',
            'comments' => 'nullable|string|max:500',
        ]);

        $token = Token::findOrFail($validated['token_id']);

        $feedback = CustomerFeedback::create([
            'token_id' => $token->id,
            'branch_id' => $token->branch_id,
            'counter_id' => $token->counter_id,
            'rating' => $validated['rating'],
            'feedback_tags' => $validated['feedback_tags'] ?? [],
            'comments' => $validated['comments'] ?? null,
            'customer_phone' => $token->customer_phone,
        ]);

        return redirect()->route('feedback.thankYou', $feedback->id);
    }

    public function thankYou(CustomerFeedback $feedback)
    {
        return view('customer.feedback_thanks', compact('feedback'));
    }
}
