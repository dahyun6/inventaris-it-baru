@extends('layout')

@section('title', __('Detail & Riwayat Aset'))

@section('header_actions')
<div class="d-flex gap-2">
    <a href="{{ route('barang.index') }}" class="btn btn-phoenix-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Kembali ke Data Aset') }}
    </a>
    <a href="{{ route('barang.handover', $barang->uuid) }}" class="btn btn-phoenix-primary btn-sm">
        <i class="fas fa-right-left me-1"></i> {{ __('Form Handover') }}
    </a>
</div>
@endsection

@section('content')
<style>
    .table-phoenix {
        margin-bottom: 0;
        font-size: 0.825rem;
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
        padding: 0.75rem 1rem !important;
    }
    .table-phoenix tbody td {
        border-bottom: 1px solid var(--phoenix-border-color);
        color: var(--phoenix-text-body);
        padding: 0.75rem 1rem !important;
    }
    .table-phoenix tbody tr:hover {
        background-color: #f8fafc;
    }
</style>

<div class="row g-4">
    <!-- Left Column: Device Info -->
    <div class="col-lg-4">
        <div class="phoenix-card h-100">
            <div class="phoenix-card-header">
                <h6 class="phoenix-card-title">
                    <i class="fas fa-microchip text-primary"></i>
                    {{ __('Informasi Hardware') }}
                </h6>
                <span class="badge-phoenix badge-phoenix-primary">
                    {{ $barang->no_aset_local ?? 'Aset' }}
                </span>
            </div>
            <div class="phoenix-card-body text-center p-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                    <i class="fas fa-laptop"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $barang->nama_barang ?? $barang->model }}</h5>
                <p class="text-muted font-monospace small mb-3">SN: {{ $barang->serial_number ?? '-' }}</p>

                <div class="text-start border-top pt-3">
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small font-semibold">{{ __('Kategori') }}</span>
                        <span class="fw-bold text-dark small">{{ $barang->category->nama_kategori ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small font-semibold">{{ __('Status Aset') }}</span>
                        @if($barang->status == 'Tersedia')
                            <span class="badge-phoenix badge-phoenix-success"><i class="fas fa-check"></i> {{ __('Tersedia') }}</span>
                        @elseif($barang->status == 'Dipinjam')
                            <span class="badge-phoenix badge-phoenix-warning"><i class="fas fa-user-clock"></i> {{ __('Dipinjam') }}</span>
                        @else
                            <span class="badge-phoenix badge-phoenix-danger"><i class="fas fa-triangle-exclamation"></i> {{ __('Rusak') }}</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small font-semibold">{{ __('Hostname') }}</span>
                        <span class="font-monospace small text-dark">{{ $barang->hostname ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small font-semibold">{{ __('Departemen') }}</span>
                        <span class="small text-dark">{{ $barang->dept ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small font-semibold">{{ __('Lokasi Unit') }}</span>
                        <span class="small text-dark">{{ $barang->unit_loc ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small font-semibold">{{ __('Tanggal Registrasi') }}</span>
                        <span class="font-monospace small text-dark">{{ $barang->created_at->format('d M Y') }}</span>
                    </div>
                </div>

                @if($barang->type_spec)
                <div class="text-start bg-light p-3 rounded-3 mt-3 border">
                    <span class="d-block text-muted small fw-bold mb-1">{{ __('Spesifikasi') }}:</span>
                    <small class="text-secondary">{{ $barang->type_spec }}</small>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Handover Logs -->
    <div class="col-lg-8">
        <div class="phoenix-card h-100">
            <div class="phoenix-card-header">
                <h6 class="phoenix-card-title">
                    <i class="fas fa-clock-rotate-left text-primary"></i>
                    {{ __('Log Riwayat Pergerakan & Serah Terima') }}
                </h6>
                <span class="badge-phoenix badge-phoenix-info font-monospace">{{ $barang->riwayat->count() }} {{ __('Transaksi') }}</span>
            </div>
            <div class="phoenix-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-phoenix">
                        <thead>
                            <tr>
                                <th class="ps-3">{{ __('TANGGAL') }}</th>
                                <th>{{ __('PENGGUNA / PENERIMA') }}</th>
                                <th>{{ __('LOKASI') }}</th>
                                <th>{{ __('CATATAN KONDISI') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barang->riwayat as $log)
                            <tr>
                                <td class="ps-3 text-nowrap font-monospace">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}</td>
                                <td>
                                    @if($log->user)
                                        <span class="fw-bold text-dark"><i class="far fa-user me-1 text-primary"></i> {{ $log->user->name }}</span>
                                    @else
                                        <span class="fw-semibold text-success"><i class="fas fa-warehouse me-1"></i> Gudang IT</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border"><i class="fas fa-location-dot me-1 text-primary"></i>{{ $log->lokasi }}</span>
                                </td>
                                <td class="text-secondary small">{{ $log->keterangan ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fs-2 opacity-25 mb-2"></i><br>
                                    {{ __('Belum ada catatan log serah terima untuk aset ini.') }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection