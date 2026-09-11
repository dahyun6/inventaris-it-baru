@extends('layout')

@section('title', __('Master Vendor'))

@section('header_actions')
<button type="button" class="btn btn-phoenix-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahVendor">
    <i class="fas fa-plus me-1"></i> {{ __('Tambah Vendor') }}
</button>
@endsection

@section('content')
<style>
    .table-phoenix {
        margin-bottom: 0;
        font-size: 0.835rem;
        width: 100% !important;
        vertical-align: middle;
    }
    .table-phoenix thead th {
        background-color: #f8fafc !important;
        border-bottom: 1px solid var(--phoenix-border-color) !important;
        color: #525b75 !important;
        font-weight: 700 !important;
        font-size: 0.725rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        padding: 0.75rem 1.25rem !important;
    }
    .table-phoenix tbody td {
        border-bottom: 1px solid var(--phoenix-border-color);
        color: var(--phoenix-text-body);
        padding: 0.85rem 1.25rem !important;
    }
    .table-phoenix tbody tr:hover {
        background-color: #f8fafc;
    }
    .search-input-phoenix {
        border: 1px solid var(--phoenix-border-color);
        border-radius: 20px;
        padding: 0.4rem 0.85rem 0.4rem 2rem;
        font-size: 0.8125rem;
        background-color: #f8fafc;
        outline: none;
        width: 240px;
        transition: all 0.2s ease;
    }
    .search-input-phoenix:focus {
        background-color: #ffffff;
        border-color: var(--phoenix-primary);
        box-shadow: 0 0 0 3px rgba(56, 116, 255, 0.15);
    }
    .search-wrapper-phoenix {
        position: relative;
    }
    .search-wrapper-phoenix i {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #8592a3;
        font-size: 0.8rem;
        pointer-events: none;
    }
</style>

