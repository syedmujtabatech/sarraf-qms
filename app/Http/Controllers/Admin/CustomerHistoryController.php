<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Token;
use Illuminate\Http\Request;

class CustomerHistoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $customerHistory = null;
        $customerSummary = null;

        if ($search) {
            $tokens = Token::with(['branch', 'serviceCategory', 'priorityCategory', 'counter', 'servedBy', 'feedback'])
                ->where(function ($q) use ($search) {
                    $q->where('customer_cnic', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('token_number', 'like', "%{$search}%");
                })
                ->orderBy('created_at', 'desc')
                ->get();

            if ($tokens->isNotEmpty()) {
                $customerHistory = $tokens;
                $customerSummary = [
                    'name' => $tokens->first()->customer_name,
                    'cnic' => $tokens->first()->customer_cnic,
                    'phone' => $tokens->first()->customer_phone,
                    'total_visits' => $tokens->count(),
                    'completed_visits' => $tokens->where('status', 'completed')->count(),
                    'avg_wait_mins' => round($tokens->where('status', 'completed')->avg('waiting_time_seconds') / 60, 1),
                    'total_transactions_val' => $tokens->sum('transaction_amount'),
                ];
            }
        }

        return view('admin.customers.history', compact('search', 'customerHistory', 'customerSummary'));
    }
}
