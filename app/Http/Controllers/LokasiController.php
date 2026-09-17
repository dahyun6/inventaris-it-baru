<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lokasi\StoreLokasiRequest;
use App\Http\Requests\Lokasi\UpdateLokasiRequest;
use App\Models\LokasiUnit;
use App\Services\LokasiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LokasiController extends Controller
{
    public function __construct(
        protected LokasiService $lokasiService
    ) {}

    public function index(): View
    {
        $lokasis = $this->lokasiService->getAllWithCount();

        return view('lokasi.index', compact('lokasis'));
    }

    public function store(StoreLokasiRequest $request): RedirectResponse
    {
        $this->lokasiService->create($request->validated());

        return redirect()->route('lokasi.index')->with('success', __('Lokasi baru berhasil ditambahkan!'));
    }

    public function update(UpdateLokasiRequest $request, LokasiUnit $lokasi): RedirectResponse
    {
        $this->lokasiService->update($lokasi, $request->validated());

        return redirect()->route('lokasi.index')->with('success', __('Data lokasi berhasil diperbarui!'));
    }

    public function destroy(LokasiUnit $lokasi): RedirectResponse
    {
        $this->lokasiService->delete($lokasi);

        return redirect()->route('lokasi.index')->with('success', __('Lokasi berhasil dihapus!'));
    }
}
