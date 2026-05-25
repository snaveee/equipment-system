@extends('layouts.app')

@section('title', 'Sign in')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-stone-50 px-4">
    <div class="w-full max-w-sm">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-slate-900">Equipment Tracker</h1>
            <p class="text-sm text-slate-500 mt-1">Sign in to continue.</p>
        </div>

        @include('partials.flash')

        <form method="POST" action="{{ route('login') }}" class="space-y-4 bg-white border border-stone-200 rounded-lg p-6">
            @csrf

            <div>
                <label for="email" class="block text-sm text-slate-700 mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-3 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="text-sm text-slate-700">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-slate-500 hover:text-slate-900">Forgot?</a>
                </div>
                <input id="password" type="password" name="password" required
                       class="w-full px-3 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none">
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="remember" class="rounded border-stone-300 text-slate-900 focus:ring-slate-900">
                Remember me
            </label>

            <button type="submit"
                    class="w-full py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">
                Sign in
            </button>
        </form>

        <p class="mt-4 text-sm text-slate-500 text-center">
            No account? <a href="{{ route('register') }}" class="text-slate-900 underline underline-offset-2">Create one</a>
        </p>
    </div>
</div>
@endsection
