@extends('layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Snapshot of inventory, transactions, and alerts.')

@section('content')
@php
    // Minimal palette: most cards are neutral; only "overdue" gets red treatment.
    $cards = [
        ['Total Equipment',    $stats['total_equipment'],      'neutral'],
        ['Available',          $stats['available'],            'neutral'],
        ['Currently Borrowed', $stats['borrowed'],             'neutral'],
        ['Under Repair',       $stats['under_repair'],         'neutral'],
        ['Damaged Items',      $stats['damaged'],              'neutral'],
        ['Borrowers',          $stats['total_borrowers'],      'neutral'],
        ['Active Loans',       $stats['active_transactions'],  'neutral'],
        ['Overdue Loans',      $stats['overdue_count'],        $stats['overdue_count'] > 0 ? 'alert' : 'neutral'],
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
    <div class="bg-white rounded-lg border border-stone-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-slate-900">Overdue Alerts</h2>
            <a href="{{ route('borrowings.overdue') }}" class="text-xs text-slate-500 hover:text-slate-900">View all →</a>
        </div>
        @forelse($overdue as $t)
            <div class="flex items-start justify-between py-3 border-b border-stone-100 last:border-0">
                <div>
                    <div class="font-medium text-slate-900">{{ $t->equipment->name }}</div>
                    <div class="text-xs text-slate-500">{{ $t->borrower->full_name }} · {{ $t->borrower->department }}</div>
                </div>
                <span class="px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700 border border-red-200">{{ $t->days_overdue }}d overdue</span>
            </div>
        @empty
            <p class="text-sm text-slate-500 py-2">No overdue items.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-lg border border-stone-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-slate-900">Damaged Equipment</h2>
            <a href="{{ route('borrowings.damaged') }}" class="text-xs text-slate-500 hover:text-slate-900">View all →</a>
        </div>
        @forelse($damaged as $e)
            <div class="flex items-start justify-between py-3 border-b border-stone-100 last:border-0">
                <div>
                    <div class="font-medium text-slate-900">{{ $e->name }}</div>
                    <div class="text-xs font-mono text-slate-500">{{ $e->serial_number }}</div>
                </div>
                @include('partials.status-badge', ['status' => $e->status])
            </div>
        @empty
            <p class="text-sm text-slate-500 py-2">No damaged equipment.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-lg border border-stone-200 p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-slate-900">Recent Transactions</h2>
            <a href="{{ route('borrowings.index') }}" class="text-xs text-slate-500 hover:text-slate-900">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs uppercase tracking-wider text-slate-500 border-b border-stone-200">
                    <tr>
                        <th class="text-left py-2 font-medium">Borrower</th>
                        <th class="text-left py-2 font-medium">Equipment</th>
                        <th class="text-left py-2 font-medium">Borrowed</th>
                        <th class="text-left py-2 font-medium">Due</th>
                        <th class="text-left py-2 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recent as $t)
                    <tr class="border-b border-stone-100 last:border-0">
                        <td class="py-2.5 text-slate-800">{{ $t->borrower->full_name }}</td>
                        <td class="py-2.5 text-slate-800">{{ $t->equipment->name }}</td>
                        <td class="py-2.5 font-mono text-xs text-slate-600">{{ $t->borrow_date->format('M d, Y') }}</td>
                        <td class="py-2.5 font-mono text-xs text-slate-600">{{ $t->expected_return_date->format('M d, Y') }}</td>
                        <td class="py-2.5">@include('partials.status-badge', ['status' => $t->status])</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-6 text-center text-slate-500">No transactions yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
