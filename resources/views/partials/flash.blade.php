@php
    $flashTypes = [
        'success' => ['bg' => 'bg-stone-50',  'border' => 'border-slate-900',  'text' => 'text-slate-900'],
        'error'   => ['bg' => 'bg-red-50',    'border' => 'border-red-500',    'text' => 'text-red-900'],
        'warning' => ['bg' => 'bg-stone-100', 'border' => 'border-slate-500',  'text' => 'text-slate-900'],
        'info'    => ['bg' => 'bg-stone-50',  'border' => 'border-slate-400',  'text' => 'text-slate-800'],
    ];
@endphp

@foreach($flashTypes as $key => $cfg)
    @if(session($key))
        <div class="mb-4 rounded-md border-l-4 {{ $cfg['bg'] }} {{ $cfg['border'] }} {{ $cfg['text'] }} px-4 py-3 text-sm">
            {{ session($key) }}
        </div>
    @endif
@endforeach

@if($errors->any())
    <div class="mb-4 rounded-md border-l-4 bg-red-50 border-red-500 text-red-900 px-4 py-3 text-sm">
        <div class="font-semibold mb-1">Please fix the following:</div>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif
