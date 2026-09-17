<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vendor\StoreVendorRequest;
use App\Http\Requests\Vendor\UpdateVendorRequest;
use App\Models\Vendor;
use App\Services\VendorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function __construct(
        protected VendorService $vendorService
    ) {}

    public function index(): View
    {
        $vendors = $this->vendorService->getAllWithCount();

        return view('vendor.index', compact('vendors'));
    }

    public function store(StoreVendorRequest $request): RedirectResponse
    {
        $this->vendorService->create($request->validated());

        return redirect()->route('vendor.index')->with('success', __('Vendor baru berhasil ditambahkan!'));
    }

    public function update(UpdateVendorRequest $request, Vendor $vendor): RedirectResponse
    {
        $this->vendorService->update($vendor, $request->validated());

        return redirect()->route('vendor.index')->with('success', __('Data vendor berhasil diperbarui!'));
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        $this->vendorService->delete($vendor);

        return redirect()->route('vendor.index')->with('success', __('Vendor berhasil dihapus!'));
    }
}
