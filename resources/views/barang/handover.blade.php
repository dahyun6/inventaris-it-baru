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
                            <label class="form-label small fw-bold">Diserahkan Kepada (Pegawai)</label>
                            <select name="user_id" class="form-select">
                                <option value="">-- Kembalikan ke Gudang / IT --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger">Tanggal Serah Terima *</label>
                            <input type="date" name="tanggal_serah_terima" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger">Lokasi Baru *</label>
                            <input type="text" name="lokasi" class="form-control" placeholder="Misal: Meja Budi, Gd. A Lt. 2..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger">Ubah Status Barang Menjadi *</label>
                            <select name="status" class="form-select" required>
                                <option value="Dipinjam" {{ $barang->status == 'Dipinjam' ? 'selected' : '' }}>Dipinjam (Digunakan)</option>
                                <option value="Tersedia" {{ $barang->status == 'Tersedia' ? 'selected' : '' }}>Tersedia (Di Gudang IT)</option>
                                <option value="Rusak" {{ $barang->status == 'Rusak' ? 'selected' : '' }}>Rusak (Dalam Perbaikan)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keterangan / Catatan Kondisi</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Misal: Pindah divisi, kelengkapan adaptor charger..."></textarea>
                    </div>

                    <div class="p-3 bg-light border-top -mx-4 -mb-4 mt-4 d-flex justify-content-between align-items-center" style="margin-left: -1.25rem; margin-right: -1.25rem; margin-bottom: -1.25rem; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                        <a href="{{ route('barang.show', $barang->uuid) }}" class="btn btn-phoenix-secondary btn-sm">Batal</a>
                        <button type="submit" class="btn btn-phoenix-primary btn-sm px-4">
                            <i class="fas fa-save me-1"></i> Simpan Handover
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection