@extends('layout')

@section('title', 'Form Serah Terima (Handover)')

@section('content')
<style>
    .card-admin { background: #fff; border-radius: 4px; box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2); margin-bottom: 20px; border-top: 3px solid #17a2b8; }
    .card-header-admin { padding: 15px 20px; border-bottom: 1px solid rgba(0,0,0,.125); font-weight: 600; color: #343a40; background-color: #f8f9fa; }
</style>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card-admin">
            <div class="card-header-admin">
                <i class="fas fa-exchange-alt text-info me-2"></i> Form Handover Aset
            </div>
            <div class="card-body p-4">
                
                <div class="alert alert-light border shadow-sm mb-4">
                    Pencatatan aset: <strong>{{ $barang->nama_barang }} ({{ $barang->serial_number }})</strong>
                </div>

                <form action="{{ route('barang.storeHandover', $barang->uuid) }}" method="POST">
                    @csrf
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Diserahkan Kepada (Pegawai)</label>
                            <select name="user_id" class="form-select">
                                <option value="">-- Kembalikan ke Gudang / IT --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Serah Terima</label>
                            <input type="date" name="tanggal_serah_terima" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Lokasi Baru</label>
                            <input type="text" name="lokasi" class="form-control" placeholder="Misal: Meja Budi, Ruang Server..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ubah Status Barang Menjadi</label>
                            <select name="status" class="form-select" required>
                                <option value="Dipinjam" {{ $barang->status == 'Dipinjam' ? 'selected' : '' }}>Dipinjam (Invited)</option>
                                <option value="Tersedia" {{ $barang->status == 'Tersedia' ? 'selected' : '' }}>Tersedia (Active)</option>
                                <option value="Rusak" {{ $barang->status == 'Rusak' ? 'selected' : '' }}>Rusak (Suspended)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Keterangan / Catatan Kondisi</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Misal: Pindah divisi, atau ada lecet di body..."></textarea>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('barang.show', $barang->uuid) }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-info text-white px-4">Simpan Handover</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection