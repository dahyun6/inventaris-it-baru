<?php

namespace App\Http\Controllers;

use App\Http\Requests\Handover\StoreHandoverRequest;
use App\Services\HandoverService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HandoverController extends Controller
{
    public function __construct(
        protected HandoverService $handoverService
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $handovers = $this->handoverService->getAllHistory($user);

        return view('handover.index', compact('handovers'));
    }

    public function accept(Request $request, $no_surat = null): RedirectResponse
    {
        $raw = $no_surat ?? $request->input('no_surat') ?? $request->input('id');
        if (!$raw) {
            return redirect()->back()->with('error', 'Nomor surat tanda terima tidak valid.');
        }

        try {
            $this->handoverService->acceptHandover((string) $raw, auth()->user());
            return redirect()->back()->with('success', 'Konfirmasi Penerimaan Aset berhasil disimpan!');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses konfirmasi: ' . $e->getMessage());
        }
    }

    public function create(): View
    {
        $formData = $this->handoverService->getCreateFormData();

        return view('handover.create', [
            'autoNoSurat' => $formData['autoNoSurat'],
            'users'       => $formData['users'],
            'barangs'     => $formData['barangs'],
            'lokasis'     => $formData['lokasis'],
        ]);
    }

    public function store(StoreHandoverRequest $request): RedirectResponse
    {
        $result = $this->handoverService->processHandover($request->validated());
        $target = $result['uuid'] ?? $result['no_surat'];

        return redirect()->route('handover.receipt', ['no_surat' => $target])
            ->with('success', 'Surat Tanda Terima Serah Terima Aset (' . $result['no_surat'] . ') berhasil diterbitkan!');
    }

    public function receipt(Request $request, $no_surat = null): View|RedirectResponse
    {
        $raw = $no_surat ?? $request->query('no_surat') ?? $request->query('id');
        $items = $this->handoverService->getReceiptItems($raw);

        $decodedNoSurat = urldecode((string) $raw);

        if ($items->isEmpty()) {
            $redirectRoute = auth()->user()?->isStaff() ? 'barang.index' : 'handover.history';
            return redirect()->route($redirectRoute)
                ->with('error', 'Surat Tanda Terima (' . ($decodedNoSurat ?: 'N/A') . ') tidak ditemukan.');
        }

        $user = auth()->user();
        if ($user && $user->isStaff()) {
            $userName = strtolower(trim($user->name));
            $isAuthorized = $items->contains(function ($item) use ($user, $userName) {
                $penerima = strtolower(trim($item->penerima_nama ?? ''));
                return $item->user_id === $user->id 
                    || ($penerima && str_contains($penerima, $userName))
                    || ($item->barang && $item->barang->isAssignedTo($user));
            });

            if (!$isAuthorized) {
                abort(403, __('Akses ditolak. Anda tidak memiliki izin untuk melihat dokumen serah terima ini.'));
            }
        }

        $first = $items->first();

        return view('handover.receipt', compact('items', 'first', 'decodedNoSurat'));
    }
}