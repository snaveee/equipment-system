<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BorrowingTransaction;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // Auto-flag overdue transactions
        BorrowingTransaction::overdue()
            ->where('status', 'active')
            ->update(['status' => 'overdue']);

        $stats = [
            'total_equipment' => Equipment::count(),
            'available' => Equipment::where('status', 'available')->count(),
            'borrowed' => Equipment::where('status', 'borrowed')->count(),
            'under_repair' => Equipment::where('status', 'under_repair')->count(),
            'damaged' => Equipment::where('condition', 'damaged')->count(),
            'total_borrowers' => User::where('role', 'borrower')->count(),
            'active_transactions' => BorrowingTransaction::whereIn('status', ['active', 'overdue'])->count(),
            'overdue_count' => BorrowingTransaction::where('status', 'overdue')->count(),
        ];

        $overdue = BorrowingTransaction::with(['borrower', 'equipment'])
            ->where('status', 'overdue')
            ->orderBy('expected_return_date')
            ->take(5)
            ->get();

        $damaged = Equipment::where('condition', 'damaged')
            ->orderByDesc('updated_at')
            ->take(5)
            ->get();

        $recent = BorrowingTransaction::with(['borrower', 'equipment'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'overdue', 'damaged', 'recent'));
    }
}
