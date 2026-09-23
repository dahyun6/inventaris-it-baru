@extends('layout')

@section('title', __('Detail Tiket Bantuan IT'))

@section('header_actions')
<div class="d-flex flex-wrap gap-2">
    <a href="{{ route('ticket.index') }}" class="btn btn-phoenix-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Daftar Tiket') }}
    </a>
    <a href="{{ route('ticket.print', $ticket->uuid) }}" target="_blank" class="btn btn-phoenix-secondary btn-sm">
        <i class="fas fa-print me-1"></i> {{ __('Cetak Formulir') }}
    </a>
    @if(!Auth::user()?->isStaff())
    <button type="button" class="btn btn-phoenix-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditTicket">
        <i class="fas fa-pencil me-1 text-warning"></i> {{ __('Edit') }}
    </button>
    @if($ticket->barang_id)
    <a href="{{ route('maintenance.create', ['barang_id' => $ticket->barang_id]) }}" class="btn btn-warning btn-sm text-dark fw-semibold">
        <i class="fas fa-screwdriver-wrench me-1"></i> {{ __('Eskalasi ke Maintenance') }}
    </a>
    @endif
    @endif
</div>
@endsection

@section('content')
<style>
    .feed-item {
        position: relative;
        padding-left: 32px;
        padding-bottom: 24px;
    }
    .feed-item:last-child {
        padding-bottom: 0;
    }
    .feed-item::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 24px;
        bottom: 0;
        width: 2px;
        background-color: var(--phoenix-border-color);
    }
    .feed-item:last-child::before {
        display: none;
    }
    .feed-icon {
        position: absolute;
        left: 0;
        top: 0;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        background: #fff;
        border: 2px solid var(--phoenix-primary);
        color: var(--phoenix-primary);
        z-index: 2;
    }
</style>

