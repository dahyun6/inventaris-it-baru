<?php

namespace App\Http\Controllers;

use App\Http\Requests\Departemen\StoreDepartemenRequest;
use App\Http\Requests\Departemen\UpdateDepartemenRequest;
use App\Models\Departemen;
use App\Services\DepartemenService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartemenController extends Controller
{
    public function __construct(
        protected DepartemenService $departemenService
    ) {}

    public function index(): View
    {
        $departemens = $this->departemenService->getAllWithCount();

        return view('departemen.index', compact('departemens'));
    }

    public function store(StoreDepartemenRequest $request): RedirectResponse
    {
        $this->departemenService->create($request->validated());

        return redirect()->route('departemen.index')->with('success', __('Departemen baru berhasil ditambahkan!'));
    }

    public function update(UpdateDepartemenRequest $request, Departemen $departemen): RedirectResponse
    {
        $this->departemenService->update($departemen, $request->validated());

        return redirect()->route('departemen.index')->with('success', __('Data departemen berhasil diperbarui!'));
    }

    public function destroy(Departemen $departemen): RedirectResponse
    {
        $this->departemenService->delete($departemen);

        return redirect()->route('departemen.index')->with('success', __('Departemen berhasil dihapus!'));
    }
}
