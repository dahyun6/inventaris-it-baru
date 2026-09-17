<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Rekapitulasi Laporan Maintenance IT') }} - {{ date('d/m/Y') }}</title>
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
            <a href="{{ route('maintenance.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> {{ __('Kembali') }}
            </a>
            <span class="fw-bold small text-dark ms-2">{{ __('Pratinjau Rekap Laporan Maintenance') }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('maintenance.export_excel', ['status' => $status]) }}" class="btn btn-sm btn-success px-3">
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
            <h4 class="fw-bold text-dark mb-1" style="font-size: 1.25rem;"><i class="fas fa-boxes-stacked text-primary me-2 no-print"></i>SBL IT ASSETS MANAGEMENT</h4>
            <div class="text-secondary small">{{ __('Laporan Rekapitulasi Servis & Maintenance Perangkat IT') }}</div>
        </div>
        <div class="text-end font-mono">
            <div class="badge bg-dark text-white px-2 py-1">{{ __('OFFICIAL REPORT') }}</div>
            <div class="text-muted small mt-1">{{ __('Tanggal Cetak:') }} {{ date('d F Y') }}</div>
        </div>
    </div>

    <!-- Metrics Summary -->
    <div class="row g-2 mb-3">
        <div class="col-3">
            <div class="p-2 border rounded bg-light text-center">
                <small class="text-muted d-block">{{ __('Total Servis') }}</small>
                <strong class="fs-6 font-mono text-dark">{{ $metrics['total'] }}</strong>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 border rounded bg-light text-center">
                <small class="text-muted d-block">{{ __('Dalam Proses') }}</small>
                <strong class="fs-6 font-mono text-warning">{{ $metrics['dalam_proses'] }}</strong>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 border rounded bg-light text-center">
                <small class="text-muted d-block">{{ __('Selesai') }}</small>
                <strong class="fs-6 font-mono text-success">{{ $metrics['selesai'] }}</strong>
            </div>
        </div>
        <div class="col-3">
            <div class="p-2 border rounded bg-light text-center">
                <small class="text-muted d-block">{{ __('Total Biaya') }}</small>
                <strong class="fs-6 font-mono text-primary">Rp {{ number_format($metrics['total_biaya'], 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    <table class="table-report">
        <thead>
            <tr>
                <th width="4%">NO</th>
                <th width="12%">NO TIKET</th>
                <th width="10%">TGL MULAI</th>
                <th width="12%">NO ASET</th>
                <th width="18%">MODEL / BRAND</th>
                <th width="14%">JENIS MAINTENANCE</th>
                <th width="10%">PELAKSANA</th>
                <th width="10%" class="text-end">BIAYA</th>
                <th width="10%" class="text-center">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenances as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="font-mono fw-bold">{{ $row->no_maintenance }}</td>
                <td class="font-mono">{{ \Carbon\Carbon::parse($row->tanggal_mulai)->format('d/m/Y') }}</td>
                <td class="font-mono">{{ $row->barang->no_aset_local ?? '-' }}</td>
                <td class="fw-semibold">{{ $row->barang->nama_barang ?? $row->barang->model ?? '-' }}</td>
                <td>{{ $row->jenis_maintenance }}</td>
                <td>{{ $row->pelaksana === 'Vendor Eksternal' ? ($row->vendor->nama_vendor ?? 'Vendor') : 'Internal IT' }}</td>
                <td class="text-end font-mono">Rp {{ number_format($row->biaya ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">
                    <span class="badge {{ $row->status === 'Selesai' ? 'bg-success' : ($row->status === 'Dalam Proses' ? 'bg-warning text-dark' : 'bg-secondary') }}" style="font-size: 9.5px;">
                        {{ $row->status }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center py-4 text-muted">{{ __('Tidak ada data maintenance tercatat.') }}</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="fw-bold bg-light">
                <td colspan="7" class="text-end">{{ __('TOTAL PENGELUARAN BIAYA:') }}</td>
                <td class="text-end font-mono text-primary">Rp {{ number_format($metrics['total_biaya'], 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="row mt-5 pt-4">
        <div class="col-6 text-center">
            <div class="text-muted small mb-5">{{ __('Dibuat Oleh (Admin IT),') }}</div>
            <div class="fw-bold border-top pt-1 d-inline-block" style="min-width: 180px;">{{ Auth::user()->name }}</div>
        </div>
        <div class="col-6 text-center">
            <div class="text-muted small mb-5">{{ __('Mengetahui (IT Manager),') }}</div>
            <div class="fw-bold border-top pt-1 d-inline-block" style="min-width: 180px;">( ......................................... )</div>
        </div>
    </div>
</div>

</body>
</html>
