<?php

namespace App\Http\Controllers;

use App\Http\Requests\EquipmentRequest;
use App\Models\Equipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    /**
     * List all equipment with search and filters.
     */
    public function index(Request $request): View
    {
        $query = Equipment::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($condition = $request->input('condition')) {
            $query->where('condition', $condition);
        }

        $equipment = $query->orderBy('name')->paginate(10)->withQueryString();
        $categories = Equipment::query()->select('category')->distinct()->orderBy('category')->pluck('category');

        return view('equipment.index', compact('equipment', 'categories'));
    }

    public function create(): View
    {
        $this->authorizeAdmin();
        return view('equipment.create');
    }

    public function store(EquipmentRequest $request): RedirectResponse
    {
        $this->authorizeAdmin();
        
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('equipment', 'public');
        }
        unset($data['photo']);

        Equipment::create($data);

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment created successfully.');
    }

    public function show(Equipment $equipment): View
    {
        $equipment->load(['transactions.borrower' => function ($q) {
            $q->orderByDesc('created_at');
        }]);
        return view('equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment): View
    {
        $this->authorizeAdmin();
        return view('equipment.edit', compact('equipment'));
    }

    public function update(EquipmentRequest $request, Equipment $equipment): RedirectResponse
    {
        $this->authorizeAdmin();
        
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($equipment->photo_path) {
                Storage::disk('public')->delete($equipment->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('equipment', 'public');
        }
        unset($data['photo']);

        $equipment->update($data);

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment updated successfully.');
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        $this->authorizeAdmin();

        if ($equipment->photo_path) {
            Storage::disk('public')->delete($equipment->photo_path);
        }
        $equipment->delete();

        return redirect()->route('equipment.index')
            ->with('success', 'Equipment deleted successfully.');
    }

    /**
     * Quick status update. Both admin and staff can mark an item as available,
     * borrowed, or under repair. If moving off "borrowed", any active transaction
     * for this item is auto-closed as returned (manual override).
     */
    public function updateStatus(\Illuminate\Http\Request $request, Equipment $equipment): RedirectResponse
    {
        $this->authorizeAdmin();
        
        $data = $request->validate([
            'status' => ['required', \Illuminate\Validation\Rule::in(['available', 'borrowed', 'under_repair'])],
        ]);

        if ($data['status'] === $equipment->status) {
            return back()->with('warning', 'Status unchanged.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($data, $equipment, $request) {
            // If moving off "borrowed", close any open transaction tied to this item
            if ($equipment->status === 'borrowed' && $data['status'] !== 'borrowed') {
                $equipment->transactions()
                    ->whereNull('actual_return_date')
                    ->update([
                        'actual_return_date'  => now()->toDateString(),
                        'return_condition'    => $equipment->condition,
                        'status'              => 'returned',
                        'follow_up_actions'   => 'Marked as returned via status override by '.$request->user()->name.'.',
                    ]);
            }

            $equipment->update(['status' => $data['status']]);
        });

        return back()->with('success', 'Equipment status updated to "'.str_replace('_',' ',$data['status']).'".');
    }
}
