@extends('layouts.app')
@section('title', 'Borrowers')
@section('heading', 'Borrowers')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <form method="GET" class="flex items-center gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, department..."
               class="px-4 py-2 rounded-full border border-stone-300 bg-white text-sm w-80 focus:border-slate-900 outline-none">
        <button class="px-4 py-2 rounded-full bg-slate-900 text-white text-sm font-medium">Search</button>
        @if(request('search'))
            <a href="{{ route('borrowers.index') }}" class="text-sm text-slate-500">Clear</a>
        @endif
    </form>
    @if(auth()->user()->isAdmin())
        <a href="{{ route('borrowers.create') }}" class="px-4 py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">+ Add Borrower</a>
    @endif
</div>

<div class="bg-white rounded-2xl border border-stone-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 text-xs font-mono uppercase tracking-wider text-slate-500 border-b border-stone-200">
            <tr>
                <th class="text-left px-4 py-3">Full Name</th>
                <th class="text-left px-4 py-3">Department</th>
                <th class="text-left px-4 py-3">Position</th>
                <th class="text-left px-4 py-3">Email</th>
                <th class="text-left px-4 py-3">Contact</th>
                <th class="text-center px-4 py-3">Loans</th>
                <th class="text-right px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($borrowers as $b)
                <tr class="border-b border-stone-100 last:border-0 hover:bg-stone-50/60">
                    <td class="px-4 py-3 font-medium text-slate-900">
                        {{ $b->name }}
                        @if($b->transactions_count >= 3)
                            <span class="ml-2 px-1.5 py-0.5 rounded bg-stone-100 text-slate-700 border border-stone-200 text-[10px] uppercase tracking-wider">frequent</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $b->department }}</td>
                    <td class="px-4 py-3">{{ $b->position }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $b->email }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $b->contact_number }}</td>
                    <td class="px-4 py-3 text-center font-semibold text-slate-900">{{ $b->transactions_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('borrowers.show', $b) }}" class="text-slate-700 hover:text-slate-900 text-xs font-medium hover:underline underline-offset-2">View</a>
                        @if(auth()->user()->isAdmin())
                            <span class="text-stone-300 mx-1">|</span>
                            <a href="{{ route('borrowers.edit', $b) }}" class="text-slate-700 hover:text-slate-900 text-xs font-medium hover:underline underline-offset-2">Edit</a>
                            <span class="text-stone-300 mx-1">|</span>
                            <form method="POST" action="{{ route('borrowers.destroy', $b) }}" class="inline" onsubmit="return confirm('Delete this borrower?')">
                                @csrf @method('DELETE')
                                <button class="text-red-700 hover:text-red-900 text-xs font-medium hover:underline underline-offset-2">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">No borrowers yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $borrowers->links() }}</div>
@endsection
