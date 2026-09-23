@extends('layout')

@section('title', __('Dashboard Overview'))

@section('header_actions')
<div class="d-flex gap-2">
    <a href="{{ route('handover.create') }}" class="btn btn-phoenix-secondary btn-sm">
        <i class="fas fa-file-signature me-1"></i> {{ __('Tanda Terima Baru') }}
    </a>
    <a href="{{ route('barang.index') }}" class="btn btn-phoenix-primary btn-sm">
        <i class="fas fa-boxes-stacked me-1"></i> {{ __('Kelola Aset') }}
    </a>
</div>
@endsection

@section('content')

<style>
    /* Phoenix Dashboard KPI Card */
    .phoenix-stat-card {
        background: #ffffff;
        border: 1px solid var(--phoenix-border-color);
        border-radius: 10px;
        padding: 1.25rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .phoenix-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04);
    }
    .stat-icon-wrapper {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .stat-number {
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--phoenix-text-emphasis);
        line-height: 1.2;
        letter-spacing: -0.03em;
        margin-top: 0.5rem;
    }
    .stat-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--phoenix-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    /* Activity feed in Phoenix style */
    .timeline-item {
        position: relative;
        padding-left: 1.75rem;
        padding-bottom: 1.25rem;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: 6px;
        top: 24px;
        bottom: 0;
        width: 2px;
        background-color: var(--phoenix-border-color);
    }
    .timeline-item:last-child::before {
        display: none;
    }
    .timeline-dot {
        position: absolute;
        left: 0;
        top: 6px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background-color: var(--phoenix-primary);
        border: 2px solid #ffffff;
        box-shadow: 0 0 0 2px var(--phoenix-primary-subtle);
    }

    /* Handover feed item in Phoenix style */
    .handover-feed-item {
        position: relative;
        padding: 0.9rem 1rem;
        border-bottom: 1px solid var(--phoenix-border-color);
        transition: background-color 0.15s ease;
    }
    .handover-feed-item:last-child {
        border-bottom: none;
    }
    .handover-feed-item:hover {
        background-color: #f8fafc;
    }
    .handover-route-box {
        background-color: #f8fafc;
        border: 1px solid var(--phoenix-border-color);
        border-radius: 8px;
        padding: 0.55rem 0.75rem;
    }
</style>

<!-- PHOENIX KPI METRICS -->
<div class="row g-3 mb-4">
    <!-- Total Aset -->
    <div class="col-sm-6 col-xl-3">
        <div class="phoenix-stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <span class="stat-label">{{ __('Total Aset Terdaftar') }}</span>
                <div class="stat-icon-wrapper bg-primary-subtle text-primary">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
            </div>
            <div>
                <div class="stat-number">{{ $total_aset }}</div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="badge-phoenix badge-phoenix-primary">
                        <i class="fas fa-database"></i> {{ __('Hardware & Devices') }}
                    </span>
                    <a href="{{ route('barang.index') }}" class="text-decoration-none text-primary fw-semibold small">
                        {{ __('Lihat') }} <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Aset Tersedia -->
    <div class="col-sm-6 col-xl-3">
        <div class="phoenix-stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <span class="stat-label">{{ __('Aset Tersedia (Gudang)') }}</span>
                <div class="stat-icon-wrapper bg-success-subtle text-success">
                    <i class="fas fa-circle-check"></i>
                </div>
            </div>
            <div>
                <div class="stat-number text-success">{{ $aset_tersedia }}</div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="badge-phoenix badge-phoenix-success">
                        <i class="fas fa-warehouse"></i> {{ __('Siap Pakai') }}
                    </span>
                    <a href="{{ route('barang.index') }}" class="text-decoration-none text-success fw-semibold small">
                        {{ __('Filter') }} <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Aset Dipinjam -->
    <div class="col-sm-6 col-xl-3">
        <div class="phoenix-stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <span class="stat-label">{{ __('Aset Sedang Dipinjam') }}</span>
                <div class="stat-icon-wrapper bg-warning-subtle text-warning">
                    <i class="fas fa-handshake"></i>
                </div>
            </div>
            <div>
                <div class="stat-number text-warning">{{ $aset_dipinjam }}</div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="badge-phoenix badge-phoenix-warning">
                        <i class="fas fa-user-check"></i> {{ __('Active in Use') }}
                    </span>
                    <a href="{{ route('handover.history') }}" class="text-decoration-none text-warning fw-semibold small">
                        {{ __('Riwayat') }} <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Aset Rusak -->
    <div class="col-sm-6 col-xl-3">
        <div class="phoenix-stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <span class="stat-label">{{ __('Aset Rusak / Perbaikan') }}</span>
                <div class="stat-icon-wrapper bg-danger-subtle text-danger">
                    <i class="fas fa-screwdriver-wrench"></i>
                </div>
            </div>
            <div>
                <div class="stat-number text-danger">{{ $aset_rusak }}</div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="badge-phoenix badge-phoenix-danger">
                        <i class="fas fa-triangle-exclamation"></i> {{ __('Maintenance') }}
                    </span>
                    <a href="{{ route('barang.index') }}" class="text-decoration-none text-danger fw-semibold small">
                        {{ __('Review') }} <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS & RECENT ACTIVITY -->
