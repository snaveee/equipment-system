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

        $user = auth()->user();

        // BORROWER: Show personal borrowing information
        if ($user->isBorrower()) {
            $stats = [
                'active_borrows' => BorrowingTransaction::where('user_id', $user->id)
                    ->whereIn('status', ['active', 'overdue'])
                    ->count(),
                'overdue_count' => BorrowingTransaction::where('user_id', $user->id)
                    ->where('status', 'overdue')
                    ->count(),
                'total_borrowed' => BorrowingTransaction::where('user_id', $user->id)->count(),
                'returned_count' => BorrowingTransaction::where('user_id', $user->id)
                    ->where('status', 'returned')
                    ->count(),
            ];

            $overdue = BorrowingTransaction::with('equipment')
                ->where('user_id', $user->id)
                ->where('status', 'overdue')
                ->orderBy('expected_return_date')
                ->get();

            $active = BorrowingTransaction::with('equipment')
                ->where('user_id', $user->id)
                ->whereIn('status', ['active', 'overdue'])
                ->orderBy('expected_return_date')
                ->get();

            $recent = BorrowingTransaction::with('equipment')
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard.borrower', compact('stats', 'overdue', 'active', 'recent'));
        }

        // ADMIN/STAFF: Show system-wide information
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

        return view('dashboard.admin-staff', compact('stats', 'overdue', 'damaged', 'recent'));
    }
}
