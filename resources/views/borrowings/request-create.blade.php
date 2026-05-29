@extends('layouts.app')
@section('title', 'Request Equipment')
@section('heading', 'Request Equipment')

@section('content')
<form method="POST" action="{{ route('borrowings.request.store') }}" class="max-w-2xl">
    @csrf

    <div class="bg-white rounded-lg border border-stone-200 p-6">
        <div class="space-y-6">
            {{-- Equipment Selection --}}
            <div>
                <label for="equipment_id" class="block text-sm font-medium text-slate-900 mb-2">Equipment *</label>
                <select id="equipment_id" name="equipment_id" required 
                        class="w-full px-4 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 outline-none @error('equipment_id') border-red-500 @enderror">
                    <option value="">Select equipment to borrow</option>
                    @forelse($availableEquipment as $item)
                        <option value="{{ $item->id }}" @selected(old('equipment_id')==$item->id)>
                            {{ $item->name }} ({{ $item->serial_number }})
                        </option>
                    @empty
                        <option disabled>No equipment available</option>
                    @endforelse
                </select>
                @error('equipment_id')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Purpose --}}
            <div>
                <label for="purpose" class="block text-sm font-medium text-slate-900 mb-2">Purpose *</label>
                <textarea id="purpose" name="purpose" required rows="3"
                          class="w-full px-4 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 outline-none @error('purpose') border-red-500 @enderror"
                          placeholder="What will you use this equipment for?">{{ old('purpose') }}</textarea>
                @error('purpose')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Borrow Date --}}
            <div>
                <label for="borrow_date" class="block text-sm font-medium text-slate-900 mb-2">Borrow Date *</label>
                <input type="date" id="borrow_date" name="borrow_date" required value="{{ old('borrow_date', now()->format('Y-m-d')) }}"
                       class="w-full px-4 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 outline-none @error('borrow_date') border-red-500 @enderror">
                @error('borrow_date')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Expected Return Date --}}
            <div>
                <label for="expected_return_date" class="block text-sm font-medium text-slate-900 mb-2">Expected Return Date *</label>
                <input type="date" id="expected_return_date" name="expected_return_date" required value="{{ old('expected_return_date') }}"
                       class="w-full px-4 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 outline-none @error('expected_return_date') border-red-500 @enderror">
                @error('expected_return_date')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex gap-3 mt-8 pt-6 border-t border-stone-200">
            <button type="submit" class="px-6 py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">
                Request Equipment
            </button>
            <a href="{{ route('borrowings.index') }}" class="px-6 py-2 rounded-md border border-stone-300 text-slate-700 text-sm hover:bg-stone-50">
                Cancel
            </a>
        </div>
    </div>
</form>
@endsection
