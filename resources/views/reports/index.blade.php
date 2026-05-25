@extends('layouts.app')
@section('title', 'Reports')
@section('heading', 'Reports & Statistics')
@section('subheading', 'Analytical view over inventory and borrowings.')

@section('content')
<div class="mb-6 flex justify-end">
    <a href="{{ route('reports.export.transactions') }}"
       class="px-4 py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">
        Export Transactions (CSV)
    </a>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg border border-stone-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Most Borrowed Equipment</h2>
        @forelse($mostBorrowed as $item)
            <div class="py-2.5 border-b border-stone-100 last:border-0 flex items-center justify-between">
                <div class="flex-1">
                    <div class="font-medium text-slate-900">{{ $item->name }}</div>
                    <div class="text-xs font-mono text-slate-500">{{ $item->serial_number }} · {{ $item->category }}</div>
                </div>
                <div class="text-xl font-semibold text-slate-900 tabular-nums">{{ $item->borrow_count }}</div>
            </div>
        @empty
            <p class="text-sm text-slate-500">No data yet.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-lg border border-stone-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Borrowing by Department</h2>
        @php($maxDept = $byDepartment->max('total') ?: 1)
        @forelse($byDepartment as $row)
            <div class="py-2.5 border-b border-stone-100 last:border-0">
                <div class="flex items-center justify-between mb-1">
                    <span class="font-medium text-sm text-slate-900">{{ $row->department }}</span>
                    <span class="font-mono text-xs text-slate-600">{{ $row->total }} loans</span>
                </div>
                <div class="h-1.5 rounded bg-stone-100 overflow-hidden">
                    <div class="h-full bg-slate-900 rounded" style="width: {{ ($row->total / $maxDept) * 100 }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500">No data yet.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-lg border border-stone-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Condition Summary</h2>
        <div class="grid grid-cols-2 gap-3">
            @foreach(['new','good','fair','damaged'] as $cond)
                <div class="p-3 rounded-md bg-stone-50 border border-stone-200">
                    <div class="text-xs uppercase tracking-wider text-slate-500">{{ ucfirst($cond) }}</div>
                    <div class="text-2xl font-semibold mt-0.5 text-slate-900">{{ $conditionSummary[$cond] ?? 0 }}</div>
                </div>
            @endforeach
        </div>

        <h3 class="text-base font-semibold text-slate-900 mt-6 mb-3">Status Summary</h3>
        <div class="grid grid-cols-3 gap-3">
            @foreach(['available','borrowed','under_repair'] as $stat)
                <div class="p-3 rounded-md bg-stone-50 border border-stone-200">
                    <div class="text-xs uppercase tracking-wider text-slate-500">{{ str_replace('_',' ',$stat) }}</div>
                    <div class="text-xl font-semibold mt-0.5 text-slate-900">{{ $statusSummary[$stat] ?? 0 }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-lg border border-stone-200 p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Monthly Borrowings (last 12 months)</h2>
        @php($maxMonthly = $monthly->max('total') ?: 1)
        @if($monthly->isEmpty())
            <p class="text-sm text-slate-500">No borrowing activity yet.</p>
        @else
            <div class="flex items-end gap-2 h-44">
                @foreach($monthly as $m)
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-slate-900 rounded-t" style="height: {{ ($m->total / $maxMonthly) * 100 }}%; min-height: 4px;"></div>
                        <div class="font-mono text-[10px] text-slate-500 whitespace-nowrap">{{ $m->month }}</div>
                        <div class="font-mono text-xs font-medium text-slate-700">{{ $m->total }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
