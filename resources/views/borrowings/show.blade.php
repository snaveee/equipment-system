@extends('layouts.app')
@section('title', 'Transaction #'.$borrowing->id)
@section('heading', 'Transaction #'.$borrowing->id)
@section('subheading', 'Borrowing record details')

@section('content')
<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg border border-stone-200 p-6 space-y-4">
        <h2 class="text-lg font-semibold text-slate-900">Borrower</h2>
        <div>
            <div class="font-medium text-slate-900">{{ $borrowing->borrower->name }}</div>
            <div class="text-sm text-slate-600">{{ $borrowing->borrower->department }} · {{ $borrowing->borrower->position }}</div>
            <div class="text-xs font-mono text-slate-500 mt-1">{{ $borrowing->borrower->email }} · {{ $borrowing->borrower->contact_number }}</div>
        </div>
        <div class="pt-3 border-t border-stone-100">
            <h2 class="text-lg font-semibold text-slate-900 mb-2">Equipment</h2>
            <div class="font-medium text-slate-900">{{ $borrowing->equipment->name }}</div>
            <div class="text-xs font-mono text-slate-500">{{ $borrowing->equipment->serial_number }}</div>
        </div>
        <div class="pt-3 border-t border-stone-100">
            <h2 class="text-lg font-semibold text-slate-900 mb-2">Purpose</h2>
            <p class="text-sm text-slate-700">{{ $borrowing->purpose }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-stone-200 p-6 space-y-4">
        <h2 class="text-lg font-semibold text-slate-900">Timeline</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-xs uppercase tracking-wider text-slate-500">Borrowed</div>
                <div class="font-mono text-slate-900">{{ $borrowing->borrow_date->format('M d, Y') }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wider text-slate-500">Expected return</div>
                <div class="font-mono text-slate-900">{{ $borrowing->expected_return_date->format('M d, Y') }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wider text-slate-500">Actual return</div>
                <div class="font-mono text-slate-900">{{ optional($borrowing->actual_return_date)->format('M d, Y') ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wider text-slate-500">Status</div>
                <div>@include('partials.status-badge', ['status' => $borrowing->status])</div>
            </div>
        </div>

        @if($borrowing->return_condition)
            <div class="pt-3 border-t border-stone-100 space-y-2">
                <div class="text-xs uppercase tracking-wider text-slate-500">Return condition</div>
                @include('partials.status-badge', ['status' => $borrowing->return_condition])
                @if($borrowing->damage_remarks)
                    <div class="mt-2">
                        <div class="text-xs uppercase tracking-wider text-slate-500">Damage remarks</div>
                        <p class="text-sm text-slate-700">{{ $borrowing->damage_remarks }}</p>
                    </div>
                @endif
                @if($borrowing->follow_up_actions)
                    <div>
                        <div class="text-xs uppercase tracking-wider text-slate-500">Follow-up actions</div>
                        <p class="text-sm text-slate-700">{{ $borrowing->follow_up_actions }}</p>
                    </div>
                @endif
            </div>
        @endif

        @if(! $borrowing->actual_return_date && auth()->user()->isAdmin())
            <a href="{{ route('borrowings.return.form', $borrowing) }}"
               class="block w-full text-center px-4 py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">
                Process Return
            </a>
        @endif
    </div>
</div>
@endsection
