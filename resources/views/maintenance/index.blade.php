@extends('layout')

@section('title', __('Maintenance & Perbaikan Aset'))

@section('header_actions')
<div class="d-flex flex-wrap gap-2">
    <a href="{{ route('maintenance.export_excel', request()->query()) }}" class="btn btn-phoenix-secondary btn-sm" title="{{ __('Ekspor data riwayat maintenance ke format spreadsheet Excel') }}">
        <i class="fas fa-file-excel text-success me-1"></i> {{ __('Export Excel') }}
    </a>
    <a href="{{ route('maintenance.report_print', request()->query()) }}" target="_blank" class="btn btn-phoenix-secondary btn-sm" title="{{ __('Buka dan cetak rekapitulasi laporan maintenance dalam format cetak') }}">
        <i class="fas fa-print text-secondary me-1"></i> {{ __('Cetak Rekap') }}
    </a>
    <a href="{{ route('maintenance.create') }}" class="btn btn-phoenix-primary btn-sm">
        <i class="fas fa-screwdriver-wrench me-1"></i> {{ __('Catat Maintenance Baru') }}
    </a>
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
</style>

@if(isset($upcomingPreventives) && $upcomingPreventives->isNotEmpty())
<!-- PREVENTIVE MAINTENANCE ALERT BANNER -->
<div class="card border-0 mb-4 shadow-sm" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 4px solid #f59e0b !important; border-radius: 12px;">
    <div class="card-body p-3 p-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning text-dark shadow-sm" style="width: 38px; height: 38px; font-size: 1.1rem;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">{{ __('Jadwal Preventive Maintenance Mendatang') }}</h6>
                    <small class="text-muted">{{ __('Aset yang mendekati atau telah melewati batas siklus servis berkala.') }}</small>
                </div>
            </div>
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold" style="font-size: 0.775rem;">
                <i class="fas fa-bell me-1"></i> {{ $upcomingPreventives->count() }} {{ __('Perangkat Perlu Perhatian') }}
            </span>
        </div>

        <div class="row g-2">
            @foreach($upcomingPreventives->take(6) as $pBarang)
            <div class="col-md-6 col-lg-4">
                <div class="bg-white p-2.5 rounded-3 border d-flex align-items-center justify-content-between p-2 shadow-xs">
                    <div class="overflow-hidden me-2">
                        <div class="fw-bold font-monospace text-dark text-truncate" style="font-size: 0.8125rem;">
                            <a href="{{ route('barang.show', $pBarang->uuid) }}" class="text-decoration-none text-dark">
                                {{ $pBarang->no_aset_local }}
                            </a>
                        </div>
                        <div class="text-muted text-truncate" style="font-size: 0.725rem;">
                            {{ $pBarang->nama_barang ?? $pBarang->model }} ({{ $pBarang->user->name ?? 'Belum teralokasi' }})
                        </div>
                        <div style="font-size: 0.7rem;" class="mt-1">
                            @if($pBarang->isMaintenanceOverdue())
                                <span class="text-danger fw-bold"><i class="fas fa-circle-exclamation me-1"></i>{{ __('Jatuh tempo:') }} {{ $pBarang->tgl_maintenance_berikutnya?->format('d M Y') }}</span>
                            @else
                                <span class="text-warning fw-semibold"><i class="fas fa-clock me-1"></i>{{ __('Jadwal:') }} {{ $pBarang->tgl_maintenance_berikutnya?->format('d M Y') }}</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('maintenance.create', ['barang_id' => $pBarang->id]) }}" class="btn btn-outline-primary btn-sm px-2 py-1 flex-shrink-0" style="font-size: 0.75rem;" title="{{ __('Jadwalkan Servis Sekarang') }}">
                        <i class="fas fa-wrench me-1"></i>{{ __('Servis') }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- KPI SUMMARY CARDS -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">{{ __('Total Tiket Servis') }}</span>
                <h4 class="fw-bold mb-0 text-dark">{{ number_format($metrics['total']) }}</h4>
                <small class="text-muted" style="font-size: 0.725rem;">{{ __('Semua riwayat maintenance') }}</small>
            </div>
            <div class="d-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 46px; height: 46px; font-size: 1.25rem;">
                <i class="fas fa-screwdriver-wrench"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">{{ __('Sedang Dalam Proses') }}</span>
                <h4 class="fw-bold mb-0 text-warning">{{ number_format($metrics['dalam_proses']) }}</h4>
                <small class="text-muted" style="font-size: 0.725rem;">{{ __('Perangkat sedang diservis') }}</small>
            </div>
            <div class="d-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle" style="width: 46px; height: 46px; font-size: 1.25rem;">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">{{ __('Selesai Diperbaiki') }}</span>
                <h4 class="fw-bold mb-0 text-success">{{ number_format($metrics['selesai']) }}</h4>
                <small class="text-muted" style="font-size: 0.725rem;">{{ __('Telah siap & kembali normal') }}</small>
            </div>
            <div class="d-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 46px; height: 46px; font-size: 1.25rem;">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">{{ __('Total Biaya Servis') }}</span>
                <h4 class="fw-bold mb-0 text-info">Rp {{ number_format($metrics['total_biaya'], 0, ',', '.') }}</h4>
                <small class="text-muted" style="font-size: 0.725rem;">{{ __('Akumulasi biaya perbaikan') }}</small>
            </div>
            <div class="d-flex align-items-center justify-content-center bg-info-subtle text-info rounded-circle" style="width: 46px; height: 46px; font-size: 1.25rem;">
                <i class="fas fa-receipt"></i>
            </div>
        </div>
    </div>
</div>

<!-- MAIN CARD -->
<div class="phoenix-card">
    <div class="phoenix-card-header flex-wrap gap-2">
        <div>
            <h6 class="phoenix-card-title mb-0">
                <i class="fas fa-tools text-primary"></i>
                {{ __('Daftar Riwayat Maintenance & Servis Hardware') }}
            </h6>
            <small class="text-muted">{{ __('Total data:') }} <strong>{{ $maintenances->count() }}</strong> {{ __('catatan') }}</small>
        </div>
        
        <!-- Filter Tabs -->
        <div class="d-flex align-items-center gap-1">
            <a href="{{ route('maintenance.index') }}" class="btn btn-sm {{ empty($status) ? 'btn-primary' : 'btn-outline-secondary' }} py-1 px-2" style="font-size: 0.775rem;">
                {{ __('Semua') }}
            </a>
            <a href="{{ route('maintenance.index', ['status' => 'Dalam Proses']) }}" class="btn btn-sm {{ $status === 'Dalam Proses' ? 'btn-warning text-dark' : 'btn-outline-secondary' }} py-1 px-2" style="font-size: 0.775rem;">
                <i class="fas fa-clock me-1"></i>{{ __('Dalam Proses') }}
            </a>
            <a href="{{ route('maintenance.index', ['status' => 'Selesai']) }}" class="btn btn-sm {{ $status === 'Selesai' ? 'btn-success' : 'btn-outline-secondary' }} py-1 px-2" style="font-size: 0.775rem;">
                <i class="fas fa-check-circle me-1"></i>{{ __('Selesai') }}
            </a>
            <a href="{{ route('maintenance.index', ['status' => 'Dibatalkan']) }}" class="btn btn-sm {{ $status === 'Dibatalkan' ? 'btn-danger' : 'btn-outline-secondary' }} py-1 px-2" style="font-size: 0.775rem;">
                {{ __('Dibatalkan') }}
            </a>
        </div>
    </div>

    <div class="phoenix-card-body p-0">
        <div class="table-responsive p-3">
            <table id="tableMaintenance" class="table table-phoenix w-100">
                <thead>
                    <tr>
                        <th width="4%">{{ __('NO') }}</th>
                        <th>{{ __('KODE TIKET') }}</th>
                        <th>{{ __('TANGGAL MULAI') }}</th>
                        <th>{{ __('NO ASET & PERANGKAT') }}</th>
                        <th>{{ __('JENIS SERVIS') }}</th>
                        <th>{{ __('PELAKSANA / TEKNISI') }}</th>
                        <th>{{ __('BIAYA') }}</th>
                        <th>{{ __('STATUS') }}</th>
                        <th class="text-center">{{ __('AKSI') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maintenances as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ route('maintenance.show', $row->uuid) }}" class="badge bg-light text-primary border font-monospace px-2 py-1 text-decoration-none" style="font-size: 0.75rem;">
                                <i class="fas fa-ticket me-1"></i>{{ $row->no_maintenance }}
                            </a>
                        </td>
                        <td class="font-monospace">
                            <div>{{ \Carbon\Carbon::parse($row->tanggal_mulai)->format('d M Y') }}</div>
                            @if($row->tanggal_selesai)
                                <small class="text-success"><i class="fas fa-check me-1"></i>{{ \Carbon\Carbon::parse($row->tanggal_selesai)->format('d M Y') }}</small>
                            @endif
                        </td>
                        <td>
                            @if($row->barang)
                                <div class="fw-bold text-dark">
                                    <a href="{{ route('barang.show', $row->barang->uuid) }}" class="text-decoration-none text-dark hover-primary font-monospace">
                                        {{ $row->barang->no_aset_local }}
                                    </a>
                                </div>
                                <small class="text-muted">{{ $row->barang->nama_barang ?? $row->barang->model }}</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary border">
                                {{ $row->jenis_maintenance }}
                            </span>
                        </td>
                        <td>
                            @if($row->pelaksana === 'Vendor Eksternal')
                                <span class="badge bg-light text-info border">
                                    <i class="fas fa-building me-1"></i>{{ $row->vendor->nama_vendor ?? 'Vendor' }}
                                </span>
                                @if($row->nama_teknisi)
                                    <div class="small text-muted">{{ $row->nama_teknisi }}</div>
                                @endif
                            @else
                                <span class="badge bg-light text-primary border">
                                    <i class="fas fa-user-gear me-1"></i>Internal IT
                                </span>
                                @if($row->nama_teknisi)
                                    <div class="small text-muted">{{ $row->nama_teknisi }}</div>
                                @endif
                            @endif
                        </td>
                        <td class="font-monospace">
                            @if($row->biaya > 0)
                                <span class="fw-semibold text-dark">Rp {{ number_format($row->biaya, 0, ',', '.') }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($row->status === 'Dalam Proses')
                                <span class="badge-phoenix badge-phoenix-warning"><i class="fas fa-clock"></i> {{ __('Dalam Proses') }}</span>
                            @elseif($row->status === 'Selesai')
                                <span class="badge-phoenix badge-phoenix-success"><i class="fas fa-check"></i> {{ __('Selesai') }}</span>
                            @else
                                <span class="badge-phoenix badge-phoenix-secondary"><i class="fas fa-ban"></i> {{ __('Dibatalkan') }}</span>
                            @endif
                        </td>
                        <td class="text-center text-nowrap">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('maintenance.show', $row->uuid) }}" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Detail Servis') }}">
                                    <i class="fas fa-eye text-primary"></i>
                                </a>
                                @if($row->status === 'Dalam Proses')
                                <button type="button" class="btn btn-phoenix-secondary py-1 px-2 btn-complete-modal" 
                                    data-id="{{ $row->uuid }}"
                                    data-no="{{ $row->no_maintenance }}"
                                    data-aset="{{ $row->barang->no_aset_local ?? '' }} - {{ $row->barang->nama_barang ?? ($row->barang->model ?? '') }}"
                                    data-biaya="{{ (int)$row->biaya }}"
                                    data-teknisi="{{ $row->nama_teknisi ?? '' }}"
                                    title="{{ __('Tandai Selesai Servis') }}">
                                    <i class="fas fa-circle-check text-success"></i>
                                </button>
                                @endif
                                <a href="{{ route('maintenance.edit', $row->uuid) }}" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Edit') }}">
                                    <i class="fas fa-pencil text-warning"></i>
                                </a>
                                <a href="{{ route('maintenance.print', $row->uuid) }}" target="_blank" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Cetak SPK / Laporan') }}">
                                    <i class="fas fa-print text-secondary"></i>
                                </a>
                                <form action="{{ route('maintenance.destroy', $row->uuid) }}" method="POST" class="d-inline form-delete-maintenance">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Hapus') }}">
                                        <i class="fas fa-trash-can text-danger"></i>
                                    </button>
                                </form>
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