<div class="row g-4">
    <!-- LEFT COLUMN: TICKET & REPORTER DETAILS -->
    <div class="col-lg-4">
        <!-- Ticket Meta Card -->
        <div class="phoenix-card mb-4">
            <div class="phoenix-card-header d-flex justify-content-between align-items-center">
                <h6 class="phoenix-card-title mb-0">
                    <i class="fas fa-ticket text-primary"></i>
                    {{ __('Info Tiket') }}
                </h6>
                <span class="badge bg-light text-primary border font-monospace px-2 py-1">
                    {{ $ticket->no_tiket }}
                </span>
            </div>
            <div class="phoenix-card-body p-3">
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">{{ __('Status Saat Ini') }}</span>
                    @if($ticket->status === 'Open')
                        <span class="badge-phoenix badge-phoenix-danger">Open</span>
                    @elseif($ticket->status === 'In Progress')
                        <span class="badge-phoenix badge-phoenix-warning">In Progress</span>
                    @elseif($ticket->status === 'Pending')
                        <span class="badge-phoenix badge-phoenix-info">Pending</span>
                    @elseif($ticket->status === 'Resolved')
                        <span class="badge-phoenix badge-phoenix-success">Resolved</span>
                    @else
                        <span class="badge-phoenix badge-phoenix-secondary">Closed</span>
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">{{ __('Tingkat Prioritas') }}</span>
                    @if($ticket->prioritas === 'Kritis')
                        <span class="badge-phoenix badge-phoenix-danger">Kritis</span>
                    @elseif($ticket->prioritas === 'Tinggi')
                        <span class="badge-phoenix badge-phoenix-warning">Tinggi</span>
                    @elseif($ticket->prioritas === 'Sedang')
                        <span class="badge-phoenix badge-phoenix-info">Sedang</span>
                    @else
                        <span class="badge-phoenix badge-phoenix-secondary">Rendah</span>
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">{{ __('Kategori Layanan') }}</span>
                    <span class="fw-semibold text-dark small">{{ $ticket->kategori }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">{{ __('Waktu Diajukan') }}</span>
                    <span class="font-monospace small text-dark">{{ $ticket->created_at->format('d M Y, H:i') }}</span>
                </div>
                @if($ticket->resolved_at)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">{{ __('Waktu Selesai') }}</span>
                    <span class="font-monospace small text-success fw-bold">{{ $ticket->resolved_at->format('d M Y, H:i') }}</span>
                </div>
                @endif
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted small">{{ __('Teknisi PIC') }}</span>
                    @if($ticket->assignedUser)
                        <span class="fw-bold text-dark small"><i class="far fa-user text-primary me-1"></i>{{ $ticket->assignedUser->name }}</span>
                    @else
                        <span class="text-muted small">Belum Ditugaskan</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Reporter Card -->
        <div class="phoenix-card mb-4">
            <div class="phoenix-card-header">
                <h6 class="phoenix-card-title mb-0">
                    <i class="fas fa-user-circle text-primary"></i>
                    {{ __('Identitas Pelapor') }}
                </h6>
            </div>
            <div class="phoenix-card-body p-3">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 44px; height: 44px; font-size: 1.25rem;">
                        <i class="far fa-user"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">{{ $ticket->nama_pelapor }}</div>
                        <small class="text-muted">{{ $ticket->email_pelapor ?? 'Tidak ada email' }}</small>
                    </div>
                </div>
                <div class="pt-2 border-top">
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span class="text-muted small">{{ __('Departemen') }}:</span>
                        <span class="small text-dark fw-semibold">{{ $ticket->departemen_pelapor ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span class="text-muted small">{{ __('Lokasi / Meja') }}:</span>
                        <span class="small text-dark">{{ $ticket->lokasi_pelapor ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Associated Asset Card -->
        @if($ticket->barang)
        <div class="phoenix-card">
            <div class="phoenix-card-header d-flex justify-content-between align-items-center">
                <h6 class="phoenix-card-title mb-0">
                    <i class="fas fa-laptop text-primary"></i>
                    {{ __('Perangkat Terkait') }}
                </h6>
                <a href="{{ route('barang.show', $ticket->barang->uuid) }}" class="badge bg-light text-primary border font-monospace text-decoration-none">
                    {{ $ticket->barang->no_aset_local }}
                </a>
            </div>
            <div class="phoenix-card-body p-3">
                <div class="fw-bold text-dark mb-1">{{ $ticket->barang->nama_barang ?? $ticket->barang->model }}</div>
                <p class="text-muted font-monospace small mb-2">SN: {{ $ticket->barang->serial_number ?? '-' }}</p>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="text-muted small">{{ __('Status Aset') }}</span>
                    <span class="badge-phoenix badge-phoenix-info">{{ $ticket->barang->status }}</span>
                </div>
                <div class="mt-3">
                    <a href="{{ route('barang.show', $ticket->barang->uuid) }}" class="btn btn-phoenix-secondary btn-sm w-100">
                        {{ __('Buka Detail Hardware') }} <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- RIGHT COLUMN: TICKET CONVERSATION & TIMELINE -->
    <div class="col-lg-8">
        <!-- Main Issue Card -->
        <div class="phoenix-card mb-4">
            <div class="phoenix-card-header">
                <div>
                    <h5 class="fw-bold text-dark mb-1">{{ $ticket->judul }}</h5>
                    <small class="text-muted">
                        <i class="far fa-clock me-1"></i> Dibuat pada {{ $ticket->created_at->translatedFormat('d F Y, H:i') }} oleh <strong>{{ $ticket->nama_pelapor }}</strong>
                    </small>
                </div>
            </div>
            <div class="phoenix-card-body p-4">
                <div class="p-3 bg-light rounded-3 border text-secondary mb-3" style="font-size: 0.9rem; line-height: 1.6; white-space: pre-line;">
                    {{ $ticket->deskripsi }}
                </div>

                @if($ticket->solusi)
                <div class="alert alert-success border-0 p-3 mb-0" style="background-color: #e8f7ec; border-radius: 8px;">
                    <div class="fw-bold text-success mb-1">
                        <i class="fas fa-circle-check me-1"></i> {{ __('Solusi / Tindakan Penyelesaian:') }}
                    </div>
                    <div class="text-dark small" style="white-space: pre-line;">
                        {{ $ticket->solusi }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Activity Timeline Card -->
        <div class="phoenix-card mb-4">
            <div class="phoenix-card-header">
                <h6 class="phoenix-card-title mb-0">
                    <i class="fas fa-comments text-primary"></i>
                    {{ __('Riwayat Tanggapan & Aktivitas Penanganan') }}
                </h6>
                <span class="badge-phoenix badge-phoenix-info font-monospace">{{ $ticket->responses->count() }} {{ __('Catatan') }}</span>
            </div>
            <div class="phoenix-card-body p-4">
                @if($ticket->responses->isEmpty())
                    <p class="text-muted text-center py-4 mb-0">{{ __('Belum ada tanggapan untuk tiket ini.') }}</p>
                @else
                    <div class="ps-1">
                        @foreach($ticket->responses as $resp)
                        @if(Auth::user()?->isStaff() && $resp->tipe === 'note')
                            @continue
                        @endif
                        <div class="feed-item">
                            <div class="feed-icon">
                                @if($resp->tipe === 'status_change')
                                    <i class="fas fa-arrows-rotate"></i>
                                @elseif($resp->tipe === 'note')
                                    <i class="fas fa-lock"></i>
                                @else
                                    <i class="fas fa-reply"></i>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div>
                                    <strong class="text-dark" style="font-size: 0.85rem;">{{ $resp->nama_pengirim }}</strong>
                                    @if($resp->tipe === 'note')
                                        <span class="badge bg-light text-warning border ms-1" style="font-size: 0.6875rem;"><i class="fas fa-lock me-1"></i>Catatan Internal</span>
                                    @elseif($resp->tipe === 'status_change')
                                        <span class="badge bg-light text-info border ms-1" style="font-size: 0.6875rem;">Status Update</span>
                                    @else
                                        <span class="badge bg-light text-primary border ms-1" style="font-size: 0.6875rem;">Balasan Helpdesk</span>
                                    @endif
                                </div>
                                <small class="text-muted font-monospace" style="font-size: 0.725rem;">
                                    {{ $resp->created_at->format('d M Y, H:i') }}
                                </small>
                            </div>
                            <div class="p-3 bg-light rounded-3 border text-secondary" style="font-size: 0.85rem; line-height: 1.5; white-space: pre-line;">
                                {{ $resp->pesan }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Add Response / Update Status Form -->
        <div class="phoenix-card">
            <div class="phoenix-card-header">
                <h6 class="phoenix-card-title mb-0">
                    <i class="fas fa-reply-all text-primary"></i>
                    {{ Auth::user()?->isStaff() ? __('Beri Tanggapan / Balasan') : __('Beri Tanggapan & Perbarui Status Tiket') }}
                </h6>
            </div>
            <div class="phoenix-card-body p-4">
                <form action="{{ route('ticket.response', $ticket->uuid) }}" method="POST">
                    @csrf

                    @if(Auth::user()?->isStaff())
                        <input type="hidden" name="tipe" value="response">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-danger">{{ __('Pesan / Tanggapan Anda') }} *</label>
                            <textarea name="pesan" class="form-control" rows="3" placeholder="{{ __('Tuliskan balasan atau perkembangan terkait kendala ini...') }}" required></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-phoenix-primary btn-sm px-4">
                                <i class="fas fa-paper-plane me-1"></i> {{ __('Kirim Tanggapan') }}
                            </button>
                        </div>
                    @else
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-danger">{{ __('Jenis Tanggapan') }} *</label>
                                <select name="tipe" class="form-select form-select-sm" required>
                                    <option value="response" selected>{{ __('Balasan Resmi (Tanggapan)') }}</option>
                                    <option value="note">{{ __('Catatan Internal (Internal IT Note)') }}</option>
                                    <option value="status_change">{{ __('Perubahan Status & Progres') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">{{ __('Ubah Status Menjadi') }}</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="Open" {{ $ticket->status === 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="In Progress" {{ $ticket->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Pending" {{ $ticket->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Resolved" {{ $ticket->status === 'Resolved' ? 'selected' : '' }}>Resolved (Selesai)</option>
                                    <option value="Closed" {{ $ticket->status === 'Closed' ? 'selected' : '' }}>Closed (Tutup)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">{{ __('Tugaskan Teknisi (PIC)') }}</label>
                                <select name="assigned_to" class="form-select form-select-sm">
                                    <option value="">{{ __('-- Biarkan Saat Ini --') }}</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" {{ $ticket->assigned_to == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-danger">{{ __('Pesan / Catatan Penanganan') }} *</label>
                            <textarea name="pesan" class="form-control" rows="3" placeholder="{{ __('Tuliskan pesan balasan ke pengguna atau tindakan teknis yang diambil...') }}" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">{{ __('Tindakan / Solusi Final (Diisi jika tiket berstatus Resolved)') }}</label>
                            <textarea name="solusi" class="form-control" rows="2" placeholder="Misal: Sudah diganti kabel LAN baru, setting IP static diubah...">{{ $ticket->solusi }}</textarea>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-phoenix-primary btn-sm px-4">
                                <i class="fas fa-paper-plane me-1"></i> {{ __('Kirim Tanggapan / Update') }}
                            </button>
                        </div>
                    @endif
                </form>
            </div>
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
                        <span class="badge bg-light text-primary border font-monospace ms-2">{{ $ticket->no_tiket }}</span>
                    </h6>
                    <small class="text-muted" style="font-size: 0.75rem;">{{ __('Perbarui data pelapor, klasifikasi masalah, PIC teknisi, atau solusi penanganan.') }}</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('ticket.update', $ticket->uuid) }}" method="POST" id="formEditTicket">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- INFORMASI PELAPOR -->
                    <div class="border-bottom pb-3 mb-3">
                        <div class="fw-bold text-uppercase text-secondary mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                            <i class="far fa-user text-primary me-1"></i> {{ __('Informasi Pelapor') }}
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-danger">{{ __('Nomor Tiket') }} *</label>
                                <input type="text" name="no_tiket" class="form-control font-monospace bg-light" value="{{ old('no_tiket', $ticket->no_tiket) }}" readonly required>
                            </div>
                            <div class="col-md-7">
                                <label class="form-label small fw-bold text-danger">{{ __('Nama Pelapor / Karyawan') }} *</label>
                                <div class="input-group">
                                    <input type="text" name="nama_pelapor" id="edit_show_nama_pelapor" class="form-control" value="{{ old('nama_pelapor', $ticket->nama_pelapor) }}" required>
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ __('Pilih User') }}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" style="max-height: 220px; overflow-y: auto;">
                                        @foreach($users as $u)
                                            <li>
                                                <a class="dropdown-item btn-select-edit-show-user" href="javascript:void(0)" 
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
                                <input type="hidden" name="user_id" id="edit_show_user_id" value="{{ old('user_id', $ticket->user_id) }}">
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">{{ __('Email Pelapor') }}</label>
                                <input type="email" name="email_pelapor" id="edit_show_email_pelapor" class="form-control" value="{{ old('email_pelapor', $ticket->email_pelapor) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">{{ __('Departemen / Divisi') }}</label>
                                <input type="text" name="departemen_pelapor" id="edit_show_departemen_pelapor" class="form-control bg-light" readonly style="cursor: not-allowed;" placeholder="{{ __('Otomatis terisi dari User...') }}" value="{{ old('departemen_pelapor', $ticket->departemen_pelapor) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">{{ __('Lokasi / Meja / Ruangan') }}</label>
                                <input type="text" name="lokasi_pelapor" id="edit_show_lokasi_pelapor" class="form-control" value="{{ old('lokasi_pelapor', $ticket->lokasi_pelapor) }}">
                            </div>
                        </div>
                    </div>

                    <!-- KLASIFIKASI LAYANAN & PENUGASAN -->
                    <div class="border-bottom pb-3 mb-3">
                        <div class="fw-bold text-uppercase text-secondary mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                            <i class="fas fa-tags text-primary me-1"></i> {{ __('Klasifikasi Layanan & Penugasan') }}
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-danger">{{ __('Kategori Tiket') }} *</label>
                                <select name="kategori" class="form-select" required>
                                    <option value="Hardware / Perangkat" {{ old('kategori', $ticket->kategori) == 'Hardware / Perangkat' ? 'selected' : '' }}>Hardware / Perangkat (PC/Laptop)</option>
                                    <option value="Software & Aplikasi" {{ old('kategori', $ticket->kategori) == 'Software & Aplikasi' ? 'selected' : '' }}>Software & Aplikasi / OS</option>
                                    <option value="Jaringan & Internet" {{ old('kategori', $ticket->kategori) == 'Jaringan & Internet' ? 'selected' : '' }}>Jaringan, WiFi & VPN</option>
                                    <option value="Email & Akun / Akses" {{ old('kategori', $ticket->kategori) == 'Email & Akun / Akses' ? 'selected' : '' }}>Email, Akun & Hak Akses</option>
                                    <option value="Printer & Scanner" {{ old('kategori', $ticket->kategori) == 'Printer & Scanner' ? 'selected' : '' }}>Printer & Scanner</option>
                                    <option value="Permintaan Pengadaan / Fasilitas" {{ old('kategori', $ticket->kategori) == 'Permintaan Pengadaan / Fasilitas' ? 'selected' : '' }}>Permintaan Pengadaan / Fasilitas</option>
                                    <option value="Lainnya" {{ old('kategori', $ticket->kategori) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-danger">{{ __('Tingkat Prioritas') }} *</label>
                                <select name="prioritas" class="form-select" required>
                                    <option value="Rendah" {{ old('prioritas', $ticket->prioritas) == 'Rendah' ? 'selected' : '' }}>Rendah (Low)</option>
                                    <option value="Sedang" {{ old('prioritas', $ticket->prioritas) == 'Sedang' ? 'selected' : '' }}>Sedang (Normal)</option>
                                    <option value="Tinggi" {{ old('prioritas', $ticket->prioritas) == 'Tinggi' ? 'selected' : '' }}>Tinggi (High)</option>
                                    <option value="Kritis" {{ old('prioritas', $ticket->prioritas) == 'Kritis' ? 'selected' : '' }}>Kritis (Urgent)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-danger">{{ __('Status Tiket') }} *</label>
                                <select name="status" class="form-select" required>
                                    <option value="Open" {{ old('status', $ticket->status) == 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="In Progress" {{ old('status', $ticket->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Pending" {{ old('status', $ticket->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Resolved" {{ old('status', $ticket->status) == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                    <option value="Closed" {{ old('status', $ticket->status) == 'Closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">{{ __('Aset Hardware Terkait (Opsional)') }}</label>
                                <select name="barang_id" class="form-select">
                                    <option value="">{{ __('-- Tidak Ada / Bukan Masalah Hardware Aset --') }}</option>
                                    @foreach($barangs as $b)
                                        <option value="{{ $b->id }}" {{ (old('barang_id', $ticket->barang_id) == $b->id) ? 'selected' : '' }}>
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
                                        <option value="{{ $u->id }}" {{ old('assigned_to', $ticket->assigned_to) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- RINCIAN MASALAH & SOLUSI -->
                    <div>
                        <div class="fw-bold text-uppercase text-secondary mb-3" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                            <i class="fas fa-circle-exclamation text-primary me-1"></i> {{ __('Rincian Masalah & Solusi') }}
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-danger">{{ __('Subjek / Judul Kendala') }} *</label>
                            <input type="text" name="judul" class="form-control" value="{{ old('judul', $ticket->judul) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-danger">{{ __('Deskripsi Rinci Masalah') }} *</label>
                            <textarea name="deskripsi" class="form-control" rows="3" required>{{ old('deskripsi', $ticket->deskripsi) }}</textarea>
                        </div>
                        <div>
                            <label class="form-label small fw-bold">{{ __('Solusi / Catatan Penyelesaian (Opsional)') }}</label>
                            <textarea name="solusi" class="form-control" rows="2" placeholder="Tuliskan tindakan atau ringkasan solusi...">{{ old('solusi', $ticket->solusi) }}</textarea>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnSelectEditShowUsers = document.querySelectorAll('.btn-select-edit-show-user');
        const editShowNamaPelapor = document.getElementById('edit_show_nama_pelapor');
        const editShowEmailPelapor = document.getElementById('edit_show_email_pelapor');
        const editShowUserId = document.getElementById('edit_show_user_id');
        const editShowDeptPelapor = document.getElementById('edit_show_departemen_pelapor');
        const editShowLokasiPelapor = document.getElementById('edit_show_lokasi_pelapor');

        btnSelectEditShowUsers.forEach(function(btn) {
            btn.addEventListener('click', function() {
                if (editShowNamaPelapor) editShowNamaPelapor.value = this.dataset.name;
                if (editShowEmailPelapor) editShowEmailPelapor.value = this.dataset.email || '';
                if (editShowUserId) editShowUserId.value = this.dataset.id || '';
                if (editShowDeptPelapor && this.dataset.departemen) editShowDeptPelapor.value = this.dataset.departemen;
                if (editShowLokasiPelapor && this.dataset.lokasi) editShowLokasiPelapor.value = this.dataset.lokasi;
            });
        });
    });
</script>
@endpush
