@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-stone-50 px-4">
    <div class="w-full max-w-sm">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-slate-900">Create account</h1>
            <p class="text-sm text-slate-500 mt-1">New users are registered as Staff/Borrower.</p>
        </div>

        @include('partials.flash')

        <form method="POST" action="{{ route('register') }}" class="space-y-4 bg-white border border-stone-200 rounded-lg p-6">
            @csrf

            <div>
                <label class="block text-sm text-slate-700 mb-1">Full name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-3 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none">
            </div>

            <div>
                <label class="block text-sm text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-3 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none">
            </div>

            <div>
                <label class="block text-sm text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full px-3 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none">
            </div>

            <div>
                <label class="block text-sm text-slate-700 mb-1">Confirm password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-3 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none">
            </div>

            <button type="submit" class="w-full py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">
                Create account
            </button>
        </form>

        <p class="mt-4 text-sm text-slate-500 text-center">
            Already registered? <a href="{{ route('login') }}" class="text-slate-900 underline underline-offset-2">Sign in</a>
        </p>
    </div>
</div>
@endsection
