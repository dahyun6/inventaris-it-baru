<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Tiket Layanan IT - {{ $ticket->no_tiket }}</title>
    <!-- Google Fonts: Nunito Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400..700&family=Nunito+Sans:ital,opsz,wght@0,6..12,400..800;1,6..12,400..800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --phoenix-font-mono: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            --phoenix-primary: #3874ff;
            --phoenix-body-bg: #f5f7fa;
            --phoenix-border-color: #e3e6ed;
            --phoenix-text-dark: #141824;
        }

        body {
            font-family: 'Nunito Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--phoenix-body-bg);
            color: #31374a;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .font-monospace, code, .doc-number {
            font-family: var(--phoenix-font-mono) !important;
            font-feature-settings: "zero" 1, "tnum" 1;
            letter-spacing: -0.015em;
        }

        .screen-toolbar {
            background: #ffffff;
            border-bottom: 1px solid var(--phoenix-border-color);
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .receipt-container {
            max-width: 860px;
            margin: 28px auto;
            background: #ffffff;
            padding: 40px 48px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--phoenix-border-color);
        }

        .doc-header {
            border-bottom: 2px solid #141824;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }

        .company-title {
            font-size: 18px;
            font-weight: 800;
            color: #141824;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .company-subtitle {
            font-size: 12.5px;
            color: #6e7891;
            font-weight: 600;
        }

        .doc-title-badge {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #141824;
            margin-top: 8px;
            text-align: center;
        }

        .doc-number {
            font-size: 12px;
            color: #525b75;
            text-align: center;
            font-family: monospace;
            margin-bottom: 18px;
        }

        .party-box {
            background-color: #f8fafc;
            border: 1px solid var(--phoenix-border-color);
            border-radius: 8px;
            padding: 12px 14px;
            height: 100%;
        }

        .party-title {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #3874ff;
            margin-bottom: 8px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 4px;
        }

        .party-table {
            width: 100%;
            font-size: 12px;
        }

        .party-table td {
            padding: 2.5px 0;
            vertical-align: top;
        }

        .party-table td.label {
            width: 120px;
            color: #6e7891;
            font-weight: 600;
        }

        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .table-items th {
            background-color: #f1f5f9;
            color: #31374a;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #cbd5e1;
            padding: 6px 10px;
            font-size: 11px;
        }

        .table-items td {
            border: 1px solid #e2e8f0;
            padding: 6px 10px;
            vertical-align: middle;
        }

        .terms-box {
            background-color: #f8fafc;
            border: 1px solid var(--phoenix-border-color);
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 11px;
            color: #525b75;
            margin-bottom: 20px;
        }

        .signatures-wrapper {
            margin-top: 30px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-title {
            font-size: 11px;
            font-weight: 700;
            color: #6e7891;
            margin-bottom: 55px;
            text-transform: uppercase;
        }

        .signature-line {
            font-weight: 700;
            color: #141824;
            font-size: 12px;
            border-top: 1px solid #141824;
            display: inline-block;
            min-width: 160px;
            padding-top: 4px;
        }

        .signature-sub {
            font-size: 10.5px;
            color: #6e7891;
        }

        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }

            .screen-toolbar, .no-print, .fas, .far, .fab, .fa, i {
                display: none !important;
            }

            .receipt-container {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }

            .party-box, .terms-box {
                background-color: #ffffff !important;
                border: 1px solid #94a3b8 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .table-items th {
                background-color: #f1f5f9 !important;
                border: 1px solid #64748b !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .table-items td {
                border: 1px solid #94a3b8 !important;
            }

            @page {
                size: A4 portrait;
                margin: 12mm 15mm;
            }
        }
    </style>
</head>
<body>

<!-- Action Bar (Hidden on Print) -->
<div class="screen-toolbar no-print">
    <div class="container-fluid d-flex justify-content-between align-items-center" style="max-width: 860px;">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('ticket.show', $ticket->uuid) }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail
            </a>
            <a href="{{ route('ticket.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-list me-1"></i> Daftar Tiket
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-sm btn-primary px-3 shadow-sm" style="background-color: var(--phoenix-primary); border-color: var(--phoenix-primary);">
                <i class="fas fa-print me-1"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>
</div>

@php
    $logoFile = null;
    $possibleFiles = ['logosbl.png', 'logosbl.jpg', 'logosbl.svg', 'logo.png', 'logo.jpg', 'logo.jpeg', 'logo.svg', 'logo.webp'];
    foreach($possibleFiles as $file) {
        if(file_exists(public_path('assets/images/' . $file))) {
            $logoFile = asset('assets/images/' . $file);
            break;
        }
    }
    if(!$logoFile && is_dir(public_path('assets/images'))) {
        $scanned = scandir(public_path('assets/images'));
        foreach($scanned as $f) {
            if(preg_match('/\.(png|jpe?g|svg|webp)$/i', $f)) {
                $logoFile = asset('assets/images/' . $f);
                break;
            }
        }
    }
