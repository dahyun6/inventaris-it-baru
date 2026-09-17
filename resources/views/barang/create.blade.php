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
                            <div class="input-group">
                                <input type="text" name="pengguna" id="tambah_pengguna" class="form-control" value="{{ old('pengguna') }}" placeholder="Nama karyawan / pemakai...">
                                @if(isset($users) && count($users) > 0)
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
                                 @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Jabatan (Position User)') }}</label>
                            <input type="text" name="position_user" class="form-control" value="{{ old('position_user') }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="tambah_dept">{{ __('Departemen') }}</label>
                            <input type="text" id="tambah_dept" name="dept" class="form-control bg-light" value="{{ old('dept') }}" readonly style="cursor: not-allowed;" placeholder="{{ __('Otomatis terisi dari User...') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="unit_loc">{{ __('Lokasi Unit (Unit Loc)') }}</label>
                            <select name="unit_loc" id="unit_loc" class="form-select">
                                <option value="">-- {{ __('Pilih Lokasi Unit') }} --</option>
                                @foreach($lokasis as $lok)
                                    <option value="{{ $lok->nama_lokasi }}" {{ old('unit_loc') == $lok->nama_lokasi ? 'selected' : '' }}>
                                        {{ $lok->nama_lokasi }} {{ $lok->kode_lokasi ? '('.$lok->kode_lokasi.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
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
                    <div class="form-section-divider">4. {{ __('Jadwal Preventive Maintenance') }}</div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Interval Servis Berkala') }}</label>
                            <select name="interval_maintenance" class="form-select">
                                <option value="0" {{ old('interval_maintenance', 0) == 0 ? 'selected' : '' }}>-- {{ __('Tanpa Jadwal Rutin') }} --</option>
                                <option value="3" {{ old('interval_maintenance') == 3 ? 'selected' : '' }}>{{ __('Setiap 3 Bulan (Kuartalan)') }}</option>
                                <option value="6" {{ old('interval_maintenance') == 6 ? 'selected' : '' }}>{{ __('Setiap 6 Bulan (Semester)') }}</option>
                                <option value="12" {{ old('interval_maintenance') == 12 ? 'selected' : '' }}>{{ __('Setiap 12 Bulan (Tahunan)') }}</option>
                                <option value="24" {{ old('interval_maintenance') == 24 ? 'selected' : '' }}>{{ __('Setiap 24 Bulan (2 Tahun)') }}</option>
                            </select>
                            <div class="form-text" style="font-size: 0.725rem;">{{ __('Sistem otomatis menghitung tanggal servis berikutnya setelah maintenance selesai.') }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Jadwal Maintenance Pertama / Berikutnya') }}</label>
                            <input type="date" name="tgl_maintenance_berikutnya" class="form-control" value="{{ old('tgl_maintenance_berikutnya') }}">
                            <div class="form-text" style="font-size: 0.725rem;">{{ __('Bisa ditentukan manual atau terisi otomatis dari interval.') }}</div>
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

        // Live Search Filtering for User Dropdown
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('user-search-input')) {
                const query = e.target.value.toLowerCase().trim();
                const dropdownMenu = e.target.closest('.dropdown-menu');
                if (!dropdownMenu) return;
                const items = dropdownMenu.querySelectorAll('.user-search-item');
                const noResult = dropdownMenu.querySelector('.user-no-result');
                let count = 0;

                items.forEach(function(item) {
                    const btn = item.querySelector('a');
                    const searchStr = btn ? (btn.getAttribute('data-search') || '') : '';
                    if (searchStr.includes(query)) {
                        item.style.display = '';
                        count++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (noResult) {
                    noResult.classList.toggle('d-none', count > 0);
                }
            }
        });

        // Auto focus search input when dropdown opens
        document.addEventListener('shown.bs.dropdown', function(e) {
            const menu = e.target.querySelector('.dropdown-menu') || e.target.nextElementSibling;
            if (menu) {
                const searchInput = menu.querySelector('.user-search-input');
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.dispatchEvent(new Event('input', { bubbles: true }));
                    setTimeout(() => searchInput.focus(), 60);
                }
            }
        });

        // User Selection Handler
        document.addEventListener('click', function(e) {
            const selectBtn = e.target.closest('.btn-select-tambah-user');
            if (selectBtn) {
                e.preventDefault();
                const targetInput = document.getElementById('tambah_pengguna');
                if (targetInput) {
                    targetInput.value = selectBtn.getAttribute('data-name') || '';
                }
                const deptVal = selectBtn.getAttribute('data-dept') || '';
                const deptInput = document.querySelector('input[name="dept"]');
                if (deptInput) {
                    deptInput.value = deptVal;
                }
                const lokasiVal = selectBtn.getAttribute('data-lokasi') || '';
                if (lokasiVal) {
                    const unitLocSelect = document.getElementById('unit_loc');
                    if (unitLocSelect) {
                        let optFound = false;
                        for (let i = 0; i < unitLocSelect.options.length; i++) {
                            if (unitLocSelect.options[i].value === lokasiVal) {
                                optFound = true;
                                break;
                            }
                        }
                        if (!optFound) {
                            unitLocSelect.add(new Option(lokasiVal, lokasiVal, true, true));
                        }
                        unitLocSelect.value = lokasiVal;
                    }
                }
                const toggle = selectBtn.closest('.input-group')?.querySelector('[data-bs-toggle="dropdown"]');
                if (toggle) {
                    const bsDropdown = bootstrap.Dropdown.getInstance(toggle) || new bootstrap.Dropdown(toggle);
                    if (bsDropdown) bsDropdown.hide();
                }
            }
        });
    });
</script>
@endpush