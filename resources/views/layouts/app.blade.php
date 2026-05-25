<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-stone-50 text-slate-800 antialiased">

@php
    // Each nav entry: [route name, label, list of route patterns that should highlight it as active]
    $nav = [
        ['dashboard',          'Dashboard',    ['dashboard']],
        ['equipment.index',    'Equipment',    ['equipment.*']],
        ['borrowers.index',    'Borrowers',    ['borrowers.*']],
        ['borrowings.index',   'Transactions', ['borrowings.index', 'borrowings.create', 'borrowings.store', 'borrowings.show', 'borrowings.return.*']],
        ['borrowings.overdue', 'Overdue',      ['borrowings.overdue']],
        ['borrowings.damaged', 'Damaged',      ['borrowings.damaged']],
    ];
@endphp

@auth
<div class="min-h-full flex">
    {{-- Sidebar --}}
    <aside class="hidden md:flex md:w-60 md:flex-col bg-slate-900 text-slate-200">
        <div class="px-6 py-6 border-b border-slate-800">
            <div class="text-sm font-semibold tracking-tight text-white">Equipment Tracker</div>
            <div class="text-xs text-slate-500 mt-0.5">Borrowing &amp; inventory</div>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-0.5 text-sm">
            @foreach($nav as [$route, $label, $patterns])
                @php $active = collect($patterns)->contains(fn($p) => request()->routeIs($p)); @endphp
                <a href="{{ route($route) }}"
                   class="block px-3 py-2 rounded-md
                          {{ $active ? 'bg-white text-slate-900 font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    {{ $label }}
                </a>
            @endforeach
            @if(auth()->user()->isAdmin())
                @php $active = request()->routeIs('reports.*'); @endphp
                <a href="{{ route('reports.index') }}"
                   class="block px-3 py-2 rounded-md
                          {{ $active ? 'bg-white text-slate-900 font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    Reports
                </a>
            @endif
        </nav>
        <div class="p-4 border-t border-slate-800 text-xs text-slate-500">
            <div class="truncate">{{ auth()->user()->email }}</div>
            <div class="mt-1 inline-block px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 uppercase tracking-wider text-[10px]">{{ auth()->user()->role }}</div>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-stone-200">
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">@yield('heading', 'Dashboard')</h1>
                    @hasSection('subheading')
                        <p class="text-sm text-slate-500 mt-0.5">@yield('subheading')</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <span class="hidden sm:inline text-sm text-slate-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="px-3 py-1.5 rounded-md border border-slate-300 text-slate-700 text-sm hover:bg-slate-100">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
            {{-- Mobile nav --}}
            <nav class="md:hidden flex overflow-x-auto gap-1.5 px-4 pb-3 text-xs">
                @foreach($nav as [$route, $label, $patterns])
                    @php $active = collect($patterns)->contains(fn($p) => request()->routeIs($p)); @endphp
                    <a href="{{ route($route) }}"
                       class="whitespace-nowrap px-2.5 py-1 rounded-md border
                              {{ $active ? 'bg-slate-900 border-slate-900 text-white font-medium' : 'border-stone-300 text-slate-600' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </header>

        <main class="flex-1 p-6">
            @include('partials.flash')
            @yield('content')
        </main>

        <footer class="px-6 py-4 border-t border-stone-200 bg-white text-xs text-slate-500">
            Equipment Borrowing &amp; Tracking · Laravel {{ app()->version() }}
        </footer>
    </div>
</div>
@else
    <main class="min-h-full">
        @include('partials.flash')
        @yield('content')
    </main>
@endauth

</body>
</html>
