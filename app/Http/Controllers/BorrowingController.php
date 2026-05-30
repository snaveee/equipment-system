<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowingReturnRequest;
use App\Http\Requests\BorrowingStoreRequest;
use App\Http\Requests\BorrowerBorrowRequest;
use App\Models\User;
use App\Models\BorrowingTransaction;
use App\Models\Equipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BorrowingController extends Controller
{
    public function index(Request $request): View
    {
        // Auto-mark overdue
        BorrowingTransaction::overdue()->where('status', 'active')
            ->update(['status' => 'overdue']);

        $query = BorrowingTransaction::with(['borrower', 'equipment']);

        // Borrowers only see their own transactions
        if (auth()->user()?->isBorrower()) {
            $query->where('user_id', auth()->id());
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $transactions = $query->latest()->paginate(10)->withQueryString();

        return view('borrowings.index', compact('transactions'));
    }

    public function create(): View
    {
        $this->authorizeAdmin();
        
        $borrowers = User::where('role', 'borrower')->orderBy('name')->get();
        $availableEquipment = Equipment::where('status', 'available')->orderBy('name')->get();
        return view('borrowings.create', compact('borrowers', 'availableEquipment'));
    }

    public function store(BorrowingStoreRequest $request): RedirectResponse
    {
        $this->authorizeAdmin();
        
        $data = $request->validated();

        return DB::transaction(function () use ($data, $request) {
            $equipment = Equipment::lockForUpdate()->findOrFail($data['equipment_id']);

            if ($equipment->status !== 'available') {
                return back()->withInput()
                    ->with('error', 'The selected equipment is not currently available.');
            }

            $data['status'] = 'active';
            $data['processed_by'] = $request->user()->id;
            // Map 'borrower_id' to 'user_id' if it comes from form
            if (isset($data['borrower_id'])) {
                $data['user_id'] = $data['borrower_id'];
                unset($data['borrower_id']);
            }
            BorrowingTransaction::create($data);

            $equipment->update(['status' => 'borrowed']);

            return redirect()->route('borrowings.index')
                ->with('success', 'Borrowing transaction recorded successfully.');
        });
    }

    public function show(BorrowingTransaction $borrowing): View
    {
        // Borrowers can only view their own transactions
        if (auth()->user()?->isBorrower() && $borrowing->user_id !== auth()->id()) {
            abort(403, 'You can only view your own transactions.');
        }
        
        $borrowing->load(['borrower', 'equipment', 'processor']);
        return view('borrowings.show', compact('borrowing'));
    }

    public function returnForm(BorrowingTransaction $borrowing): View
    {
        if ($borrowing->actual_return_date) {
            abort(404);
        }
        $borrowing->load(['borrower', 'equipment']);
        return view('borrowings.return', compact('borrowing'));
    }

    public function processReturn(BorrowingReturnRequest $request, BorrowingTransaction $borrowing): RedirectResponse
    {
        if ($borrowing->actual_return_date) {
            return back()->with('error', 'This transaction has already been returned.');
        }

        $data = $request->validated();

        DB::transaction(function () use ($data, $borrowing) {
            $borrowing->update([
                'actual_return_date' => $data['actual_return_date'],
                'return_condition' => $data['return_condition'],
                'damage_remarks' => $data['damage_remarks'] ?? null,
                'follow_up_actions' => $data['follow_up_actions'] ?? null,
                'status' => 'returned',
            ]);

            $newStatus = $data['return_condition'] === 'damaged' ? 'under_repair' : 'available';

            $borrowing->equipment->update([
                'condition' => $data['return_condition'],
                'status' => $newStatus,
            ]);
        });

        return redirect()->route('borrowings.index')
            ->with('success', 'Return processed successfully.');
    }

    public function overdue(): View
    {
        $this->authorizeAdmin();
        
        // Auto-flag overdue first
        BorrowingTransaction::overdue()->where('status', 'active')
            ->update(['status' => 'overdue']);

        $transactions = BorrowingTransaction::with(['borrower', 'equipment'])
            ->overdue()
            ->orderBy('expected_return_date')
            ->paginate(10);

        return view('borrowings.overdue', compact('transactions'));
    }

    public function damaged(): View
    {
        $this->authorizeAdmin();
        
        $transactions = BorrowingTransaction::with(['borrower', 'equipment'])
            ->where('return_condition', 'damaged')
            ->latest('actual_return_date')
            ->paginate(10);

        return view('borrowings.damaged', compact('transactions'));
    }

    /**
     * Borrower: Show form to request borrowing equipment
     */
    public function borrowerRequestCreate(): View
    {
        $availableEquipment = Equipment::where('status', 'available')->orderBy('name')->get();
        return view('borrowings.request-create', compact('availableEquipment'));
    }

    /**
     * Borrower: Store borrow request
     */
    public function borrowerRequestStore(BorrowerBorrowRequest $request): RedirectResponse
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data, $request) {
            $equipment = Equipment::lockForUpdate()->findOrFail($data['equipment_id']);

            if ($equipment->status !== 'available') {
                return back()->withInput()
                    ->with('error', 'The selected equipment is not currently available.');
            }

            $data['status'] = 'active';
            $data['user_id'] = $request->user()->id;  // Borrower can only borrow for themselves
            $data['processed_by'] = null;  // Not processed by admin yet, just self-initiated

            BorrowingTransaction::create($data);
            $equipment->update(['status' => 'borrowed']);

            return redirect()->route('borrowings.index')
                ->with('success', 'Equipment borrowed successfully.');
        });
    }

    /**
     * Borrower: Show return form for their own transaction
     */
    public function borrowerReturnForm(BorrowingTransaction $borrowing): View
    {
        if ($borrowing->user_id !== auth()->id()) {
            abort(403, 'You can only return your own borrowed items.');
        }

        if ($borrowing->actual_return_date) {
            abort(404);
        }

        $borrowing->load(['equipment']);
        return view('borrowings.request-return', compact('borrowing'));
    }

    /**
     * Borrower: Process their own return
     */
    public function borrowerProcessReturn(BorrowingReturnRequest $request, BorrowingTransaction $borrowing): RedirectResponse
    {
        if ($borrowing->user_id !== auth()->id()) {
            abort(403, 'You can only return your own borrowed items.');
        }

        if ($borrowing->actual_return_date) {
            return back()->with('error', 'This item has already been returned.');
        }

        $data = $request->validated();

        DB::transaction(function () use ($data, $borrowing) {
            $borrowing->update([
                'actual_return_date' => $data['actual_return_date'],
                'return_condition' => $data['return_condition'],
                'damage_remarks' => $data['damage_remarks'] ?? null,
                'follow_up_actions' => $data['follow_up_actions'] ?? null,
                'status' => 'returned',
            ]);

            $newStatus = $data['return_condition'] === 'damaged' ? 'under_repair' : 'available';

            $borrowing->equipment->update([
                'condition' => $data['return_condition'],
                'status' => $newStatus,
            ]);
        });

        return redirect()->route('borrowings.index')
            ->with('success', 'Item returned successfully.');
    }
}

