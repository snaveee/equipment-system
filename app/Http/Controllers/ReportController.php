<?php

namespace App\Http\Controllers;

use App\Models\BorrowingTransaction;
use App\Models\Equipment;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(): View
    {
        // Most borrowed equipment
        $mostBorrowed = Equipment::query()
            ->withCount('transactions as borrow_count')
            ->orderByDesc('borrow_count')
            ->take(10)
            ->get();

        // Borrowing frequency by department
        $byDepartment = BorrowingTransaction::query()
            ->select('users.department', DB::raw('COUNT(borrowing_transactions.id) as total'))
            ->join('users', 'borrowing_transactions.user_id', '=', 'users.id')
            ->groupBy('users.department')
            ->orderByDesc('total')
            ->get();

        // Equipment condition summary
        $conditionSummary = Equipment::query()
            ->select('condition', DB::raw('COUNT(*) as total'))
            ->groupBy('condition')
            ->pluck('total', 'condition');

        $statusSummary = Equipment::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Monthly borrowing statistics (last 12 months)
        $monthly = BorrowingTransaction::query()
            ->select(
                DB::raw("DATE_FORMAT(borrow_date, '%Y-%m') as month"),
                DB::raw('COUNT(*) as total')
            )
            ->where('borrow_date', '>=', now()->subMonths(12)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('reports.index', compact(
            'mostBorrowed',
            'byDepartment',
            'conditionSummary',
            'statusSummary',
            'monthly'
        ));
    }

    /**
     * Export all transactions as CSV.
     */
    public function exportTransactions(): StreamedResponse
    {
        $filename = 'transactions_'.now()->format('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'ID', 'Borrower', 'Department', 'Equipment', 'Serial #',
                'Purpose', 'Borrow Date', 'Expected Return', 'Actual Return',
                'Return Condition', 'Status', 'Damage Remarks',
            ]);

            BorrowingTransaction::with(['borrower', 'equipment'])
                ->orderBy('id')
                ->chunk(200, function ($rows) use ($handle) {
                    foreach ($rows as $t) {
                        fputcsv($handle, [
                            $t->id,
                            $t->borrower?->name,
                            $t->borrower?->department,
                            $t->equipment?->name,
                            $t->equipment?->serial_number,
                            $t->purpose,
                            optional($t->borrow_date)->toDateString(),
                            optional($t->expected_return_date)->toDateString(),
                            optional($t->actual_return_date)->toDateString(),
                            $t->return_condition,
                            $t->status,
                            $t->damage_remarks,
                        ]);
                    }
                });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
