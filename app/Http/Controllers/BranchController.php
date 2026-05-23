<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the branches.
     */
    public function index()
    {
        $branches = Branch::withCount('users')->orderBy('name')->paginate(10);
        return view('owner.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new branch.
     */
    public function create()
    {
        return view('owner.branches.create');
    }

    /**
     * Store a newly created branch in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama cabang wajib diisi.',
            'name.max' => 'Nama cabang maksimal 255 karakter.',
            'phone.max' => 'Nomor telepon maksimal 50 karakter.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Branch::create($validated);

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified branch.
     */
    public function edit(Branch $branch)
    {
        return view('owner.branches.edit', compact('branch'));
    }

    /**
     * Update the specified branch in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Nama cabang wajib diisi.',
            'name.max' => 'Nama cabang maksimal 255 karakter.',
            'phone.max' => 'Nomor telepon maksimal 50 karakter.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', 'Cabang berhasil diperbarui.');
    }

}
