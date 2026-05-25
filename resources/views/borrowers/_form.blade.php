@php
    $isEdit = isset($borrower);
    $values = $isEdit ? $borrower : null;
@endphp
<form method="POST" action="{{ $isEdit ? route('borrowers.update', $borrower) : route('borrowers.store') }}"
      class="space-y-5 bg-white border border-stone-200 rounded-2xl p-6 max-w-2xl">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div>
        <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Full Name</label>
        <input type="text" name="name" value="{{ old('name', $values?->name) }}" required
               class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white focus:border-slate-900 outline-none">
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Department / Section</label>
            <input type="text" name="department" value="{{ old('department', $values?->department) }}" required
                   class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white focus:border-slate-900 outline-none">
        </div>
        <div>
            <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Position / Role</label>
            <input type="text" name="position" value="{{ old('position', $values?->position) }}" required
                   class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white focus:border-slate-900 outline-none">
        </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Contact Number</label>
            <input type="text" name="contact_number" value="{{ old('contact_number', $values?->contact_number) }}" required
                   class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white font-mono focus:border-slate-900 outline-none">
        </div>
        <div>
            <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email', $values?->email) }}" required
                   class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white font-mono focus:border-slate-900 outline-none">
        </div>
    </div>

    @if($isEdit)
        <div>
            <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">New Password (leave blank to keep current)</label>
            <input type="password" name="password" placeholder="Leave blank to keep current password"
                   class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white focus:border-slate-900 outline-none">
        </div>
        <div>
            <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Confirm Password</label>
            <input type="password" name="password_confirmation"
                   class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white focus:border-slate-900 outline-none">
        </div>
    @else
        <input type="hidden" name="password" value="TempPassword123!">
        <input type="hidden" name="password_confirmation" value="TempPassword123!">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
            <p class="text-xs text-blue-900"><strong>Note:</strong> A temporary password will be set. The borrower can reset it on first login.</p>
        </div>
    @endif

    <div class="flex items-center gap-3 pt-4 border-t border-stone-200">
        <button class="px-4 py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">
            {{ $isEdit ? 'Update' : 'Register' }} Borrower
        </button>
        <a href="{{ route('borrowers.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
    </div>
</form>