<!-- MODAL QUICK COMPLETE -->
<div class="modal fade" id="modalCompleteMaintenance" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold text-dark">
                    <i class="fas fa-circle-check text-success me-1"></i>
                    {{ __('Selesaikan Maintenance / Servis') }}
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCompleteMaintenance" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 p-3 mb-3 d-flex align-items-center gap-2" style="background-color: var(--phoenix-primary-subtle); color: var(--phoenix-primary); border-radius: 8px;">
                        <i class="fas fa-laptop-medical fs-4"></i>
                        <div>
                            <div class="fw-bold small" id="modalCompleteNo">MNT-...</div>
                            <div class="small" id="modalCompleteAset">-</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger">{{ __('Tanggal Selesai Servis') }} *</label>
                        <input type="date" name="tanggal_selesai" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Biaya Final (Rp)') }}</label>
                            <input type="number" step="any" name="biaya" id="modalCompleteBiaya" class="form-control form-control-sm" placeholder="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">{{ __('Teknisi Pelaksana') }}</label>
                            <input type="text" name="nama_teknisi" id="modalCompleteTeknisi" class="form-control form-control-sm" placeholder="Nama Teknisi">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger">{{ __('Tindakan / Solusi Perbaikan') }} *</label>
                        <textarea name="tindakan_perbaikan" class="form-control form-control-sm" rows="3" placeholder="Misal: Ganti SSD 512GB, install ulang OS, bersihkan kipas..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger">{{ __('Ubah Status Aset Menjadi') }} *</label>
                        <select name="status_aset_setelahnya" class="form-select form-select-sm" required>
                            <option value="Tersedia" selected>{{ __('Tersedia (Siap Digunakan / Di Gudang IT)') }}</option>
                            <option value="Dipinjam">{{ __('Dipinjam (Kembali Digunakan Pegawai)') }}</option>
                            <option value="Rusak">{{ __('Rusak (Afkir / Tidak Dapat Diperbaiki)') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top py-2">
                    <button type="button" class="btn btn-phoenix-secondary btn-sm" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-success btn-sm px-3">
                        <i class="fas fa-check me-1"></i> {{ __('Simpan & Selesaikan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#tableMaintenance').DataTable({
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "{{ __('Semua') }}"]],
            "pageLength": 10,
            "scrollX": true,
            "dom": "<'row mb-3 align-items-center'<'col-12 col-md-6'l><'col-12 col-md-6 d-flex justify-content-md-end justify-content-start'f>>" +
                   "<'row'<'col-12'tr>>" +
                   "<'row mt-3 align-items-center'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
            "language": {
                "search": "{{ __('Quick search...') }}",
                "lengthMenu": "{{ __('Tampilkan _MENU_ baris') }}",
                "info": "{{ __('Menampilkan _START_ - _END_ dari _TOTAL_ data') }}",
                "infoEmpty": "{{ __('Tidak ada data maintenance') }}",
                "infoFiltered": "({{ __('disaring dari total _MAX_ data') }})",
                "paginate": {
                    "first": "{{ __('Awal') }}",
                    "last": "{{ __('Akhir') }}",
                    "next": "{{ __('Maju') }} <i class='fas fa-chevron-right ms-1'></i>",
                    "previous": "<i class='fas fa-chevron-left me-1'></i> {{ __('Mundur') }}"
                }
            }
        });

        // Quick Complete Modal Trigger
        $('.btn-complete-modal').on('click', function() {
            var id = $(this).data('id');
            var no = $(this).data('no');
            var aset = $(this).data('aset');
            var biaya = $(this).data('biaya');
            var teknisi = $(this).data('teknisi');

            $('#modalCompleteNo').text(no);
            $('#modalCompleteAset').text(aset);
            $('#modalCompleteBiaya').val(biaya || '');
            $('#modalCompleteTeknisi').val(teknisi || '');
            $('#formCompleteMaintenance').attr('action', '/maintenance/' + id + '/complete');

            var modal = new bootstrap.Modal(document.getElementById('modalCompleteMaintenance'));
            modal.show();
        });

        // SweetAlert Delete Confirmation
        $('.form-delete-maintenance').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            Swal.fire({
                title: '{{ __("Hapus Data Maintenance?") }}',
                text: '{{ __("Data servis dan log biaya terkait akan dihapus secara permanen.") }}',
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
