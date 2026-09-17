<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Cetak Label QR Code') }} - {{ $barang->no_aset_local }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400;0,600;0,700;0,800;1,700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <!-- QRCode JS Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <style>
        :root {
            --font-mono: 'JetBrains Mono', monospace;
            --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --label-width: 65mm;
            --label-height: 35mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: var(--font-sans);
            background-color: #0f172a;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }

        .screen-toolbar {
            background: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }

        .preview-canvas {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 70px);
            padding: 36px 20px;
            background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
        }

        /* -------------------------------------------------------------
           MODERN INDUSTRIAL ASSET QR STICKER
           ------------------------------------------------------------- */
        .asset-qr-sticker {
            width: var(--label-width);
            height: var(--label-height);
            background: #ffffff;
            border: 1.75px solid #090d16;
            border-radius: 5px;
            padding: 2.5mm 3mm;
            position: relative;
            box-shadow: 0 10px 25px rgba(0,0,0,0.35);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            page-break-inside: avoid;
            color: #090d16;
        }

        /* Subtle Corner Accent / Crop Marks */
        .asset-qr-sticker::before {
            content: "";
            position: absolute;
            top: 1.5mm;
            left: 1.5mm;
            width: 3mm;
            height: 3mm;
            border-top: 1.2px solid #090d16;
            border-left: 1.2px solid #090d16;
            pointer-events: none;
            opacity: 0.3;
        }
        .asset-qr-sticker::after {
            content: "";
            position: absolute;
            bottom: 1.5mm;
            right: 1.5mm;
            width: 3mm;
            height: 3mm;
            border-bottom: 1.2px solid #090d16;
            border-right: 1.2px solid #090d16;
            pointer-events: none;
            opacity: 0.3;
        }

        /* Header */
        .label-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.2px solid #090d16;
            padding-bottom: 1.2mm;
            margin-bottom: 1.2mm;
        }

        .label-brand-block {
            display: flex;
            align-items: center;
            gap: 1.5mm;
        }

        .brand-dot {
            width: 2.2mm;
            height: 2.2mm;
            background-color: #090d16;
            border-radius: 50%;
            display: inline-block;
        }

        .label-brand-title {
            font-size: 6.8pt;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #090d16;
            line-height: 1;
        }

        .label-badge-group {
            display: flex;
            align-items: center;
            gap: 1mm;
        }

        .label-chip {
            font-family: var(--font-mono);
            font-size: 4.6pt;
            font-weight: 700;
            background: #f1f5f9;
            color: #0f172a;
            border: 0.75px solid #cbd5e1;
            padding: 1px 3mm;
            border-radius: 2px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            line-height: 1.2;
        }

        .label-chip-dark {
            background: #090d16;
            color: #ffffff;
            border-color: #090d16;
        }

        /* Body Layout: 2 Columns */
        .label-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2.5mm;
            flex: 1;
            min-height: 0;
            padding: 0.2mm 0;
        }

        /* QR Code Matrix Box */
        .qr-matrix-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .label-qr-wrapper {
            width: 20mm;
            height: 20mm;
            background: #ffffff;
            border: 1px solid #090d16;
            border-radius: 3px;
            padding: 0.8mm;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .label-qr-wrapper img, .label-qr-wrapper canvas {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }

        .qr-micro-text {
            font-family: var(--font-mono);
            font-size: 3.8pt;
            font-weight: 700;
            color: #475569;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-top: 0.6mm;
            text-align: center;
        }

        /* Asset Information Block */
        .label-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            overflow: hidden;
        }

        /* Asset Code Banner */
        .asset-code-banner {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 2.5px solid #090d16;
            padding: 0.8mm 1.8mm;
            border-radius: 2px;
            margin-bottom: 1mm;
        }

        .label-code-tag {
            font-size: 3.8pt;
            font-weight: 800;
            letter-spacing: 0.06em;
            color: #64748b;
            text-transform: uppercase;
            line-height: 1;
            margin-bottom: 0.3mm;
        }

        .label-code-value {
            font-family: var(--font-mono);
            font-size: 9.2pt;
            font-weight: 800;
            letter-spacing: 0.02em;
            color: #090d16;
            line-height: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Model Block */
        .info-field-group {
            margin-bottom: 0.6mm;
            overflow: hidden;
        }

        .field-label {
            font-size: 4pt;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            line-height: 1;
            margin-bottom: 0.3mm;
        }

        .field-value-model {
            font-size: 6.8pt;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .field-value-sn {
            font-family: var(--font-mono);
            font-size: 5.6pt;
            font-weight: 600;
            color: #334155;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Footer */
        .label-footer {
            border-top: 0.8px solid #cbd5e1;
            padding-top: 0.8mm;
            margin-top: 0.8mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: var(--font-mono);
            font-size: 3.8pt;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #64748b;
            line-height: 1;
        }

        .footer-left {
            color: #090d16;
            font-weight: 800;
        }

        /* -------------------------------------------------------------
           PRESET STICKER SIZES
           ------------------------------------------------------------- */
        .size-compact {
            --label-width: 50mm;
            --label-height: 30mm;
        }
        .size-compact .label-qr-wrapper {
            width: 16mm;
            height: 16mm;
        }
        .size-compact .label-code-value {
            font-size: 7.8pt;
        }
        .size-compact .field-value-model {
            font-size: 5.8pt;
        }
        .size-compact .field-value-sn {
            font-size: 4.8pt;
        }

        .size-standard {
            --label-width: 65mm;
            --label-height: 35mm;
        }

        .size-large {
            --label-width: 80mm;
            --label-height: 45mm;
        }
        .size-large .label-qr-wrapper {
            width: 25mm;
            height: 25mm;
        }
        .size-large .label-code-value {
            font-size: 11.5pt;
        }
        .size-large .field-value-model {
            font-size: 8.5pt;
        }
        .size-large .field-value-sn {
            font-size: 6.8pt;
        }
        .size-large .label-chip {
            font-size: 5.5pt;
        }

        /* Print Settings */
        @media print {
            body {
                background: #ffffff !important;
            }
            .screen-toolbar, .no-print {
                display: none !important;
            }
            .preview-canvas {
                padding: 0 !important;
                margin: 0 !important;
                min-height: auto !important;
                background: transparent !important;
                display: block !important;
            }
            .asset-qr-sticker {
                box-shadow: none !important;
                margin: 0 !important;
                border: 1.5px solid #000000 !important;
            }
            @page {
                size: auto;
                margin: 2mm 2mm;
            }
        }
    </style>
</head>
<body>

<!-- TOPBAR / CONTROLS -->
<div class="screen-toolbar no-print">
    <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-3" style="max-width: 960px;">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('barang.show', $barang->uuid) }}" class="btn btn-sm btn-outline-light">
                <i class="fas fa-arrow-left me-1"></i> {{ __('Kembali') }}
            </a>
            <span class="fw-bold small text-white ms-2">{{ __('Pratinjau Stiker QR Code') }}</span>
            <span class="badge bg-dark border border-secondary font-monospace text-warning">{{ $barang->no_aset_local }}</span>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Label Dimension Switcher -->
            <div class="input-group input-group-sm" style="width: auto;">
                <span class="input-group-text bg-dark border-secondary text-white"><i class="fas fa-tag me-1"></i> {{ __('Ukuran') }}:</span>
                <select id="selectLabelSize" class="form-select form-select-sm bg-dark border-secondary text-white" style="width: 155px;">
                    <option value="size-compact">5 x 3 cm (Mini)</option>
                    <option value="size-standard" selected>6.5 x 3.5 cm (Standar)</option>
                    <option value="size-large">8 x 4.5 cm (Besar)</option>
                </select>
            </div>

            <!-- Print Copies Multiplier -->
            <div class="input-group input-group-sm" style="width: auto;">
                <span class="input-group-text bg-dark border-secondary text-white"><i class="fas fa-copy me-1"></i> {{ __('Jumlah') }}:</span>
                <input type="number" id="inputCopies" class="form-control bg-dark border-secondary text-white" value="1" min="1" max="50" style="width: 60px;">
            </div>

            <button type="button" class="btn btn-sm btn-primary px-3 fw-bold" onclick="window.print()">
                <i class="fas fa-print me-1"></i> {{ __('Cetak Label QR') }}
            </button>
        </div>
    </div>
</div>

<!-- PREVIEW CANVAS -->
<div class="preview-canvas">
    <div id="labelsContainer" class="d-flex flex-wrap gap-4 justify-content-center">
        <!-- Single QR Code Sticker Template -->
        <div class="asset-qr-sticker size-standard" id="baseSticker">
            <!-- Header -->
            <div class="label-header">
                <div class="label-brand-block">
                    <span class="brand-dot"></span>
                    <span class="label-brand-title">SBL IT ASSET</span>
                </div>
                <div class="label-badge-group">
                    <span class="label-chip">{{ $barang->category->nama_kategori ?? 'HARDWARE' }}</span>
                    @if($barang->dept)
                    <span class="label-chip label-chip-dark">{{ $barang->dept }}</span>
                    @endif
                </div>
            </div>

            <!-- Body -->
            <div class="label-body">
                <!-- QR Code Matrix Box -->
                <div class="qr-matrix-card">
                    <div class="label-qr-wrapper" id="qrContainer"></div>
                    <div class="qr-micro-text"><i class="fas fa-qrcode"></i> SCAN ME</div>
                </div>

                <!-- Asset Identity Info -->
                <div class="label-info">
                    <!-- Asset Code Banner -->
                    <div class="asset-code-banner">
                        <div class="label-code-tag">ASSET NUMBER</div>
                        <div class="label-code-value">{{ $barang->no_aset_local }}</div>
                    </div>

                    <!-- Model / Name Info -->
                    <div class="info-field-group">
                        <div class="field-label">MODEL / PERANGKAT</div>
                        <div class="field-value-model">{{ $barang->model ?? ($barang->nama_barang ?? 'N/A') }}</div>
                    </div>

                    <!-- Serial Number Info -->
                    <div class="info-field-group mb-0">
                        <div class="field-label">SERIAL NUMBER</div>
                        <div class="field-value-sn">{{ $barang->serial_number ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="label-footer">
                <span class="footer-left">PROPERTY OF COMPANY</span>
                <span>DO NOT REMOVE</span>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const qrPayload = "{{ url('/barang/' . $barang->uuid) }}";

        // Generate QR Code with high crispness
        function renderQr(containerId, size = 128) {
            const container = document.getElementById(containerId);
            if (container) {
                container.innerHTML = "";
                new QRCode(container, {
                    text: qrPayload,
                    width: size,
                    height: size,
                    colorDark : "#090d16",
                    colorLight : "#ffffff",
                    correctLevel : QRCode.CorrectLevel.M
                });
            }
        }

        renderQr("qrContainer", 128);

        // Label Size Selector Handler
        const selectSize = document.getElementById('selectLabelSize');
        if (selectSize) {
            selectSize.addEventListener('change', function() {
                const stickers = document.querySelectorAll('.asset-qr-sticker');
                stickers.forEach(st => {
                    st.classList.remove('size-compact', 'size-standard', 'size-large');
                    st.classList.add(this.value);
                });
            });
        }

        // Copies Multiplier Handler
        const inputCopies = document.getElementById('inputCopies');
        const labelsContainer = document.getElementById('labelsContainer');
        const baseSticker = document.getElementById('baseSticker');

        if (inputCopies && labelsContainer && baseSticker) {
            inputCopies.addEventListener('change', function() {
                const count = Math.max(1, parseInt(this.value) || 1);
                const currentSize = selectSize ? selectSize.value : 'size-standard';
                
                labelsContainer.innerHTML = "";
                for (let i = 0; i < count; i++) {
                    const clone = baseSticker.cloneNode(true);
                    clone.id = `sticker_${i}`;
                    clone.className = `asset-qr-sticker ${currentSize}`;
                    
                    const qr = clone.querySelector('.label-qr-wrapper');
                    if (qr) {
                        qr.id = `qrContainer_${i}`;
                        qr.innerHTML = "";
                    }

                    labelsContainer.appendChild(clone);
                    renderQr(`qrContainer_${i}`, 128);
                }
            });
        }

        // Auto print parameter (?print=1)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('print') === '1') {
            setTimeout(() => {
                window.print();
            }, 600);
        }
    });
</script>

</body>
</html>
