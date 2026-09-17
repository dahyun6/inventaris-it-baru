@extends('layout')

@section('title', __('IT Helpdesk & Layanan Support'))

@section('header_actions')
<div class="d-flex flex-wrap gap-2">
    @if(!Auth::user()?->isStaff())
    <a href="{{ route('ticket.export_excel', request()->query()) }}" class="btn btn-phoenix-secondary btn-sm" title="{{ __('Ekspor tiket IT Helpdesk ke format spreadsheet Excel') }}">
        <i class="fas fa-file-excel text-success me-1"></i> {{ __('Export Excel') }}
    </a>
    <a href="{{ route('ticket.report_print', request()->query()) }}" target="_blank" class="btn btn-phoenix-secondary btn-sm" title="{{ __('Buka dan cetak rekapitulasi laporan tiket helpdesk') }}">
        <i class="fas fa-print text-secondary me-1"></i> {{ __('Cetak Rekap') }}
    </a>
    @endif
    <button type="button" class="btn btn-phoenix-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCreateTicket">
        <i class="fas fa-plus me-1"></i> {{ __('Buat Tiket Bantuan') }}
    </button>
</div>
@endsection

@section('content')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

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
        white-space: nowrap;
    }
    .table-phoenix tbody td {
        border-bottom: 1px solid var(--phoenix-border-color);
        color: var(--phoenix-text-body);
        padding: 0.75rem 1rem !important;
        white-space: nowrap;
    }
    .table-phoenix tbody tr:hover {
        background-color: #f8fafc;
    }
    div.dataTables_wrapper div.dataTables_length select {
        width: 75px;
        display: inline-block;
        padding: 0.35rem 0.6rem;
        border-radius: 6px;
        border: 1px solid var(--phoenix-border-color);
        font-size: 0.8125rem;
    }
    div.dataTables_wrapper div.dataTables_filter input {
        border-radius: 20px;
        border: 1px solid var(--phoenix-border-color);
        padding: 0.35rem 0.85rem;
        font-size: 0.8125rem;
    }
    .kpi-card {
        background: #ffffff;
        border: 1px solid var(--phoenix-border-color);
        border-radius: 10px;
        padding: 1.1rem 1.25rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .modal-section-title {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #525b75;
        font-weight: 700;
        margin-bottom: 0.875rem;
    }
</style>

<!-- KPI SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="kpi-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">{{ __('Total Tiket') }}</span>
                <h4 class="fw-bold mb-0 text-dark">{{ number_format($metrics['total']) }}</h4>
                <small class="text-muted" style="font-size: 0.725rem;">{{ __('Semua antrean tiket') }}</small>
            </div>
            <div class="d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 44px; height: 44px; font-size: 1.15rem;">
                <i class="fas fa-ticket"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">{{ __('Tiket Open') }}</span>
                <h4 class="fw-bold mb-0 text-danger">{{ number_format($metrics['open']) }}</h4>
                <small class="text-muted" style="font-size: 0.725rem;">{{ __('Menunggu penanganan') }}</small>
            </div>
            <div class="d-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle" style="width: 44px; height: 44px; font-size: 1.15rem;">
                <i class="fas fa-envelope-open-text"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">{{ __('In Progress') }}</span>
                <h4 class="fw-bold mb-0 text-warning">{{ number_format($metrics['in_progress']) }}</h4>
                <small class="text-muted" style="font-size: 0.725rem;">{{ __('Sedang dikerjakan') }}</small>
            </div>
            <div class="d-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle" style="width: 44px; height: 44px; font-size: 1.15rem;">
                <i class="fas fa-screwdriver-wrench"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="kpi-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">{{ __('Resolved') }}</span>
                <h4 class="fw-bold mb-0 text-success">{{ number_format($metrics['resolved']) }}</h4>
                <small class="text-muted" style="font-size: 0.725rem;">{{ __('Telah diselesaikan') }}</small>
            </div>
            <div class="d-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 44px; height: 44px; font-size: 1.15rem;">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>
    </div>
</div>

<!-- MAIN CARD -->
<div class="phoenix-card">
    <div class="phoenix-card-header flex-wrap gap-2">
        <div>
            <h6 class="phoenix-card-title mb-0">
                <i class="fas fa-headset text-primary"></i>
                {{ __('Daftar Antrean Tiket Layanan IT') }}
            </h6>
            <small class="text-muted">{{ __('Total antrean:') }} <strong>{{ $tickets->count() }}</strong> {{ __('tiket') }}</small>
        </div>

        <!-- Filter Status Tabs -->
        <div class="d-flex flex-wrap align-items-center gap-1">
            <a href="{{ route('ticket.index') }}" class="btn btn-sm {{ empty($status) ? 'btn-primary' : 'btn-outline-secondary' }} py-1 px-2" style="font-size: 0.75rem;">
                {{ __('Semua') }}
            </a>
            <a href="{{ route('ticket.index', ['status' => 'Open']) }}" class="btn btn-sm {{ $status === 'Open' ? 'btn-danger text-white' : 'btn-outline-secondary' }} py-1 px-2" style="font-size: 0.75rem;">
                {{ __('Open') }}
            </a>
            <a href="{{ route('ticket.index', ['status' => 'In Progress']) }}" class="btn btn-sm {{ $status === 'In Progress' ? 'btn-warning text-dark' : 'btn-outline-secondary' }} py-1 px-2" style="font-size: 0.75rem;">
                {{ __('In Progress') }}
            </a>
            <a href="{{ route('ticket.index', ['status' => 'Pending']) }}" class="btn btn-sm {{ $status === 'Pending' ? 'btn-info text-white' : 'btn-outline-secondary' }} py-1 px-2" style="font-size: 0.75rem;">
                {{ __('Pending') }}
            </a>
            <a href="{{ route('ticket.index', ['status' => 'Resolved']) }}" class="btn btn-sm {{ $status === 'Resolved' ? 'btn-success text-white' : 'btn-outline-secondary' }} py-1 px-2" style="font-size: 0.75rem;">
                {{ __('Resolved') }}
            </a>
            <a href="{{ route('ticket.index', ['status' => 'Closed']) }}" class="btn btn-sm {{ $status === 'Closed' ? 'btn-secondary text-white' : 'btn-outline-secondary' }} py-1 px-2" style="font-size: 0.75rem;">
                {{ __('Closed') }}
            </a>
        </div>
    </div>

    <div class="phoenix-card-body p-0">
        <div class="table-responsive p-3">
            <table id="tableTicket" class="table table-phoenix w-100">
                <thead>
                    <tr>
                        <th width="4%">{{ __('NO') }}</th>
                        <th>{{ __('NO TIKET') }}</th>
                        <th>{{ __('TANGGAL') }}</th>
                        <th>{{ __('PRIORITAS') }}</th>
                        <th>{{ __('JUDUL & KATEGORI') }}</th>
                        <th>{{ __('PELAPOR') }}</th>
                        <th>{{ __('ASET TERKAIT') }}</th>
                        <th>{{ __('TEKNISI PIC') }}</th>
                        <th>{{ __('STATUS') }}</th>
                        <th class="text-center">{{ __('AKSI') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ route('ticket.show', $row->id) }}" class="badge bg-light text-primary border font-monospace px-2 py-1 text-decoration-none" style="font-size: 0.75rem;">
                                {{ $row->no_tiket }}
                            </a>
                        </td>
                        <td class="font-monospace text-dark">{{ $row->created_at->format('d M Y, H:i') }}</td>
                        <td>
                            @if($row->prioritas === 'Kritis')
                                <span class="badge-phoenix badge-phoenix-danger">Kritis</span>
                            @elseif($row->prioritas === 'Tinggi')
                                <span class="badge-phoenix badge-phoenix-warning">Tinggi</span>
                            @elseif($row->prioritas === 'Sedang')
                                <span class="badge-phoenix badge-phoenix-info">Sedang</span>
                            @else
                                <span class="badge-phoenix badge-phoenix-secondary">Rendah</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark mb-1">
                                <a href="{{ route('ticket.show', $row->id) }}" class="text-decoration-none text-dark hover-primary">
                                    {{ Str::limit($row->judul, 40) }}
                                </a>
                            </div>
                            <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">
                                {{ $row->kategori }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $row->nama_pelapor }}</div>
                            <small class="text-muted">{{ $row->departemen_pelapor ?? '-' }} {{ $row->lokasi_pelapor ? '('.$row->lokasi_pelapor.')' : '' }}</small>
                        </td>
                        <td>
                            @if($row->barang)
                                <div>
                                    <a href="{{ route('barang.show', $row->barang->uuid) }}" class="badge bg-light text-primary border font-monospace text-decoration-none">
                                        {{ $row->barang->no_aset_local }}
                                    </a>
                                </div>
                                <small class="text-muted">{{ Str::limit($row->barang->nama_barang ?? $row->barang->model, 25) }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($row->assignedUser)
                                <span class="badge bg-light text-primary border">
                                    <i class="far fa-user me-1"></i>{{ $row->assignedUser->name }}
                                </span>
                            @else
                                <span class="text-muted small">Belum Ditugaskan</span>
                            @endif
                        </td>
                        <td>
                            @if($row->status === 'Open')
                                <span class="badge-phoenix badge-phoenix-danger">Open</span>
                            @elseif($row->status === 'In Progress')
                                <span class="badge-phoenix badge-phoenix-warning">In Progress</span>
                            @elseif($row->status === 'Pending')
                                <span class="badge-phoenix badge-phoenix-info">Pending</span>
                            @elseif($row->status === 'Resolved')
                                <span class="badge-phoenix badge-phoenix-success">Resolved</span>
                            @else
                                <span class="badge-phoenix badge-phoenix-secondary">Closed</span>
                            @endif
                        </td>
                        <td class="text-center text-nowrap">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('ticket.show', $row->id) }}" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Detail Tiket') }}">
                                    <i class="fas fa-comments text-primary"></i>
                                </a>
                                <a href="{{ route('ticket.print', $row->id) }}" target="_blank" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Cetak Tiket') }}">
                                    <i class="fas fa-print text-secondary"></i>
                                </a>
                                @if(!Auth::user()?->isStaff())
                                <button type="button" class="btn btn-phoenix-secondary py-1 px-2 btn-edit-ticket"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditTicket"
                                    data-url="{{ route('ticket.update', $row->id) }}"
                                    data-no-tiket="{{ $row->no_tiket }}"
                                    data-user-id="{{ $row->user_id }}"
                                    data-nama-pelapor="{{ $row->nama_pelapor }}"
                                    data-email-pelapor="{{ $row->email_pelapor }}"
                                    data-departemen-pelapor="{{ $row->departemen_pelapor }}"
                                    data-lokasi-pelapor="{{ $row->lokasi_pelapor }}"
                                    data-kategori="{{ $row->kategori }}"
                                    data-prioritas="{{ $row->prioritas }}"
                                    data-status="{{ $row->status }}"
                                    data-barang-id="{{ $row->barang_id }}"
                                    data-assigned-to="{{ $row->assigned_to }}"
                                    data-judul="{{ $row->judul }}"
                                    data-deskripsi="{{ $row->deskripsi }}"
                                    data-solusi="{{ $row->solusi }}"
                                    title="{{ __('Edit Tiket') }}">
                                    <i class="fas fa-pencil text-warning"></i>
                                </button>
                                <form action="{{ route('ticket.destroy', $row->id) }}" method="POST" class="d-inline form-delete-ticket">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Hapus') }}">
                                        <i class="fas fa-trash-can text-danger"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL CREATE TICKET -->