@endphp

<div class="receipt-container">
    <!-- Header Dokumen / Kop Surat -->
    <div class="doc-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            @if($logoFile)
                <img src="{{ $logoFile }}" alt="Logo PT. SCG Barito Logistics" style="max-height: 48px; max-width: 140px; object-fit: contain;">
            @endif
            <div>
                <div class="company-title">PANDORA IT HELPDESK & SUPPORT</div>
                <div class="company-subtitle">IT Dept. PT. SCG Barito Logistics</div>
            </div>
        </div>
        <div class="text-end">
            <div class="badge bg-dark text-white px-2 py-1" style="font-size: 11px;">IT HELPDESK TICKET FORM</div>
            <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($ticket->created_at)->translatedFormat('d F Y, H:i') }}</div>
        </div>
    </div>

    <!-- Judul Dokumen & Nomor -->
    <div class="doc-title-badge">LEMBAR PERMOHONAN & PENYELESAIAN LAYANAN IT SUPPORT</div>
    <div class="doc-number">Nomor Tiket: <strong>{{ $ticket->no_tiket }}</strong></div>

    <!-- Informasi Pelapor & Klasifikasi -->
    <div class="row g-3 mb-3">
        <div class="col-6">
            <div class="party-box">
                <div class="party-title">DATA PEMOHON / PELAPOR</div>
                <table class="party-table">
                    <tr>
                        <td class="label">Nama Karyawan</td>
                        <td>: <strong>{{ $ticket->nama_pelapor }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Email</td>
                        <td>: {{ $ticket->email_pelapor ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Departemen</td>
                        <td>: {{ $ticket->departemen_pelapor ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Lokasi / Meja</td>
                        <td>: {{ $ticket->lokasi_pelapor ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-6">
            <div class="party-box">
                <div class="party-title">KLASIFIKASI LAYANAN & PIC</div>
                <table class="party-table">
                    <tr>
                        <td class="label">Kategori</td>
                        <td>: <strong>{{ $ticket->kategori }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Prioritas</td>
                        <td>: <strong>{{ $ticket->prioritas }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Status Tiket</td>
                        <td>: <strong>{{ $ticket->status }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Teknisi PIC</td>
                        <td>: {{ $ticket->assignedUser->name ?? 'Belum Ditugaskan' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Perangkat Aset</td>
                        <td>: {{ $ticket->barang ? '[' . $ticket->barang->no_aset_local . '] ' . ($ticket->barang->nama_barang ?? $ticket->barang->model) : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Rincian Masalah & Solusi -->
    <div class="fw-bold mb-1" style="font-size: 12px; color: #141824;">SUBJEK & RINCIAN KENDALA:</div>
    <div class="p-3 bg-light rounded-2 border mb-3 text-secondary" style="font-size: 12.5px; white-space: pre-line;">
        <div class="fw-bold text-dark mb-1">{{ $ticket->judul }}</div>
        {{ $ticket->deskripsi }}
    </div>

    @if($ticket->solusi)
    <div class="fw-bold mb-1" style="font-size: 12px; color: #141824;">TINDAKAN / SOLUSI PENYELESAIAN IT:</div>
    <div class="p-3 bg-light rounded-2 border mb-3 text-secondary" style="font-size: 12.5px; white-space: pre-line;">
        {{ $ticket->solusi }}
    </div>
    @endif

    <!-- Catatan & Ketentuan -->
    <div class="terms-box">
        <div class="fw-bold mb-1" style="color: #141824;">Catatan Layanan:</div>
        <div>1. Formulir ini merupakan rekaman resmi permohonan layanan dukungan teknis divisi IT.</div>
        <div>2. Tiket berstatus <strong>Resolved/Closed</strong> menandakan permohonan telah selesai ditangani dan disetujui pemohon.</div>
    </div>

    <!-- Tanda Tangan Para Pihak -->
    <div class="signatures-wrapper">
        <div class="row text-center">
            <div class="col-4">
                <div class="signature-box">
                    <div class="signature-title">Pemohon / Karyawan</div>
                    <div>
                        <div class="signature-line">{{ $ticket->nama_pelapor }}</div>
                        <div class="signature-sub">{{ $ticket->departemen_pelapor ?? 'User / Staff' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="signature-box">
                    <div class="signature-title">Teknisi Pelaksana (PIC)</div>
                    <div>
                        <div class="signature-line">{{ $ticket->assignedUser->name ?? 'Teknisi IT' }}</div>
                        <div class="signature-sub">IT Helpdesk Support</div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="signature-box">
                    <div class="signature-title">Mengetahui / IT Head</div>
                    <div>
                        <div class="signature-line">( ....................................... )</div>
                        <div class="signature-sub">Head of IT Infrastructure</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', function() {
        const params = new URLSearchParams(window.location.search);
        if (params.get('print') === '1' || params.get('print') === 'true') {
            setTimeout(function() {
                window.print();
            }, 300);
        }
    });
</script>

</body>
</html>
