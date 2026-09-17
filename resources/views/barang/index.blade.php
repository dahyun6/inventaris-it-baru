@extends('layout')

@section('title', __('Master Data Inventaris IT'))

@section('header_actions')
@if(!Auth::user()?->isStaff())
<div class="d-flex flex-wrap gap-2">
    <a href="{{ route('barang.barcode.batch') }}" target="_blank" class="btn btn-phoenix-secondary btn-sm" title="{{ __('Cetak Label QR Code Seluruh Aset / Batch') }}">
        <i class="fas fa-qrcode text-primary me-1"></i> {{ __('Cetak QR Code') }}
    </a>
    <a href="{{ route('barang.template') }}" class="btn btn-phoenix-secondary btn-sm" title="{{ __('Unduh file mentahan / format template Excel') }}">
        <i class="fas fa-file-arrow-down text-primary me-1"></i> {{ __('Template Excel') }}
    </a>
    <button class="btn btn-phoenix-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalImport">
        <i class="fas fa-file-excel text-success me-1"></i> {{ __('Import Excel') }}
    </button>
    <button class="btn btn-phoenix-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fas fa-plus me-1"></i> {{ __('Entry Aset Baru') }}
    </button>
</div>
@endif
@endsection

@section('content')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<style>
    /* Phoenix Filter Panel */
    .phoenix-filter-panel {
        background: #ffffff;
        border: 1px solid var(--phoenix-border-color);
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .filter-label {
        font-size: 0.72rem;
        font-weight: 800;
        color: var(--phoenix-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.4rem;
        display: block;
    }
    .filter-input {
        font-size: 0.8125rem;
        padding: 0.45rem 0.75rem;
        border-radius: 8px;
        border: 1px solid var(--phoenix-border-color);
        width: 100%;
        background-color: #f8fafc;
        color: var(--phoenix-text-body);
        transition: all 0.2s ease;
    }
    .filter-input:focus {
        background-color: #ffffff;
        border-color: var(--phoenix-primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(56, 116, 255, 0.15);
    }

    /* Phoenix Modern Table */
    .table-phoenix {
        margin-bottom: 0;
        font-size: 0.825rem;
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
        padding: 0.75rem 1rem !important;
        white-space: nowrap;
    }
    .table-phoenix tbody td {
        border-bottom: 1px solid var(--phoenix-border-color);
        color: var(--phoenix-text-body);
        padding: 0.75rem 1rem !important;
        white-space: nowrap;
    }
    .table-phoenix tbody tr:hover {
        background-color: #f8fafc;
    }

    .form-section-divider {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--phoenix-primary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 1.25rem;
        margin-bottom: 0.75rem;
        padding-bottom: 0.35rem;
        border-bottom: 1px solid var(--phoenix-border-color);
    }
    .form-section-divider:first-child,
    .form-section-divider:first-of-type {
        margin-top: 0;
    }
    .modal-phoenix textarea.form-control {
        resize: vertical;
        min-height: 60px;
    }

    .modal-phoenix .modal-content {
        border-radius: 12px;
        border: 1px solid var(--phoenix-border-color);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .modal-phoenix .modal-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--phoenix-border-color);
    }
    .modal-phoenix .modal-body {
        padding: 1.5rem;
    }
    .modal-phoenix .modal-footer {
        padding: 0.85rem 1.5rem;
        border-top: 1px solid var(--phoenix-border-color);
        background-color: #f8fafc;
    }

    div.dataTables_wrapper div.dataTables_length select {
        width: 75px;
        display: inline-block;
        padding: 0.35rem 0.6rem;
        border-radius: 6px;
        border: 1px solid var(--phoenix-border-color);
        font-size: 0.8125rem;
    }
    div.dataTables_wrapper div.dataTables_filter input {
        border-radius: 20px;
        border: 1px solid var(--phoenix-border-color);
        padding: 0.35rem 0.85rem;
        font-size: 0.8125rem;
    }
</style>

@php
    // Ambil Data Unik untuk Filter
    $filterJenis = $barangs->pluck('category.nama_kategori')->unique()->filter()->sort();
    $filterModel = $barangs->pluck('model')->unique()->filter()->sort();
    $filterVendor = $barangs->pluck('vendor')->unique()->filter()->sort();
    $filterDept = $barangs->pluck('dept')->unique()->filter()->sort();
    $filterLoc = $barangs->pluck('unit_loc')->unique()->filter()->sort();
@endphp

<!-- FILTER CONTROL PANEL -->
<div class="phoenix-filter-panel">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge-phoenix badge-phoenix-primary"><i class="fas fa-filter"></i> Filters</span>
            <span class="fw-bold text-dark" style="font-size: 0.875rem;">{{ __('Saring & Cari Data Aset') }}</span>
        </div>
        <button type="button" id="btnResetAllFilters" class="btn btn-phoenix-secondary btn-sm py-1 px-2" style="font-size: 0.775rem;">
            <i class="fas fa-rotate-left me-1 text-danger"></i> {{ __('Reset Filter') }}
        </button>
    </div>

    <div class="row g-3">
        <div class="col-6 col-md-4 col-xl-2">
            <label class="filter-label">{{ __('Jenis Perangkat') }}</label>
            <select id="selectFilterJenis" class="filter-input">
                <option value="">-- {{ __('Semua') }} --</option>
                @foreach($filterJenis as $jenis) <option value="{{ $jenis }}">{{ $jenis }}</option> @endforeach
            </select>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <label class="filter-label">{{ __('Model / Brand') }}</label>
            <select id="selectFilterModel" class="filter-input">
                <option value="">-- {{ __('Semua') }} --</option>
                @foreach($filterModel as $model) <option value="{{ $model }}">{{ $model }}</option> @endforeach
            </select>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <label class="filter-label">{{ __('Vendor / Toko') }}</label>
            <select id="selectFilterVendor" class="filter-input">
                <option value="">-- {{ __('Semua') }} --</option>
                @foreach($filterVendor as $vendor) <option value="{{ $vendor }}">{{ $vendor }}</option> @endforeach
            </select>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <label class="filter-label">{{ __('Departemen (Dept)') }}</label>
            <select id="selectFilterDept" class="filter-input">
                <option value="">-- {{ __('Semua') }} --</option>
                @foreach($filterDept as $dept) <option value="{{ $dept }}">{{ $dept }}</option> @endforeach
            </select>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <label class="filter-label">{{ __('Unit Lokasi') }}</label>
            <select id="selectFilterLoc" class="filter-input">
                <option value="">-- {{ __('Semua') }} --</option>
                @foreach($filterLoc as $loc) <option value="{{ $loc }}">{{ $loc }}</option> @endforeach
            </select>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <label class="filter-label">{{ __('Tanggal Beli') }}</label>
            <input type="date" id="inputFilterBuyDate" class="filter-input">
        </div>
    </div>
</div>

<!-- MAIN ASSET TABLE CARD -->
<div class="phoenix-card">
    <div class="phoenix-card-header">
        <div>
            <h6 class="phoenix-card-title">
                <i class="fas fa-server text-primary"></i>
                {{ __('Daftar Inventaris Perangkat IT') }}
            </h6>
            <small class="text-muted">{{ __('Total terdaftar:') }} <strong>{{ $barangs->count() }}</strong> {{ __('aset hardware') }}</small>
        </div>
        <div>
            <span class="badge-phoenix badge-phoenix-primary font-monospace">{{ $barangs->where('status', 'Tersedia')->count() }} {{ __('Siap Pakai') }}</span>
        </div>
    </div>

    <div class="phoenix-card-body p-0">
        <div class="table-responsive p-3">
            <table id="tableInventaris" class="table table-phoenix w-100">
                <thead>
                    <tr>
                        <th width="3%">{{ __('NO') }}</th>
                        <th>{{ __('NO ASET LOCAL') }}</th>
                        <th>{{ __('JENIS') }}</th>
                        <th>{{ __('MODEL') }}</th>
                        <th>{{ __('SPESIFIKASI') }}</th>
                        <th>{{ __('SERIAL NUMBER') }}</th>
                        <th>{{ __('HOSTNAME') }}</th>
                        <th>{{ __('DEPT') }}</th>
                        <th>{{ __('PENGGUNA') }}</th>
                        <th>{{ __('LOKASI') }}</th>
                        <th>{{ __('VENDOR') }}</th>
                        <th>{{ __('TGL BELI') }}</th>
                        <th>{{ __('CATATAN') }}</th>
                        <th class="text-center">{{ __('STATUS') }}</th>
                        <th class="text-center">{{ __('AKSI') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($barangs as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="font-monospace fw-bold text-dark">{{ $item->no_aset_local ?? '-' }}</td>
                        <td class="fw-semibold">{{ $item->category->nama_kategori ?? '-' }}</td>
                        <td>{{ $item->model }}</td>
                        <td>{{ Str::limit($item->type_spec ?? '-', 30) }}</td>
                        <td class="font-monospace text-muted">{{ $item->serial_number ?? '-' }}</td>
                        <td class="font-monospace">{{ $item->hostname ?? '-' }}</td>
                        <td>{{ $item->dept ?? '-' }}</td>
                        <td>
                            {{ $item->pengguna ?? '-' }}
                            @if($item->position_user) <br><small class="text-muted">{{ $item->position_user }}</small> @endif
                        </td>
                        <td>{{ $item->unit_loc ?? '-' }}</td>
                        <td>{{ $item->vendor ?? '-' }}</td>
                        <td class="font-monospace">{{ $item->buy_date ? \Carbon\Carbon::parse($item->buy_date)->format('d-m-Y') : '-' }}</td>
                        <td>{{ Str::limit($item->note ?? '-', 30) }}</td>
                        <td class="text-center">
                            @if($item->status == 'Tersedia') 
                                <span class="badge-phoenix badge-phoenix-success"><i class="fas fa-check"></i> {{ __('Tersedia') }}</span>
                            @elseif($item->status == 'Dipinjam') 
                                <span class="badge-phoenix badge-phoenix-warning"><i class="fas fa-user-clock"></i> {{ __('Dipinjam') }}</span>
                            @else 
                                <span class="badge-phoenix badge-phoenix-danger"><i class="fas fa-triangle-exclamation"></i> {{ __('Rusak') }}</span> 
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('barang.show', $item->uuid) }}" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Detail & History') }}">
                                    <i class="fas fa-eye text-primary"></i>
                                </a>
                                <a href="{{ route('barang.barcode', $item->uuid) }}" target="_blank" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Cetak Label QR Code Aset') }}">
                                    <i class="fas fa-qrcode text-secondary"></i>
                                </a>
                                @if(!Auth::user()?->isStaff())
                                <button type="button" class="btn btn-phoenix-secondary py-1 px-2 btn-edit-barang" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditBarang" 
                                    data-url="{{ route('barang.update', $item->uuid) }}"
                                    data-no-aset="{{ $item->no_aset_local }}"
                                    data-category-id="{{ $item->category_id }}"
                                    data-model="{{ $item->model }}"
                                    data-serial-number="{{ $item->serial_number }}"
                                    data-hostname="{{ $item->hostname }}"
                                    data-status="{{ $item->status }}"
                                    data-type-spec="{{ $item->type_spec }}"
                                    data-pengguna="{{ $item->pengguna }}"
                                    data-position-user="{{ $item->position_user }}"
                                    data-dept="{{ $item->dept }}"
                                    data-unit-loc="{{ $item->unit_loc }}"
                                    data-buy-date="{{ $item->buy_date }}"
                                    data-vendor="{{ $item->vendor }}"
                                    data-note="{{ $item->note }}"
                                    title="{{ __('Edit Data') }}">
                                    <i class="fas fa-pen text-warning"></i>
                                </button>
                                
                                <form action="{{ route('barang.destroy', $item->uuid) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Hapus aset ini secara permanen?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Hapus Data') }}">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(!Auth::user()?->isStaff())
<!-- MODAL EDIT DATA ASET -->
<div class="modal fade modal-phoenix" id="modalEditBarang" tabindex="-1" aria-labelledby="modalEditBarangLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold" id="modalEditBarangLabel"><i class="fas fa-pen-to-square text-primary me-2"></i>{{ __('Update Data Aset') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditBarang" action="" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-section-divider">1. {{ __('Klasifikasi & Identitas') }}</div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="edit_no_aset_local">{{ __('No Aset Local') }}</label>
                            <input type="text" id="edit_no_aset_local" name="no_aset_local" class="form-control font-monospace bg-light text-muted" readonly style="cursor: not-allowed;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger" for="edit_category_id">{{ __('Jenis / Kategori') }} *</label>
                            <select id="edit_category_id" name="category_id" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger" for="edit_model">{{ __('Model Perangkat') }} *</label>
                            <input type="text" id="edit_model" name="model" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="edit_serial_number">{{ __('Serial Number (SN)') }}</label>
                            <input type="text" id="edit_serial_number" name="serial_number" class="form-control font-monospace">
                        </div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="edit_hostname">{{ __('Hostname PC/Laptop') }}</label>
                            <input type="text" id="edit_hostname" name="hostname" class="form-control font-monospace">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger" for="edit_status">{{ __('Status Kondisi') }} *</label>
                            <select id="edit_status" name="status" class="form-select" required>
                                <option value="Tersedia">{{ __('Tersedia') }}</option>
                                <option value="Dipinjam">{{ __('Dipinjam') }}</option>
                                <option value="Rusak">{{ __('Rusak') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold" for="edit_type_spec">{{ __('Spesifikasi (Type Spec)') }}</label>
                        <textarea id="edit_type_spec" name="type_spec" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-section-divider">2. {{ __('Distribusi & Pengguna') }}</div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="edit_pengguna">{{ __('Nama Pengguna') }}</label>
                            <div class="input-group">
                                <input type="text" id="edit_pengguna" name="pengguna" class="form-control" placeholder="Nama karyawan / pemakai...">
                                <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" title="{{ __('Pilih User') }}">
                                    <i class="fas fa-users text-primary"></i>
                                    <span>{{ __('Pilih User') }}</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm p-2" style="width: 320px; max-width: 90vw;">
                                    <div class="p-1 mb-2 border-bottom pb-2">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-search" style="font-size: 0.75rem;"></i></span>
                                            <input type="text" class="form-control border-start-0 user-search-input" placeholder="{{ __('Cari nama / email...') }}" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="user-list-scroll" style="max-height: 200px; overflow-y: auto;">
                                        <ul class="list-unstyled mb-0 user-items-list">
                                            @foreach($users as $u)
                                                <li class="user-search-item mb-1">
                                                    <a class="dropdown-item py-1.5 px-2 rounded-2 btn-select-edit-user text-wrap" href="javascript:void(0)" 
                                                        data-name="{{ $u->name }}"
                                                        data-dept="{{ $u->departemen?->nama_departemen ?? '' }}"
                                                        data-lokasi="{{ $u->lokasi?->nama_lokasi ?? '' }}"
                                                        data-search="{{ strtolower($u->name . ' ' . $u->email . ' ' . ($u->departemen?->nama_departemen ?? '') . ' ' . ($u->lokasi?->nama_lokasi ?? '')) }}">
                                                        <div class="fw-bold text-dark lh-sm" style="font-size: 0.8125rem;">{{ $u->name }}</div>
                                                        <div class="text-muted small lh-sm d-flex align-items-center gap-1 mt-0.5 flex-wrap" style="font-size: 0.725rem;">
                                                            @if($u->departemen)
                                                                <span class="badge bg-light text-primary border" style="font-size: 0.675rem;">
                                                                    <i class="fas fa-building me-1 opacity-75"></i>{{ $u->departemen->nama_departemen }}
                                                                </span>
                                                            @endif
                                                            @if($u->lokasi)
                                                                <span class="badge bg-light text-secondary border" style="font-size: 0.675rem;">
                                                                    <i class="fas fa-location-dot me-1 text-primary opacity-75"></i>{{ $u->lokasi->nama_lokasi }}
                                                                </span>
                                                            @endif
                                                            <span class="text-truncate"><i class="fas fa-envelope-open-text me-1 opacity-50"></i>{{ $u->email }}</span>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <div class="user-no-result text-center text-muted py-3 small d-none">
                                            <i class="fas fa-user-slash me-1 opacity-50"></i> {{ __('User tidak ditemukan') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="edit_position_user">{{ __('Jabatan (Position User)') }}</label>
                            <input type="text" id="edit_position_user" name="position_user" class="form-control">
                        </div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="edit_dept">{{ __('Departemen') }}</label>
                            <input type="text" id="edit_dept" name="dept" class="form-control bg-light" readonly style="cursor: not-allowed;" placeholder="{{ __('Otomatis terisi dari User...') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="edit_unit_loc">{{ __('Lokasi Unit (Unit Loc)') }}</label>
                            <select id="edit_unit_loc" name="unit_loc" class="form-select">
                                <option value="">-- {{ __('Pilih Lokasi Unit') }} --</option>
                                @foreach($lokasis as $lok)
                                    <option value="{{ $lok->nama_lokasi }}">{{ $lok->nama_lokasi }} {{ $lok->kode_lokasi ? '('.$lok->kode_lokasi.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-section-divider">3. {{ __('Pembelian & Catatan') }}</div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="edit_buy_date">{{ __('Tanggal Beli (Buy Date)') }}</label>
                            <input type="date" id="edit_buy_date" name="buy_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="edit_vendor">{{ __('Vendor / Toko') }}</label>
                            <select id="edit_vendor" name="vendor" class="form-select">
                                <option value="">-- {{ __('Pilih Vendor') }} --</option>
                                @foreach($vendors as $ven)
                                    <option value="{{ $ven->nama_vendor }}">{{ $ven->nama_vendor }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold" for="edit_note">{{ __('Catatan Internal (Note)') }}</label>
                        <textarea id="edit_note" name="note" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-phoenix-secondary btn-sm" data-bs-dismiss="modal">{{ __('Tutup') }}</button>
                    <button type="submit" class="btn btn-phoenix-primary btn-sm px-4"><i class="fas fa-save me-1"></i> {{ __('Update Data') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL IMPORT EXCEL -->
<div class="modal fade modal-phoenix" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="fas fa-file-excel text-success me-2"></i>{{ __('Import Data Aset dari Excel') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('barang.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- File Mentahan / Template Download Card -->
                    <div class="p-3 mb-3 rounded-3" style="background-color: var(--phoenix-primary-subtle); border: 1px dashed var(--phoenix-primary-border);">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-dark"><i class="fas fa-file-arrow-down text-primary me-1"></i> {{ __('File Mentahan / Format Template') }}</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size: 0.65rem;">.XLSX</span>
                        </div>
                        <p class="text-muted small mb-2" style="font-size: 0.775rem; line-height: 1.4;">
                            {{ __('Gunakan file template resmi agar susunan kolom sesuai dengan pembaca database.') }}
                        </p>
                        <a href="{{ route('barang.template') }}" class="btn btn-phoenix-primary btn-sm w-100 fw-bold py-1.5" style="font-size: 0.8125rem;">
                            <i class="fas fa-download me-1 text-white"></i> {{ __('Download File Mentahan (.xlsx)') }}
                        </a>
                    </div>

                    <!-- Guidelines -->
                    <div class="mb-3">
                        <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                            <i class="fas fa-circle-info text-info me-1"></i> {{ __('Petunjuk Pengisian File:') }}
                        </div>
                        <ul class="text-muted small ps-3 mb-0" style="font-size: 0.75rem; line-height: 1.5;">
                            <li>Kolom <strong>model</strong> wajib diisi (misal: <em>ThinkPad T14</em>).</li>
                            <li>Kolom <strong>jenis</strong> akan otomatis masuk ke Kategori (misal: <em>Laptop</em>).</li>
                            <li>Format <strong>buy_date</strong> gunakan format tanggal standar (contoh: <code>2024-05-20</code>).</li>
                            <li>Kolom <strong>no_aset_local</strong> boleh dikosongkan agar dibuat otomatis oleh sistem.</li>
                        </ul>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold" for="file_excel_input">{{ __('Pilih File Excel yang Sudah Diisi') }} <span class="text-danger">*</span></label>
                        <input type="file" id="file_excel_input" name="file_excel" class="form-control" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-phoenix-secondary btn-sm" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 text-white"><i class="fas fa-cloud-arrow-up me-1"></i> {{ __('Mulai Import') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH ASET BARU -->
<div class="modal fade modal-phoenix" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="fas fa-circle-plus text-primary me-2"></i>{{ __('Entry Aset Baru') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('barang.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-section-divider">1. {{ __('Klasifikasi & Identitas') }}</div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('No Aset Local') }}</label>
                            <input type="text" id="no_aset_tambah" name="no_aset_local" class="form-control font-monospace bg-light text-muted" readonly placeholder="Auto..." style="cursor: not-allowed;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger">{{ __('Jenis / Kategori') }} *</label>
                            <select id="kategori_tambah" name="category_id" class="form-select" required>
                                <option value="">-- {{ __('Semua') }} --</option>
                                @foreach($categories as $cat) 
                                    <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option> 
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger">{{ __('Model Perangkat') }} *</label>
                            <input type="text" name="model" class="form-control" placeholder="ThinkPad, Dell..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Serial Number (SN)') }}</label>
                            <input type="text" name="serial_number" class="form-control font-monospace" placeholder="PF3X4920">
                        </div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Hostname PC/Laptop') }}</label>
                            <input type="text" name="hostname" class="form-control font-monospace" placeholder="LAP-MKT-01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger">{{ __('Status Kondisi') }} *</label>
                            <select name="status" class="form-select" required>
                                <option value="Tersedia">{{ __('Tersedia') }}</option>
                                <option value="Dipinjam">{{ __('Dipinjam') }}</option>
                                <option value="Rusak">{{ __('Rusak') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">{{ __('Spesifikasi (Type Spec)') }}</label>
                        <textarea name="type_spec" class="form-control" rows="2" placeholder="Core i5, 16GB RAM..."></textarea>
                    </div>

                    <div class="form-section-divider">2. {{ __('Distribusi & Pengguna') }}</div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Nama Pengguna') }}</label>
                            <div class="input-group">
                                <input type="text" name="pengguna" id="tambah_pengguna" class="form-control" placeholder="Nama karyawan / pemakai...">
                                <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" title="{{ __('Pilih User') }}">
                                    <i class="fas fa-users text-primary"></i>
                                    <span>{{ __('Pilih User') }}</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm p-2" style="width: 320px; max-width: 90vw;">
                                    <div class="p-1 mb-2 border-bottom pb-2">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fas fa-search" style="font-size: 0.75rem;"></i></span>
                                            <input type="text" class="form-control border-start-0 user-search-input" placeholder="{{ __('Cari nama / email...') }}" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="user-list-scroll" style="max-height: 200px; overflow-y: auto;">
                                        <ul class="list-unstyled mb-0 user-items-list">
                                            @foreach($users as $u)
                                                <li class="user-search-item mb-1">
                                                    <a class="dropdown-item py-1.5 px-2 rounded-2 btn-select-tambah-user text-wrap" href="javascript:void(0)" 
                                                        data-name="{{ $u->name }}"
                                                        data-dept="{{ $u->departemen?->nama_departemen ?? '' }}"
                                                        data-lokasi="{{ $u->lokasi?->nama_lokasi ?? '' }}"
                                                        data-search="{{ strtolower($u->name . ' ' . $u->email . ' ' . ($u->departemen?->nama_departemen ?? '') . ' ' . ($u->lokasi?->nama_lokasi ?? '')) }}">
                                                        <div class="fw-bold text-dark lh-sm" style="font-size: 0.8125rem;">{{ $u->name }}</div>
                                                        <div class="text-muted small lh-sm d-flex align-items-center gap-1 mt-0.5 flex-wrap" style="font-size: 0.725rem;">
                                                            @if($u->departemen)
                                                                <span class="badge bg-light text-primary border" style="font-size: 0.675rem;">
                                                                    <i class="fas fa-building me-1 opacity-75"></i>{{ $u->departemen->nama_departemen }}
                                                                </span>
                                                            @endif
                                                            @if($u->lokasi)
                                                                <span class="badge bg-light text-secondary border" style="font-size: 0.675rem;">
                                                                    <i class="fas fa-location-dot me-1 text-primary opacity-75"></i>{{ $u->lokasi->nama_lokasi }}
                                                                </span>
                                                            @endif
                                                            <span class="text-truncate"><i class="fas fa-envelope-open-text me-1 opacity-50"></i>{{ $u->email }}</span>
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <div class="user-no-result text-center text-muted py-3 small d-none">
                                            <i class="fas fa-user-slash me-1 opacity-50"></i> {{ __('User tidak ditemukan') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Jabatan (Position User)') }}</label>
                            <input type="text" name="position_user" class="form-control">
                        </div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Departemen') }}</label>
                            <input type="text" id="tambah_dept" name="dept" class="form-control bg-light" readonly style="cursor: not-allowed;" placeholder="{{ __('Otomatis terisi dari User...') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="tambah_unit_loc">{{ __('Lokasi Unit (Unit Loc)') }}</label>
                            <select id="tambah_unit_loc" name="unit_loc" class="form-select">
                                <option value="">-- {{ __('Pilih Lokasi Unit') }} --</option>
                                @foreach($lokasis as $lok)
                                    <option value="{{ $lok->nama_lokasi }}">{{ $lok->nama_lokasi }} {{ $lok->kode_lokasi ? '('.$lok->kode_lokasi.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-section-divider">3. {{ __('Pembelian & Catatan') }}</div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Tanggal Beli (Buy Date)') }}</label>
                            <input type="date" name="buy_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Vendor / Toko') }}</label>
                            <select name="vendor" class="form-select">
                                <option value="">-- {{ __('Pilih Vendor') }} --</option>
                                @foreach($vendors as $ven)
                                    <option value="{{ $ven->nama_vendor }}">{{ $ven->nama_vendor }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-section-divider">4. {{ __('Jadwal Preventive Maintenance') }}</div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Interval Servis Berkala') }}</label>
                            <select name="interval_maintenance" class="form-select">
                                <option value="0">-- {{ __('Tanpa Jadwal Rutin') }} --</option>
                                <option value="3">{{ __('Setiap 3 Bulan (Kuartalan)') }}</option>
                                <option value="6">{{ __('Setiap 6 Bulan (Semester)') }}</option>
                                <option value="12">{{ __('Setiap 12 Bulan (Tahunan)') }}</option>
                                <option value="24">{{ __('Setiap 24 Bulan (2 Tahun)') }}</option>
                            </select>
                            <div class="form-text" style="font-size: 0.725rem;">{{ __('Sistem otomatis menghitung jadwal servis setelah maintenance.') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Jadwal Maintenance Pertama / Berikutnya') }}</label>
                            <input type="date" name="tgl_maintenance_berikutnya" class="form-control">
                            <div class="form-text" style="font-size: 0.725rem;">{{ __('Bisa ditentukan manual atau terisi otomatis dari interval.') }}</div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold">{{ __('Catatan Tambahan') }}</label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-phoenix-secondary btn-sm" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-phoenix-primary btn-sm px-4"><i class="fas fa-save me-1"></i> {{ __('Simpan Data') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event delegation for dynamic edit modal
        $(document).on('click', '.btn-edit-barang', function() {
            const btn = $(this);
            const form = $('#formEditBarang');
            
            form.attr('action', btn.data('url'));
            $('#edit_no_aset_local').val(btn.data('no-aset') || '');
            $('#edit_category_id').val(btn.data('category-id') || '');
            $('#edit_model').val(btn.data('model') || '');
            $('#edit_serial_number').val(btn.data('serial-number') || '');
            $('#edit_hostname').val(btn.data('hostname') || '');
            $('#edit_status').val(btn.data('status') || 'Tersedia');
            $('#edit_type_spec').val(btn.data('type-spec') || '');
            $('#edit_pengguna').val(btn.data('pengguna') || '');
            $('#edit_position_user').val(btn.data('position-user') || '');
            $('#edit_dept').val(btn.data('dept') || '');
            
            const unitLocVal = btn.data('unit-loc') || '';
            const editUnitLocSelect = $('#edit_unit_loc');
            if (unitLocVal && editUnitLocSelect.find(`option[value="${unitLocVal}"]`).length === 0) {
                editUnitLocSelect.append(new Option(unitLocVal, unitLocVal, true, true));
            }
            editUnitLocSelect.val(unitLocVal);

            $('#edit_buy_date').val(btn.data('buy-date') || '');
            
            const vendorVal = btn.data('vendor') || '';
            const editVendorSelect = $('#edit_vendor');
            if (vendorVal && editVendorSelect.find(`option[value="${vendorVal}"]`).length === 0) {
                editVendorSelect.append(new Option(vendorVal, vendorVal, true, true));
            }
            editVendorSelect.val(vendorVal);

            $('#edit_note').val(btn.data('note') || '');
        });

        // Live Search Filtering for User Picker Dropdown
        $(document).on('input', '.user-search-input', function() {
            const query = $(this).val().toLowerCase().trim();
            const dropdownMenu = $(this).closest('.dropdown-menu');
            const items = dropdownMenu.find('.user-search-item');
            const noResult = dropdownMenu.find('.user-no-result');
            let matched = 0;

            items.each(function() {
                const searchStr = $(this).find('a').data('search') || '';
                if (searchStr.includes(query)) {
                    $(this).show();
                    matched++;
                } else {
                    $(this).hide();
                }
            });

            if (matched === 0) {
                noResult.removeClass('d-none');
            } else {
                noResult.addClass('d-none');
            }
        });

        // Auto-focus search input and reset filter when dropdown is opened
        $(document).on('shown.bs.dropdown', function(e) {
            const dropdownMenu = $(e.target).find('.dropdown-menu');
            if (dropdownMenu.length) {
                const searchInput = dropdownMenu.find('.user-search-input');
                if (searchInput.length) {
                    searchInput.val('').trigger('input');
                    setTimeout(() => searchInput.focus(), 60);
                }
            }
        });

        // Helper to close dropdown after selecting user
        function closeUserDropdown(element) {
            const dropdownToggle = $(element).closest('.input-group').find('[data-bs-toggle="dropdown"]')[0];
            if (dropdownToggle) {
                const instance = bootstrap.Dropdown.getInstance(dropdownToggle) || new bootstrap.Dropdown(dropdownToggle);
                if (instance) instance.hide();
            }
        }

        // User Picker Selection for Tambah & Edit
        $(document).on('click', '.btn-select-tambah-user', function(e) {
            e.preventDefault();
            const name = $(this).data('name') || '';
            const dept = $(this).data('dept') || '';
            const lokasi = $(this).data('lokasi') || '';
            $('#tambah_pengguna').val(name);
            $('#tambah_dept').val(dept);
            if (lokasi) {
                if ($('#tambah_unit_loc').find(`option[value="${lokasi}"]`).length === 0) {
                    $('#tambah_unit_loc').append(new Option(lokasi, lokasi, true, true));
                }
                $('#tambah_unit_loc').val(lokasi);
            }
            closeUserDropdown(this);
        });
        $(document).on('click', '.btn-select-edit-user', function(e) {
            e.preventDefault();
            const name = $(this).data('name') || '';
            const dept = $(this).data('dept') || '';
            const lokasi = $(this).data('lokasi') || '';
            $('#edit_pengguna').val(name);
            $('#edit_dept').val(dept);
            if (lokasi) {
                if ($('#edit_unit_loc').find(`option[value="${lokasi}"]`).length === 0) {
                    $('#edit_unit_loc').append(new Option(lokasi, lokasi, true, true));
                }
                $('#edit_unit_loc').val(lokasi);
            }
            closeUserDropdown(this);
        });

        // AJAX generation of asset code
        const categorySelect = document.getElementById('kategori_tambah');
        const noAsetInput = document.getElementById('no_aset_tambah');

        if(categorySelect && noAsetInput) {
            categorySelect.addEventListener('change', function() {
                const categoryId = this.value;

                if (!categoryId) {
                    noAsetInput.value = '';
                    return;
                }

                noAsetInput.value = 'Loading...';

                fetch(`/barang/generate-code?category_id=${categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        if(data.kode) {
                            noAsetInput.value = data.kode;
                        } else {
                            noAsetInput.value = '';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        noAsetInput.value = 'Error';
                    });
            });
        }
    });
</script>

<script>
    $(document).ready(function() {
        var table = $('#tableInventaris').DataTable({
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "{{ __('Semua') }}"]],
            "pageLength": 10,
            "scrollX": true,
            "dom": "<'row mb-3 align-items-center'<'col-12 col-md-6'l><'col-12 col-md-6 d-flex justify-content-md-end justify-content-start'f>>" +
                   "<'row'<'col-12'tr>>" +
                   "<'row mt-3 align-items-center'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
            "language": {
                "search": "{{ __('Quick search...') }}",
                "lengthMenu": "{{ __('Tampilkan _MENU_ baris') }}",
                "info": "{{ __('Menampilkan _START_ - _END_ dari _TOTAL_ aset') }}",
                "infoEmpty": "{{ __('Tidak ada data aset') }}",
                "infoFiltered": "({{ __('disaring dari total _MAX_ aset') }})",
                "paginate": {
                    "first": "{{ __('Awal') }}",
                    "last": "{{ __('Akhir') }}",
                    "next": "{{ __('Maju') }} <i class='fas fa-chevron-right ms-1'></i>",
                    "previous": "<i class='fas fa-chevron-left me-1'></i> {{ __('Mundur') }}"
                }
            }
        });

        // Check for URL query param from quick search
        const urlParams = new URLSearchParams(window.location.search);
        const qParam = urlParams.get('q');
        if (qParam) {
            table.search(qParam).draw();
        }

        function exactMatchSearch(colIdx, value) {
            var val = $.fn.dataTable.util.escapeRegex(value);
            table.column(colIdx).search(val ? '^' + val + '$' : '', true, false).draw();
        }

        $('#selectFilterJenis').on('change', function() { exactMatchSearch(2, this.value); });
        $('#selectFilterModel').on('change', function() { exactMatchSearch(3, this.value); });
        $('#selectFilterDept').on('change', function() { exactMatchSearch(7, this.value); });
        $('#selectFilterLoc').on('change', function() { exactMatchSearch(9, this.value); });
        $('#selectFilterVendor').on('change', function() { exactMatchSearch(10, this.value); });

        $('#inputFilterBuyDate').on('change', function() {
            let val = this.value; 
            if (val) {
                let parts = val.split('-');
                val = parts[2] + '-' + parts[1] + '-' + parts[0]; 
            }
            table.column(11).search(val).draw();
        });

        $('#btnResetAllFilters').on('click', function() {
            $('#selectFilterJenis').val('');
            $('#selectFilterModel').val('');
            $('#selectFilterVendor').val('');
            $('#selectFilterDept').val('');
            $('#selectFilterLoc').val('');
            $('#inputFilterBuyDate').val('');
            
            table.columns().search('').draw();
        });
    });
</script>
@endpush