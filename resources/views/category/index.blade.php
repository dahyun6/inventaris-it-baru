@extends('layout')

@section('title', __('Kategori Master Aset'))

@section('header_actions')
<button type="button" class="btn btn-phoenix-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
    <i class="fas fa-plus me-1"></i> {{ __('Tambah Kategori') }}
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
                    <input type="text" id="categorySearch" class="search-input-phoenix" placeholder="{{ __('Cari kategori...') }}">
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-phoenix badge-phoenix-primary">
                        Total: {{ $categories->count() }} {{ __('Kategori Assets') }}
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-phoenix">
                    <thead>
                        <tr>
                            <th width="8%">{{ __('NO') }}</th>
                            <th width="35%">{{ __('NAMA KATEGORI') }}</th>
                            <th width="20%">{{ __('KODE PREFIX') }}</th>
                            <th width="20%" class="text-center">{{ __('TOTAL ASET') }}</th>
                            <th width="17%" class="text-center">{{ __('AKSI') }}</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTableBody">
                        @forelse($categories as $index => $cat)
                        <tr class="category-row">
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark category-name">{{ $cat->nama_kategori }}</td>
                            <td>
                                @if($cat->kode_prefix)
                                    <span class="badge bg-light text-primary border font-monospace px-2 py-1" style="font-size: 0.75rem;">
                                        {{ $cat->kode_prefix }}
                                    </span>
                                @else
                                    <span class="text-muted fst-italic small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge-phoenix badge-phoenix-info font-monospace">
                                    {{ $cat->barangs_count }} UNIT
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-phoenix-secondary py-1 px-2 btn-edit-category" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditKategori" 
                                        data-url="{{ route('category.update', $cat->id) }}"
                                        data-nama="{{ $cat->nama_kategori }}"
                                        data-prefix="{{ $cat->kode_prefix }}"
                                        title="{{ __('Edit Kategori') }}">
                                        <i class="fas fa-pen text-warning"></i>
                                    </button>
                                    <form action="{{ route('category.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Hapus kategori ini?') }}')">
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
                                <i class="fas fa-folder-open fs-2 opacity-25 mb-2"></i><br>
                                {{ __('Belum ada kategori master terdaftar.') }}
                            </td>
                        </tr>
                        @endforelse
                        
                        <tr id="noCategoryFound" style="display: none;">
                            <td colspan="5" class="text-center py-4 text-danger fw-semibold">
                                <i class="fas fa-circle-exclamation me-1"></i> {{ __('Kategori tidak ditemukan.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center" style="font-size: 0.775rem; color: var(--phoenix-text-muted);">
                <span>{{ __('Kategori Master Aset') }}</span>
                <span class="font-monospace">Prefix used for auto-generated asset codes</span>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT KATEGORI -->
<div class="modal fade" id="modalEditKategori" tabindex="-1" aria-labelledby="modalEditKategoriLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: 1px solid var(--phoenix-border-color); max-width: 440px; margin: auto;">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold" id="modalEditKategoriLabel"><i class="fas fa-pen-to-square text-primary me-2"></i>{{ __('Edit Kategori') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditCategory" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold" for="edit_nama_kategori">{{ __('Nama Kategori') }} <span class="text-danger">*</span></label>
                        <input type="text" id="edit_nama_kategori" name="nama_kategori" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold" for="edit_kode_prefix">{{ __('Kode Prefix (Opsional)') }}</label>
                        <input type="text" id="edit_kode_prefix" name="kode_prefix" class="form-control font-monospace" placeholder="Ex: L, PC, PRN...">
                        <small class="text-muted" style="font-size: 0.725rem;">Digunakan sebagai awalan penomoran aset otomatis</small>
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

<!-- MODAL TAMBAH KATEGORI -->
<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: 1px solid var(--phoenix-border-color); max-width: 440px; margin: auto;">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold"><i class="fas fa-circle-plus text-primary me-2"></i>{{ __('Tambah Kategori') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('category.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">{{ __('Nama Kategori') }} <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Laptop, Printer..." required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">{{ __('Kode Prefix (Opsional)') }}</label>
                        <input type="text" name="kode_prefix" class="form-control font-monospace" placeholder="Ex: L, PC, PRN...">
                        <small class="text-muted" style="font-size: 0.725rem;">Digunakan sebagai awalan penomoran aset otomatis</small>
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
        const categorySearch = document.getElementById('categorySearch');
        const rows = document.querySelectorAll('.category-row');
        const noCategoryFound = document.getElementById('noCategoryFound');

        document.querySelectorAll('.btn-edit-category').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = document.getElementById('formEditCategory');
                form.action = this.dataset.url;
                document.getElementById('edit_nama_kategori').value = this.dataset.nama || '';
                document.getElementById('edit_kode_prefix').value = this.dataset.prefix || '';
            });
        });

        if (categorySearch) {
            categorySearch.addEventListener('input', function() {
                const term = this.value.toLowerCase().trim();
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

                noCategoryFound.style.display = (visibleRows === 0 && rows.length > 0) ? '' : 'none';
            });
        }
    });
</script>
@endpush