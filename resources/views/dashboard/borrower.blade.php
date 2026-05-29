@extends('layouts.app')

@section('title', 'My Dashboard')
@section('heading', 'My Dashboard')

@section('content')
@php
    $cards = [
        ['Active Loans', $stats['active_borrows'], 'neutral'],
        ['Overdue Items', $stats['overdue_count'], $stats['overdue_count'] > 0 ? 'alert' : 'neutral'],
        ['Total Borrowed', $stats['total_borrowed'], 'neutral'],
        ['Returned Items', $stats['returned_count'], 'neutral'],
    ];
@endphp

<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
    @foreach($cards as [$label, $value, $tone])
        @php
            $bg = $tone === 'alert' ? 'bg-red-50 border-red-200' : 'bg-white border-stone-200';
            $valueColor = $tone === 'alert' ? 'text-red-700' : 'text-slate-900';
            $labelColor = $tone === 'alert' ? 'text-red-700' : 'text-slate-500';
        @endphp
        <div class="rounded-lg border p-4 {{ $bg }}">
            <div class="text-xs uppercase tracking-wider {{ $labelColor }}">{{ $label }}</div>
            <div class="mt-2 text-3xl font-semibold {{ $valueColor }}">{{ $value }}</div>
        </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-2 gap-6">
    @if($stats['overdue_count'] > 0)
    <div class="bg-white rounded-lg border border-red-200 bg-red-50 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-red-900">Overdue Items</h2>
            <a href="{{ route('borrowings.index') }}" class="text-xs text-red-600 hover:text-red-900">View all →</a>
        </div>
        @forelse($overdue as $t)
            <div class="flex items-start justify-between py-3 border-b border-red-100 last:border-0">
                <div>
                    <div class="font-medium text-slate-900">{{ $t->equipment->name }}</div>
                    <div class="text-xs text-slate-600">Due: {{ $t->expected_return_date->format('M d, Y') }}</div>
                </div>
                <span class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">{{ $t->days_overdue }}d overdue</span>
            </div>
        @empty
            <p class="text-sm text-red-700 py-2">No overdue items.</p>
        @endforelse
    </div>
    @endif

    <div class="bg-white rounded-lg border border-stone-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-slate-900">Currently Borrowed</h2>
            <a href="{{ route('borrowings.index') }}" class="text-xs text-slate-500 hover:text-slate-900">Manage →</a>
        </div>
        @forelse($active as $t)
            <div class="flex items-start justify-between py-3 border-b border-stone-100 last:border-0">
                <div>
                    <div class="font-medium text-slate-900">{{ $t->equipment->name }}</div>
                    <div class="text-xs text-slate-500">
                        Borrowed: {{ $t->borrow_date->format('M d, Y') }} · Due: {{ $t->expected_return_date->format('M d, Y') }}
                    </div>
                </div>
                @include('partials.status-badge', ['status' => $t->status])
            </div>
        @empty
            <p class="text-sm text-slate-500 py-2">No active loans.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-lg border border-stone-200 p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-slate-900">Borrowing History</h2>
            <a href="{{ route('borrowings.index') }}" class="text-xs text-slate-500 hover:text-slate-900">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs uppercase tracking-wider text-slate-500 border-b border-stone-200">
                    <tr>
                        <th class="text-left py-2 font-medium">Equipment</th>
                        <th class="text-left py-2 font-medium">Borrowed</th>
                        <th class="text-left py-2 font-medium">Due</th>
                        <th class="text-left py-2 font-medium">Returned</th>
                        <th class="text-left py-2 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recent as $t)
                    <tr class="border-b border-stone-100 last:border-0">
                        <td class="py-2.5 text-slate-800">{{ $t->equipment->name }}</td>
                        <td class="py-2.5 font-mono text-xs text-slate-600">{{ $t->borrow_date->format('M d, Y') }}</td>
                        <td class="py-2.5 font-mono text-xs text-slate-600">{{ $t->expected_return_date->format('M d, Y') }}</td>
                        <td class="py-2.5 font-mono text-xs text-slate-600">
                            @if($t->actual_return_date)
                                {{ $t->actual_return_date->format('M d, Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="py-2.5">@include('partials.status-badge', ['status' => $t->status])</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-slate-500">No borrowing history.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