<div class="row">
    <div class="col-lg-11 col-xl-10 mx-auto">
        <div class="phoenix-card">
            <div class="phoenix-card-header">
                <div class="search-wrapper-phoenix">
                    <i class="fas fa-search"></i>
                    <input type="text" id="vendorSearch" class="search-input-phoenix" placeholder="{{ __('Cari vendor...') }}">
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-phoenix badge-phoenix-primary">
                        Total: {{ $vendors->count() }} {{ __('Master Vendor') }}
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-phoenix">
                    <thead>
                        <tr>
                            <th width="6%">{{ __('NO') }}</th>
                            <th width="26%">{{ __('NAMA VENDOR') }}</th>
                            <th width="24%">{{ __('KONTAK') }}</th>
                            <th width="24%">{{ __('ALAMAT') }}</th>
                            <th width="10%" class="text-center">{{ __('TOTAL ASET') }}</th>
                            <th width="10%" class="text-center">{{ __('AKSI') }}</th>
                        </tr>
                    </thead>
                    <tbody id="vendorTableBody">
                        @forelse($vendors as $index => $ven)
                        <tr class="vendor-row">
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark vendor-name">{{ $ven->nama_vendor }}</div>
                            </td>
                            <td>
                                @if($ven->telepon || $ven->email)
                                    <div class="d-flex flex-column gap-1">
                                        @if($ven->telepon)
                                            <span class="small text-secondary font-monospace">
                                                <i class="fas fa-phone text-primary me-1" style="font-size: 0.75rem;"></i>{{ $ven->telepon }}
                                            </span>
                                        @endif
                                        @if($ven->email)
                                            <span class="small text-muted font-monospace">
                                                <i class="fas fa-envelope text-info me-1" style="font-size: 0.75rem;"></i>{{ $ven->email }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted fst-italic small">-</span>
                                @endif
                            </td>
                            <td>
                                @if($ven->alamat)
                                    <span class="small text-secondary text-truncate d-inline-block" style="max-width: 250px;" title="{{ $ven->alamat }}">
                                        {{ $ven->alamat }}
                                    </span>
                                @else
                                    <span class="text-muted fst-italic small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge-phoenix badge-phoenix-info font-monospace">
                                    {{ $ven->barangs_count }} UNIT
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-phoenix-secondary py-1 px-2 btn-edit-vendor" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditVendor" 
                                        data-url="{{ route('vendor.update', $ven->id) }}"
                                        data-nama="{{ $ven->nama_vendor }}"
                                        data-alamat="{{ $ven->alamat }}"
                                        data-telepon="{{ $ven->telepon }}"
                                        data-email="{{ $ven->email }}"
                                        title="{{ __('Edit Vendor') }}">
                                        <i class="fas fa-pen text-warning"></i>
                                    </button>
                                    <form action="{{ route('vendor.destroy', $ven->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Hapus vendor ini?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Hapus Data') }}">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-truck-field fs-2 opacity-25 mb-2"></i><br>
                                {{ __('Belum ada vendor terdaftar.') }}
                            </td>
                        </tr>
                        @endforelse
                        
                        <tr id="noVendorFound" style="display: none;">
                            <td colspan="6" class="text-center py-4 text-danger fw-semibold">
                                <i class="fas fa-circle-exclamation me-1"></i> {{ __('Vendor tidak ditemukan.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center" style="font-size: 0.775rem; color: var(--phoenix-text-muted);">
                <span>{{ __('Daftar Vendor & Rekanan IT') }}</span>
                <span class="font-monospace">Registered suppliers & store partners</span>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT VENDOR -->
<div class="modal fade" id="modalEditVendor" tabindex="-1" aria-labelledby="modalEditVendorLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: 1px solid var(--phoenix-border-color); max-width: 480px; margin: auto;">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold" id="modalEditVendorLabel"><i class="fas fa-pen-to-square text-primary me-2"></i>{{ __('Edit Vendor') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditVendor" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold" for="edit_nama_vendor">{{ __('Nama Vendor / Toko') }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_nama_vendor" name="nama_vendor" class="form-control" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold" for="edit_telepon">{{ __('Nomor Telepon / HP') }}</label>
                            <input type="text" id="edit_telepon" name="telepon" class="form-control font-monospace" placeholder="0812...">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold" for="edit_email">{{ __('Alamat Email') }}</label>
                            <input type="email" id="edit_email" name="email" class="form-control font-monospace" placeholder="vendor@domain.com">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold" for="edit_alamat">{{ __('Alamat Kantor / Toko') }}</label>
                        <textarea id="edit_alamat" name="alamat" class="form-control" rows="2" placeholder="Jl..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-phoenix-secondary btn-sm" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-phoenix-primary btn-sm px-3"><i class="fas fa-save me-1"></i> {{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH VENDOR -->
<div class="modal fade" id="modalTambahVendor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: 1px solid var(--phoenix-border-color); max-width: 480px; margin: auto;">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold"><i class="fas fa-circle-plus text-primary me-2"></i>{{ __('Tambah Vendor') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('vendor.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">{{ __('Nama Vendor / Toko') }} <span class="text-danger">*</span></label>
                        <input type="text" name="nama_vendor" class="form-control" placeholder="Contoh: PT. Sumber Berkat, Bhinneka..." required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">{{ __('Nomor Telepon / HP') }}</label>
                            <input type="text" name="telepon" class="form-control font-monospace" placeholder="0812...">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">{{ __('Alamat Email') }}</label>
                            <input type="email" name="email" class="form-control font-monospace" placeholder="vendor@domain.com">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">{{ __('Alamat Kantor / Toko') }}</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Jl..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-phoenix-secondary btn-sm" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-phoenix-primary btn-sm px-3"><i class="fas fa-save me-1"></i> {{ __('Simpan') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const vendorSearch = document.getElementById('vendorSearch');
        const rows = document.querySelectorAll('.vendor-row');
        const noVendorFound = document.getElementById('noVendorFound');

        document.querySelectorAll('.btn-edit-vendor').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = document.getElementById('formEditVendor');
                form.action = this.dataset.url;
                document.getElementById('edit_nama_vendor').value = this.dataset.nama || '';
                document.getElementById('edit_telepon').value = this.dataset.telepon || '';
                document.getElementById('edit_email').value = this.dataset.email || '';
                document.getElementById('edit_alamat').value = this.dataset.alamat || '';
            });
        });

        if (vendorSearch) {
            vendorSearch.addEventListener('input', function() {
                const term = this.value.toLowerCase().trim();
                let visibleRows = 0;

                rows.forEach(row => {
                    const name = row.querySelector('.vendor-name').textContent.toLowerCase();
                    if (name.includes(term)) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                noVendorFound.style.display = (visibleRows === 0 && rows.length > 0) ? '' : 'none';
            });
        }
    });
</script>
@endpush
