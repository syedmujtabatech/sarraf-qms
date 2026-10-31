<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\PriorityCategory;
use App\Models\ServiceCategory;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $branchId = $request->get('branch_id', Branch::first()->id ?? null);
        $startDate = $request->get('start_date', Carbon::today()->subDays(7)->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());
        $branches = Branch::all();

        $tokensQuery = Token::with(['serviceCategory', 'priorityCategory', 'counter', 'servedBy'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);

        $tokens = (clone $tokensQuery)->orderBy('id', 'desc')->paginate(25);

        // Summary Aggregates
        $totalIssued = (clone $tokensQuery)->count();
        $totalServed = (clone $tokensQuery)->where('status', 'completed')->count();
        $totalNoShow = (clone $tokensQuery)->where('status', 'no_show')->count();
        $avgWaitSecs = (clone $tokensQuery)->where('status', 'completed')->avg('waiting_time_seconds') ?: 0;
        $avgServeSecs = (clone $tokensQuery)->where('status', 'completed')->avg('serving_time_seconds') ?: 0;

        // SBP Priority Compliance (Pensioner & Special Persons SLA)
        $priorityTotal = (clone $tokensQuery)->whereHas('priorityCategory', function ($q) {
            $q->whereIn('code', ['PWD', 'PENSIONER', 'SENIOR']);
        })->count();

        $priorityServedInUnder10Mins = (clone $tokensQuery)->whereHas('priorityCategory', function ($q) {
            $q->whereIn('code', ['PWD', 'PENSIONER', 'SENIOR']);
        })->where('status', 'completed')->where('waiting_time_seconds', '<=', 600)->count();

        $sbpSlaPercentage = $priorityTotal > 0 ? round(($priorityServedInUnder10Mins / $priorityTotal) * 100, 1) : 100;

        // Teller Performance Breakdown
        $tellerStats = User::where('role', 'teller')
            ->withCount(['servedTokens' => function ($q) use ($startDate, $endDate) {
                $q->where('status', 'completed')
                    ->whereBetween('created_at', [
                        Carbon::parse($startDate)->startOfDay(),
                        Carbon::parse($endDate)->endOfDay()
                    ]);
            }])
            ->get();

        return view('admin.reports.index', compact(
            'tokens',
            'branches',
            'branchId',
            'startDate',
            'endDate',
            'totalIssued',
            'totalServed',
            'totalNoShow',
            'avgWaitSecs',
            'avgServeSecs',
            'priorityTotal',
            'sbpSlaPercentage',
            'tellerStats'
        ));
    }

    public function exportCsv(Request $request)
    {
        $branchId = $request->get('branch_id');
        $startDate = $request->get('start_date', Carbon::today()->subDays(7)->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());

        $tokens = Token::with(['branch', 'serviceCategory', 'priorityCategory', 'counter', 'servedBy'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->get();

        $filename = "BankFlow_QMS_Report_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($tokens) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Token Number',
                'Branch',
                'Service',
                'Priority Tier',
                'Customer Name',
                'CNIC',
                'Phone',
                'Counter',
                'Teller',
                'Status',
                'Wait Time (Mins)',
                'Serve Time (Mins)',
                'Transaction Amount',
                'Date & Time'
            ]);

            foreach ($tokens as $t) {
                fputcsv($file, [
                    $t->token_number,
                    $t->branch->name ?? '',
                    $t->serviceCategory->name ?? '',
                    $t->priorityCategory->name ?? '',
                    $t->customer_name,
                    $t->customer_cnic,
                    $t->customer_phone,
                    $t->counter->name ?? '',
                    $t->servedBy->name ?? '',
                    ucfirst($t->status),
                    round($t->waiting_time_seconds / 60, 1),
                    round($t->serving_time_seconds / 60, 1),
                    $t->transaction_amount ? number_format($t->transaction_amount, 2) : '',
                    $t->created_at->toDateTimeString(),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
