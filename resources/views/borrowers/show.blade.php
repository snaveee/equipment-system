@extends('layouts.app')
@section('title', $borrower->name)
@section('heading', $borrower->name)
@section('subheading', $borrower->department.' · '.$borrower->position)

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg border border-stone-200 p-6 space-y-3">
        <div>
            <div class="text-xs uppercase tracking-wider text-slate-500">Email</div>
            <div class="font-mono text-sm text-slate-900">{{ $borrower->email }}</div>
        </div>
        <div>
            <div class="text-xs uppercase tracking-wider text-slate-500">Contact</div>
            <div class="font-mono text-sm text-slate-900">{{ $borrower->contact_number }}</div>
        </div>
        <div class="pt-3 border-t border-stone-100">
            <div class="text-xs uppercase tracking-wider text-slate-500">Total transactions</div>
            <div class="text-3xl font-semibold text-slate-900">{{ $borrower->transactions()->count() }}</div>
        </div>
        @if(auth()->user()->isAdmin())
            <div class="pt-3 border-t border-stone-200">
                <a href="{{ route('borrowers.edit', $borrower) }}" class="block text-center px-3 py-2 rounded-md border border-slate-300 text-slate-700 text-sm hover:bg-slate-100">Edit Borrower</a>
            </div>
        @endif
    </div>

    <div class="lg:col-span-2 bg-white rounded-lg border border-stone-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Borrowing History</h2>
        <table class="w-full text-sm">
            <thead class="text-xs uppercase tracking-wider text-slate-500 border-b border-stone-200">
                <tr>
                    <th class="text-left py-2 font-medium">Equipment</th>
                    <th class="text-left py-2 font-medium">Purpose</th>
                    <th class="text-left py-2 font-medium">Borrowed</th>
                    <th class="text-left py-2 font-medium">Returned</th>
                    <th class="text-left py-2 font-medium">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                    <tr class="border-b border-stone-100 last:border-0">
                        <td class="py-2.5 text-slate-800">{{ $t->equipment->name ?? '—' }}</td>
                        <td class="py-2.5 text-xs text-slate-600">{{ Str::limit($t->purpose, 40) }}</td>
                        <td class="py-2.5 font-mono text-xs text-slate-600">{{ $t->borrow_date->format('M d, Y') }}</td>
                        <td class="py-2.5 font-mono text-xs text-slate-600">{{ optional($t->actual_return_date)->format('M d, Y') ?? '—' }}</td>
                        <td class="py-2.5">@include('partials.status-badge', ['status' => $t->status])</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-slate-500">No history yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $transactions->links() }}</div>
    </div>
</div>
@endsection
