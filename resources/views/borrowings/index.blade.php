@extends('layouts.app')
@section('title', 'Transactions')
@section('heading', 'Borrowing Transactions')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <form method="GET" class="flex flex-wrap items-center gap-2">
        <select name="status" class="px-4 py-2 rounded-full border border-stone-300 bg-white text-sm">
            <option value="">All statuses</option>
            @foreach(['active','overdue','returned'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2 rounded-full bg-slate-900 text-white text-sm font-medium">Filter</button>
        @if(request('status'))
            <a href="{{ route('borrowings.index') }}" class="text-sm text-slate-500">Clear</a>
        @endif
    </form>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('borrowings.create') }}" class="px-4 py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">+ New Transaction</a>
    @elseif(auth()->user()->isBorrower())
        <a href="{{ route('borrowings.request.create') }}" class="px-4 py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">+ Request Equipment</a>
    @endif
</div>

<div class="bg-white rounded-2xl border border-stone-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 text-xs font-mono uppercase tracking-wider text-slate-500 border-b border-stone-200">
            <tr>
                <th class="text-left px-4 py-3">#</th>
                @if(auth()->user()->isAdmin())
                    <th class="text-left px-4 py-3">Borrower</th>
                @endif
                <th class="text-left px-4 py-3">Equipment</th>
                <th class="text-left px-4 py-3">Borrowed</th>
                <th class="text-left px-4 py-3">Due</th>
                <th class="text-left px-4 py-3">Returned</th>
                <th class="text-left px-4 py-3">Status</th>
                <th class="text-right px-4 py-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
                <tr class="border-b border-stone-100 last:border-0 hover:bg-stone-50/60">
                    <td class="px-4 py-3 font-mono text-xs">#{{ $t->id }}</td>
                    @if(auth()->user()->isAdmin())
                        <td class="px-4 py-3">
                            <div class="font-semibold">{{ $t->borrower->name }}</div>
                            <div class="text-xs text-slate-500">{{ $t->borrower->department }}</div>
                        </td>
                    @endif
                    <td class="px-4 py-3">
                        <div>{{ $t->equipment->name }}</div>
                        <div class="text-xs font-mono text-slate-500">{{ $t->equipment->serial_number }}</div>
                    </td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $t->borrow_date->format('M d, Y') }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $t->expected_return_date->format('M d, Y') }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ optional($t->actual_return_date)->format('M d, Y') ?? '—' }}</td>
                    <td class="px-4 py-3">@include('partials.status-badge', ['status' => $t->status])</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('borrowings.show', $t) }}" class="text-slate-700 hover:text-slate-900 text-xs font-medium hover:underline underline-offset-2">View</a>
                        @if(! $t->actual_return_date && auth()->user()->isAdmin())
                            <span class="text-stone-300 mx-1">|</span>
                            <a href="{{ route('borrowings.return.form', $t) }}" class="text-slate-900 text-xs font-medium hover:underline underline-offset-2">Return</a>
                        @elseif(! $t->actual_return_date && auth()->user()->isBorrower() && $t->user_id === auth()->id())
                            <span class="text-stone-300 mx-1">|</span>
                            <a href="{{ route('borrowings.request.return', $t) }}" class="text-slate-900 text-xs font-medium hover:underline underline-offset-2">Return</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ auth()->user()->isAdmin() ? '8' : '7' }}" class="px-4 py-12 text-center text-slate-500">No transactions yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $transactions->links() }}</div>
@endsection
