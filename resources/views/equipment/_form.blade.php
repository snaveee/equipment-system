@php
    $isEdit = isset($equipment);
    $values = $isEdit ? $equipment : null;
@endphp
<form method="POST" action="{{ $isEdit ? route('equipment.update', $equipment) : route('equipment.store') }}"
      enctype="multipart/form-data" class="space-y-5 bg-white border border-stone-200 rounded-2xl p-6 max-w-3xl">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Name</label>
            <input type="text" name="name" value="{{ old('name', $values?->name) }}" required
                   class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white focus:border-slate-900 outline-none">
        </div>
        <div>
            <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Category</label>
            <input type="text" name="category" value="{{ old('category', $values?->category) }}" required
                   class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white focus:border-slate-900 outline-none">
        </div>
    </div>

    <div>
        <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Description</label>
        <textarea name="description" rows="3"
                  class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white focus:border-slate-900 outline-none">{{ old('description', $values?->description) }}</textarea>
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Serial / Asset #</label>
            <input type="text" name="serial_number" value="{{ old('serial_number', $values?->serial_number) }}" required
                   class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white font-mono outline-none focus:border-slate-900">
        </div>
        <div>
            <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Condition</label>
            <select name="condition" required class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white outline-none focus:border-slate-900">
                @foreach(['new','good','fair','damaged'] as $c)
                    <option value="{{ $c }}" @selected(old('condition', $values?->condition)===$c)>{{ ucfirst($c) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Status</label>
            <select name="status" required class="w-full px-4 py-2.5 rounded-lg border border-stone-300 bg-white outline-none focus:border-slate-900">
                @foreach(['available','borrowed','under_repair'] as $s)
                    <option value="{{ $s }}" @selected(old('status', $values?->status)===$s)>{{ str_replace('_',' ',ucfirst($s)) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="block text-xs font-mono uppercase tracking-wider text-slate-600 mb-1.5">Photo (optional)</label>
        @if($isEdit && $equipment->photo_path)
            <img src="{{ asset('storage/'.$equipment->photo_path) }}" class="w-32 h-32 rounded-lg object-cover border border-stone-200 mb-2">
        @endif
        <input type="file" name="photo" accept="image/*"
               class="block text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-slate-900 file:text-white file:text-xs file:font-semibold hover:file:bg-slate-700">
    </div>

    <div class="flex items-center gap-3 pt-4 border-t border-stone-200">
        <button class="px-4 py-2 rounded-md bg-slate-900 text-white text-sm font-medium hover:bg-slate-700">
            {{ $isEdit ? 'Update' : 'Create' }} Equipment
        </button>
        <a href="{{ route('equipment.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
    </div>
</form>
