@extends('layouts.app')

@section('title', 'Equipment')
@section('heading', 'Equipment Inventory')
@section('subheading', 'Catalog of all assets with their availability and condition.')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <form method="GET" action="{{ route('equipment.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, serial..."
               class="px-4 py-2 rounded-full border border-stone-300 bg-white text-sm focus:border-slate-900 outline-none w-64">
        <select name="category" class="px-4 py-2 rounded-full border border-stone-300 bg-white text-sm">
            <option value="">All categories</option>
            @foreach($categories as $c)
                <option value="{{ $c }}" @selected(request('category')===$c)>{{ $c }}</option>
            @endforeach
        </select>
        <select name="status" class="px-4 py-2 rounded-full border border-stone-300 bg-white text-sm">
            <option value="">All status</option>
            @foreach(['Available','Borrowed','Under_Repair'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ str_replace('_',' ',$s) }}</option>
            @endforeach
        </select>
        <select name="condition" class="px-4 py-2 rounded-full border border-stone-300 bg-white text-sm">
            <option value="">All conditions</option>
            @foreach(['New','Good','Fair','Damaged'] as $c)
                <option value="{{ $c }}" @selected(request('condition')===$c)>{{ $c }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2 rounded-full bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">Filter</button>
        @if(request()->hasAny(['search','category','status','condition']))
            <a href="{{ route('equipment.index') }}" class="text-sm text-slate-500 hover:text-slate-900">Clear</a>
        @endif
    </form>

    @if(auth()->user()->isAdmin())
        <a href="{{ route('equipment.create') }}"
           class="px-4 py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">
            + Add Equipment
        </a>
    @endif
</div>

<div class="bg-white rounded-2xl border border-stone-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-stone-50 text-xs font-mono uppercase tracking-wider text-slate-500 border-b border-stone-200">
                <tr>
                    <th class="text-left px-4 py-3">Photo</th>
                    <th class="text-left px-4 py-3">Name</th>
                    <th class="text-left px-4 py-3">Category</th>
                    <th class="text-left px-4 py-3">Serial #</th>
                    <th class="text-left px-4 py-3">Condition</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-right px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipment as $item)
                    <tr class="border-b border-stone-100 last:border-0 hover:bg-stone-50/60">
                        <td class="px-4 py-3">
                            @if($item->photo_path)
                                <img src="{{ asset('storage/'.$item->photo_path) }}" alt="" class="w-12 h-12 rounded-lg object-cover border border-stone-200">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-stone-100 border border-stone-200 flex items-center justify-center text-slate-400 text-xs font-mono">N/A</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900">{{ $item->name }}</div>
                            <div class="text-xs text-slate-500 truncate max-w-xs">{{ $item->description }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $item->category }}</td>
                        <td class="px-4 py-3 font-mono text-xs">{{ $item->serial_number }}</td>
                        <td class="px-4 py-3">@include('partials.status-badge', ['status' => $item->condition])</td>
                        <td class="px-4 py-3">@include('partials.status-badge', ['status' => $item->status])</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('equipment.show', $item) }}" class="text-slate-700 hover:text-slate-900 font-medium text-xs underline-offset-2 hover:underline">View</a>
                            @if(auth()->user()->isAdmin())
                                <span class="text-stone-300 mx-1">|</span>
                                <a href="{{ route('equipment.edit', $item) }}" class="text-slate-700 hover:text-slate-900 font-medium text-xs underline-offset-2 hover:underline">Edit</a>
                                <span class="text-stone-300 mx-1">|</span>
                                <form method="POST" action="{{ route('equipment.destroy', $item) }}" class="inline" onsubmit="return confirm('Delete this equipment?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-700 hover:text-red-900 font-medium text-xs underline-offset-2 hover:underline">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">No equipment found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $equipment->links() }}</div>
@endsection