<div class="row g-4">
    <!-- Trend Chart -->
    <div class="col-lg-7 col-xl-8">
        <div class="phoenix-card h-100">
            <div class="phoenix-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="phoenix-card-title">
                        <i class="fas fa-chart-line text-primary"></i>
                        {{ __('Trend Pergerakan & Penambahan Aset') }}
                    </h6>
                    <small class="text-muted">{{ __('Monitoring pengadaan aset masuk vs aset selesai perbaikan') }}</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <select id="selectYearTrend" class="form-select form-select-sm" style="width: auto; min-width: 90px; border-radius: 6px; font-weight: 600; font-size: 0.8125rem;">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                    <select id="selectMonthTrend" class="form-select form-select-sm" style="width: auto; min-width: 155px; border-radius: 6px; font-weight: 600; font-size: 0.8125rem;">
                        @foreach($monthList as $value => $label)
                            <option value="{{ $value }}" {{ $selectedMonth === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="phoenix-card-body">
                <!-- Phoenix Total Sells Mini Summary -->
                <div class="d-flex align-items-center flex-wrap gap-4 mb-3 pb-3 border-bottom">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                            {{ __('Total Aset Masuk') }}
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="fs-5 fw-bold text-dark" id="statTotalMasuk">{{ $total_masuk_periode }}</span>
                            <span class="badge-phoenix badge-phoenix-primary" style="font-size: 0.6875rem;">
                                <i class="fas fa-box-archive"></i> Unit
                            </span>
                        </div>
                    </div>
                    <div class="vr opacity-25 d-none d-sm-block" style="height: 32px;"></div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                            {{ __('Total Selesai Diperbaiki') }}
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <span class="fs-5 fw-bold text-success" id="statTotalDiperbaiki">{{ $total_diperbaiki_periode }}</span>
                            <span class="badge-phoenix badge-phoenix-success" style="font-size: 0.6875rem;">
                                <i class="fas fa-screwdriver-wrench"></i> Unit
                            </span>
                        </div>
                    </div>
                </div>

                <div style="height: 280px; position: relative;">
                    <canvas id="assetTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Handover Activity Feed -->
    <div class="col-lg-5 col-xl-4">
        <div class="phoenix-card h-100 d-flex flex-column justify-content-between">
            <div class="phoenix-card-header d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="phoenix-card-title">
                        <i class="fas fa-arrow-right-arrow-left text-primary"></i>
                        {{ __('Aktivitas Handover Terkini') }}
                    </h6>
                    <small class="text-muted">{{ __('Log serah terima unit & pergerakan lokasi') }}</small>
                </div>
                <span class="badge-phoenix badge-phoenix-primary">{{ $recent_handovers->count() }} {{ __('Terkini') }}</span>
            </div>
            <div class="phoenix-card-body p-0 flex-grow-1" style="max-height: 400px; overflow-y: auto;">
                @forelse($recent_handovers as $log)
                <div class="handover-feed-item">
                    <!-- Top row: No Aset + Kategori & Tanggal Lengkap -->
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="d-flex align-items-center gap-1.5 overflow-hidden">
                            @if($log->barang)
                                <a href="{{ route('barang.show', $log->barang->uuid) }}" class="fw-bold font-monospace text-primary text-decoration-none" style="font-size: 0.8125rem;" title="{{ __('Lihat Detail Aset') }}">
                                    <i class="fas fa-barcode me-1"></i>{{ $log->barang->no_aset_local }}
                                </a>
                                <span class="badge bg-light text-secondary border font-monospace text-truncate" style="font-size: 0.675rem;">
                                    {{ $log->barang->category->nama_kategori ?? 'Unit Aset' }}
                                </span>
                            @else
                                <span class="fw-bold text-dark font-monospace" style="font-size: 0.8125rem;">Unit #{{ $log->barang_id }}</span>
                            @endif
                        </div>
                        <div class="text-muted font-monospace text-nowrap" style="font-size: 0.725rem;" title="{{ \Carbon\Carbon::parse($log->tanggal_serah_terima ?? $log->created_at)->format('d M Y, H:i') }}">
                            <i class="far fa-calendar-alt text-primary me-1"></i>{{ \Carbon\Carbon::parse($log->tanggal_serah_terima ?? $log->created_at)->translatedFormat('d M Y') }}
                        </div>
                    </div>

                    <!-- Model / Nama Unit (Per 1 Unit) -->
                    <div class="text-dark fw-semibold text-truncate mb-2" style="font-size: 0.825rem;">
                        {{ $log->barang->nama_barang ?? ($log->barang->model ?? 'Perangkat IT') }}
                        @if($log->barang && $log->barang->serial_number)
                            <span class="text-muted fw-normal font-monospace" style="font-size: 0.725rem;">&bull; SN: {{ $log->barang->serial_number }}</span>
                        @endif
                    </div>

                    <!-- Route Box: Dari Pihak A ke B & Dari Lokasi Asal ke Tujuan -->
                    <div class="handover-route-box mb-2">
                        <!-- Pihak A ke Pihak B -->
                        <div class="d-flex align-items-center justify-content-between mb-1 pb-1 border-bottom flex-wrap gap-1" style="font-size: 0.775rem;">
                            <div class="d-flex align-items-center text-truncate" style="max-width: 46%;">
                                <span class="text-muted me-1 small"><i class="fas fa-user-minus text-secondary me-0.5"></i>{{ __('Dari:') }}</span>
                                <span class="fw-bold text-dark text-truncate" title="{{ $log->pemberi_nama }}">{{ $log->pemberi_nama }}</span>
                            </div>
                            <div class="text-primary px-1" style="font-size: 0.75rem;">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                            <div class="d-flex align-items-center text-truncate" style="max-width: 46%;">
                                <span class="text-muted me-1 small"><i class="fas fa-user-plus text-primary me-0.5"></i>{{ __('Ke:') }}</span>
                                <span class="fw-bold text-primary text-truncate" title="{{ $log->penerima_display }}">{{ $log->penerima_display }}</span>
                            </div>
                        </div>

                        <!-- Lokasi Asal ke Lokasi Tujuan -->
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-1" style="font-size: 0.725rem;">
                            <div class="d-flex align-items-center text-secondary text-truncate" style="max-width: 46%;">
                                <i class="fas fa-map-pin text-danger me-1 flex-shrink-0"></i>
                                <span class="text-truncate font-monospace" title="{{ $log->lokasi_asal }}">{{ $log->lokasi_asal }}</span>
                            </div>
                            <div class="text-muted px-1" style="font-size: 0.65rem;">
                                <i class="fas fa-angles-right text-success"></i>
                            </div>
                            <div class="d-flex align-items-center text-dark text-truncate" style="max-width: 46%;">
                                <i class="fas fa-location-dot text-success me-1 flex-shrink-0"></i>
                                <span class="fw-bold text-dark text-truncate font-monospace" title="{{ $log->lokasi }}">{{ $log->lokasi }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom row: Dokumen Surat Tanda Terima (STT) & Relative Time -->
                    <div class="d-flex align-items-center justify-content-between pt-0.5">
                        <div>
                            @if($log->no_surat)
                                <a href="{{ route('handover.receipt', $log->uuid ?? $log->no_surat) }}" target="_blank" class="badge-phoenix badge-phoenix-info text-decoration-none" style="font-size: 0.6875rem;">
                                    <i class="fas fa-file-signature me-1"></i> {{ $log->no_surat }}
                                </a>
                            @else
                                <span class="badge bg-light text-muted border font-monospace" style="font-size: 0.6875rem;">
                                    <i class="fas fa-file-lines me-1"></i> {{ __('Log Internal') }}
                                </span>
                            @endif
                        </div>
                        <div class="text-muted small font-monospace" style="font-size: 0.7rem;">
                            {{ \Carbon\Carbon::parse($log->tanggal_serah_terima ?? $log->created_at)->diffForHumans() }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-5 px-3">
                    <i class="fas fa-clipboard-check fs-2 opacity-25 mb-2"></i>
                    <p class="mb-0 small fw-semibold text-dark">{{ __('Belum Ada Aktivitas Handover') }}</p>
                    <small class="text-muted">{{ __('Data serah terima aset per unit akan tampil di sini.') }}</small>
                </div>
                @endforelse
            </div>
            <div class="p-3 bg-light border-top text-center">
                <a href="{{ route('handover.history') }}" class="text-decoration-none text-primary fw-bold small">
                    {{ __('Buka Semua Riwayat Handover') }} <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- PREVENTIVE MAINTENANCE & AUDIT ACTIVITY ROW -->
<div class="row g-4 mt-1 mb-2">
    <!-- Upcoming Preventive Maintenance Widget -->
    <div class="col-lg-6">
        <div class="phoenix-card h-100">
            <div class="phoenix-card-header d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="phoenix-card-title">
                        <i class="fas fa-calendar-check text-warning"></i>
                        {{ __('Preventive Maintenance Mendatang') }}
                    </h6>
                    <small class="text-muted">{{ __('Aset yang memerlukan servis/pemeliharaan berkala') }}</small>
                </div>
                @if(isset($preventive_overdue_count) && $preventive_overdue_count > 0)
                    <span class="badge bg-danger-subtle text-danger fw-bold font-monospace" style="font-size: 0.75rem;">
                        <i class="fas fa-circle-exclamation me-1"></i>{{ $preventive_overdue_count }} Overdue
                    </span>
                @else
                    <span class="badge-phoenix badge-phoenix-warning">{{ count($upcoming_preventives ?? []) }} {{ __('Terjadwal') }}</span>
                @endif
            </div>
            <div class="phoenix-card-body p-0">
                @if(isset($upcoming_preventives) && $upcoming_preventives->isNotEmpty())
                    <div class="list-group list-group-flush">
                        @foreach($upcoming_preventives as $pItem)
                        <div class="list-group-item d-flex align-items-center justify-content-between p-3 border-bottom">
                            <div class="d-flex align-items-center gap-3 overflow-hidden me-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center {{ $pItem->isMaintenanceOverdue() ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning' }} flex-shrink-0" style="width: 38px; height: 38px; font-size: 1rem;">
                                    <i class="fas {{ $pItem->isMaintenanceOverdue() ? 'fa-triangle-exclamation' : 'fa-clock' }}"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('barang.show', $pItem->uuid) }}" class="fw-bold text-dark font-monospace text-decoration-none text-truncate" style="font-size: 0.85rem;">
                                            {{ $pItem->no_aset_local }}
                                        </a>
                                        <span class="badge bg-light text-secondary border" style="font-size: 0.6875rem;">
                                            {{ $pItem->category->nama_kategori ?? 'Aset' }}
                                        </span>
                                    </div>
                                    <div class="text-muted small text-truncate" style="font-size: 0.775rem;">
                                        {{ $pItem->nama_barang ?? $pItem->model }} &bull; <span class="text-dark">{{ $pItem->user->name ?? ($pItem->pengguna ?? 'Belum ada pengguna') }}</span>
                                    </div>
                                    <div class="small mt-0.5" style="font-size: 0.725rem;">
                                        @if($pItem->isMaintenanceOverdue())
                                            <span class="text-danger fw-bold"><i class="fas fa-circle-exclamation me-1"></i>{{ __('Jatuh tempo:') }} {{ $pItem->tgl_maintenance_berikutnya?->format('d M Y') }}</span>
                                        @else
                                            <span class="text-secondary"><i class="fas fa-calendar-day me-1 text-warning"></i>{{ __('Jadwal:') }} {{ $pItem->tgl_maintenance_berikutnya?->format('d M Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('maintenance.create', ['barang_id' => $pItem->id]) }}" class="btn btn-outline-primary btn-sm px-2.5 py-1 text-nowrap flex-shrink-0" style="font-size: 0.775rem;">
                                <i class="fas fa-screwdriver-wrench me-1"></i>{{ __('Servis') }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4 px-3">
                        <i class="fas fa-circle-check fs-2 text-success opacity-50 mb-2"></i>
                        <p class="mb-0 small fw-semibold text-dark">{{ __('Semua Aset Terpelihara Baik') }}</p>
                        <small class="text-muted">{{ __('Tidak ada jadwal maintenance yang mendekati batas waktu.') }}</small>
                    </div>
                @endif
            </div>
            <div class="p-3 bg-light border-top text-center">
                <a href="{{ route('maintenance.index') }}" class="text-decoration-none text-primary fw-bold small">
                    {{ __('Buka Modul Maintenance Aset') }} <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Audit Activity Log Widget -->
    <div class="col-lg-6">
        <div class="phoenix-card h-100">
            <div class="phoenix-card-header d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="phoenix-card-title">
                        <i class="fas fa-clock-rotate-left text-primary"></i>
                        {{ __('Audit Activity Log Terkini') }}
                    </h6>
                    <small class="text-muted">{{ __('Rekam jejak aktivitas & transparansi sistem') }}</small>
                </div>
                <a href="{{ route('activity_logs.index') }}" class="badge-phoenix badge-phoenix-primary text-decoration-none">
                    {{ __('Semua Log') }}
                </a>
            </div>
            <div class="phoenix-card-body p-0">
                @if(isset($recent_activities) && $recent_activities->isNotEmpty())
                    <div class="list-group list-group-flush">
                        @foreach($recent_activities as $act)
                        <div class="list-group-item p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <span class="badge {{ $act->getActionBadgeClass() }} me-1 font-monospace" style="font-size: 0.65rem;">
                                        {{ $act->action }}
                                    </span>
                                    <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.65rem;">
                                        {{ $act->module }}
                                    </span>
                                    <span class="fw-bold text-dark ms-1" style="font-size: 0.825rem;">
                                        {{ $act->user_name }}
                                    </span>
                                </div>
                                <small class="text-muted font-monospace text-nowrap" style="font-size: 0.7rem;">
                                    {{ $act->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <div class="text-secondary small mt-1 text-truncate" style="font-size: 0.775rem;">
                                {{ $act->description }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4 px-3">
                        <i class="fas fa-list-check fs-2 opacity-25 mb-2"></i>
                        <p class="mb-0 small">{{ __('Belum ada riwayat aktivitas sistem tercatat.') }}</p>
                    </div>
                @endif
            </div>
            <div class="p-3 bg-light border-top text-center">
                <a href="{{ route('activity_logs.index') }}" class="text-decoration-none text-primary fw-bold small">
                    {{ __('Lihat Log Aktivitas Lengkap') }} <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('assetTrendChart').getContext('2d');
        
        // Phoenix Gradient Palette
        let gradientPrimary = ctx.createLinearGradient(0, 0, 0, 280);
        gradientPrimary.addColorStop(0, 'rgba(56, 116, 255, 0.35)');
        gradientPrimary.addColorStop(1, 'rgba(56, 116, 255, 0.02)');

        let gradientSuccess = ctx.createLinearGradient(0, 0, 0, 280);
        gradientSuccess.addColorStop(0, 'rgba(37, 184, 101, 0.35)');
        gradientSuccess.addColorStop(1, 'rgba(37, 184, 101, 0.02)');

        const trendChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels_grafik) !!},
                datasets: [
                    {
                        label: '{{ __("Aset Masuk") }}',
                        data: {!! json_encode($data_aset_masuk) !!},
                        borderColor: '#3874ff',
                        backgroundColor: gradientPrimary,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#3874ff',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.35
                    },
                    {
                        label: '{{ __("Aset Diperbaiki") }}',
                        data: {!! json_encode($data_aset_diperbaiki) !!},
                        borderColor: '#25b865',
                        backgroundColor: gradientSuccess,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#25b865',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.35
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 10,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { family: "'Nunito Sans', sans-serif", size: 12, weight: 600 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#141824',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: { family: "'Nunito Sans', sans-serif", size: 12, weight: 700 },
                        bodyFont: { family: "'Nunito Sans', sans-serif", size: 12 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [3, 3], color: '#e3e6ed' },
                        ticks: {
                            precision: 0,
                            font: { family: "'Nunito Sans', sans-serif", size: 11 },
                            color: '#6e7891'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: "'Nunito Sans', sans-serif", size: 11 },
                            color: '#6e7891'
                        }
                    }
                }
            }
        });

        // Filter by Year & Month AJAX Handler
        const yearSelect = document.getElementById('selectYearTrend');
        const monthSelect = document.getElementById('selectMonthTrend');

        function reloadTrendChart() {
            const y = yearSelect ? yearSelect.value : '';
            const m = monthSelect ? monthSelect.value : '';

            fetch(`{{ route('dashboard') }}?year=${encodeURIComponent(y)}&month=${encodeURIComponent(m)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                trendChart.data.labels = data.labels;
                trendChart.data.datasets[0].data = data.data_aset_masuk;
                trendChart.data.datasets[1].data = data.data_aset_diperbaiki;
                trendChart.update();

                const elMasuk = document.getElementById('statTotalMasuk');
                const elDiperbaiki = document.getElementById('statTotalDiperbaiki');
                if (elMasuk) elMasuk.textContent = data.total_masuk;
                if (elDiperbaiki) elDiperbaiki.textContent = data.total_diperbaiki;
            })
            .catch(err => console.error("Error loading trend chart data:", err));
        }

        if (yearSelect) {
            yearSelect.addEventListener('change', reloadTrendChart);
        }
        if (monthSelect) {
            monthSelect.addEventListener('change', reloadTrendChart);
        }
    });
</script>
@endpush