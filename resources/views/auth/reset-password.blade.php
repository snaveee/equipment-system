@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-stone-50 px-4">
    <div class="w-full max-w-sm">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-slate-900">Set a new password</h1>
        </div>

        @include('partials.flash')

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4 bg-white border border-stone-200 rounded-lg p-6">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label class="block text-sm text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $request->email) }}" required
                       class="w-full px-3 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none">
            </div>

            <div>
                <label class="block text-sm text-slate-700 mb-1">New password</label>
                <input type="password" name="password" required
                       class="w-full px-3 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none">
            </div>

            <div>
                <label class="block text-sm text-slate-700 mb-1">Confirm password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full px-3 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none">
            </div>

            <button type="submit" class="w-full py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">
                Reset password
            </button>
        </form>
    </div>
</div>
@endsection
