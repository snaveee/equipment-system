<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowerRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class BorrowerController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin();

        $borrowers = User::where('role', 'borrower')
            ->withCount('transactions')
            ->orderByDesc('transactions_count')
            ->orderBy('name')
            ->paginate(10);

        return view('borrowers.index', compact('borrowers'));
    }

    public function create(): View
    {
        $this->authorizeAdmin();

        return view('borrowers.create');
    }

    public function store(BorrowerRequest $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validated();
        $data['role'] = 'borrower';
        // Set a temporary password; borrower will reset on first login
        $data['password'] = Hash::make('TempPassword123!');

        User::create($data);

        return redirect()->route('borrowers.index')
            ->with('success', 'Borrower registered successfully.');
    }

    public function show(User $borrower): View
    {
        if ($borrower->role !== 'borrower') {
            abort(404);
        }

        // Allow admin/staff to view any borrower, or borrowers to view only themselves
        $this->authorizeBorrowerOrSelf($borrower->id);

        $transactions = $borrower->transactions()
            ->with('equipment')
            ->latest()
            ->paginate(10);

        return view('borrowers.show', compact('borrower', 'transactions'));
    }

    public function edit(User $borrower): View
    {
        $this->authorizeAdmin();
        if ($borrower->role !== 'borrower') {
            abort(404);
        }

        return view('borrowers.edit', compact('borrower'));
    }

    public function update(BorrowerRequest $request, User $borrower): RedirectResponse
    {
        $this->authorizeAdmin();
        if ($borrower->role !== 'borrower') {
            abort(404);
        }

        $data = $request->validated();
        // Don't overwrite password if not provided
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $borrower->update($data);

        return redirect()->route('borrowers.index')
            ->with('success', 'Borrower updated successfully.');
    }

    public function destroy(User $borrower): RedirectResponse
    {
        $this->authorizeAdmin();
        if ($borrower->role !== 'borrower') {
            abort(404);
        }

        $borrower->delete();

        return redirect()->route('borrowers.index')
            ->with('success', 'Borrower deleted successfully.');
    }

    protected function authorizeAdmin(): void
    {
        if (! request()->user() || ! request()->user()->isAdmin()) {
            abort(403, 'Admin access required.');
        }
    }
}
