<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Tanda Terima Aset - {{ $decodedNoSurat }}</title>
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
            font-size: 13.5px;
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
            margin-bottom: 24px;
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
            margin-top: 10px;
            text-align: center;
        }

        .doc-number {
            font-size: 12px;
            color: #525b75;
            text-align: center;
            font-family: monospace;
            margin-bottom: 20px;
        }

        .party-box {
            background-color: #f8fafc;
            border: 1px solid var(--phoenix-border-color);
            border-radius: 8px;
            padding: 14px 16px;
            height: 100%;
        }

        .party-title {
            font-size: 11.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--phoenix-primary);
            margin-bottom: 8px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
        }

        .party-table {
            width: 100%;
            font-size: 12.5px;
            margin-bottom: 0;
        }

        .party-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .party-table td.label {
            width: 95px;
            color: #6e7891;
            font-weight: 600;
        }

        .table-items {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        .table-items th {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #141824;
            font-weight: 800;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .table-items td {
            border: 1px solid var(--phoenix-border-color);
            padding: 8px 10px;
            color: #31374a;
            vertical-align: middle;
        }

        .terms-box {
            font-size: 11.5px;
            color: #525b75;
            background: #f8fafc;
            border: 1px solid var(--phoenix-border-color);
            border-radius: 8px;
            padding: 10px 14px;
            margin-top: 15px;
            margin-bottom: 25px;
        }

        .terms-box ol {
            margin: 0;
            padding-left: 18px;
        }

        .terms-box li {
            margin-bottom: 3px;
        }

        .signatures-wrapper {
            margin-top: 30px;
        }

        .signature-box {
            text-align: center;
            font-size: 12px;
        }

        .signature-role {
            font-weight: 700;
            color: #31374a;
            margin-bottom: 60px;
        }

        .signature-name {
            font-weight: 800;
            color: #141824;
            border-top: 1px solid #141824;
            display: inline-block;
            min-width: 160px;
            padding-top: 4px;
        }

        .signature-dept {
            font-size: 11px;
            color: #6e7891;
        }

        /* PRINT STYLING SPECIFIC FOR A4 & PDF EXPORT */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 11pt !important;
            }

            .screen-toolbar, .no-print {
                display: none !important;
            }

            .receipt-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .party-box {
                background: #f8fafc !important;
                border: 1px solid #94a3b8 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .table-items {
                break-inside: auto;
            }

            .table-items tr {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .table-items th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .table-items td, .table-items th {
                border: 1px solid #94a3b8 !important;
            }

            .terms-box {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .signatures-wrapper {
                break-inside: avoid;
                page-break-inside: avoid;
                margin-top: 36px;
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
            <a href="{{ route('handover.history') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Riwayat
            </a>
            <a href="{{ route('handover.create') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-plus me-1"></i> Buat Tanda Terima Baru
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-sm btn-primary px-3 shadow-sm" style="background-color: var(--phoenix-primary); border-color: var(--phoenix-primary);">
                <i class="fas fa-print me-1"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>
</div>

<div class="receipt-container">
    <!-- Header Dokumen / Kop Surat -->
    <div class="doc-header d-flex justify-content-between align-items-center">
        <div>
            <div class="company-title"><i class="fas fa-boxes-stacked text-primary me-2 no-print"></i>SBL IT ASSETS MANAGEMENT</div>
        </div>
        <div class="text-end">
            <div class="badge bg-dark text-white px-2 py-1" style="font-size: 11px;">OFFICIAL RECORD</div>
            <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($first->tanggal_serah_terima ?? now())->translatedFormat('d F Y') }}</div>
        </div>
    </div>

    <!-- Judul Dokumen & Nomor -->
    <div class="doc-title-badge">SURAT TANDA TERIMA SERAH TERIMA ASET IT</div>
    <div class="doc-number">Nomor Dokumen: <strong>{{ $first->no_surat ?? $decodedNoSurat }}</strong></div>

    <p style="font-size: 12.5px; text-align: justify; margin-bottom: 16px;">
        Pada hari ini, tanggal <strong>{{ \Carbon\Carbon::parse($first->tanggal_serah_terima ?? now())->translatedFormat('d F Y') }}</strong>, telah dilakukan serah terima fasilitas / perangkat keras kerja IT untuk kebutuhan operasional dengan rincian pihak dan aset sebagai berikut:
    </p>

    <!-- Informasi Pihak I dan Pihak II -->
    <div class="row g-3 mb-3">
        <div class="col-6">
            <div class="party-box">
                <div class="party-title">PIHAK I (YANG MENYERAHKAN)</div>
                <table class="party-table">
                    <tr>
                        <td class="label">Nama Admin</td>
                        <td>: <strong>{{ $first->diserahkan_oleh ?? Auth::user()->name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Departemen</td>
                        <td>: IT Department / Support</td>
                    </tr>
                    <tr>
                        <td class="label">Peran</td>
                        <td>: IT Asset Administrator</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-6">
            <div class="party-box">
                <div class="party-title">PIHAK II (YANG MENERIMA)</div>
                <table class="party-table">
                    <tr>
                        <td class="label">Nama Penerima</td>
                        <td>: <strong>{{ $first->penerima_nama ?? ($first->user->name ?? '-') }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Departemen</td>
                        <td>: {{ $first->penerima_dept ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jabatan</td>
                        <td>: {{ $first->penerima_jabatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Lokasi Unit</td>
                        <td>: {{ $first->lokasi ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Aset yang Diserahterimakan -->
    <div class="fw-bold mb-1" style="font-size: 12px; color: #141824;"><i class="fas fa-desktop me-1 text-primary"></i> RINCIAN PERANGKAT / HARDWARE YANG DISERAHKAN:</div>
    <table class="table-items">
        <thead>
            <tr>
                <th width="5%" class="text-center">NO</th>
                <th width="18%">KODE ASET</th>
                <th width="15%">KATEGORI</th>
                <th width="20%">MODEL / BRAND</th>
                <th width="20%">SERIAL NUMBER (SN)</th>
                <th width="22%">STATUS / KONDISI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="font-monospace fw-bold">{{ $item->barang->no_aset_local ?? '-' }}</td>
                <td>{{ $item->barang->category->nama_kategori ?? '-' }}</td>
                <td class="fw-semibold">{{ $item->barang->model ?? '-' }}</td>
                <td class="font-monospace">{{ $item->barang->serial_number ?? '-' }}</td>
                <td>
                    <span class="fw-semibold text-dark">{{ $item->barang->status ?? 'Dipinjam' }}</span>
                    @if($item->barang->type_spec)
                        <br><small class="text-muted" style="font-size: 10.5px;">{{ Str::limit($item->barang->type_spec, 40) }}</small>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Catatan Tambahan -->
    @if($first->keterangan)
    <div style="font-size: 12px; margin-bottom: 12px;">
        <strong>Catatan & Kelengkapan Aksesoris:</strong>
        <div style="background: #f8fafc; border: 1px solid var(--phoenix-border-color); padding: 8px 12px; border-radius: 6px; margin-top: 4px;">
            {{ $first->keterangan }}
        </div>
    </div>
    @endif

    <!-- Syarat & Ketentuan -->
    <div class="terms-box">
        <strong>Ketentuan & Pernyataan Tanggung Jawab:</strong>
        <ol>
            <li>Penerima bertanggung jawab penuh terhadap perawatan, kebersihan, dan keamanan fisik maupun sistem perangkat selama masa penggunaan.</li>
            <li>Perangkat hanya dipergunakan untuk menunjang aktivitas kerja dan kedinasan perusahaan.</li>
            <li>Apabila terjadi kendala teknis, kerusakan, atau kehilangan, penerima wajib segera melaporkan kepada Departemen IT.</li>
        </ol>
    </div>

    <!-- Area Tanda Tangan Fisik / Digital -->
    <div class="signatures-wrapper">
        <div class="row">
            <div class="col-4">
                <div class="signature-box">
                    <div class="signature-role">Yang Menyerahkan,</div>
                    <div class="signature-name">{{ $first->diserahkan_oleh ?? Auth::user()->name }}</div>
                    <div class="signature-dept">IT Department</div>
                </div>
            </div>
            <div class="col-4">
                <div class="signature-box">
                    <div class="signature-role">Yang Menerima,</div>
                    <div class="signature-name">{{ $first->penerima_nama ?? ($first->user->name ?? 'Karyawan / User') }}</div>
                    <div class="signature-dept">{{ $first->penerima_dept ?? 'Pengguna Aset' }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="signature-box">
                    <div class="signature-role">Mengetahui,</div>
                    <div class="signature-name">Head of IT / Supervisor</div>
                    <div class="signature-dept">Management IT</div>
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
            }, 350);
        }
    });
</script>
</body>
</html>
