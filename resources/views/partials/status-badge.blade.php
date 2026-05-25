@php
    // Minimal palette: neutral, dark (attention), red (critical only)
    $cls = match($status) {
        'overdue', 'damaged'                      => 'bg-red-50 text-red-700 border border-red-200',
        'borrowed', 'active', 'fair'              => 'bg-slate-900 text-white',
        default                                   => 'bg-stone-100 text-slate-700 border border-stone-200',
    };
@endphp
<span class="px-2 py-0.5 rounded text-xs font-medium uppercase tracking-wider {{ $cls }}">{{ str_replace('_',' ',$status) }}</span>
