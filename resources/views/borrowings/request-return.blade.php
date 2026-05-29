@extends('layouts.app')
@section('title', 'Return Equipment')
@section('heading', 'Return Equipment')
@section('subheading', $borrowing->equipment->name)

@section('content')
<form method="POST" action="{{ route('borrowings.request.return.process', $borrowing) }}" class="max-w-2xl">
    @csrf

    <div class="bg-white rounded-lg border border-stone-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-slate-900 mb-4">Equipment Details</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <div class="text-xs uppercase tracking-wider text-slate-500">Equipment</div>
                <div class="font-medium text-slate-900">{{ $borrowing->equipment->name }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wider text-slate-500">Serial Number</div>
                <div class="font-mono text-slate-900">{{ $borrowing->equipment->serial_number }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wider text-slate-500">Borrowed On</div>
                <div class="text-slate-900">{{ $borrowing->borrow_date->format('M d, Y') }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wider text-slate-500">Expected Return</div>
                <div class="text-slate-900">{{ $borrowing->expected_return_date->format('M d, Y') }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-stone-200 p-6">
        <div class="space-y-6">
            {{-- Return Date --}}
            <div>
                <label for="actual_return_date" class="block text-sm font-medium text-slate-900 mb-2">Return Date *</label>
                <input type="date" id="actual_return_date" name="actual_return_date" required value="{{ old('actual_return_date', now()->format('Y-m-d')) }}"
                       class="w-full px-4 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 outline-none @error('actual_return_date') border-red-500 @enderror">
                @error('actual_return_date')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Return Condition --}}
            <div>
                <label for="return_condition" class="block text-sm font-medium text-slate-900 mb-2">Condition Upon Return *</label>
                <select id="return_condition" name="return_condition" required 
                        class="w-full px-4 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 outline-none @error('return_condition') border-red-500 @enderror">
                    <option value="">Select condition</option>
                    <option value="new" @selected(old('return_condition')=='new')>New</option>
                    <option value="good" @selected(old('return_condition')=='good')>Good</option>
                    <option value="fair" @selected(old('return_condition')=='fair')>Fair</option>
                    <option value="damaged" @selected(old('return_condition')=='damaged')>Damaged</option>
                </select>
                @error('return_condition')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Damage Remarks --}}
            <div>
                <label for="damage_remarks" class="block text-sm font-medium text-slate-900 mb-2">Damage Remarks (if applicable)</label>
                <textarea id="damage_remarks" name="damage_remarks" rows="3"
                          class="w-full px-4 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 outline-none @error('damage_remarks') border-red-500 @enderror"
                          placeholder="Describe any damage or issues with the equipment.">{{ old('damage_remarks') }}</textarea>
                @error('damage_remarks')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Follow-up Actions --}}
            <div>
                <label for="follow_up_actions" class="block text-sm font-medium text-slate-900 mb-2">Follow-up Actions (if needed)</label>
                <textarea id="follow_up_actions" name="follow_up_actions" rows="3"
                          class="w-full px-4 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 outline-none @error('follow_up_actions') border-red-500 @enderror"
                          placeholder="Any actions needed (repair, replacement, etc.)">{{ old('follow_up_actions') }}</textarea>
                @error('follow_up_actions')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 mt-8 pt-6 border-t border-stone-200">
            <button type="submit" class="px-6 py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">
                Return Equipment
            </button>
            <a href="{{ route('borrowings.index') }}" class="px-6 py-2 rounded-md border border-stone-300 text-slate-700 text-sm hover:bg-stone-50">
                Cancel
            </a>
        </div>
    </div>
</form>
@endsection