<div class="modal fade" id="modalCreateTicket" tabindex="-1" aria-labelledby="modalCreateTicketLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border" style="border-radius: 12px;">
            <div class="modal-header border-bottom">
                <div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="modalCreateTicketLabel">
                        <i class="fas fa-headset text-primary me-2"></i>{{ __('Buat Tiket Bantuan IT Baru') }}
                    </h6>
                    <small class="text-muted" style="font-size: 0.75rem;">{{ __('Merekam keluhan teknis, permintaan fasilitas IT, atau laporan kendala sistem.') }}</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('ticket.store') }}" method="POST" id="formCreateTicket">
                @csrf
                <div class="modal-body p-4">
                    <!-- INFORMASI PELAPOR -->
                    <div class="border-bottom pb-3 mb-3">
                        <div class="modal-section-title">
                            <i class="far fa-user text-primary me-1"></i> {{ __('Informasi Pelapor') }}
                        </div>
                        @if(Auth::user()?->isStaff())
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-danger">{{ __('Nomor Tiket') }} *</label>
                                    <input type="text" name="no_tiket" class="form-control font-monospace bg-light" value="{{ old('no_tiket', $autoNo ?? '') }}" readonly required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-danger">{{ __('Nama Pelapor') }} *</label>
                                    <input type="text" name="nama_pelapor" class="form-control bg-light" value="{{ Auth::user()->name }}" readonly required>
                                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">{{ __('Email Pelapor') }}</label>
                                    <input type="email" name="email_pelapor" class="form-control bg-light" value="{{ Auth::user()->email }}" readonly>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">{{ __('Departemen / Divisi') }}</label>
                                    <input type="text" name="departemen_pelapor" class="form-control bg-light" value="{{ old('departemen_pelapor', Auth::user()->departemen?->nama_departemen ?? '') }}" readonly style="cursor: not-allowed;" placeholder="{{ __('Otomatis dari Akun...') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">{{ __('Lokasi / Meja / Ruangan') }}</label>
                                    <input type="text" name="lokasi_pelapor" class="form-control" placeholder="Misal: Gd. B Lt. 2 Meja 14..." value="{{ old('lokasi_pelapor') }}">
                                </div>
                            </div>
                        @else
                            <div class="row g-3 mb-3">
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-danger">{{ __('Nomor Tiket') }} *</label>
                                    <input type="text" name="no_tiket" class="form-control font-monospace bg-light" value="{{ old('no_tiket', $autoNo ?? '') }}" readonly required>
                                </div>
                                <div class="col-md-7">
                                    <label class="form-label small fw-bold text-danger">{{ __('Nama Pelapor / Karyawan') }} *</label>
                                    <div class="input-group">
                                        <input type="text" name="nama_pelapor" id="create_nama_pelapor" class="form-control" placeholder="Nama Lengkap Karyawan..." value="{{ old('nama_pelapor') }}" required>
                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ __('Pilih User') }}
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" style="max-height: 220px; overflow-y: auto;">
                                            @foreach($users as $u)
                                                <li>
                                                    <a class="dropdown-item btn-select-create-user" href="javascript:void(0)" 
                                                        data-id="{{ $u->id }}" 
                                                        data-name="{{ $u->name }}" 
                                                        data-email="{{ $u->email }}"
                                                        data-departemen="{{ $u->departemen?->nama_departemen ?? '' }}"
                                                        data-lokasi="{{ $u->lokasi?->nama_lokasi ?? '' }}">
                                                        {{ $u->name }} <small class="text-muted">({{ $u->departemen?->nama_departemen ? $u->departemen->nama_departemen . ' - ' : '' }}{{ $u->email }})</small>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <input type="hidden" name="user_id" id="create_user_id" value="{{ old('user_id') }}">
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">{{ __('Email Pelapor') }}</label>
                                    <input type="email" name="email_pelapor" id="create_email_pelapor" class="form-control" placeholder="user@company.com" value="{{ old('email_pelapor') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">{{ __('Departemen / Divisi') }}</label>
                                    <input type="text" name="departemen_pelapor" id="create_departemen_pelapor" class="form-control bg-light" readonly style="cursor: not-allowed;" placeholder="{{ __('Otomatis terisi dari User...') }}" value="{{ old('departemen_pelapor') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">{{ __('Lokasi / Meja / Ruangan') }}</label>
                                    <input type="text" name="lokasi_pelapor" id="create_lokasi_pelapor" class="form-control" placeholder="Misal: Gd. B Lt. 2 Meja 14..." value="{{ old('lokasi_pelapor') }}">
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- KLASIFIKASI LAYANAN & PERANGKAT -->
                    <div class="border-bottom pb-3 mb-3">
                        <div class="modal-section-title">
                            <i class="fas fa-tags text-primary me-1"></i> {{ __('Klasifikasi Layanan & Penugasan') }}
                        </div>
                        @if(Auth::user()?->isStaff())
                            <input type="hidden" name="status" value="Open">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-danger">{{ __('Kategori Tiket') }} *</label>
                                    <select name="kategori" class="form-select" required>
                                        <option value="Hardware / Perangkat" {{ old('kategori') == 'Hardware / Perangkat' ? 'selected' : '' }}>Hardware / Perangkat (PC/Laptop)</option>
                                        <option value="Software & Aplikasi" {{ old('kategori') == 'Software & Aplikasi' ? 'selected' : '' }}>Software & Aplikasi / OS</option>
                                        <option value="Jaringan & Internet" {{ old('kategori') == 'Jaringan & Internet' ? 'selected' : '' }}>Jaringan, WiFi & VPN</option>
                                        <option value="Email & Akun / Akses" {{ old('kategori') == 'Email & Akun / Akses' ? 'selected' : '' }}>Email, Akun & Hak Akses</option>
                                        <option value="Printer & Scanner" {{ old('kategori') == 'Printer & Scanner' ? 'selected' : '' }}>Printer & Scanner</option>
                                        <option value="Permintaan Pengadaan / Fasilitas" {{ old('kategori') == 'Permintaan Pengadaan / Fasilitas' ? 'selected' : '' }}>Permintaan Pengadaan / Fasilitas</option>
                                        <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-danger">{{ __('Tingkat Prioritas') }} *</label>
                                    <select name="prioritas" class="form-select" required>
                                        <option value="Rendah" {{ old('prioritas') == 'Rendah' ? 'selected' : '' }}>Rendah (Low)</option>
                                        <option value="Sedang" {{ old('prioritas', 'Sedang') == 'Sedang' ? 'selected' : '' }}>Sedang (Normal)</option>
                                        <option value="Tinggi" {{ old('prioritas') == 'Tinggi' ? 'selected' : '' }}>Tinggi (High)</option>
                                        <option value="Kritis" {{ old('prioritas') == 'Kritis' ? 'selected' : '' }}>Kritis (Urgent)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold">{{ __('Aset Hardware Terkait (Aset Saya)') }}</label>
                                    <select name="barang_id" class="form-select">
                                        <option value="">{{ __('-- Tidak Ada / Bukan Masalah Hardware Aset --') }}</option>
                                        @foreach($barangs as $b)
                                            <option value="{{ $b->id }}" {{ old('barang_id') == $b->id ? 'selected' : '' }}>
                                                [{{ $b->no_aset_local }}] {{ $b->nama_barang ?? $b->model }} ({{ $b->serial_number ?? 'No SN' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @else
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-danger">{{ __('Kategori Tiket') }} *</label>
                                    <select name="kategori" class="form-select" required>
                                        <option value="Hardware / Perangkat" {{ old('kategori') == 'Hardware / Perangkat' ? 'selected' : '' }}>Hardware / Perangkat (PC/Laptop)</option>
                                        <option value="Software & Aplikasi" {{ old('kategori') == 'Software & Aplikasi' ? 'selected' : '' }}>Software & Aplikasi / OS</option>
                                        <option value="Jaringan & Internet" {{ old('kategori') == 'Jaringan & Internet' ? 'selected' : '' }}>Jaringan, WiFi & VPN</option>
                                        <option value="Email & Akun / Akses" {{ old('kategori') == 'Email & Akun / Akses' ? 'selected' : '' }}>Email, Akun & Hak Akses</option>
                                        <option value="Printer & Scanner" {{ old('kategori') == 'Printer & Scanner' ? 'selected' : '' }}>Printer & Scanner</option>
                                        <option value="Permintaan Pengadaan / Fasilitas" {{ old('kategori') == 'Permintaan Pengadaan / Fasilitas' ? 'selected' : '' }}>Permintaan Pengadaan / Fasilitas</option>
                                        <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-danger">{{ __('Tingkat Prioritas') }} *</label>
                                    <select name="prioritas" class="form-select" required>
                                        <option value="Rendah" {{ old('prioritas') == 'Rendah' ? 'selected' : '' }}>Rendah (Low)</option>
                                        <option value="Sedang" {{ old('prioritas', 'Sedang') == 'Sedang' ? 'selected' : '' }}>Sedang (Normal)</option>
                                        <option value="Tinggi" {{ old('prioritas') == 'Tinggi' ? 'selected' : '' }}>Tinggi (High)</option>
                                        <option value="Kritis" {{ old('prioritas') == 'Kritis' ? 'selected' : '' }}>Kritis (Urgent)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-danger">{{ __('Status Awal') }} *</label>
                                    <select name="status" class="form-select" required>
                                        <option value="Open" {{ old('status', 'Open') == 'Open' ? 'selected' : '' }}>Open</option>
                                        <option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">{{ __('Aset Hardware Terkait (Opsional)') }}</label>
                                    <select name="barang_id" class="form-select">
                                        <option value="">{{ __('-- Tidak Ada / Bukan Masalah Hardware Aset --') }}</option>
                                        @foreach($barangs as $b)
                                            <option value="{{ $b->id }}" {{ old('barang_id') == $b->id ? 'selected' : '' }}>
                                                [{{ $b->no_aset_local }}] {{ $b->nama_barang ?? $b->model }} ({{ $b->serial_number ?? 'No SN' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">{{ __('Tugaskan ke Teknisi IT (PIC)') }}</label>
                                    <select name="assigned_to" class="form-select">
                                        <option value="">{{ __('-- Belum Ditugaskan / Antrean Terbuka --') }}</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}" {{ old('assigned_to') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- RINCIAN MASALAH -->
                    <div>
                        <div class="modal-section-title">
                            <i class="fas fa-circle-exclamation text-primary me-1"></i> {{ __('Rincian Masalah / Permohonan') }}
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-danger">{{ __('Subjek / Judul Kendala') }} *</label>
                            <input type="text" name="judul" class="form-control" placeholder="Misal: Layar laptop berkedip dan tidak bisa menyala..." value="{{ old('judul') }}" required>
                        </div>
                        <div>
                            <label class="form-label small fw-bold text-danger">{{ __('Deskripsi Rinci Masalah') }} *</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="{{ __('Tuliskan kronologi, pesan error yang muncul, atau langkah yang sudah dicoba...') }}" required>{{ old('deskripsi') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-phoenix-secondary btn-sm" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-phoenix-primary btn-sm px-3">
                        <i class="fas fa-paper-plane me-1"></i> {{ __('Terbitkan Tiket') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(!Auth::user()?->isStaff())
<!-- MODAL EDIT TICKET -->
<div class="modal fade" id="modalEditTicket" tabindex="-1" aria-labelledby="modalEditTicketLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border" style="border-radius: 12px;">
            <div class="modal-header border-bottom">
                <div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="modalEditTicketLabel">
                        <i class="fas fa-pen-to-square text-primary me-2"></i>{{ __('Edit Tiket Helpdesk') }}
                        <span id="edit_ticket_badge" class="badge bg-light text-primary border font-monospace ms-2"></span>
                    </h6>
                    <small class="text-muted" style="font-size: 0.75rem;">{{ __('Perbarui data pelapor, klasifikasi masalah, PIC teknisi, atau solusi penanganan.') }}</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="" method="POST" id="formEditTicket">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- INFORMASI PELAPOR -->
                    <div class="border-bottom pb-3 mb-3">
                        <div class="modal-section-title">
                            <i class="far fa-user text-primary me-1"></i> {{ __('Informasi Pelapor') }}
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-danger">{{ __('Nomor Tiket') }} *</label>
                                <input type="text" name="no_tiket" id="edit_no_tiket" class="form-control font-monospace bg-light" readonly required>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label small fw-bold text-danger">{{ __('Nama Pelapor / Karyawan') }} *</label>
                                <div class="input-group">
                                    <input type="text" name="nama_pelapor" id="edit_nama_pelapor" class="form-control" required>
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ __('Pilih User') }}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" style="max-height: 220px; overflow-y: auto;">
                                        @foreach($users as $u)
                                            <li>
                                                <a class="dropdown-item btn-select-edit-user" href="javascript:void(0)" 
                                                    data-id="{{ $u->id }}" 
                                                    data-name="{{ $u->name }}" 
                                                    data-email="{{ $u->email }}"
                                                    data-departemen="{{ $u->departemen?->nama_departemen ?? '' }}"
                                                    data-lokasi="{{ $u->lokasi?->nama_lokasi ?? '' }}">
                                                    {{ $u->name }} <small class="text-muted">({{ $u->departemen?->nama_departemen ? $u->departemen->nama_departemen . ' - ' : '' }}{{ $u->email }})</small>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <input type="hidden" name="user_id" id="edit_user_id">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">{{ __('Email Pelapor') }}</label>
                                <input type="email" name="email_pelapor" id="edit_email_pelapor" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">{{ __('Departemen / Divisi') }}</label>
                                <input type="text" name="departemen_pelapor" id="edit_departemen_pelapor" class="form-control bg-light" readonly style="cursor: not-allowed;" placeholder="{{ __('Otomatis terisi dari User...') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">{{ __('Lokasi / Meja / Ruangan') }}</label>
                                <input type="text" name="lokasi_pelapor" id="edit_lokasi_pelapor" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- KLASIFIKASI LAYANAN & PENUGASAN -->
                    <div class="border-bottom pb-3 mb-3">
                        <div class="modal-section-title">
                            <i class="fas fa-tags text-primary me-1"></i> {{ __('Klasifikasi Layanan & Penugasan') }}
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-danger">{{ __('Kategori Tiket') }} *</label>
                                <select name="kategori" id="edit_kategori" class="form-select" required>
                                    <option value="Hardware / Perangkat">Hardware / Perangkat (PC/Laptop)</option>
                                    <option value="Software & Aplikasi">Software & Aplikasi / OS</option>
                                    <option value="Jaringan & Internet">Jaringan, WiFi & VPN</option>
                                    <option value="Email & Akun / Akses">Email, Akun & Hak Akses</option>
                                    <option value="Printer & Scanner">Printer & Scanner</option>
                                    <option value="Permintaan Pengadaan / Fasilitas">Permintaan Pengadaan / Fasilitas</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-danger">{{ __('Tingkat Prioritas') }} *</label>
                                <select name="prioritas" id="edit_prioritas" class="form-select" required>
                                    <option value="Rendah">Rendah (Low)</option>
                                    <option value="Sedang">Sedang (Normal)</option>
                                    <option value="Tinggi">Tinggi (High)</option>
                                    <option value="Kritis">Kritis (Urgent)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-danger">{{ __('Status Tiket') }} *</label>
                                <select name="status" id="edit_status" class="form-select" required>
                                    <option value="Open">Open</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Resolved">Resolved</option>
                                    <option value="Closed">Closed</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">{{ __('Aset Hardware Terkait (Opsional)') }}</label>
                                <select name="barang_id" id="edit_barang_id" class="form-select">
                                    <option value="">{{ __('-- Tidak Ada / Bukan Masalah Hardware Aset --') }}</option>
                                    @foreach($barangs as $b)
                                        <option value="{{ $b->id }}">
                                            [{{ $b->no_aset_local }}] {{ $b->nama_barang ?? $b->model }} ({{ $b->serial_number ?? 'No SN' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">{{ __('Tugaskan ke Teknisi IT (PIC)') }}</label>
                                <select name="assigned_to" id="edit_assigned_to" class="form-select">
                                    <option value="">{{ __('-- Belum Ditugaskan / Antrean Terbuka --') }}</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- RINCIAN MASALAH & SOLUSI -->
                    <div>
                        <div class="modal-section-title">
                            <i class="fas fa-circle-exclamation text-primary me-1"></i> {{ __('Rincian Masalah & Solusi') }}
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-danger">{{ __('Subjek / Judul Kendala') }} *</label>
                            <input type="text" name="judul" id="edit_judul" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-danger">{{ __('Deskripsi Rinci Masalah') }} *</label>
                            <textarea name="deskripsi" id="edit_deskripsi" class="form-control" rows="3" required></textarea>
                        </div>
                        <div>
                            <label class="form-label small fw-bold">{{ __('Solusi / Catatan Penyelesaian (Opsional)') }}</label>
                            <textarea name="solusi" id="edit_solusi" class="form-control" rows="2" placeholder="Tuliskan tindakan atau ringkasan solusi..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-phoenix-secondary btn-sm" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-phoenix-primary btn-sm px-3">
                        <i class="fas fa-save me-1"></i> {{ __('Simpan Perubahan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#tableTicket').DataTable({
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "{{ __('Semua') }}"]],
            "pageLength": 10,
            "scrollX": true,
            "dom": "<'row mb-3 align-items-center'<'col-12 col-md-6'l><'col-12 col-md-6 d-flex justify-content-md-end justify-content-start'f>>" +
                   "<'row'<'col-12'tr>>" +
                   "<'row mt-3 align-items-center'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
            "language": {
                "search": "{{ __('Quick search...') }}",
                "lengthMenu": "{{ __('Tampilkan _MENU_ baris') }}",
                "info": "{{ __('Menampilkan _START_ - _END_ dari _TOTAL_ tiket') }}",
                "infoEmpty": "{{ __('Tidak ada tiket') }}",
                "infoFiltered": "({{ __('disaring dari total _MAX_ data') }})",
                "paginate": {
                    "first": "{{ __('Awal') }}",
                    "last": "{{ __('Akhir') }}",
                    "next": "{{ __('Maju') }} <i class='fas fa-chevron-right ms-1'></i>",
                    "previous": "<i class='fas fa-chevron-left me-1'></i> {{ __('Mundur') }}"
                }
            }
        });

        // Quick user picker for Create Modal
        $('.btn-select-create-user').on('click', function() {
            $('#create_nama_pelapor').val($(this).data('name'));
            $('#create_email_pelapor').val($(this).data('email') || '');
            $('#create_user_id').val($(this).data('id') || '');
            if ($(this).data('departemen')) {
                $('#create_departemen_pelapor').val($(this).data('departemen'));
            }
            if ($(this).data('lokasi')) {
                $('#create_lokasi_pelapor').val($(this).data('lokasi'));
            }
        });

        // Quick user picker for Edit Modal
        $('.btn-select-edit-user').on('click', function() {
            $('#edit_nama_pelapor').val($(this).data('name'));
            $('#edit_email_pelapor').val($(this).data('email') || '');
            $('#edit_user_id').val($(this).data('id') || '');
            if ($(this).data('departemen')) {
                $('#edit_departemen_pelapor').val($(this).data('departemen'));
            }
            if ($(this).data('lokasi')) {
                $('#edit_lokasi_pelapor').val($(this).data('lokasi'));
            }
        });

        // Populate Edit Modal
        $(document).on('click', '.btn-edit-ticket', function() {
            const btn = $(this);
            const form = $('#formEditTicket');
            
            form.attr('action', btn.data('url'));
            $('#edit_ticket_badge').text(btn.data('no-tiket') || '');
            $('#edit_no_tiket').val(btn.data('no-tiket') || '');
            $('#edit_user_id').val(btn.data('user-id') || '');
            $('#edit_nama_pelapor').val(btn.data('nama-pelapor') || '');
            $('#edit_email_pelapor').val(btn.data('email-pelapor') || '');
            $('#edit_departemen_pelapor').val(btn.data('departemen-pelapor') || '');
            $('#edit_lokasi_pelapor').val(btn.data('lokasi-pelapor') || '');
            $('#edit_kategori').val(btn.data('kategori') || 'Hardware / Perangkat');
            $('#edit_prioritas').val(btn.data('prioritas') || 'Sedang');
            $('#edit_status').val(btn.data('status') || 'Open');
            $('#edit_barang_id').val(btn.data('barang-id') || '');
            $('#edit_assigned_to').val(btn.data('assigned-to') || '');
            $('#edit_judul').val(btn.data('judul') || '');
            $('#edit_deskripsi').val(btn.data('deskripsi') || '');
            $('#edit_solusi').val(btn.data('solusi') || '');
        });

        // SweetAlert2 delete confirmation
        $('.form-delete-ticket').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: '{{ __("Hapus Tiket Bantuan?") }}',
                text: '{{ __("Tiket dan semua riwayat tanggapan akan dihapus secara permanen.") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e63757',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '{{ __("Ya, Hapus!") }}',
                cancelButtonText: '{{ __("Batal") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
