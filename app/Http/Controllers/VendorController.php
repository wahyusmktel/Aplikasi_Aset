<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $vendors = Vendor::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('vendors.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:vendors,code',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Vendor::create($request->all());

        alert()->success('Berhasil!', 'Data rekanan berhasil ditambahkan.');
        return redirect()->route('vendors.index');
    }

    public function show(Vendor $vendor)
    {
        return redirect()->route('vendors.index');
    }

    public function edit(Vendor $vendor)
    {
        return redirect()->route('vendors.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:vendors,code,' . $vendor->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $vendor->update($request->all());

        alert()->success('Berhasil!', 'Data rekanan berhasil diperbarui.');
        return redirect()->route('vendors.index');
    }

    /**
     * Remove the specified resource in storage.
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        alert()->success('Berhasil!', 'Data rekanan berhasil dihapus.');
        return redirect()->route('vendors.index');
    }
}
