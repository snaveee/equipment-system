@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-stone-50 px-4">
    <div class="w-full max-w-sm">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-slate-900">Reset password</h1>
            <p class="text-sm text-slate-500 mt-1">Enter your email and we'll send a reset link.</p>
        </div>

        @include('partials.flash')

        @if(session('status'))
            <div class="mb-4 p-3 rounded-md bg-stone-50 border border-stone-200 text-slate-800 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4 bg-white border border-stone-200 rounded-lg p-6">
            @csrf
            <div>
                <label class="block text-sm text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-3 py-2 rounded-md border border-stone-300 bg-white text-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none">
            </div>
            <button type="submit" class="w-full py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">
                Send reset link
            </button>
        </form>

        <p class="mt-4 text-sm text-slate-500 text-center">
            <a href="{{ route('login') }}" class="text-slate-900 underline underline-offset-2">Back to sign in</a>
        </p>
    </div>
</div>
@endsection
