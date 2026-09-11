@extends('layout')

@section('title', __('Penerbitan Surat Tanda Terima Aset'))

@section('header_actions')
<a href="{{ route('handover.history') }}" class="btn btn-phoenix-secondary btn-sm">
    <i class="fas fa-arrow-left me-1"></i> {{ __('Kembali ke Riwayat') }}
</a>
@endsection

@section('content')
<style>
    .form-section-divider {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--phoenix-primary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 1.25rem;
        margin-bottom: 0.85rem;
        padding-bottom: 0.35rem;
        border-bottom: 1px solid var(--phoenix-border-color);
    }
    .form-label {
        font-size: 0.8125rem;
        font-weight: 700;
        color: var(--phoenix-text-body);
        margin-bottom: 0.35rem;
    }
    .form-control, .form-select {
        border: 1px solid var(--phoenix-border-color);
        border-radius: 8px;
        font-size: 0.825rem;
        padding: 0.5rem 0.85rem;
        background-color: #ffffff;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--phoenix-primary);
        box-shadow: 0 0 0 3px rgba(56, 116, 255, 0.15);
    }
    .asset-checkbox-table tbody tr {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .asset-checkbox-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .asset-checkbox-table tbody tr.table-active {
        background-color: var(--phoenix-primary-subtle) !important;
    }
</style>

<form action="{{ route('handover.store') }}" method="POST" id="formHandoverReceipt">
    @csrf

    <div class="row g-4">
        <!-- Kolom Kiri: Administrasi & Data Penerima -->
        <div class="col-lg-5">
            <div class="phoenix-card">
                <div class="phoenix-card-header">
                    <h6 class="phoenix-card-title">
                        <i class="fas fa-file-signature text-primary"></i>
                        1. {{ __('Informasi Dokumen & Penerima') }}
                    </h6>
                    <span class="badge-phoenix badge-phoenix-primary">{{ __('Official Records') }}</span>
                </div>
                <div class="phoenix-card-body">
                    <div class="mb-3">
                        <label class="form-label text-danger">{{ __('Nomor Surat Tanda Terima') }} *</label>
                        <input type="text" name="no_surat" class="form-control font-monospace fw-bold text-primary" value="{{ old('no_surat', $autoNoSurat) }}" required>
                        <small class="text-muted" style="font-size: 0.725rem;">Format resmi dokumen berita acara serah terima</small>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-danger">{{ __('Tanggal Serah Terima') }} *</label>
                            <input type="date" name="tanggal_serah_terima" class="form-control" value="{{ old('tanggal_serah_terima', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-danger">{{ __('Diserahkan Oleh (IT)') }} *</label>
                            <input type="text" name="diserahkan_oleh" class="form-control" value="{{ old('diserahkan_oleh', Auth::user()->name) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-danger">{{ __('Ubah Status Aset Menjadi') }} *</label>
                        <select name="status" class="form-select" required>
                            <option value="Dipinjam" selected>{{ __('Dipinjam') }} (Diserahkan ke User)</option>
                            <option value="Tersedia">{{ __('Tersedia') }} (Pengembalian ke Gudang IT)</option>
                            <option value="Rusak">{{ __('Rusak') }} (Perbaikan / Maintenance)</option>
                        </select>
                    </div>

                    <div class="form-section-divider">2. {{ __('Data Penerima (Recipient)') }}</div>

                    <div class="mb-3">
                        <label class="form-label">Pilih dari User Terdaftar (Auto-fill)</label>
                        <select id="selectQuickUser" class="form-select">
                            <option value="">-- {{ __('Pilih') }} --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->name }}" data-email="{{ $u->email }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-danger">{{ __('Nama Lengkap Penerima') }} *</label>
                        <input type="text" name="penerima_nama" id="penerima_nama" class="form-control" value="{{ old('penerima_nama') }}" placeholder="Contoh: Budi Santoso" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Departemen / Divisi') }}</label>
                            <input type="text" name="penerima_dept" id="penerima_dept" class="form-control" value="{{ old('penerima_dept') }}" placeholder="Contoh: Marketing">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Jabatan (Position)') }}</label>
                            <input type="text" name="penerima_jabatan" id="penerima_jabatan" class="form-control" value="{{ old('penerima_jabatan') }}" placeholder="Contoh: Staff Marketing">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-danger">{{ __('Lokasi Penempatan Unit') }} *</label>
                        <input type="text" name="lokasi" id="lokasi" class="form-control" value="{{ old('lokasi') }}" placeholder="Contoh: Gd. A Lt. 2 / Meja 14" required>
                    </div>

                    <div class="form-section-divider">3. {{ __('Catatan & Kelengkapan') }}</div>
                    <div class="mb-2">
                        <label class="form-label">{{ __('Catatan / Kelengkapan Aksesoris') }}</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Unit Laptop, Charger Ori 65W, Mouse Wireless, Tas Laptop.">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Checklist Aset -->
        <div class="col-lg-7">
            <div class="phoenix-card">
                <div class="phoenix-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="phoenix-card-title">
                            <i class="fas fa-boxes-stacked text-primary"></i>
                            {{ __('Pilih Hardware / Aset') }}
                        </h6>
                        <span class="badge-phoenix badge-phoenix-primary" id="selectedCountBadge">0 {{ __('Dipilih') }}</span>
                    </div>
                    <div>
                        <input type="text" id="assetSearchInput" class="form-control form-control-sm" style="width: 180px; border-radius: 20px;" placeholder="{{ __('Cari aset / SN...') }}">
                    </div>
                </div>
                <div class="phoenix-card-body p-0">
                    <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                        <span class="small text-muted"><i class="fas fa-info-circle me-1 text-primary"></i> Centang satu atau beberapa perangkat yang diserahterimakan dalam surat ini.</span>
                    </div>
                    <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                        <table class="table table-hover asset-checkbox-table mb-0" id="tableSelectAsset" style="font-size: 0.8125rem;">
                            <thead class="sticky-top" style="font-size: 0.725rem; text-transform: uppercase; color: #525b75; background-color: #f8fafc !important; z-index: 5; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                <tr>
                                    <th width="8%" class="text-center bg-light">{{ __('PILIH') }}</th>
                                    <th class="bg-light">{{ __('NO ASET') }}</th>
                                    <th class="bg-light">{{ __('KATEGORI') }}</th>
                                    <th class="bg-light">{{ __('MODEL / BRAND') }}</th>
                                    <th class="bg-light">{{ __('SERIAL NUMBER (SN)') }}</th>
                                    <th class="bg-light">{{ __('STATUS') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barangs as $b)
                                <tr class="asset-row">
                                    <td class="text-center align-middle">
                                        <input class="form-check-input asset-checkbox" type="checkbox" name="barang_ids[]" value="{{ $b->id }}" id="chk{{ $b->id }}" style="cursor: pointer;">
                                    </td>
                                    <td class="font-monospace fw-bold align-middle">{{ $b->no_aset_local ?? '-' }}</td>
                                    <td class="align-middle">{{ $b->category->nama_kategori ?? '-' }}</td>
                                    <td class="fw-semibold align-middle asset-model">{{ $b->model }}</td>
                                    <td class="font-monospace text-muted align-middle asset-sn">{{ $b->serial_number ?? '-' }}</td>
                                    <td class="align-middle">
                                        @if($b->status == 'Tersedia') 
                                            <span class="badge-phoenix badge-phoenix-success" style="font-size: 0.7rem;">{{ __('Tersedia') }}</span>
                                        @elseif($b->status == 'Dipinjam') 
                                            <span class="badge-phoenix badge-phoenix-warning" style="font-size: 0.7rem;">{{ __('Dipinjam') }}</span>
                                        @else 
                                            <span class="badge-phoenix badge-phoenix-danger" style="font-size: 0.7rem;">{{ __('Rusak') }}</span> 
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">{{ __('Belum ada data aset terdaftar.') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 bg-light border-top d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <span class="small text-muted" id="selectionSummary">Pilih minimal 1 aset untuk melanjutkan.</span>
                        <button type="submit" class="btn btn-phoenix-primary px-4 py-2" id="btnSubmitReceipt" disabled>
                            <i class="fas fa-print me-1"></i> {{ __('Terbitkan Surat Tanda Terima') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quickUserSelect = document.getElementById('selectQuickUser');
        const penerimaInput = document.getElementById('penerima_nama');
        const checkboxes = document.querySelectorAll('.asset-checkbox');
        const countBadge = document.getElementById('selectedCountBadge');
        const summaryText = document.getElementById('selectionSummary');
        const submitBtn = document.getElementById('btnSubmitReceipt');
        const searchInput = document.getElementById('assetSearchInput');
        const assetRows = document.querySelectorAll('.asset-row');

        if (quickUserSelect) {
            quickUserSelect.addEventListener('change', function() {
                if (this.value) {
                    penerimaInput.value = this.value;
                }
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                assetRows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }

        function updateSelectionCount() {
            const selected = Array.from(checkboxes).filter(chk => chk.checked);
            const count = selected.length;
            countBadge.textContent = count + " {{ __('Dipilih') }}";

            if (count > 0) {
                countBadge.className = 'badge-phoenix badge-phoenix-success';
                summaryText.textContent = count + ' devices selected.';
                submitBtn.disabled = false;
            } else {
                countBadge.className = 'badge-phoenix badge-phoenix-primary';
                summaryText.textContent = 'Select at least 1 device to proceed.';
                submitBtn.disabled = true;
            }

            checkboxes.forEach(chk => {
                const row = chk.closest('tr');
                if (chk.checked) {
                    row.classList.add('table-active');
                } else {
                    row.classList.remove('table-active');
                }
            });
        }

        checkboxes.forEach(chk => {
            chk.addEventListener('change', updateSelectionCount);
        });

        assetRows.forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.tagName !== 'INPUT') {
                    const chk = this.querySelector('.asset-checkbox');
                    if (chk) {
                        chk.checked = !chk.checked;
                        updateSelectionCount();
                    }
                }
            });
        });
    });
</script>
@endpush
