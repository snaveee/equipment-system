@extends('layouts.app')
@section('title', 'Process Return')
@section('heading', 'Process Return')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 bg-slate-900 text-slate-200 rounded-lg p-6">
        <div class="text-xs uppercase tracking-wider text-slate-400 mb-3">Transaction #{{ $borrowing->id }}</div>
        <div class="text-lg font-semibold text-white mb-1">{{ $borrowing->equipment->name }}</div>
        <div class="font-mono text-xs text-slate-400 mb-4">{{ $borrowing->equipment->serial_number }}</div>
        <div class="space-y-2 text-sm">
            <div>Borrower: <span class="text-white">{{ $borrowing->borrower->name }}</span></div>
            <div>Borrowed: <span class="font-mono">{{ $borrowing->borrow_date->format('M d, Y') }}</span></div>
            <div>Expected: <span class="font-mono">{{ $borrowing->expected_return_date->format('M d, Y') }}</span></div>
            @if($borrowing->days_overdue > 0)
                <div class="pt-2 mt-2 border-t border-slate-800">
                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700 border border-red-200">{{ $borrowing->days_overdue }} days overdue</span>
                </div>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('borrowings.return.process', $borrowing) }}"
          class="lg:col-span-2 bg-white border border-stone-200 rounded-lg p-6 space-y-5">
        @csrf

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-600 mb-1.5">Actual Return Date</label>
                <input type="date" name="actual_return_date" value="{{ old('actual_return_date', now()->toDateString()) }}" required
                       class="w-full px-3 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none">
            </div>
            <div>
                <label class="block text-xs uppercase tracking-wider text-slate-600 mb-1.5">Return Condition</label>
                <select name="return_condition" required class="w-full px-3 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none">
                    @foreach(['new','good','fair','damaged'] as $c)
                        <option value="{{ $c }}" @selected(old('return_condition')===$c)>{{ ucfirst($c) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs uppercase tracking-wider text-slate-600 mb-1.5">Damage Remarks (if any)</label>
            <textarea name="damage_remarks" rows="3" placeholder="Describe damage observed on the equipment..."
                      class="w-full px-3 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none">{{ old('damage_remarks') }}</textarea>
        </div>

        <div>
            <label class="block text-xs uppercase tracking-wider text-slate-600 mb-1.5">Follow-up Actions</label>
            <textarea name="follow_up_actions" rows="2" placeholder="Repair, replace, charge borrower..."
                      class="w-full px-3 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none">{{ old('follow_up_actions') }}</textarea>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-stone-200">
            <button class="px-4 py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">
                Confirm Return
            </button>
            <a href="{{ route('borrowings.show', $borrowing) }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
        </div>
    </form>
</div>
@endsection
