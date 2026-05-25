@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-stone-50 via-amber-50/40 to-stone-100">
    <nav class="px-6 sm:px-12 py-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-slate-900 flex items-center justify-center">
                <span class="text-amber-400 font-fraunces font-black text-xl">E</span>
            </div>
            <div>
                <div class="text-xs uppercase tracking-[0.2em] text-amber-700 font-mono">Equipment</div>
                <div class="font-fraunces text-lg font-bold leading-none">Borrowing Tracker</div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-700 hover:text-slate-900">Sign in</a>
            <a href="{{ route('register') }}" class="px-4 py-2 rounded-full bg-slate-900 text-white text-sm font-medium hover:bg-slate-700 transition">Get started</a>
        </div>
    </nav>

    <section class="px-6 sm:px-12 pt-12 pb-20 max-w-6xl">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-200/60 border border-amber-300 text-amber-900 text-xs font-mono uppercase tracking-wider mb-6">
            <span class="w-2 h-2 rounded-full bg-amber-600"></span> Laravel 12 · Fortify · Tailwind
        </div>
        <h1 class="font-fraunces text-5xl sm:text-7xl lg:text-8xl font-black leading-[0.95] tracking-tight text-slate-900">
            Track every<br>
            <span class="italic font-light">borrowed</span> asset<br>
            <span class="text-amber-600">end-to-end.</span>
        </h1>
        <p class="mt-8 max-w-2xl text-lg text-slate-600 leading-relaxed">
            A clean MVC application for schools, offices, and organizations to manage equipment inventory and borrowing transactions — who borrowed what, when it's due, and the condition on return.
        </p>
        <div class="mt-10 flex flex-wrap gap-3">
            <a href="{{ route('login') }}" class="px-6 py-3 rounded-full bg-slate-900 text-white font-medium hover:bg-slate-700 transition">Open the system</a>
            <a href="{{ route('register') }}" class="px-6 py-3 rounded-full border-2 border-slate-900 text-slate-900 font-medium hover:bg-slate-900 hover:text-white transition">Create account</a>
        </div>

        <div class="mt-20 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['Inventory', 'Catalog with photos, categories, conditions and status'],
                ['Borrowers', 'Profiles with full history and frequent-borrower insights'],
                ['Returns', 'Track due dates, overdue flags, and damage on return'],
                ['Reports', 'Most-borrowed items, by-department stats, CSV export'],
            ] as [$h, $d])
                <div class="p-5 rounded-2xl bg-white border border-stone-200 hover:border-amber-400 hover:-translate-y-1 transition">
                    <div class="font-mono text-xs text-amber-700 uppercase tracking-wider mb-2">{{ $h }}</div>
                    <div class="text-sm text-slate-700 leading-relaxed">{{ $d }}</div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
