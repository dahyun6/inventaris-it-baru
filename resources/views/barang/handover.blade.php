@extends('layout')

@section('title', 'Form Serah Terima (Handover)')

@section('header_actions')
<a href="{{ route('barang.show', $barang->uuid) }}" class="btn btn-phoenix-secondary btn-sm">
    <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Aset
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 col-xl-7 mx-auto">
        <div class="phoenix-card">
            <div class="phoenix-card-header">
                <h6 class="phoenix-card-title">
                    <i class="fas fa-right-left text-primary"></i>
                    Catat Serah Terima / Perpindahan Aset
                </h6>
            </div>
            <div class="phoenix-card-body">
                
                <div class="alert alert-info border-0 p-3 mb-4 d-flex align-items-center gap-3" style="background-color: var(--phoenix-primary-subtle); color: var(--phoenix-primary); border-radius: 8px;">
                    <i class="fas fa-laptop fs-3"></i>
                    <div>
                        <div class="fw-bold" style="font-size: 0.9rem;">{{ $barang->nama_barang ?? $barang->model }}</div>
                        <div class="small font-monospace">SN: {{ $barang->serial_number ?? '-' }} | No Aset: {{ $barang->no_aset_local ?? '-' }}</div>
                    </div>
                </div>

                <form action="{{ route('barang.storeHandover', $barang->uuid) }}" method="POST">
                    @csrf
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="handover_user_id">{{ __('Diserahkan Kepada (Pegawai)') }}</label>
                            <select name="user_id" id="handover_user_id" class="form-select">
                                <option value="" data-dept="" data-lokasi="">-- {{ __('Kembalikan ke Gudang / IT') }} --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" data-dept="{{ $user->departemen?->nama_departemen ?? '' }}" data-lokasi="{{ $user->lokasi?->nama_lokasi ?? '' }}" data-name="{{ $user->name }}">
                                        {{ $user->name }} {{ $user->departemen ? '('.$user->departemen->nama_departemen.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="handover_dept_preview" class="mt-1 small text-muted d-none">
                                <i class="fas fa-building me-1 text-primary"></i> {{ __('Departemen') }}: <strong id="handover_dept_name" class="text-dark"></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger">{{ __('Tanggal Serah Terima') }} *</label>
                            <input type="date" name="tanggal_serah_terima" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger">{{ __('Lokasi Baru') }} *</label>
                            <input type="text" name="lokasi" id="handover_lokasi" class="form-control" list="handoverLokasiList" placeholder="Misal: Meja Budi, Gd. A Lt. 2..." required>
                            <datalist id="handoverLokasiList">
                                @if(isset($lokasis))
                                    @foreach($lokasis as $lok)
                                        <option value="{{ $lok->nama_lokasi }}">
                                    @endforeach
                                @endif
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger">{{ __('Ubah Status Barang Menjadi') }} *</label>
                            <select name="status" class="form-select" required>
                                <option value="Dipinjam" {{ $barang->status == 'Dipinjam' ? 'selected' : '' }}>{{ __('Dipinjam (Digunakan)') }}</option>
                                <option value="Tersedia" {{ $barang->status == 'Tersedia' ? 'selected' : '' }}>{{ __('Tersedia (Di Gudang IT)') }}</option>
                                <option value="Rusak" {{ $barang->status == 'Rusak' ? 'selected' : '' }}>{{ __('Rusak (Dalam Perbaikan)') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">{{ __('Keterangan / Catatan Kondisi') }}</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Misal: Pindah divisi, kelengkapan adaptor charger..."></textarea>
                    </div>

                    <div class="p-3 bg-light border-top -mx-4 -mb-4 mt-4 d-flex justify-content-between align-items-center" style="margin-left: -1.25rem; margin-right: -1.25rem; margin-bottom: -1.25rem; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                        <a href="{{ route('barang.show', $barang->uuid) }}" class="btn btn-phoenix-secondary btn-sm">{{ __('Batal') }}</a>
                        <button type="submit" class="btn btn-phoenix-primary btn-sm px-4">
                            <i class="fas fa-save me-1"></i> {{ __('Simpan Handover') }}
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
        const userSelect = document.getElementById('handover_user_id');
        const deptPreview = document.getElementById('handover_dept_preview');
        const deptName = document.getElementById('handover_dept_name');
        const lokasiInput = document.getElementById('handover_lokasi');

        if (userSelect) {
            userSelect.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const dept = selected ? selected.getAttribute('data-dept') : '';
                const lokasi = selected ? selected.getAttribute('data-lokasi') : '';

                if (dept && deptPreview && deptName) {
                    deptName.textContent = dept;
                    deptPreview.classList.remove('d-none');
                } else if (deptPreview) {
                    deptPreview.classList.add('d-none');
                }

                if (lokasi && lokasiInput) {
                    lokasiInput.value = lokasi;
                }
            });
        }
    });
</script>
@endpush