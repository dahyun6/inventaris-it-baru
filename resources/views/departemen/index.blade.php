@extends('layout')

@section('title', __('Master Departemen'))

@section('header_actions')
<button type="button" class="btn btn-phoenix-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahDepartemen">
    <i class="fas fa-plus me-1"></i> {{ __('Tambah Departemen') }}
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
    <div class="col-lg-10 col-xl-9 mx-auto">
        <div class="phoenix-card">
            <div class="phoenix-card-header">
                <div class="search-wrapper-phoenix">
                    <i class="fas fa-search"></i>
                    <input type="text" id="departemenSearch" class="search-input-phoenix" placeholder="{{ __('Cari departemen...') }}">
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-phoenix badge-phoenix-primary">
                        Total: {{ $departemens->count() }} {{ __('Departemen') }}
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-phoenix">
                    <thead>
                        <tr>
                            <th width="8%">{{ __('NO') }}</th>
                            <th width="42%">{{ __('NAMA DEPARTEMEN') }}</th>
                            <th width="20%" class="text-center">{{ __('TOTAL USER') }}</th>
                            <th width="15%" class="text-center">{{ __('TOTAL ASET') }}</th>
                            <th width="15%" class="text-center">{{ __('AKSI') }}</th>
                        </tr>
                    </thead>
                    <tbody id="departemenTableBody">
                        @forelse($departemens as $index => $dept)
                        <tr class="departemen-row">
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem; font-weight: 700;">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <span class="fw-bold text-dark departemen-name">{{ $dept->nama_departemen }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge-phoenix badge-phoenix-info font-monospace">
                                    {{ $dept->users_count }} USER
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge-phoenix badge-phoenix-secondary font-monospace">
                                    {{ $dept->barangs_count }} UNIT
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-phoenix-secondary py-1 px-2 btn-edit-dept" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditDepartemen" 
                                        data-url="{{ route('departemen.update', $dept->id) }}"
                                        data-nama="{{ $dept->nama_departemen }}"
                                        title="{{ __('Edit Data') }}">
                                        <i class="fas fa-pen text-warning"></i>
                                    </button>
                                    <form action="{{ route('departemen.destroy', $dept->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Hapus departemen ini?') }}')">
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
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-building-circle-xmark fs-2 opacity-25 mb-2"></i><br>
                                {{ __('Belum ada data departemen terdaftar.') }}
                            </td>
                        </tr>
                        @endforelse
                        
                        <tr id="noDepartemenFound" style="display: none;">
                            <td colspan="5" class="text-center py-4 text-danger fw-semibold">
                                <i class="fas fa-circle-exclamation me-1"></i> {{ __('Departemen tidak ditemukan.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center" style="font-size: 0.775rem; color: var(--phoenix-text-muted);">
                <span>{{ __('Master Data Departemen & Divisi Perusahaan') }}</span>
                <span class="font-monospace">Total: {{ $departemens->count() }}</span>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT DEPARTEMEN -->
<div class="modal fade" id="modalEditDepartemen" tabindex="-1" aria-labelledby="modalEditDepartemenLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: 1px solid var(--phoenix-border-color); max-width: 440px; margin: auto;">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold" id="modalEditDepartemenLabel"><i class="fas fa-building-pen text-primary me-2"></i>{{ __('Edit Departemen') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditDepartemen" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold" for="edit_nama_departemen">{{ __('Nama Departemen') }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_nama_departemen" name="nama_departemen" class="form-control" placeholder="Contoh: IT, HRD, Finance, Warehouse..." required>
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

<!-- MODAL TAMBAH DEPARTEMEN -->
<div class="modal fade" id="modalTambahDepartemen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: 1px solid var(--phoenix-border-color); max-width: 440px; margin: auto;">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold"><i class="fas fa-building-circle-plus text-primary me-2"></i>{{ __('Tambah Departemen') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('departemen.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">{{ __('Nama Departemen') }} <span class="text-danger">*</span></label>
                        <input type="text" name="nama_departemen" class="form-control" placeholder="Contoh: IT, HRD, Finance, Warehouse..." required>
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
        const departemenSearch = document.getElementById('departemenSearch');
        const rows = document.querySelectorAll('.departemen-row');
        const noDepartemenFound = document.getElementById('noDepartemenFound');

        document.querySelectorAll('.btn-edit-dept').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = document.getElementById('formEditDepartemen');
                form.action = this.dataset.url;
                document.getElementById('edit_nama_departemen').value = this.dataset.nama || '';
            });
        });

        if (departemenSearch) {
            departemenSearch.addEventListener('input', function() {
                const term = this.value.toLowerCase().trim();
                let visibleRows = 0;

                rows.forEach(row => {
                    const name = row.querySelector('.departemen-name').textContent.toLowerCase();
                    if (name.includes(term)) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                noDepartemenFound.style.display = (visibleRows === 0 && rows.length > 0) ? '' : 'none';
            });
        }
    });
</script>
@endpush
