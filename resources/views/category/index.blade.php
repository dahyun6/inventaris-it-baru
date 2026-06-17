@extends('layout')

@section('title', 'Master Kategori')

@section('content')
<style>
    /* Styling khas Tabel & Card AdminLTE */
    .card-admin { background: #fff; border-radius: 4px; box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2); margin-bottom: 20px; border-top: 3px solid #007bff; }
    .card-header-admin { padding: 15px 20px; border-bottom: 1px solid rgba(0,0,0,.125); display: flex; justify-content: space-between; align-items: center; }
    
    .table-admin { margin-bottom: 0; font-size: 14.5px; }
    .table-admin thead th { border-bottom: 2px solid #dee2e6; color: #343a40; font-weight: 600; background-color: #f8f9fa; }
    .table-admin tbody td { vertical-align: middle; border-bottom: 1px solid #dee2e6; color: #495057; }

    /* Badge Solid khas AdminLTE */
    .badge-unit { background-color: #007bff; color: white; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 11px; }
    
    .btn-action { background: #fff; border: 1px solid #ced4da; color: #495057; padding: 2px 8px; }
    .btn-action:hover { background: #f8f9fa; }

    .search-input-admin { border: 1px solid #ced4da; border-radius: 4px; padding: 5px 12px; font-size: 13px; outline: none; width: 250px; }
    .search-input-admin:focus { border-color: #80bdff; }
</style>

<div class="row">
    <div class="col-md-10 mx-auto">
        
        <div class="card-admin">
            <div class="card-header-admin">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                        <i class="fas fa-plus me-1"></i> Tambah Kategori
                    </button>
                    <input type="text" id="categorySearch" class="search-input-admin" placeholder="Search categories...">
                </div>
                <div class="text-muted small">Total: <strong>{{ $categories->count() }}</strong> Kategori</div>
            </div>

            <div class="table-responsive">
                <table class="table table-admin table-hover">
                    <thead>
                        <tr>
                            <th width="8%" class="ps-3">#</th>
                            <th width="35%">NAMA KATEGORI</th>
                            <th width="15%">KODE PREFIX</th>
                            <th width="20%" class="text-center">TOTAL ASET</th>
                            <th width="22%" class="text-center pe-3">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTableBody">
                        @forelse($categories as $index => $cat)
                        <tr class="category-row">
                            <td class="ps-3 text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark category-name">{{ $cat->nama_kategori }}</td>
                            <td>
                                @if($cat->kode_prefix)
                                    <span class="badge bg-secondary">{{ $cat->kode_prefix }}</span>
                                @else
                                    <span class="text-muted fst-italic">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge-unit shadow-sm">{{ $cat->barangs_count }} UNIT</span>
                            </td>
                            <td class="text-center pe-3">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-action text-warning" data-bs-toggle="modal" data-bs-target="#modalEditKategori{{ $cat->id }}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('category.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori ini? Semua barang di dalamnya juga akan ikut terhapus!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-action text-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div class="modal fade" id="modalEditKategori{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content" style="border-radius: 4px;">
                                    <div class="modal-header bg-light border-0">
                                        <h5 class="modal-title fs-6 fw-bold">Edit Kategori</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('category.update', $cat->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Nama Kategori <span class="text-danger">*</span></label>
                                                <input type="text" name="nama_kategori" class="form-control form-control-sm" required value="{{ $cat->nama_kategori }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Kode Prefix (Opsional)</label>
                                                <input type="text" name="kode_prefix" class="form-control form-control-sm" value="{{ $cat->kode_prefix }}" placeholder="Ex: L, PC, PRN...">
                                                <small class="text-muted" style="font-size: 11px;">Akan digunakan sebagai huruf awalan kode aset otomatis.</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light border-0">
                                            <button type="button" class="btn btn-xs btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-xs btn-success">Update</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada kategori master.</td>
                        </tr>
                        @endforelse
                        
                        <tr id="noCategoryFound" style="display: none;">
                            <td colspan="5" class="text-center py-4 text-danger fw-bold">Kategori tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="p-3 bg-light border-top text-end" style="font-size: 12px; color: #6c757d;">
                Showing All Registered IT Asset Categories
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="border-radius: 4px;">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fs-6 fw-bold">Tambah Kategori</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('category.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" class="form-control form-control-sm" placeholder="Ex: Server, Printer..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kode Prefix (Opsional)</label>
                        <input type="text" name="kode_prefix" class="form-control form-control-sm" placeholder="Ex: L, PC, PRN...">
                        <small class="text-muted" style="font-size: 11px;">Akan digunakan sebagai huruf awalan kode aset otomatis.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-xs btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-xs btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySearch = document.getElementById('categorySearch');
        const rows = document.querySelectorAll('.category-row');
        const noCategoryFound = document.getElementById('noCategoryFound');

        categorySearch.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            let visibleRows = 0;

            rows.forEach(row => {
                const name = row.querySelector('.category-name').textContent.toLowerCase();
                if (name.includes(term)) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            });

            noCategoryFound.style.display = visibleRows === 0 ? '' : 'none';
        });
    });
</script>
@endpush