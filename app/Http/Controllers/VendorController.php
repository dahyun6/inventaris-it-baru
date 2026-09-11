<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::withCount('barangs')->orderBy('nama_vendor')->get();
        return view('vendor.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_vendor' => 'required|string|unique:vendors,nama_vendor|max:255',
            'alamat'      => 'nullable|string',
            'telepon'     => 'nullable|string|max:50',
            'email'       => 'nullable|email|max:100',
        ]);

        Vendor::create($request->all());

        return redirect()->route('vendor.index')->with('success', __('Vendor baru berhasil ditambahkan!'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'nama_vendor' => 'required|string|max:255|unique:vendors,nama_vendor,' . $vendor->id,
            'alamat'      => 'nullable|string',
            'telepon'     => 'nullable|string|max:50',
            'email'       => 'nullable|email|max:100',
        ]);

        $vendor->update($request->all());

        return redirect()->route('vendor.index')->with('success', __('Data vendor berhasil diperbarui!'));
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()->route('vendor.index')->with('success', __('Vendor berhasil dihapus!'));
    }
}
