@extends('layout')

@section('title', __('Detail & Riwayat Aset'))

@section('header_actions')
<div class="d-flex flex-wrap gap-2">
    <a href="{{ route('barang.index') }}" class="btn btn-phoenix-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Kembali ke Data Aset') }}
    </a>
    <a href="{{ route('barang.barcode', $barang->uuid) }}" target="_blank" class="btn btn-phoenix-secondary btn-sm" title="{{ __('Cetak Label QR Code Aset') }}">
        <i class="fas fa-qrcode me-1 text-primary"></i> {{ __('Cetak QR Code') }}
    </a>
    @if(!Auth::user()?->isStaff())
    <a href="{{ route('barang.handover', $barang->uuid) }}" class="btn btn-phoenix-primary btn-sm">
        <i class="fas fa-right-left me-1"></i> {{ __('Form Handover') }}
    </a>
    <a href="{{ route('maintenance.create', ['barang_id' => $barang->id]) }}" class="btn btn-phoenix-warning btn-sm">
        <i class="fas fa-screwdriver-wrench me-1"></i> {{ __('Catat Maintenance') }}
    </a>
    @endif
    <a href="{{ route('ticket.index') }}" class="btn btn-phoenix-info btn-sm">
        <i class="fas fa-headset me-1"></i> {{ __('Helpdesk IT') }}
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
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small font-semibold">{{ __('Jadwal Servis Berkala') }}</span>
                        <span class="small">
                            @if($barang->tgl_maintenance_berikutnya)
                                @if($barang->isMaintenanceOverdue())
                                    <span class="badge bg-danger-subtle text-danger font-monospace"><i class="fas fa-circle-exclamation me-1"></i>{{ $barang->tgl_maintenance_berikutnya->format('d M Y') }} ({{ __('Overdue') }})</span>
                                @elseif($barang->isMaintenanceDueSoon())
                                    <span class="badge bg-warning-subtle text-warning font-monospace"><i class="fas fa-clock me-1"></i>{{ $barang->tgl_maintenance_berikutnya->format('d M Y') }} ({{ __('Segera') }})</span>
                                @else
                                    <span class="badge bg-light text-dark border font-monospace">{{ $barang->tgl_maintenance_berikutnya->format('d M Y') }}</span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </span>
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

        <!-- Asset Tag / QR Code Card -->
        <div class="phoenix-card mt-3">
            <div class="phoenix-card-header d-flex align-items-center justify-content-between">
                <h6 class="phoenix-card-title">
                    <i class="fas fa-qrcode text-primary"></i>
                    {{ __('Label & QR Code Aset') }}
                </h6>
                <a href="{{ route('barang.barcode', $barang->uuid) }}" target="_blank" class="badge-phoenix badge-phoenix-primary text-decoration-none">
                    <i class="fas fa-print me-1"></i> {{ __('Cetak Stiker') }}
                </a>
            </div>
            <div class="phoenix-card-body text-center p-3">
                <div class="bg-white p-3 border rounded-3 shadow-sm d-inline-block w-100" style="max-width: 300px; border: 1.5px solid #090d16 !important; text-align: left;">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-1.5 mb-2" style="border-color: #090d16 !important;">
                        <div class="d-flex align-items-center gap-1">
                            <span class="rounded-circle bg-dark d-inline-block" style="width: 6px; height: 6px;"></span>
                            <span class="fw-bold text-dark font-monospace" style="font-size: 0.7rem; letter-spacing: 0.05em;">PANDORA IT ASSET</span>
                        </div>
                        <div class="d-flex gap-1">
                            <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.55rem;">{{ $barang->category->nama_kategori ?? 'HARDWARE' }}</span>
                            @if($barang->dept)
                            <span class="badge bg-dark text-white font-monospace" style="font-size: 0.55rem;">{{ $barang->dept }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2.5 my-2">
                        <div class="d-flex flex-column align-items-center flex-shrink-0">
                            <div id="showQrContainer" class="p-1 border rounded-2 bg-white" style="width: 82px; height: 82px; border-color: #090d16 !important; display: flex; align-items: center; justify-content: center;"></div>
                            <span class="text-muted font-monospace fw-bold mt-1" style="font-size: 0.55rem; letter-spacing: 0.04em;"><i class="fas fa-qrcode"></i> SCAN ME</span>
                        </div>
                        <div class="overflow-hidden flex-1">
                            <div class="bg-light p-1.5 rounded-1 border mb-1.5" style="border-left: 3px solid #090d16 !important;">
                                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.5rem; letter-spacing: 0.05em; line-height: 1;">ASSET NUMBER</div>
                                <div class="fw-bold font-monospace text-dark text-truncate" style="font-size: 0.85rem;">{{ $barang->no_aset_local }}</div>
                            </div>
                            <div class="mb-1">
                                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.5rem; letter-spacing: 0.04em; line-height: 1;">MODEL</div>
                                <div class="fw-bold text-dark text-truncate" style="font-size: 0.75rem;">{{ $barang->model ?? ($barang->nama_barang ?? 'N/A') }}</div>
                            </div>
                            <div>
                                <div class="text-muted text-uppercase fw-bold" style="font-size: 0.5rem; letter-spacing: 0.04em; line-height: 1;">SERIAL NUMBER</div>
                                <div class="font-monospace text-secondary text-truncate" style="font-size: 0.68rem;">{{ $barang->serial_number ?: '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-1.5 border-top mt-2" style="border-color: #cbd5e1 !important; font-size: 0.52rem; font-family: var(--phoenix-font-mono);">
                        <span class="fw-bold text-dark">PROPERTY OF COMPANY</span>
                        <span class="text-muted">DO NOT REMOVE</span>
                    </div>
                </div>

                <div class="mt-2.5">
                    <a href="{{ route('barang.barcode', $barang->uuid) }}" target="_blank" class="btn btn-phoenix-secondary btn-sm w-100 py-1.5" style="font-size: 0.775rem;">
                        <i class="fas fa-print me-1 text-primary"></i> {{ __('Buka Format Cetak Stiker') }}
                    </a>
                </div>
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
                                <th>{{ __('NO DOKUMEN / SURAT') }}</th>
                                <th>{{ __('PIHAK SERAH TERIMA (DARI ➔ KE)') }}</th>
                                <th>{{ __('PERGERAKAN LOKASI (DARI ➔ KE)') }}</th>
                                <th>{{ __('CATATAN KONDISI') }}</th>
                                <th class="text-center">{{ __('DOKUMEN') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barang->riwayat as $log)
                            <tr>
                                <td class="ps-3 text-nowrap font-monospace">{{ \Carbon\Carbon::parse($log->tanggal_serah_terima ?? $log->created_at)->format('d M Y') }}</td>
                                <td>
                                    @if($log->no_surat)
                                        <span class="badge bg-light text-primary border font-monospace px-2 py-1" style="font-size: 0.75rem;">
                                            <i class="fas fa-file-lines me-1"></i>{{ $log->no_surat }}
                                        </span>
                                    @else
                                        <span class="text-muted small">Log Langsung</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                        <span class="text-muted"><i class="fas fa-user-minus text-secondary me-1"></i>{{ $log->pemberi_nama }}</span>
                                        <i class="fas fa-arrow-right text-primary mx-1"></i>
                                        <span class="fw-bold text-primary"><i class="fas fa-user-plus text-primary me-1"></i>{{ $log->penerima_display }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5 font-monospace" style="font-size: 0.775rem;">
                                        <span class="text-secondary"><i class="fas fa-map-pin text-danger me-1"></i>{{ $log->lokasi_asal }}</span>
                                        <i class="fas fa-arrow-right-long text-success mx-1"></i>
                                        <span class="fw-bold text-dark"><i class="fas fa-location-dot text-success me-1"></i>{{ $log->lokasi ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>{{ Str::limit($log->keterangan ?? '-', 35) }}</td>
                                <td class="text-center text-nowrap">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('handover.receipt', $log->uuid ?? ($log->no_surat ?: $log->id)) }}" target="_blank" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Preview Dokumen') }}">
                                            <i class="fas fa-eye text-primary me-1"></i> {{ __('Preview') }}
                                        </a>
                                        <a href="{{ route('handover.receipt', $log->uuid ?? ($log->no_surat ?: $log->id)) }}?print=1" target="_blank" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Cetak PDF') }}">
                                            <i class="fas fa-print text-secondary"></i> {{ __('PDF') }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
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

        <!-- Maintenance Logs Card -->
        <div class="phoenix-card mt-4">
            <div class="phoenix-card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="phoenix-card-title mb-0">
                        <i class="fas fa-screwdriver-wrench text-warning"></i>
                        {{ __('Riwayat Maintenance & Servis Hardware') }}
                    </h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-phoenix badge-phoenix-warning font-monospace">{{ $barang->maintenances->count() }} {{ __('Tiket Servis') }}</span>
                    <a href="{{ route('maintenance.create', ['barang_id' => $barang->id]) }}" class="btn btn-phoenix-primary btn-sm py-1 px-2" style="font-size: 0.75rem;">
                        <i class="fas fa-plus me-1"></i> {{ __('Servis Baru') }}
                    </a>
                </div>
            </div>
            <div class="phoenix-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-phoenix">
                        <thead>
                            <tr>
                                <th class="ps-3">{{ __('TANGGAL') }}</th>
                                <th>{{ __('NO TIKET') }}</th>
                                <th>{{ __('JENIS SERVIS') }}</th>
                                <th>{{ __('PELAKSANA / TEKNISI') }}</th>
                                <th>{{ __('BIAYA') }}</th>
                                <th>{{ __('STATUS') }}</th>
                                <th class="text-center">{{ __('DOKUMEN') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barang->maintenances as $mnt)
                            <tr>
                                <td class="ps-3 text-nowrap font-monospace">
                                    {{ \Carbon\Carbon::parse($mnt->tanggal_mulai)->format('d M Y') }}
                                </td>
                                <td>
                                    <a href="{{ route('maintenance.show', $mnt->uuid) }}" class="badge bg-light text-primary border font-monospace px-2 py-1 text-decoration-none" style="font-size: 0.75rem;">
                                        <i class="fas fa-ticket me-1"></i>{{ $mnt->no_maintenance }}
                                    </a>
                                </td>
                                <td>{{ $mnt->jenis_maintenance }}</td>
                                <td>
                                    @if($mnt->pelaksana === 'Vendor Eksternal')
                                        <span class="badge bg-light text-info border"><i class="fas fa-building me-1"></i>{{ $mnt->vendor->nama_vendor ?? 'Vendor' }}</span>
                                    @else
                                        <span class="badge bg-light text-primary border"><i class="fas fa-user-gear me-1"></i>Internal IT</span>
                                    @endif
                                </td>
                                <td class="font-monospace">
                                    @if($mnt->biaya > 0)
                                        <span class="fw-semibold text-dark">Rp {{ number_format($mnt->biaya, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($mnt->status === 'Dalam Proses')
                                        <span class="badge-phoenix badge-phoenix-warning"><i class="fas fa-clock"></i> {{ __('Dalam Proses') }}</span>
                                    @elseif($mnt->status === 'Selesai')
                                        <span class="badge-phoenix badge-phoenix-success"><i class="fas fa-check"></i> {{ __('Selesai') }}</span>
                                    @else
                                        <span class="badge-phoenix badge-phoenix-secondary"><i class="fas fa-ban"></i> {{ __('Dibatalkan') }}</span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('maintenance.show', $mnt->uuid) }}" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Detail Servis') }}">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>
                                        <a href="{{ route('maintenance.print', $mnt->uuid) }}" target="_blank" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Cetak SPK') }}">
                                            <i class="fas fa-print text-secondary"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-screwdriver-wrench fs-3 opacity-25 mb-2"></i><br>
                                    {{ __('Belum ada catatan servis/maintenance untuk perangkat ini.') }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Helpdesk Tickets Card -->
        <div class="phoenix-card mt-4">
            <div class="phoenix-card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="phoenix-card-title mb-0">
                        <i class="fas fa-headset text-primary"></i>
                        {{ __('Riwayat Tiket Helpdesk Terkait') }}
                    </h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-phoenix badge-phoenix-info font-monospace">{{ $barang->tickets->count() }} {{ __('Tiket') }}</span>
                    <a href="{{ route('ticket.index') }}" class="btn btn-phoenix-primary btn-sm py-1 px-2" style="font-size: 0.75rem;">
                        <i class="fas fa-arrow-up-right-from-square me-1"></i> {{ __('Buka Helpdesk') }}
                    </a>
                </div>
            </div>
            <div class="phoenix-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-phoenix">
                        <thead>
                            <tr>
                                <th class="ps-3">{{ __('TANGGAL') }}</th>
                                <th>{{ __('NO TIKET') }}</th>
                                <th>{{ __('JUDUL / KELUHAN') }}</th>
                                <th>{{ __('PELAPOR') }}</th>
                                <th>{{ __('PRIORITAS') }}</th>
                                <th>{{ __('STATUS') }}</th>
                                <th class="text-center">{{ __('AKSI') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barang->tickets as $tkt)
                            <tr>
                                <td class="ps-3 text-nowrap font-monospace">
                                    {{ $tkt->created_at->format('d M Y, H:i') }}
                                </td>
                                <td>
                                    <a href="{{ route('ticket.show', $tkt->uuid) }}" class="badge bg-light text-primary border font-monospace px-2 py-1 text-decoration-none" style="font-size: 0.75rem;">
                                        <i class="fas fa-ticket me-1"></i>{{ $tkt->no_tiket }}
                                    </a>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ Str::limit($tkt->judul, 35) }}</span>
                                </td>
                                <td>{{ $tkt->nama_pelapor }}</td>
                                <td>
                                    @if($tkt->prioritas === 'Kritis')
                                        <span class="badge priority-badge-kritis py-0 px-1" style="font-size: 0.6875rem;">Kritis</span>
                                    @elseif($tkt->prioritas === 'Tinggi')
                                        <span class="badge priority-badge-tinggi py-0 px-1" style="font-size: 0.6875rem;">Tinggi</span>
                                    @elseif($tkt->prioritas === 'Sedang')
                                        <span class="badge priority-badge-sedang py-0 px-1" style="font-size: 0.6875rem;">Sedang</span>
                                    @else
                                        <span class="badge priority-badge-rendah py-0 px-1" style="font-size: 0.6875rem;">Rendah</span>
                                    @endif
                                </td>
                                <td>
                                    @if($tkt->status === 'Open')
                                        <span class="badge-phoenix badge-phoenix-danger">Open</span>
                                    @elseif($tkt->status === 'In Progress')
                                        <span class="badge-phoenix badge-phoenix-warning">In Progress</span>
                                    @elseif($tkt->status === 'Pending')
                                        <span class="badge-phoenix badge-phoenix-info">Pending</span>
                                    @elseif($tkt->status === 'Resolved')
                                        <span class="badge-phoenix badge-phoenix-success">Resolved</span>
                                    @else
                                        <span class="badge-phoenix badge-phoenix-secondary">Closed</span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('ticket.show', $tkt->uuid) }}" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Detail Tiket') }}">
                                            <i class="fas fa-comments text-primary"></i>
                                        </a>
                                        <a href="{{ route('ticket.print', $tkt->uuid) }}" target="_blank" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Cetak') }}">
                                            <i class="fas fa-print text-secondary"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-headset fs-3 opacity-25 mb-2"></i><br>
                                    {{ __('Belum ada tiket bantuan/keluhan untuk perangkat ini.') }}
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrUrl = "{{ url('/barang/' . $barang->uuid) }}";
        const qrContainer = document.getElementById("showQrContainer");
        if (qrContainer) {
            new QRCode(qrContainer, {
                text: qrUrl,
                width: 72,
                height: 72,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
        }
    });
</script>
@endpush