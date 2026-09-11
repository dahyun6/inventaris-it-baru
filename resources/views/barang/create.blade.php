@extends('layout')

@section('title', __('Tambah Aset Baru'))

@section('header_actions')
<a href="{{ route('barang.index') }}" class="btn btn-phoenix-secondary btn-sm">
    <i class="fas fa-arrow-left me-1"></i> {{ __('Kembali ke Data Aset') }}
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-10 col-xl-9 mx-auto">
        <div class="phoenix-card">
            <div class="phoenix-card-header">
                <h6 class="phoenix-card-title">
                    <i class="fas fa-circle-plus text-primary"></i>
                    {{ __('Entry Aset Baru') }}
                </h6>
            </div>
            <form action="{{ route('barang.store') }}" method="POST">
                @csrf
                <div class="phoenix-card-body">
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
                            <input type="text" name="model" class="form-control" placeholder="ThinkPad, Dell..." required value="{{ old('model') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Serial Number (SN)') }}</label>
                            <input type="text" name="serial_number" class="form-control font-monospace" placeholder="PF3X4920" value="{{ old('serial_number') }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Hostname PC/Laptop') }}</label>
                            <input type="text" name="hostname" class="form-control font-monospace" placeholder="LAP-MKT-01" value="{{ old('hostname') }}">
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
                        <textarea name="type_spec" class="form-control" rows="2" placeholder="Core i5, 16GB RAM...">{{ old('type_spec') }}</textarea>
                    </div>

                    <div class="form-section-divider">2. {{ __('Distribusi & Pengguna') }}</div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Nama Pengguna') }}</label>
                            <input type="text" name="pengguna" class="form-control" value="{{ old('pengguna') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Jabatan (Position User)') }}</label>
                            <input type="text" name="position_user" class="form-control" value="{{ old('position_user') }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Departemen') }}</label>
                            <input type="text" name="dept" class="form-control" value="{{ old('dept') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Lokasi Unit (Unit Loc)') }}</label>
                            <input type="text" name="unit_loc" class="form-control" value="{{ old('unit_loc') }}">
                        </div>
                    </div>

                    <div class="form-section-divider">3. {{ __('Pembelian & Catatan') }}</div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Tanggal Beli (Buy Date)') }}</label>
                            <input type="date" name="buy_date" class="form-control" value="{{ old('buy_date') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Vendor / Toko') }}</label>
                            <select name="vendor" class="form-select">
                                <option value="">-- {{ __('Pilih Vendor') }} --</option>
                                @foreach($vendors as $ven)
                                    <option value="{{ $ven->nama_vendor }}" {{ old('vendor') == $ven->nama_vendor ? 'selected' : '' }}>
                                        {{ $ven->nama_vendor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">{{ __('Catatan Tambahan') }}</label>
                        <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
                    </div>
                </div>

                <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                    <a href="{{ route('barang.index') }}" class="btn btn-phoenix-secondary btn-sm">{{ __('Batal') }}</a>
                    <button type="submit" class="btn btn-phoenix-primary btn-sm px-4">
                        <i class="fas fa-save me-1"></i> {{ __('Simpan Data') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
                        noAsetInput.value = data.kode || '';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        noAsetInput.value = '';
                    });
            });
        }
    });
</script>
@endpush