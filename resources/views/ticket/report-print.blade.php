<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Rekapitulasi Layanan IT Helpdesk') }} - {{ date('d/m/Y') }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400..700&family=Nunito+Sans:ital,opsz,wght@0,6..12,400..800;1,6..12,400..800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --phoenix-font-mono: 'JetBrains Mono', monospace;
            --phoenix-primary: #3874ff;
        }
        body {
            font-family: 'Nunito Sans', -apple-system, sans-serif;
            background-color: #f5f7fa;
            color: #141824;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .font-mono {
            font-family: var(--phoenix-font-mono);
        }
        .screen-toolbar {
            background: #ffffff;
            border-bottom: 1px solid #e3e6ed;
            padding: 10px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .report-page {
            max-width: 1060px;
            margin: 24px auto;
            background: #ffffff;
            padding: 36px 40px;
            border-radius: 10px;
            border: 1px solid #e3e6ed;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }
        .table-report {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 16px;
        }
        .table-report th {
            background-color: #f1f5f9;
            color: #31374a;
            font-weight: 700;
            text-transform: uppercase;
            border: 1px solid #cbd5e1;
            padding: 7px 8px;
            text-align: left;
        }
        .table-report td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            vertical-align: top;
        }
        .table-report tr:nth-child(even) {
            background-color: #fafbfc;
        }
        @media print {
            body {
                background: #ffffff !important;
            }
            .screen-toolbar, .no-print {
                display: none !important;
            }
            .report-page {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            @page {
                size: A4 landscape;
                margin: 10mm 12mm;
            }
        }
    </style>
</head>
<body>

<div class="screen-toolbar no-print">
    <div class="container-fluid d-flex justify-content-between align-items-center" style="max-width: 1060px;">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('ticket.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> {{ __('Kembali') }}
            </a>
            <span class="fw-bold small text-dark ms-2">{{ __('Pratinjau Rekap Laporan Helpdesk') }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('ticket.export_excel', ['status' => $status ?? null, 'prioritas' => $prioritas ?? null, 'kategori' => $kategori ?? null]) }}" class="btn btn-sm btn-success px-3">
                <i class="fas fa-file-excel me-1"></i> {{ __('Download Excel') }}
            </a>
            <button onclick="window.print()" class="btn btn-sm btn-primary px-3">
                <i class="fas fa-print me-1"></i> {{ __('Cetak / PDF') }}
            </button>
        </div>
    </div>
</div>

<div class="report-page">
    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
        <div>
            <h4 class="fw-bold text-dark mb-1" style="font-size: 1.25rem;"><i class="fas fa-headset text-primary me-2 no-print"></i>SBL IT HELPDESK & SUPPORT</h4>
            <div class="text-secondary small">{{ __('Laporan Rekapitulasi Tiket Layanan & Penanganan Masalah IT') }}</div>
        </div>
        <div class="text-end font-mono">
            <div class="badge bg-dark text-white px-2 py-1">{{ __('HELPDESK LOG') }}</div>
            <div class="text-muted small mt-1">{{ __('Tanggal Cetak:') }} {{ date('d F Y') }}</div>
        </div>
    </div>

    <!-- Metrics Summary -->
    <div class="row g-2 mb-3">
        <div class="col-3">
            <div class="p-2 border rounded bg-light text-center">
                <small class="text-muted d-block">{{ __('Total Tiket') }}</small>
                <strong class="fs-6 font-mono text-dark">{{ $metrics['total'] }}</strong>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 border rounded bg-light text-center">
                <small class="text-muted d-block">{{ __('Open') }}</small>
                <strong class="fs-6 font-mono text-danger">{{ $metrics['open'] }}</strong>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 border rounded bg-light text-center">
                <small class="text-muted d-block">{{ __('In Progress') }}</small>
                <strong class="fs-6 font-mono text-warning">{{ $metrics['in_progress'] }}</strong>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 border rounded bg-light text-center">
                <small class="text-muted d-block">{{ __('Resolved / Selesai') }}</small>
                <strong class="fs-6 font-mono text-success">{{ $metrics['resolved'] }}</strong>
            </div>
        </div>
    </div>

    <table class="table-report">
        <thead>
            <tr>
                <th width="4%">NO</th>
                <th width="12%">NO TIKET</th>
                <th width="10%">TGL PENGAJUAN</th>
                <th width="8%">PRIORITAS</th>
                <th width="14%">KATEGORI</th>
                <th width="20%">JUDUL KELUHAN</th>
                <th width="14%">PELAPOR / DEPT</th>
                <th width="10%">TEKNISI PIC</th>
                <th width="8%" class="text-center">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="font-mono fw-bold">{{ $row->no_tiket }}</td>
                <td class="font-mono">{{ $row->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="badge {{ $row->prioritas === 'Kritis' ? 'bg-danger' : ($row->prioritas === 'Tinggi' ? 'bg-warning text-dark' : 'bg-secondary') }}" style="font-size: 9px;">
                        {{ $row->prioritas }}
                    </span>
                </td>
                <td>{{ $row->kategori }}</td>
                <td class="fw-semibold">{{ $row->judul }}</td>
                <td>
                    <div>{{ $row->nama_pelapor }}</div>
                    @if($row->departemen_pelapor)
                        <small class="text-muted">({{ $row->departemen_pelapor }})</small>
                    @endif
                </td>
                <td>{{ $row->assignedUser?->name ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge {{ $row->status === 'Resolved' || $row->status === 'Closed' ? 'bg-success' : ($row->status === 'In Progress' ? 'bg-warning text-dark' : 'bg-danger') }}" style="font-size: 9.5px;">
                        {{ $row->status }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center py-4 text-muted">{{ __('Tidak ada data tiket tercatat.') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="row mt-5 pt-4">
        <div class="col-6 text-center">
            <div class="text-muted small mb-5">{{ __('Dibuat Oleh (Admin Helpdesk),') }}</div>
            <div class="fw-bold border-top pt-1 d-inline-block" style="min-width: 180px;">{{ Auth::user()->name }}</div>
        </div>
        <div class="col-6 text-center">
            <div class="text-muted small mb-5">{{ __('Mengetahui (IT Supervisor),') }}</div>
            <div class="fw-bold border-top pt-1 d-inline-block" style="min-width: 180px;">( ......................................... )</div>
        </div>
    </div>
</div>

</body>
</html>
