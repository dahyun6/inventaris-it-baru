@extends('layout')

@section('title', 'Inventaris IT')

@section('content')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .card-admin { background: #fff; border-radius: 4px; box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2); margin-bottom: 20px; border-top: 3px solid var(--primary-blue); }
    .card-header-admin { padding: 15px 20px; border-bottom: 1px solid rgba(0,0,0,.125); display: flex; justify-content: space-between; align-items: center; }
    
    .table-admin { margin-bottom: 0; font-size: 13.5px; width: 100% !important; }
    
    /* PERBAIKAN 1: Padding kanan diperbesar (30px) agar teks tidak menabrak panah sorting DataTables */
    .table-admin thead th { 
        border-bottom: 2px solid #dee2e6; 
        color: #343a40; 
        font-weight: 600; 
        white-space: nowrap; 
        background: #f8f9fa; 
        padding: 12px 30px 12px 15px !important; 
    }
    .table-admin tbody td { 
        vertical-align: middle; 
        border-bottom: 1px solid #dee2e6; 
        color: #495057; 
        white-space: nowrap; /* Memastikan teks tidak turun ke bawah, memicu scroll horizontal */
        padding: 10px 15px !important; 
    }
    
    /* Penyesuaian posisi panah DataTables agar lebih rapi */
    table.dataTable thead th.sorting:before, 
    table.dataTable thead th.sorting:after,
    table.dataTable thead th.sorting_asc:before,
    table.dataTable thead th.sorting_asc:after,
    table.dataTable thead th.sorting_desc:before,
    table.dataTable thead th.sorting_desc:after {
        right: 8px !important;
        bottom: 12px !important;
    }
    
    .badge-admin { padding: 5px 10px; font-weight: 500; font-size: 12px; border-radius: 4px; }
    .badge-active { background-color: #28a745; color: white; }
    .badge-invited { background-color: #17a2b8; color: white; }
    .badge-suspended { background-color: #6c757d; color: white; }
    
    .form-section-title { font-size: 13px; font-weight: 700; color: #0d6efd; text-transform: uppercase; margin-top: 15px; margin-bottom: 10px; border-bottom: 1px solid #dee2e6; padding-bottom: 5px; }
    .modal-body .form-label { font-size: 13px; font-weight: 600; margin-bottom: 5px; }
    
    /* PERBAIKAN 2: Jarak label filter ke input box diperbesar (margin-bottom: 8px) */
    .filter-panel { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; padding: 15px; margin-bottom: 15px; }
    .filter-label { font-size: 12px; font-weight: 700; color: #495057; text-transform: uppercase; margin-bottom: 8px; display: block; }
    .filter-input { font-size: 13.5px; padding: 8px 10px; border-radius: 4px; border: 1px solid #ced4da; width: 100%; background-color: #fff; }
    .filter-input:focus { border-color: #80bdff; outline: none; box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25); }
    
    div.dataTables_wrapper div.dataTables_length select { width: 70px; display: inline-block; padding: 4px 8px; }
    .btn-group .btn { margin-right: 3px; }
    /* PERBAIKAN: Search Bar DataTables untuk Mobile */
    @media (max-width: 768px) {
        div.dataTables_wrapper div.dataTables_filter {
            text-align: left !important;
            margin-top: 10px;
        }
        div.dataTables_wrapper div.dataTables_filter label {
            display: flex;
            align-items: center;
            width: 100%;
        }
        div.dataTables_wrapper div.dataTables_filter input {
            flex: 1; /* Memaksa input untuk mengisi sisa ruang tanpa melebihi layar */
            margin-left: 10px !important;
            width: 100% !important;
        }
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

<div class="card-admin" style="border-top: 3px solid #6c757d;">
    <div class="card-header-admin bg-light" style="padding: 10px 20px;">
        <span class="fw-bold text-secondary"><i class="fas fa-filter me-2"></i>Control Panel Filter Aset</span>
        <button type="button" id="btnResetAllFilters" class="btn btn-sm btn-outline-danger px-3"><i class="fas fa-undo me-1"></i> Reset Semua Filter</button>
    </div>
    <div class="card-body bg-light shadow-inner p-4">
        <div class="row g-4">
            <div class="col-md-2 col-6">
                <label class="filter-label">Jenis Perangkat</label>
                <select id="selectFilterJenis" class="filter-input">
                    <option value="">-- Semua --</option>
                    @foreach($filterJenis as $jenis) <option value="{{ $jenis }}">{{ $jenis }}</option> @endforeach
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="filter-label">Model / Brand</label>
                <select id="selectFilterModel" class="filter-input">
                    <option value="">-- Semua --</option>
                    @foreach($filterModel as $model) <option value="{{ $model }}">{{ $model }}</option> @endforeach
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="filter-label">Vendor / Toko</label>
                <select id="selectFilterVendor" class="filter-input">
                    <option value="">-- Semua --</option>
                    @foreach($filterVendor as $vendor) <option value="{{ $vendor }}">{{ $vendor }}</option> @endforeach
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="filter-label">Departemen (Dept)</label>
                <select id="selectFilterDept" class="filter-input">
                    <option value="">-- Semua --</option>
                    @foreach($filterDept as $dept) <option value="{{ $dept }}">{{ $dept }}</option> @endforeach
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label class="filter-label">Unit Lokasi</label>
                <select id="selectFilterLoc" class="filter-input">
                    <option value="">-- Semua --</option>
                    @foreach($filterLoc as $loc) <option value="{{ $loc }}">{{ $loc }}</option> @endforeach
                </select>
            </div>
            <div class="col-md-2 col-12">
                <label class="filter-label">Tanggal Beli</label>
                <input type="date" id="inputFilterBuyDate" class="filter-input">
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card-admin">
    <div class="card-header-admin bg-white d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-server me-2 text-primary"></i>Master Data Inventaris</h5>
        </div>
        <div class="dt-buttons d-flex gap-2">
            <button class="btn btn-success btn-sm text-white m-0" data-bs-toggle="modal" data-bs-target="#modalImport">
                <i class="fas fa-file-excel me-1"></i> Import Excel
            </button>
            <button class="btn btn-primary btn-sm m-0" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-1"></i> Entry Aset Baru
            </button>
        </div>
    </div>

    <div class="card-body p-4">
        <div class="table-responsive">
            <table id="tableInventaris" class="table table-admin table-hover table-striped w-100">
                <thead>
                    <tr>
                        <th width="3%">NO</th>
                        <th>NO ASET LOCAL</th>
                        <th>JENIS</th>
                        <th>MODEL</th>
                        <th>SPESIFIKASI</th>
                        <th>SERIAL NUMBER</th>
                        <th>HOSTNAME</th>
                        <th>DEPT</th>
                        <th>PENGGUNA</th>
                        <th>LOKASI</th>
                        <th>VENDOR</th>
                        <th>TGL BELI</th>
                        <th>CATATAN</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center">AKSI</th>
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
                            @if($item->status == 'Tersedia') <span class="badge-admin badge-active">Tersedia</span>
                            @elseif($item->status == 'Dipinjam') <span class="badge-admin badge-invited">Dipinjam</span>
                            @else <span class="badge-admin badge-suspended">Rusak</span> @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <a href="{{ route('barang.show', $item->uuid) }}" class="btn btn-xs btn-light text-primary" title="Detail & History"><i class="fas fa-folder-open"></i></a>
                                <button type="button" class="btn btn-xs btn-light text-warning" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}" title="Edit Data"><i class="fas fa-edit"></i></button>
                                
                                <form action="{{ route('barang.destroy', $item->uuid) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus aset ini secara permanen?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-light text-danger" title="Hapus Data"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content" style="border-radius: 4px;">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title fs-6 fw-bold">Update Data Aset</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('barang.update', $item->uuid) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body bg-light pt-0">
                                        <div class="form-section-title">1. Klasifikasi & Identitas</div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-6">
                                                <label class="form-label">No Aset Local</label>
                                                <input type="text" name="no_aset_local" class="form-control" value="{{ $item->no_aset_local }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-danger">Jenis / Kategori *</label>
                                                <select name="category_id" class="form-select" required>
                                                    @foreach($categories as $cat)
                                                        <option value="{{ $cat->id }}" {{ $item->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-6">
                                                <label class="form-label text-danger">Model Perangkat *</label>
                                                <input type="text" name="model" class="form-control" value="{{ $item->model }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Serial Number (SN)</label>
                                                <input type="text" name="serial_number" class="form-control" value="{{ $item->serial_number }}">
                                            </div>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Hostname PC/Laptop</label>
                                                <input type="text" name="hostname" class="form-control" value="{{ $item->hostname }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-danger">Status Kondisi *</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="Tersedia" {{ $item->status == 'Tersedia' ? 'selected' : '' }}>Tersedia (Di Gudang)</option>
                                                    <option value="Dipinjam" {{ $item->status == 'Dipinjam' ? 'selected' : '' }}>Dipinjam (Digunakan)</option>
                                                    <option value="Rusak" {{ $item->status == 'Rusak' ? 'selected' : '' }}>Rusak (Perbaikan)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Spesifikasi (Type Spec)</label>
                                            <textarea name="type_spec" class="form-control" rows="2">{{ $item->type_spec }}</textarea>
                                        </div>

                                        <div class="form-section-title">2. Distribusi & Pengguna</div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Nama Pengguna</label>
                                                <input type="text" name="pengguna" class="form-control" value="{{ $item->pengguna }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Jabatan (Position User)</label>
                                                <input type="text" name="position_user" class="form-control" value="{{ $item->position_user }}">
                                            </div>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Departemen (Dept)</label>
                                                <input type="text" name="dept" class="form-control" value="{{ $item->dept }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Lokasi Unit (Unit Loc)</label>
                                                <input type="text" name="unit_loc" class="form-control" value="{{ $item->unit_loc }}">
                                            </div>
                                        </div>

                                        <div class="form-section-title">3. Pembelian & Catatan</div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Tanggal Beli (Buy Date)</label>
                                                <input type="date" name="buy_date" class="form-control" value="{{ $item->buy_date }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Vendor / Toko</label>
                                                <input type="text" name="vendor" class="form-control" value="{{ $item->vendor }}">
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label">Catatan Internal (Note)</label>
                                            <textarea name="note" class="form-control" rows="2">{{ $item->note }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-white">
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        <button type="submit" class="btn btn-sm btn-warning px-4"><i class="fas fa-save me-1"></i> Update Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 4px;">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fs-6 fw-bold"><i class="fas fa-upload me-2"></i>Import Data Excel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('barang.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih File (.xlsx / .csv)</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-success px-4"><i class="fas fa-cloud-upload-alt me-1"></i> Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 4px;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold"><i class="fas fa-plus-circle me-2"></i>Entry Aset Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('barang.store') }}" method="POST">
                @csrf
                <div class="modal-body bg-light pt-0">
                    <div class="form-section-title">1. Klasifikasi & Identitas</div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
    <label class="form-label">No Aset Local</label>
    <!-- WAJIB ADA: id="no_aset_tambah" dan readonly -->
    <input type="text" id="no_aset_tambah" name="no_aset_local" class="form-control" readonly placeholder="Otomatis...">
</div>
<div class="col-md-6">
    <label class="form-label text-danger">Jenis / Kategori *</label>
    <!-- WAJIB ADA: id="kategori_tambah" -->
    <select id="kategori_tambah" name="category_id" class="form-select" required>
        <option value="">-- Pilih --</option>
        @foreach($categories as $cat) 
             <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option> 
        @endforeach
    </select>
</div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-danger">Model Perangkat *</label>
                            <input type="text" name="model" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Serial Number (SN)</label>
                            <input type="text" name="serial_number" class="form-control">
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Hostname</label>
                            <input type="text" name="hostname" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-danger">Status Awal *</label>
                            <select name="status" class="form-select" required>
                                <option value="Tersedia">Tersedia</option>
                                <option value="Dipinjam">Dipinjam</option>
                                <option value="Rusak">Rusak</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Spesifikasi (Type Spec)</label>
                        <textarea name="type_spec" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-section-title">2. Distribusi & Pengguna</div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Nama Pengguna</label>
                            <input type="text" name="pengguna" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan (Position)</label>
                            <input type="text" name="position_user" class="form-control">
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Departemen</label>
                            <input type="text" name="dept" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lokasi Unit</label>
                            <input type="text" name="unit_loc" class="form-control">
                        </div>
                    </div>

                    <div class="form-section-title">3. Pembelian & Catatan</div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Beli</label>
                            <input type="date" name="buy_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vendor</label>
                            <input type="text" name="vendor" class="form-control">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Catatan Tambahan</label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // PERBAIKAN: Memanggil berdasarkan ID khusus modal tambah
        const categorySelect = document.getElementById('kategori_tambah');
        const noAsetInput = document.getElementById('no_aset_tambah');

        if(categorySelect && noAsetInput) {
            categorySelect.addEventListener('change', function() {
                const categoryId = this.value;

                // Jika user memilih opsi kosong, kosongkan juga kode asetnya
                if (!categoryId) {
                    noAsetInput.value = '';
                    return;
                }

                // Mengubah status input menjadi loading
                noAsetInput.value = 'Loading...';

                // Tanya ke server via AJAX
                fetch(`/barang/generate-code?category_id=${categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        if(data.kode) {
                            noAsetInput.value = data.kode; // Masukkan format L2730-001 ke form
                        } else {
                            noAsetInput.value = ''; // Kosongkan jika kategori tidak punya prefix
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        noAsetInput.value = 'Gagal memuat';
                    });
            });
        }
    });
</script>

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables dengan scroll horizontal
        var table = $('#tableInventaris').DataTable({
            "lengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
            "pageLength": 10,
            "scrollX": true, // Mencegah tabel terpotong di layar kecil
            "dom": "<'row mb-2'<'col-12 col-md-6'l><'col-12 col-md-6 d-flex justify-content-md-end justify-content-start mt-2 mt-md-0'f>>" +
                   "<'row'<'col-12'tr>>" +
                   "<'row mt-2'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
            "language": {
                "search": "🔍 Pencarian Cepat:",
                "lengthMenu": "Tampilkan _MENU_ baris",
                "info": "Menampilkan _START_ s/d _END_ dari _TOTAL_ data aset",
                "infoEmpty": "Tidak ada data aset",
                "infoFiltered": "(disaring dari total _MAX_ aset)",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "Maju <i class='fas fa-angle-right ms-1'></i>",
                    "previous": "<i class='fas fa-angle-left me-1'></i> Mundur"
                }
            }
        });

        // Exact Match (Presisi) menggunakan Regex untuk mencegah bug "SUSTAINAIBILITY"
        function exactMatchSearch(colIdx, value) {
            var val = $.fn.dataTable.util.escapeRegex(value);
            table.column(colIdx).search(val ? '^' + val + '$' : '', true, false).draw();
        }

        // PERHATIAN: Indeks kolom berubah karena kita menambahkan kolom Spesifikasi, Hostname, dan Catatan
        $('#selectFilterJenis').on('change', function() { exactMatchSearch(2, this.value); });
        $('#selectFilterModel').on('change', function() { exactMatchSearch(3, this.value); });
        $('#selectFilterDept').on('change', function() { exactMatchSearch(7, this.value); }); // Dept sekarang kolom ke-8 (index 7)
        $('#selectFilterLoc').on('change', function() { exactMatchSearch(9, this.value); });  // Lokasi sekarang kolom ke-10 (index 9)
        $('#selectFilterVendor').on('change', function() { exactMatchSearch(10, this.value); }); // Vendor sekarang kolom ke-11 (index 10)

        // Konversi Format Tanggal Beli dari YYYY-MM-DD ke DD-MM-YYYY agar sesuai dengan tampilan di tabel (index 11)
        $('#inputFilterBuyDate').on('change', function() {
            let val = this.value; 
            if (val) {
                let parts = val.split('-');
                val = parts[2] + '-' + parts[1] + '-' + parts[0]; 
            }
            table.column(11).search(val).draw();
        });

        // Reset All Filters
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