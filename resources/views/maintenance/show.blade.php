@extends('layout')

@section('title', __('Detail Tiket Maintenance'))

@section('header_actions')
<div class="d-flex flex-wrap gap-2">
    <a href="{{ route('maintenance.index') }}" class="btn btn-phoenix-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Daftar Maintenance') }}
    </a>
    <a href="{{ route('maintenance.print', $maintenance->uuid) }}" target="_blank" class="btn btn-phoenix-secondary btn-sm">
        <i class="fas fa-print me-1"></i> {{ __('Cetak SPK / Laporan') }}
    </a>
    <a href="{{ route('maintenance.edit', $maintenance->uuid) }}" class="btn btn-phoenix-secondary btn-sm">
        <i class="fas fa-pencil me-1 text-warning"></i> {{ __('Edit') }}
    </a>
    @if($maintenance->status === 'Dalam Proses')
    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalShowComplete">
        <i class="fas fa-circle-check me-1"></i> {{ __('Selesaikan Servis') }}
    </button>
    @endif
</div>
@endsection

@section('content')
<div class="row g-4">
    <!-- LEFT COLUMN: ASSET INFO -->
    <div class="col-lg-4">
        <div class="phoenix-card h-100">
            <div class="phoenix-card-header">
                <h6 class="phoenix-card-title mb-0">
                    <i class="fas fa-laptop-code text-primary"></i>
                    {{ __('Perangkat Terkait') }}
                </h6>
                <span class="badge-phoenix badge-phoenix-primary font-monospace">
                    {{ $maintenance->barang->no_aset_local ?? 'Aset' }}
                </span>
            </div>
            <div class="phoenix-card-body text-center p-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mb-3" style="width: 64px; height: 64px; font-size: 1.75rem;">
                    <i class="fas fa-laptop"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $maintenance->barang->nama_barang ?? $maintenance->barang->model }}</h5>
                <p class="text-muted font-monospace small mb-3">SN: {{ $maintenance->barang->serial_number ?? '-' }}</p>

                <div class="text-start border-top pt-3">
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small">{{ __('Kategori') }}</span>
                        <span class="fw-bold text-dark small">{{ $maintenance->barang->category->nama_kategori ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small">{{ __('Status Aset Saat Ini') }}</span>
                        @if($maintenance->barang->status == 'Tersedia')
                            <span class="badge-phoenix badge-phoenix-success"><i class="fas fa-check"></i> {{ __('Tersedia') }}</span>
                        @elseif($maintenance->barang->status == 'Dipinjam')
                            <span class="badge-phoenix badge-phoenix-warning"><i class="fas fa-user-clock"></i> {{ __('Dipinjam') }}</span>
                        @else
                            <span class="badge-phoenix badge-phoenix-danger"><i class="fas fa-triangle-exclamation"></i> {{ __('Rusak') }}</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small">{{ __('Lokasi Terakhir') }}</span>
                        <span class="small text-dark">{{ $maintenance->barang->unit_loc ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small">{{ __('Pengguna Terakhir') }}</span>
                        <span class="small text-dark fw-semibold">{{ $maintenance->barang->pengguna ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small">{{ __('Lihat Master Aset') }}</span>
                        <a href="{{ route('barang.show', $maintenance->barang->uuid) }}" class="btn btn-phoenix-secondary btn-sm py-0 px-2" style="font-size: 0.75rem;">
                            {{ __('Detail Aset') }} <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: MAINTENANCE TICKET DETAILS -->
    <div class="col-lg-8">
        <div class="phoenix-card h-100">
            <div class="phoenix-card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="phoenix-card-title mb-0">
                        <i class="fas fa-file-lines text-primary"></i>
                        {{ __('Rincian Tiket Maintenance') }}: <span class="font-monospace">{{ $maintenance->no_maintenance }}</span>
                    </h6>
                </div>
                <div>
                    @if($maintenance->status === 'Dalam Proses')
                        <span class="badge-phoenix badge-phoenix-warning fs-0"><i class="fas fa-clock"></i> {{ __('Dalam Proses') }}</span>
                    @elseif($maintenance->status === 'Selesai')
                        <span class="badge-phoenix badge-phoenix-success fs-0"><i class="fas fa-circle-check"></i> {{ __('Selesai') }}</span>
                    @else
                        <span class="badge-phoenix badge-phoenix-secondary fs-0"><i class="fas fa-ban"></i> {{ __('Dibatalkan') }}</span>
                    @endif
                </div>
            </div>

            <div class="phoenix-card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small d-block mb-1">{{ __('Jenis Servis') }}</span>
                            <span class="fw-bold text-dark">{{ $maintenance->jenis_maintenance }}</span>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small d-block mb-1">{{ __('Tanggal Pengerjaan') }}</span>
                            <div class="fw-bold text-dark font-monospace">{{ $maintenance->tanggal_mulai ? $maintenance->tanggal_mulai->format('d M Y') : '-' }}</div>
                            @if($maintenance->tanggal_selesai)
                                <small class="text-success"><i class="fas fa-check-circle me-1"></i>Selesai: {{ $maintenance->tanggal_selesai->format('d M Y') }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small d-block mb-1">{{ __('Biaya Perbaikan') }}</span>
                            <span class="fw-bold text-primary font-monospace" style="font-size: 1.1rem;">
                                Rp {{ number_format($maintenance->biaya, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small d-block mb-1">{{ __('Pelaksana Servis') }}</span>
                            @if($maintenance->pelaksana === 'Vendor Eksternal')
                                <div class="fw-bold text-dark"><i class="fas fa-building text-info me-1"></i>{{ $maintenance->vendor->nama_vendor ?? 'Vendor Eksternal' }}</div>
                                @if($maintenance->vendor)
                                    <small class="text-muted d-block">{{ $maintenance->vendor->telepon ?? '' }} | {{ $maintenance->vendor->alamat ?? '' }}</small>
                                @endif
                            @else
                                <div class="fw-bold text-dark"><i class="fas fa-user-gear text-primary me-1"></i>Internal IT Team</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <span class="text-muted small d-block mb-1">{{ __('Teknisi / Penanggung Jawab') }}</span>
                            <div class="fw-bold text-dark">{{ $maintenance->nama_teknisi ?? '-' }}</div>
                            <small class="text-muted">{{ __('Dicatat oleh:') }} {{ $maintenance->user->name ?? 'Admin IT' }}</small>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2" style="font-size: 0.825rem;">
                        <i class="fas fa-triangle-exclamation text-danger me-1"></i> {{ __('Gejala & Deskripsi Kendala Kerusakan') }}
                    </h6>
                    <div class="p-3 bg-light rounded-3 border text-secondary" style="font-size: 0.875rem; white-space: pre-line;">
                        {{ $maintenance->deskripsi_kendala }}
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="fw-bold text-dark mb-2" style="font-size: 0.825rem;">
                        <i class="fas fa-check-double text-success me-1"></i> {{ __('Tindakan Perbaikan & Solusi Penanganan') }}
                    </h6>
                    <div class="p-3 bg-light rounded-3 border text-secondary" style="font-size: 0.875rem; white-space: pre-line;">
                        {{ $maintenance->tindakan_perbaikan ?: __('Belum ada catatan tindakan perbaikan / masih dalam tahap penanganan.') }}
                    </div>
                </div>

                @if($maintenance->status_aset_setelahnya)
                <div class="alert alert-light border d-flex align-items-center justify-content-between p-3 mb-0">
                    <span class="small text-muted">{{ __('Status yang ditetapkan pada aset setelah perbaikan:') }}</span>
                    <span class="badge-phoenix badge-phoenix-info">{{ $maintenance->status_aset_setelahnya }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- MODAL QUICK COMPLETE -->
<div class="modal fade" id="modalShowComplete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold text-dark">
                    <i class="fas fa-circle-check text-success me-1"></i>
                    {{ __('Selesaikan Maintenance / Servis') }}
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('maintenance.complete', $maintenance->uuid) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 p-3 mb-3 d-flex align-items-center gap-2" style="background-color: var(--phoenix-primary-subtle); color: var(--phoenix-primary); border-radius: 8px;">
                        <i class="fas fa-laptop-medical fs-4"></i>
                        <div>
                            <div class="fw-bold small">{{ $maintenance->no_maintenance }}</div>
                            <div class="small">{{ $maintenance->barang->no_aset_local ?? '' }} - {{ $maintenance->barang->nama_barang ?? ($maintenance->barang->model ?? '') }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger">{{ __('Tanggal Selesai Servis') }} *</label>
                        <input type="date" name="tanggal_selesai" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Biaya Final (Rp)') }}</label>
                            <input type="number" step="any" name="biaya" class="form-control form-control-sm" placeholder="0" value="{{ (int)$maintenance->biaya }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Teknisi Pelaksana') }}</label>
                            <input type="text" name="nama_teknisi" class="form-control form-control-sm" placeholder="Nama Teknisi" value="{{ $maintenance->nama_teknisi }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger">{{ __('Tindakan / Solusi Perbaikan') }} *</label>
                        <textarea name="tindakan_perbaikan" class="form-control form-control-sm" rows="3" placeholder="Misal: Penggantian SSD 512GB, install Windows 11, pembersihan pasta thermal..." required>{{ $maintenance->tindakan_perbaikan }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger">{{ __('Ubah Status Aset Menjadi') }} *</label>
                        <select name="status_aset_setelahnya" class="form-select form-select-sm" required>
                            <option value="Tersedia" selected>{{ __('Tersedia (Siap Digunakan / Di Gudang IT)') }}</option>
                            <option value="Dipinjam">{{ __('Dipinjam (Kembali Digunakan Pegawai)') }}</option>
                            <option value="Rusak">{{ __('Rusak (Afkir / Tidak Dapat Diperbaiki)') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-phoenix-secondary btn-sm" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-success btn-sm px-3">
                        <i class="fas fa-check me-1"></i> {{ __('Simpan & Selesaikan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
