<?php

namespace App\Http\Controllers;

use App\Http\Requests\Barang\ImportBarangRequest;
use App\Http\Requests\Barang\SingleHandoverRequest;
use App\Http\Requests\Barang\StoreBarangRequest;
use App\Http\Requests\Barang\UpdateBarangRequest;
use App\Models\Barang;
use App\Models\User;
use App\Services\BarangService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarangController extends Controller
{
    public function __construct(
        protected BarangService $barangService
    ) {}

    public function index(): View
    {
        $barangs = $this->barangService->getAll(auth()->user());
        $formData = $this->barangService->getFormData();

        return view('barang.index', [
            'barangs'    => $barangs,
            'categories' => $formData['categories'],
            'vendors'    => $formData['vendors'],
            'users'      => $formData['users'],
            'lokasis'    => $formData['lokasis'],
        ]);
    }

    public function create(): View
    {
        $formData = $this->barangService->getFormData();

        return view('barang.create', [
            'categories' => $formData['categories'],
            'vendors'    => $formData['vendors'],
            'users'      => $formData['users'],
            'lokasis'    => $formData['lokasis'],
        ]);
    }

    public function store(StoreBarangRequest $request): RedirectResponse
    {
        $this->barangService->create($request->validated());

        return redirect()->route('barang.index')->with('success', 'Aset berhasil ditambahkan!');
    }

    public function show(Barang $barang): View
    {
        if (!$barang->isAssignedTo(auth()->user())) {
            abort(403, __('Akses ditolak. Anda hanya dapat melihat data aset yang ter-assign ke akun Anda.'));
        }

        $barang = $this->barangService->getDetail($barang);

        return view('barang.show', compact('barang'));
    }

    public function edit(Barang $barang): View
    {
        $formData = $this->barangService->getFormData();

        return view('barang.edit', [
            'barang'     => $barang,
            'categories' => $formData['categories'],
            'vendors'    => $formData['vendors'],
            'users'      => $formData['users'],
            'lokasis'    => $formData['lokasis'],
        ]);
    }

    public function update(UpdateBarangRequest $request, Barang $barang): RedirectResponse
    {
        $this->barangService->update($barang, $request->validated());

        return redirect()->route('barang.index')->with('success', 'Aset berhasil diperbarui!');
    }

    public function destroy(Barang $barang): RedirectResponse
    {
        $this->barangService->delete($barang);

        return redirect()->route('barang.index')->with('success', 'Aset berhasil dihapus!');
    }

    public function handover(Barang $barang): View
    {
        $users = User::with(['departemen', 'lokasi'])->orderBy('name')->get();
        $lokasis = \App\Models\LokasiUnit::orderBy('nama_lokasi')->get();

        return view('barang.handover', compact('barang', 'users', 'lokasis'));
    }

    public function storeHandover(SingleHandoverRequest $request, Barang $barang): RedirectResponse
    {
        $this->barangService->recordHandover($barang, $request->validated());

        return redirect()->route('barang.show', $barang->uuid)
            ->with('success', 'Proses Handover berhasil dicatat!');
    }

    public function importExcel(ImportBarangRequest $request): RedirectResponse
    {
        try {
            $count = $this->barangService->importExcel($request->file('file_excel'));

            return redirect()->route('barang.index')
                ->with('success', 'Fantastis! Berhasil menambahkan ' . $count . ' baris data dari Excel!');
        } catch (Exception $e) {
            return redirect()->route('barang.index')
                ->withErrors(['Terjadi kesalahan format Excel: ' . $e->getMessage()]);
        }
    }

    public function downloadTemplate(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return $this->barangService->downloadTemplate();
    }

    public function generateAssetCode(Request $request): JsonResponse
    {
        $code = $this->barangService->getSuggestedCodeForCategory($request->category_id);

        return response()->json(['kode' => $code]);
    }

    public function printBarcode(Barang $barang): View
    {
        $user = auth()->user();
        if ($user && $user->isStaff() && !$barang->isAssignedTo($user)) {
            abort(403, __('Akses ditolak. Anda hanya dapat mencetak barcode untuk aset milik Anda.'));
        }

        $barang->load(['category']);

        return view('barang.barcode', compact('barang'));
    }

    public function printBarcodeBatch(Request $request): View
    {
        $categoryId = $request->query('category_id');
        $dept = $request->query('dept');
        $unitLoc = $request->query('unit_loc');
        $status = $request->query('status');
        $vendor = $request->query('vendor');
        $q = $request->query('q');
        $selectedIds = $request->query('ids');

        $query = Barang::with(['category'])->latest();

        if (!empty($categoryId) && $categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        if (!empty($dept) && $dept !== 'all') {
            $query->where('dept', $dept);
        }

        if (!empty($unitLoc) && $unitLoc !== 'all') {
            $query->where('unit_loc', $unitLoc);
        }

        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($vendor) && $vendor !== 'all') {
            $query->where('vendor', $vendor);
        }

        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('no_aset_local', 'like', "%{$q}%")
                    ->orWhere('model', 'like', "%{$q}%")
                    ->orWhere('serial_number', 'like', "%{$q}%")
                    ->orWhere('pengguna', 'like', "%{$q}%")
                    ->orWhere('hostname', 'like', "%{$q}%");
            });
        }

        if (!empty($selectedIds)) {
            $ids = is_array($selectedIds) ? $selectedIds : explode(',', $selectedIds);
            $query->whereIn('id', $ids);
        }

        $barangs = $query->get();
        $categories = \App\Models\Category::orderBy('nama_kategori')->get();
        $departemens = \App\Models\Departemen::orderBy('nama_departemen')->get();
        $lokasis = \App\Models\LokasiUnit::orderBy('nama_lokasi')->get();
        $vendors = \App\Models\Vendor::orderBy('nama_vendor')->get();

        return view('barang.barcode-batch', compact(
            'barangs',
            'categories',
            'departemens',
            'lokasis',
            'vendors',
            'categoryId',
            'dept',
            'unitLoc',
            'status',
            'vendor',
            'q'
        ));
    }
}