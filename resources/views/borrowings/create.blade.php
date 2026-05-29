@extends('layouts.app')
@section('title', 'New Transaction')
@section('heading', 'New Borrowing Transaction')

@section('content')
@if($availableEquipment->isEmpty())
    <div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm">
        No equipment is currently available. Process a return first.
    </div>
@endif

<form method="POST" action="{{ route('borrowings.store') }}" class="space-y-5 bg-white border border-stone-200 rounded-2xl p-6 max-w-3xl">
    @csrf

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Borrower</label>
            <select name="user_id" required class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white outline-none focus:border-slate-900">
                <option value="">— Select borrower —</option>
                @foreach($borrowers as $b)
                    <option value="{{ $b->id }}" @selected(old('user_id')==$b->id)>{{ $b->name }} ({{ $b->department }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Equipment (available only)</label>
            <select name="equipment_id" required class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white outline-none focus:border-slate-900">
                <option value="">— Select equipment —</option>
                @foreach($availableEquipment as $e)
                    <option value="{{ $e->id }}" @selected(old('equipment_id')==$e->id)>{{ $e->name }} ({{ $e->serial_number }})</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Purpose</label>
        <textarea name="purpose" rows="2" required
                  class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white focus:border-slate-900 outline-none">{{ old('purpose') }}</textarea>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Borrow Date</label>
            <input type="date" name="borrow_date" value="{{ old('borrow_date', now()->toDateString()) }}" required
                   class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white outline-none focus:border-slate-900">
        </div>
        <div>
            <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Expected Return Date</label>
            <input type="date" name="expected_return_date" value="{{ old('expected_return_date', now()->addDays(7)->toDateString()) }}" required
                   class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white outline-none focus:border-slate-900">
        </div>
    </div>

    <div class="flex items-center gap-3 pt-4 border-t border-stone-200">
        <button class="px-4 py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700" {{ $availableEquipment->isEmpty() ? 'disabled' : '' }}>
            Record Transaction
        </button>
        <a href="{{ route('borrowings.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
    </div>
</form>
@endsection
