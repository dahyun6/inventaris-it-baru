@extends('layout')

@section('title', __('Catat Maintenance / Servis Baru'))

@section('header_actions')
<a href="{{ route('maintenance.index') }}" class="btn btn-phoenix-secondary btn-sm">
    <i class="fas fa-arrow-left me-1"></i> {{ __('Kembali ke Daftar Maintenance') }}
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-9 col-xl-8 mx-auto">
        <div class="phoenix-card">
            <div class="phoenix-card-header">
                <div>
                    <h6 class="phoenix-card-title mb-0">
                        <i class="fas fa-screwdriver-wrench text-primary"></i>
                        {{ __('Form Pencatatan Tiket Maintenance & Servis Aset') }}
                    </h6>
                    <small class="text-muted">{{ __('Merekam perbaikan, servis berkala, atau penggantian suku cadang.') }}</small>
                </div>
            </div>

            <div class="phoenix-card-body p-4">
                @if($errors->any())
                <div class="alert alert-danger border-0 p-3 mb-4" style="border-radius: 8px;">
                    <div class="fw-bold mb-1"><i class="fas fa-triangle-exclamation me-1"></i> {{ __('Harap periksa kesalahan input berikut:') }}</div>
                    <ul class="mb-0 ps-3 small">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('maintenance.store') }}" method="POST" id="formCreateMaintenance">
                    @csrf

                    <!-- INFORMASI TIKET & PERANGKAT -->
                    <div class="border-bottom pb-4 mb-4">
                        <h6 class="fw-bold text-dark mb-3" style="font-size: 0.875rem;">
                            {{ __('Informasi Tiket & Perangkat') }}
                        </h6>

                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-danger">{{ __('Nomor Tiket Maintenance') }} *</label>
                                <input type="text" name="no_maintenance" class="form-control font-monospace bg-light" value="{{ old('no_maintenance', $autoNo) }}" readonly style="cursor: not-allowed;" required>
                                <small class="text-muted" style="font-size: 0.725rem;">{{ __('Nomor unik referensi servis (otomatis).') }}</small>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label small fw-bold text-danger">{{ __('Pilih Aset / Perangkat') }} *</label>
                                <select name="barang_id" id="selectBarangId" class="form-select" required>
                                    <option value="">{{ __('-- Pilih Perangkat Aset --') }}</option>
                                    @foreach($barangs as $b)
                                        <option value="{{ $b->id }}" {{ (old('barang_id', $selectedBarang?->id) == $b->id) ? 'selected' : '' }} data-sn="{{ $b->serial_number }}" data-kategori="{{ $b->category->nama_kategori ?? '-' }}" data-lokasi="{{ $b->unit_loc ?? '-' }}" data-status="{{ $b->status }}">
                                            [{{ $b->no_aset_local }}] {{ $b->nama_barang ?? $b->model }} ({{ $b->serial_number ?? 'No SN' }}) - {{ $b->status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- DETAIL SERVIS & PELAKSANA -->
                    <div class="border-bottom pb-4 mb-4">
                        <h6 class="fw-bold text-dark mb-3" style="font-size: 0.875rem;">
                            {{ __('Detail Servis & Pelaksana') }}
                        </h6>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-danger">{{ __('Jenis Maintenance') }} *</label>
                                <select name="jenis_maintenance" class="form-select" required>
                                    <option value="Perbaikan / Kerusakan" {{ old('jenis_maintenance') == 'Perbaikan / Kerusakan' ? 'selected' : '' }}>{{ __('Perbaikan / Kerusakan (Corrective)') }}</option>
                                    <option value="Pemeliharaan Rutin" {{ old('jenis_maintenance') == 'Pemeliharaan Rutin' ? 'selected' : '' }}>{{ __('Pemeliharaan Rutin (Preventive)') }}</option>
                                    <option value="Upgrade Hardware" {{ old('jenis_maintenance') == 'Upgrade Hardware' ? 'selected' : '' }}>{{ __('Upgrade Hardware (RAM / SSD / dll)') }}</option>
                                    <option value="Penggantian Sparepart" {{ old('jenis_maintenance') == 'Penggantian Sparepart' ? 'selected' : '' }}>{{ __('Penggantian Sparepart (Baterai, Layar, dll)') }}</option>
                                    <option value="Install Ulang / Software" {{ old('jenis_maintenance') == 'Install Ulang / Software' ? 'selected' : '' }}>{{ __('Install Ulang / Penanganan Software') }}</option>
                                    <option value="Pembersihan / Cleaning" {{ old('jenis_maintenance') == 'Pembersihan / Cleaning' ? 'selected' : '' }}>{{ __('Pembersihan / Cleaning & Pasta Thermal') }}</option>
                                    <option value="Lainnya" {{ old('jenis_maintenance') == 'Lainnya' ? 'selected' : '' }}>{{ __('Lainnya') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-danger">{{ __('Tanggal Mulai Servis') }} *</label>
                                <input type="date" name="tanggal_mulai" class="form-control" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-danger">{{ __('Pihak Pelaksana') }} *</label>
                                <select name="pelaksana" id="selectPelaksana" class="form-select" required>
                                    <option value="Internal IT" {{ old('pelaksana') == 'Internal IT' ? 'selected' : '' }}>{{ __('Internal IT') }}</option>
                                    <option value="Vendor Eksternal" {{ old('pelaksana') == 'Vendor Eksternal' ? 'selected' : '' }}>{{ __('Vendor Eksternal / Servis Center') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4" id="divVendorSelect" style="{{ old('pelaksana') == 'Vendor Eksternal' ? '' : 'display: none;' }}">
                                <label class="form-label small fw-bold text-danger">{{ __('Pilih Mitra Vendor') }} *</label>
                                <select name="vendor_id" id="selectVendorId" class="form-select">
                                    <option value="">{{ __('-- Pilih Vendor --') }}</option>
                                    @foreach($vendors as $v)
                                        <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>{{ $v->nama_vendor }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">{{ __('Nama Teknisi') }}</label>
                                <input type="text" name="nama_teknisi" class="form-control" placeholder="Misal: Budi / Teknisi Vendor" value="{{ old('nama_teknisi') }}">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">{{ __('Estimasi / Biaya Perbaikan (Rp)') }}</label>
                                <input type="number" step="any" name="biaya" class="form-control font-monospace" placeholder="0" value="{{ old('biaya', 0) }}">
                                <small class="text-muted" style="font-size: 0.725rem;">{{ __('Kosongkan atau isi 0 jika garansi / tanpa biaya.') }}</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-danger">{{ __('Status Awal Pengerjaan') }} *</label>
                                <select name="status" id="selectStatus" class="form-select" required>
                                    <option value="Dalam Proses" {{ old('status') == 'Dalam Proses' ? 'selected' : '' }}>{{ __('Dalam Proses (Sedang Dikerjakan)') }}</option>
                                    <option value="Selesai" {{ old('status') == 'Selesai' ? 'selected' : '' }}>{{ __('Selesai (Langsung Dituntaskan)') }}</option>
                                    <option value="Dibatalkan" {{ old('status') == 'Dibatalkan' ? 'selected' : '' }}>{{ __('Dibatalkan') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- KENDALA & CATATAN TINDAKAN -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-3" style="font-size: 0.875rem;">
                            {{ __('Deskripsi Kerusakan & Rencana Solusi') }}
                        </h6>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-danger">{{ __('Deskripsi Kendala / Kerusakan') }} *</label>
                            <textarea name="deskripsi_kendala" class="form-control" rows="3" placeholder="{{ __('Tuliskan gejala kerusakan, keluhan user, atau tujuan perawatan...') }}" required>{{ old('deskripsi_kendala') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">{{ __('Tindakan / Solusi yang Dikerjakan (Opsional)') }}</label>
                            <textarea name="tindakan_perbaikan" class="form-control" rows="3" placeholder="{{ __('Rincian tindakan, part yang diganti, atau catatan penanganan...') }}">{{ old('tindakan_perbaikan') }}</textarea>
                        </div>

                        <div class="row g-3" id="divSelesaiFields" style="{{ old('status') == 'Selesai' ? '' : 'display: none;' }}">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-danger">{{ __('Tanggal Selesai') }}</label>
                                <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-danger">{{ __('Status Akhir Barang') }}</label>
                                <select name="status_aset_setelahnya" class="form-select">
                                    <option value="Tersedia" {{ old('status_aset_setelahnya') == 'Tersedia' ? 'selected' : '' }}>{{ __('Tersedia (Siap Digunakan / Di Gudang IT)') }}</option>
                                    <option value="Dipinjam" {{ old('status_aset_setelahnya') == 'Dipinjam' ? 'selected' : '' }}>{{ __('Dipinjam (Kembali ke User)') }}</option>
                                    <option value="Rusak" {{ old('status_aset_setelahnya') == 'Rusak' ? 'selected' : '' }}>{{ __('Rusak (Afkir / Rusak Total)') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER ACTIONS -->
                    <div class="p-3 bg-light border-top -mx-4 -mb-4 mt-4 d-flex justify-content-between align-items-center" style="margin-left: -1.5rem; margin-right: -1.5rem; margin-bottom: -1.5rem; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                        <a href="{{ route('maintenance.index') }}" class="btn btn-phoenix-secondary btn-sm">{{ __('Batal') }}</a>
                        <button type="submit" class="btn btn-phoenix-primary btn-sm px-4">
                            <i class="fas fa-save me-1"></i> {{ __('Simpan Tiket Maintenance') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectPelaksana = document.getElementById('selectPelaksana');
        const divVendorSelect = document.getElementById('divVendorSelect');
        const selectVendorId = document.getElementById('selectVendorId');
        const selectStatus = document.getElementById('selectStatus');
        const divSelesaiFields = document.getElementById('divSelesaiFields');

        function togglePelaksana() {
            if (selectPelaksana.value === 'Vendor Eksternal') {
                divVendorSelect.style.display = 'block';
                selectVendorId.setAttribute('required', 'required');
            } else {
                divVendorSelect.style.display = 'none';
                selectVendorId.removeAttribute('required');
            }
        }

        function toggleStatus() {
            if (selectStatus.value === 'Selesai') {
                divSelesaiFields.style.display = 'flex';
            } else {
                divSelesaiFields.style.display = 'none';
            }
        }

        selectPelaksana.addEventListener('change', togglePelaksana);
        selectStatus.addEventListener('change', toggleStatus);
    });
</script>
@endpush
