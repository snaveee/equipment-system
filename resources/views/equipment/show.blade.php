@extends('layouts.app')
@section('title', $equipment->name)
@section('heading', $equipment->name)
@section('subheading', 'Equipment detail and borrowing history')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 bg-white rounded-lg border border-stone-200 p-6">
        @if($equipment->photo_path)
            <img src="{{ asset('storage/'.$equipment->photo_path) }}" class="w-full aspect-square rounded-md object-cover border border-stone-200 mb-4">
        @else
            <div class="w-full aspect-square rounded-md bg-stone-100 border border-stone-200 flex items-center justify-center text-slate-400 text-xs mb-4">No photo</div>
        @endif

        <div class="space-y-3 text-sm">
            <div>
                <div class="text-xs uppercase tracking-wider text-slate-500">Category</div>
                <div class="font-medium text-slate-900">{{ $equipment->category }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wider text-slate-500">Serial / Asset #</div>
                <div class="font-mono text-slate-900">{{ $equipment->serial_number }}</div>
            </div>
            <div class="flex gap-2 pt-2">
                @include('partials.status-badge', ['status' => $equipment->condition])
                @include('partials.status-badge', ['status' => $equipment->status])
            </div>
            @if($equipment->description)
                <div class="pt-2 border-t border-stone-100">
                    <div class="text-xs uppercase tracking-wider text-slate-500 mb-1">Description</div>
                    <p class="text-slate-700">{{ $equipment->description }}</p>
                </div>
            @endif
        </div>

        {{-- Status quick-change available to ALL roles --}}
        <form method="POST" action="{{ route('equipment.status', $equipment) }}"
              class="mt-6 pt-4 border-t border-stone-200 space-y-2">
            @csrf @method('PATCH')
            <label class="text-xs uppercase tracking-wider text-slate-500">Change Status</label>
            <div class="flex gap-2">
                <select name="status" class="flex-1 px-3 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 outline-none">
                    @foreach(['available','borrowed','under_repair'] as $s)
                        <option value="{{ $s }}" @selected($equipment->status===$s)>{{ str_replace('_',' ',ucfirst($s)) }}</option>
                    @endforeach
                </select>
                <button class="px-4 py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">Update</button>
            </div>
            <p class="text-xs text-slate-500">Moving off "borrowed" auto-closes any open transaction for this item.</p>
        </form>

        @if(auth()->user()->isAdmin())
            <div class="mt-4 pt-4 border-t border-stone-200 flex gap-2">
                <a href="{{ route('equipment.edit', $equipment) }}" class="flex-1 text-center px-3 py-2 rounded-md border border-slate-300 text-slate-700 text-sm hover:bg-slate-100">Edit</a>
                <form method="POST" action="{{ route('equipment.destroy', $equipment) }}" class="flex-1" onsubmit="return confirm('Delete this equipment?')">
                    @csrf @method('DELETE')
                    <button class="w-full px-3 py-2 rounded-md border border-red-300 text-red-700 text-sm hover:bg-red-50">Delete</button>
                </form>
            </div>
        @endif
    </div>

    <div class="lg:col-span-2 bg-white rounded-lg border border-stone-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Borrowing History</h2>
        @forelse($equipment->transactions as $t)
            <div class="py-3 border-b border-stone-100 last:border-0 flex items-center justify-between">
                <div>
                    <div class="font-medium text-slate-900">{{ $t->borrower->full_name ?? '—' }}</div>
                    <div class="text-xs text-slate-500">{{ $t->purpose }}</div>
                    <div class="text-xs font-mono text-slate-500 mt-1">
                        {{ $t->borrow_date->format('M d, Y') }} → {{ optional($t->actual_return_date)->format('M d, Y') ?? $t->expected_return_date->format('M d, Y') }}
                    </div>
                </div>
                @include('partials.status-badge', ['status' => $t->status])
            </div>
        @empty
            <p class="text-sm text-slate-500">No borrowing history yet.</p>
        @endforelse
    </div>
</div>
@endsection
