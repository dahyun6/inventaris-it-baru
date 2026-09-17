<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Cetak Batch Label QR Code Aset IT') }} - {{ date('d/m/Y') }}</title>
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

        /* Top Controls Bar */
        .screen-toolbar {
            background: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }

        /* Filter Panel */
        .filter-panel {
            background: #182234;
            border-bottom: 1px solid #334155;
            padding: 14px 24px;
        }

        .filter-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
            display: block;
        }

        .filter-control {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #ffffff !important;
            font-size: 0.8125rem !important;
            border-radius: 6px;
        }

        .filter-control::placeholder {
            color: #e2e8f0 !important;
            opacity: 0.85 !important;
        }

        .filter-control::-webkit-input-placeholder {
            color: #e2e8f0 !important;
            opacity: 0.85 !important;
        }

        .filter-control::-moz-placeholder {
            color: #e2e8f0 !important;
            opacity: 0.85 !important;
        }

        .filter-control:-ms-input-placeholder {
            color: #e2e8f0 !important;
            opacity: 0.85 !important;
        }

        .filter-control:focus {
            border-color: #3874ff !important;
            background-color: #0f172a !important;
            color: #ffffff !important;
            box-shadow: 0 0 0 2px rgba(56, 116, 255, 0.25) !important;
        }

        /* Batch Selection Bar */
        .selection-bar {
            background: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .sheet-container {
            max-width: 1100px;
            margin: 24px auto;
            background: #ffffff;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.35);
        }

        /* -------------------------------------------------------------
           BATCH GRID LAYOUT FOR A4 SHEETS
           ------------------------------------------------------------- */
        .qrcode-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4.5mm;
            justify-content: center;
        }

        /* Asset QR Code Sticker Card Wrapper */
        .sticker-card-wrapper {
            position: relative;
            transition: all 0.2s ease;
        }

        .sticker-card-wrapper.is-unselected {
            opacity: 0.35;
            filter: grayscale(0.8);
        }

        /* Screen Checkbox Toggle */
        .sticker-select-toggle {
            position: absolute;
            top: -6px;
            right: -6px;
            z-index: 10;
            background: #ffffff;
            border: 1.5px solid #090d16;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: all 0.15s ease;
        }

        .sticker-select-toggle input {
            cursor: pointer;
            width: 14px;
            height: 14px;
            margin: 0;
            accent-color: #090d16;
        }

        /* Asset QR Code Sticker Card */
        .asset-qr-sticker {
            width: 100%;
            height: 35mm;
            background: #ffffff;
            border: 1.5px solid #090d16;
            border-radius: 4px;
            padding: 2.2mm 2.8mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            page-break-inside: avoid;
            break-inside: avoid;
            position: relative;
            color: #090d16;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        }

        /* Header */
        .label-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #090d16;
            padding-bottom: 0.9mm;
            margin-bottom: 0.9mm;
        }

        .label-brand-block {
            display: flex;
            align-items: center;
            gap: 1.2mm;
        }

        .brand-dot {
            width: 1.8mm;
            height: 1.8mm;
            background-color: #090d16;
            border-radius: 50%;
            display: inline-block;
        }

        .label-brand-title {
            font-size: 6.2pt;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #090d16;
            line-height: 1;
        }

        .label-badge-group {
            display: flex;
            align-items: center;
            gap: 0.8mm;
        }

        .label-chip {
            font-family: var(--font-mono);
            font-size: 4.2pt;
            font-weight: 700;
            background: #f1f5f9;
            color: #0f172a;
            border: 0.6px solid #cbd5e1;
            padding: 0.8px 2.5mm;
            border-radius: 2px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            line-height: 1.2;
        }

        .label-chip-dark {
            background: #090d16;
            color: #ffffff;
            border-color: #090d16;
        }

        /* Body Layout */
        .label-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2mm;
            flex: 1;
            min-height: 0;
            padding: 0.2mm 0;
        }

        .qr-matrix-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .label-qr-wrapper {
            width: 18mm;
            height: 18mm;
            background: #ffffff;
            border: 0.8px solid #090d16;
            border-radius: 2px;
            padding: 0.6mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .label-qr-wrapper img, .label-qr-wrapper canvas {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }

        .qr-micro-text {
            font-family: var(--font-mono);
            font-size: 3.5pt;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-top: 0.4mm;
            text-align: center;
            line-height: 1;
        }

        /* Info Section */
        .label-info {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            overflow: hidden;
        }

        .asset-code-banner {
            background: #f8fafc;
            border: 0.8px solid #e2e8f0;
            border-left: 2mm solid #090d16;
            padding: 0.6mm 1.5mm;
            border-radius: 2px;
            margin-bottom: 0.8mm;
        }

        .label-code-tag {
            font-size: 3.5pt;
            font-weight: 800;
            letter-spacing: 0.05em;
            color: #64748b;
            text-transform: uppercase;
            line-height: 1;
        }

        .label-code-value {
            font-family: var(--font-mono);
            font-size: 8.5pt;
            font-weight: 800;
            letter-spacing: 0.02em;
            color: #090d16;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .info-field-group {
            margin-bottom: 0.4mm;
            overflow: hidden;
        }

        .field-label {
            font-size: 3.6pt;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            line-height: 1;
        }

        .field-value-model {
            font-size: 6.2pt;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .field-value-sn {
            font-family: var(--font-mono);
            font-size: 5.2pt;
            font-weight: 600;
            color: #334155;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Footer */
        .label-footer {
            border-top: 0.6px solid #cbd5e1;
            padding-top: 0.6mm;
            margin-top: 0.6mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: var(--font-mono);
            font-size: 3.5pt;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #64748b;
            line-height: 1;
        }

        .footer-left {
            color: #090d16;
            font-weight: 800;
        }

        /* -------------------------------------------------------------
           GRID VARIANTS
           ------------------------------------------------------------- */
        .grid-cols-2 {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .grid-cols-2 .asset-qr-sticker {
            height: 42mm;
            padding: 3mm 3.5mm;
        }
        .grid-cols-2 .label-qr-wrapper {
            width: 23mm;
            height: 23mm;
        }
        .grid-cols-2 .label-code-value {
            font-size: 10.5pt;
        }
        .grid-cols-2 .field-value-model {
            font-size: 7.8pt;
        }
        .grid-cols-2 .field-value-sn {
            font-size: 6.2pt;
        }

        .grid-cols-3 {
            grid-template-columns: repeat(3, 1fr) !important;
        }

        .grid-cols-4 {
            grid-template-columns: repeat(4, 1fr) !important;
        }
        .grid-cols-4 .asset-qr-sticker {
            height: 30mm;
            padding: 1.8mm 2.2mm;
        }
        .grid-cols-4 .label-qr-wrapper {
            width: 14mm;
            height: 14mm;
        }
        .grid-cols-4 .label-code-value {
            font-size: 7.2pt;
        }
        .grid-cols-4 .field-value-model {
            font-size: 5.2pt;
        }
        .grid-cols-4 .field-value-sn {
            font-size: 4.2pt;
        }

        /* Print Settings */
        @media print {
            body {
                background: #ffffff !important;
            }
            .screen-toolbar, .filter-panel, .selection-bar, .sticker-select-toggle, .no-print {
                display: none !important;
            }
            .sticker-card-wrapper.is-unselected {
                display: none !important;
            }
            .sheet-container {
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                max-width: 100% !important;
            }
            .asset-qr-sticker {
                box-shadow: none !important;
                border: 1.5px solid #000000 !important;
                break-inside: avoid;
            }
            @page {
                size: A4 portrait;
                margin: 6mm 6mm;
            }
        }
    </style>
</head>
<body>

<!-- TOPBAR -->
<div class="screen-toolbar no-print">
    <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-3" style="max-width: 1100px;">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('barang.index') }}" class="btn btn-sm btn-outline-light">
                <i class="fas fa-arrow-left me-1"></i> {{ __('Kembali') }}
            </a>
            <span class="fw-bold small text-white ms-2">{{ __('Cetak Batch Label QR Code Aset') }}</span>
            <span class="badge bg-primary text-white rounded-pill ms-1 font-monospace" id="badgeTotal">{{ $barangs->count() }} {{ __('Total') }}</span>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="input-group input-group-sm" style="width: auto;">
                <span class="input-group-text bg-dark border-secondary text-white"><i class="fas fa-table-cells me-1"></i> {{ __('Layout') }}:</span>
                <select id="selectGridLayout" class="form-select form-select-sm bg-dark border-secondary text-white" style="width: 175px;">
                    <option value="grid-cols-2">2 Kolom (8 x 4.5 cm)</option>
                    <option value="grid-cols-3" selected>3 Kolom (6.5 x 3.5 cm)</option>
                    <option value="grid-cols-4">4 Kolom (5 x 3 cm)</option>
                </select>
            </div>

            <button type="button" class="btn btn-sm btn-primary px-3 fw-bold" onclick="printSelectedStickers()">
                <i class="fas fa-print me-1"></i> {{ __('Cetak Label Terpilih') }} (<span id="btnSelectedCount">{{ $barangs->count() }}</span>)
            </button>
        </div>
    </div>
</div>

<!-- ADVANCED FILTER PANEL -->
<div class="filter-panel no-print">
    <div class="container-fluid" style="max-width: 1100px;">
        <form method="GET" action="{{ route('barang.barcode.batch') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                <!-- Search Box -->
                <div class="col-12 col-md-4 col-lg-3">
                    <label class="filter-label"><i class="fas fa-search me-1"></i> {{ __('Cari Aset / SN / Model') }}</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="q" class="form-control filter-control" placeholder="{{ __('Ketik no aset, model, SN...') }}" value="{{ $q ?? '' }}">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-magnifying-glass"></i></button>
                    </div>
                </div>

                <!-- Kategori -->
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="filter-label"><i class="fas fa-tags me-1"></i> {{ __('Kategori') }}</label>
                    <select name="category_id" class="form-select form-select-sm filter-control" onchange="this.form.submit()">
                        <option value="all">-- {{ __('Semua Kategori') }} --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Departemen -->
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="filter-label"><i class="fas fa-building me-1"></i> {{ __('Departemen') }}</label>
                    <select name="dept" class="form-select form-select-sm filter-control" onchange="this.form.submit()">
                        <option value="all">-- {{ __('Semua Dept') }} --</option>
                        @foreach($departemens as $d)
                            <option value="{{ $d->nama_departemen }}" {{ ($dept ?? '') === $d->nama_departemen ? 'selected' : '' }}>
                                {{ $d->nama_departemen }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Lokasi Unit -->
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="filter-label"><i class="fas fa-location-dot me-1"></i> {{ __('Lokasi Unit') }}</label>
                    <select name="unit_loc" class="form-select form-select-sm filter-control" onchange="this.form.submit()">
                        <option value="all">-- {{ __('Semua Lokasi') }} --</option>
                        @foreach($lokasis as $l)
                            <option value="{{ $l->nama_lokasi }}" {{ ($unitLoc ?? '') === $l->nama_lokasi ? 'selected' : '' }}>
                                {{ $l->nama_lokasi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="col-6 col-md-4 col-lg-2">
                    <label class="filter-label"><i class="fas fa-circle-check me-1"></i> {{ __('Status Aset') }}</label>
                    <select name="status" class="form-select form-select-sm filter-control" onchange="this.form.submit()">
                        <option value="all">-- {{ __('Semua Status') }} --</option>
                        <option value="Tersedia" {{ ($status ?? '') === 'Tersedia' ? 'selected' : '' }}>{{ __('Tersedia') }}</option>
                        <option value="Dipinjam" {{ ($status ?? '') === 'Dipinjam' ? 'selected' : '' }}>{{ __('Dipinjam') }}</option>
                        <option value="Rusak" {{ ($status ?? '') === 'Rusak' ? 'selected' : '' }}>{{ __('Rusak') }}</option>
                    </select>
                </div>

                <!-- Reset Button -->
                <div class="col-auto">
                    @if(!empty($categoryId) && $categoryId !== 'all' || !empty($dept) && $dept !== 'all' || !empty($unitLoc) && $unitLoc !== 'all' || !empty($status) && $status !== 'all' || !empty($q))
                        <a href="{{ route('barang.barcode.batch') }}" class="btn btn-sm btn-outline-danger" title="{{ __('Reset Semua Filter') }}">
                            <i class="fas fa-times me-1"></i> {{ __('Reset') }}
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- SELECTION CONTROLS BAR -->
<div class="selection-bar no-print">
    <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap gap-2" style="max-width: 1100px;">
        <div class="d-flex align-items-center gap-2">
            <span class="small text-light me-1"><i class="fas fa-check-double me-1 text-primary"></i> {{ __('Pilihan Cetak:') }}</span>
            <button type="button" class="btn btn-sm btn-outline-light py-0.5 px-2" style="font-size: 0.75rem;" onclick="toggleSelectAll(true)">
                <i class="fas fa-check-square me-1"></i> {{ __('Pilih Semua') }}
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2 text-light" style="font-size: 0.75rem;" onclick="toggleSelectAll(false)">
                <i class="fas fa-square me-1"></i> {{ __('Batal Pilih') }}
            </button>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-dark border border-secondary text-info font-monospace py-1.5 px-2.5" style="font-size: 0.75rem;">
                <span id="selectedCountText">{{ $barangs->count() }}</span> / {{ $barangs->count() }} {{ __('Stiker Siap Cetak') }}
            </span>
        </div>
    </div>
</div>

<!-- SHEET / LABELS CONTAINER -->
<div class="sheet-container">
    @if($barangs->isNotEmpty())
        <div id="qrcodeGrid" class="qrcode-grid grid-cols-3">
            @foreach($barangs as $index => $b)
            <div class="sticker-card-wrapper" id="wrapper_{{ $index }}">
                <!-- Interactive Select Checkbox for Screen Mode -->
                <label class="sticker-select-toggle no-print" title="{{ __('Centang untuk cetak stiker ini') }}">
                    <input type="checkbox" class="sticker-checkbox" data-index="{{ $index }}" checked onchange="handleStickerToggle({{ $index }})">
                </label>

                <!-- Sticker Label Content -->
                <div class="asset-qr-sticker" onclick="toggleStickerClick({{ $index }})">
                    <!-- Header -->
                    <div class="label-header">
                        <div class="label-brand-block">
                            <span class="brand-dot"></span>
                            <span class="label-brand-title">SBL IT ASSET</span>
                        </div>
                        <div class="label-badge-group">
                            <span class="label-chip">{{ $b->category->nama_kategori ?? 'HARDWARE' }}</span>
                            @if($b->dept)
                            <span class="label-chip label-chip-dark">{{ $b->dept }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="label-body">
                        <!-- QR Code Matrix Box -->
                        <div class="qr-matrix-card">
                            <div class="label-qr-wrapper" id="qrContainer_{{ $index }}" data-url="{{ url('/barang/' . $b->uuid) }}"></div>
                            <div class="qr-micro-text">SCAN ME</div>
                        </div>

                        <!-- Asset Identity Info -->
                        <div class="label-info">
                            <!-- Asset Code Banner -->
                            <div class="asset-code-banner">
                                <div class="label-code-tag">ASSET NUMBER</div>
                                <div class="label-code-value">{{ $b->no_aset_local }}</div>
                            </div>

                            <!-- Model / Name Info -->
                            <div class="info-field-group">
                                <div class="field-label">MODEL / PERANGKAT</div>
                                <div class="field-value-model">{{ $b->model ?? ($b->nama_barang ?? 'N/A') }}</div>
                            </div>

                            <!-- Serial Number Info -->
                            <div class="info-field-group mb-0">
                                <div class="field-label">SERIAL NUMBER</div>
                                <div class="field-value-sn">{{ $b->serial_number ?: '-' }}</div>
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
            @endforeach
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <i class="fas fa-qrcode fs-1 opacity-25 mb-3"></i>
            <h6>{{ __('Tidak ada data aset yang cocok dengan filter.') }}</h6>
            <p class="small mb-0">{{ __('Silakan ubah filter kategori, departemen, lokasi, atau kata kunci pencarian di atas.') }}</p>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Render QR Code for each item
        const qrElements = document.querySelectorAll('.label-qr-wrapper');
        qrElements.forEach(qr => {
            const url = qr.getAttribute('data-url');
            if (url) {
                qr.innerHTML = "";
                new QRCode(qr, {
                    text: url,
                    width: 96,
                    height: 96,
                    colorDark: "#090d16",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            }
        });

        // Grid Layout Switcher
        const selectLayout = document.getElementById('selectGridLayout');
        const qrcodeGrid = document.getElementById('qrcodeGrid');
        if (selectLayout && qrcodeGrid) {
            selectLayout.addEventListener('change', function() {
                qrcodeGrid.classList.remove('grid-cols-2', 'grid-cols-3', 'grid-cols-4');
                qrcodeGrid.classList.add(this.value);
            });
        }
    });

    // Checkbox toggle handler
    function handleStickerToggle(index) {
        const wrapper = document.getElementById('wrapper_' + index);
        const checkbox = wrapper ? wrapper.querySelector('.sticker-checkbox') : null;
        if (wrapper && checkbox) {
            if (checkbox.checked) {
                wrapper.classList.remove('is-unselected');
            } else {
                wrapper.classList.add('is-unselected');
            }
            updateCounts();
        }
    }

    // Toggle by clicking the card itself
    function toggleStickerClick(index) {
        const wrapper = document.getElementById('wrapper_' + index);
        const checkbox = wrapper ? wrapper.querySelector('.sticker-checkbox') : null;
        if (checkbox) {
            checkbox.checked = !checkbox.checked;
            handleStickerToggle(index);
        }
    }

    // Select All / Deselect All
    function toggleSelectAll(checked) {
        const checkboxes = document.querySelectorAll('.sticker-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = checked;
            const index = cb.getAttribute('data-index');
            const wrapper = document.getElementById('wrapper_' + index);
            if (wrapper) {
                if (checked) {
                    wrapper.classList.remove('is-unselected');
                } else {
                    wrapper.classList.add('is-unselected');
                }
            }
        });
        updateCounts();
    }

    // Update live counter badge
    function updateCounts() {
        const selectedCount = document.querySelectorAll('.sticker-checkbox:checked').length;
        const countText = document.getElementById('selectedCountText');
        const btnCount = document.getElementById('btnSelectedCount');
        if (countText) countText.textContent = selectedCount;
        if (btnCount) btnCount.textContent = selectedCount;
    }

    // Print with validation
    function printSelectedStickers() {
        const selectedCount = document.querySelectorAll('.sticker-checkbox:checked').length;
        if (selectedCount === 0) {
            alert("{{ __('Pilih setidaknya 1 stiker untuk dicetak.') }}");
            return;
        }
        window.print();
    }
</script>

</body>
</html>
