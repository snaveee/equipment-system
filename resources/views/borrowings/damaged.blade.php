@extends('layouts.app')
@section('title', 'Damaged Returns')
@section('heading', 'Damaged Returns')
@section('subheading', 'Equipment returned in damaged condition.')

@section('content')
<div class="bg-white rounded-2xl border border-stone-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-stone-50 text-xs font-mono uppercase tracking-wider text-slate-500 border-b border-stone-200">
            <tr>
                <th class="text-left px-4 py-3">Equipment</th>
                <th class="text-left px-4 py-3">Borrower</th>
                <th class="text-left px-4 py-3">Returned</th>
                <th class="text-left px-4 py-3">Damage Remarks</th>
                <th class="text-left px-4 py-3">Follow-up</th>
                <th class="text-right px-4 py-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
                <tr class="border-b border-stone-100 last:border-0 hover:bg-stone-50/60">
                    <td class="px-4 py-3">
                        <div class="font-semibold">{{ $t->equipment->name }}</div>
                        <div class="text-xs font-mono text-slate-500">{{ $t->equipment->serial_number }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div>{{ $t->borrower->name }}</div>
                        <div class="text-xs text-slate-500">{{ $t->borrower->department }}</div>
                    </td>
                    <td class="px-4 py-3 font-mono text-xs">{{ optional($t->actual_return_date)->format('M d, Y') }}</td>
                    <td class="px-4 py-3 text-xs text-slate-700 max-w-xs">{{ Str::limit($t->damage_remarks, 80) }}</td>
                    <td class="px-4 py-3 text-xs text-slate-700 max-w-xs">{{ Str::limit($t->follow_up_actions, 80) }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('borrowings.show', $t) }}" class="text-slate-700 hover:text-slate-900 text-xs font-medium hover:underline underline-offset-2">View →</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-slate-500">No damaged returns logged.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $transactions->links() }}</div>
@endsection
