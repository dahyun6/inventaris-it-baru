@extends('layout')

@section('title', __('Riwayat & Surat Serah Terima (Handover)'))

@section('header_actions')
@if(!Auth::user()?->isStaff())
<a href="{{ route('handover.create') }}" class="btn btn-phoenix-primary btn-sm">
    <i class="fas fa-file-signature me-1"></i> {{ __('Terbitkan Surat Tanda Terima') }}
</a>
@endif
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
</style>

<div class="phoenix-card">
    <div class="phoenix-card-header">
        <div>
            <h6 class="phoenix-card-title">
                <i class="fas fa-file-invoice text-primary"></i>
                {{ Auth::user()?->isStaff() ? __('Dokumen Tanda Terima & BAST Saya') : __('Dokumen Berita Acara & Log Handover') }}
            </h6>
            <small class="text-muted">{{ __('Total dokumen serah terima:') }} <strong>{{ $handovers->count() }}</strong> {{ __('log tercatat') }}</small>
        </div>
        <div>
            <span class="badge-phoenix badge-phoenix-info font-monospace">{{ __('Official Records') }}</span>
        </div>
    </div>

    <div class="phoenix-card-body p-0">
        <div class="table-responsive p-3">
            <table id="tableHandover" class="table table-phoenix w-100">
                <thead>
                    <tr>
                        <th width="4%">{{ __('NO') }}</th>
                        <th>{{ __('NO DOKUMEN / SURAT') }}</th>
                        <th>{{ __('TANGGAL') }}</th>
                        <th>{{ __('KODE ASET') }}</th>
                        <th>{{ __('MODEL / KATEGORI') }}</th>
                        <th>{{ __('PIHAK SERAH TERIMA') }}</th>
                        <th>{{ __('LOKASI') }}</th>
                        <th>{{ __('STATUS PENERIMAAN') }}</th>
                        <th class="text-center">{{ __('AKSI & DOKUMEN') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($handovers as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($row->no_surat)
                                <span class="badge bg-light text-primary border font-monospace px-2 py-1" style="font-size: 0.75rem;">
                                    <i class="fas fa-file-lines me-1"></i>{{ $row->no_surat }}
                                </span>
                            @else
                                <span class="text-muted small">Log Langsung</span>
                            @endif
                        </td>
                        <td class="font-monospace text-nowrap">{{ \Carbon\Carbon::parse($row->tanggal ?? $row->created_at)->format('d-m-Y') }}</td>
                        <td class="fw-bold text-dark font-monospace">
                            @if(!empty($row->barang_uuid))
                                <a href="{{ route('barang.show', $row->barang_uuid) }}" class="text-primary text-decoration-none font-monospace">{{ $row->kode_aset }}</a>
                            @else
                                {{ $row->kode_aset }}
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark" style="font-size: 0.8125rem;">{{ $row->model ?? '-' }}</div>
                            <small class="text-muted">{{ $row->kategori ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                                <span class="text-muted"><i class="fas fa-user-minus text-secondary me-1"></i>{{ $row->pemberi_nama ?? ($row->diserahkan_oleh ?? 'Gudang IT') }}</span>
                                <i class="fas fa-arrow-right text-primary mx-1"></i>
                                <span class="fw-bold text-primary"><i class="fas fa-user-plus text-primary me-1"></i>{{ $row->pengguna_terakhir ?? '-' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1.5 font-monospace" style="font-size: 0.775rem;">
                                <span class="text-secondary"><i class="fas fa-map-pin text-danger me-1"></i>{{ $row->lokasi_asal ?? 'Gudang IT' }}</span>
                                <i class="fas fa-arrow-right-long text-success mx-1"></i>
                                <span class="fw-bold text-dark"><i class="fas fa-location-dot text-success me-1"></i>{{ $row->lokasi ?? '-' }}</span>
                            </div>
                        </td>
                        <td>
                            @if(($row->status_terima ?? 'accepted') === 'accepted')
                                <span class="badge-phoenix badge-phoenix-success" style="font-size: 0.725rem;">
                                    <i class="fas fa-check-circle"></i> {{ __('Diterima') }}
                                </span>
                                @if(!empty($row->accepted_at))
                                    <div class="text-muted font-monospace mt-0.5" style="font-size: 0.6875rem;">
                                        {{ \Carbon\Carbon::parse($row->accepted_at)->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            @else
                                <span class="badge-phoenix badge-phoenix-warning" style="font-size: 0.725rem;">
                                    <i class="fas fa-clock"></i> {{ __('Menunggu Konfirmasi') }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center text-nowrap">
                            @php
                                $handoverTarget = $row->handover_uuid ?? ($row->uuid ?? ($row->no_surat ?: $row->id));
                            @endphp
                            <div class="d-inline-flex align-items-center gap-1">
                                @if(($row->status_terima ?? 'accepted') === 'pending')
                                    <form action="{{ route('handover.accept', $handoverTarget) }}" method="POST" class="d-inline form-accept-handover">
                                        @csrf
                                        <button type="button" class="btn btn-phoenix-primary btn-sm py-1 px-2.5 btn-trigger-accept" data-nosurat="{{ $handoverTarget }}" title="{{ __('Konfirmasi Penerimaan Aset') }}">
                                            <i class="fas fa-check me-1"></i> {{ __('Accept') }}
                                        </button>
                                    </form>
                                @endif
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('handover.receipt', $handoverTarget) }}" target="_blank" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Preview') }}">
                                        <i class="fas fa-eye text-primary me-1"></i> {{ __('Preview') }}
                                    </a>
                                    <a href="{{ route('handover.receipt', $handoverTarget) }}?print=1" target="_blank" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('PDF') }}">
                                        <i class="fas fa-print text-secondary"></i> {{ __('PDF') }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tableHandover').DataTable({
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "{{ __('Semua') }}"]],
            "pageLength": 10,
            "scrollX": true,
            "dom": "<'row mb-3 align-items-center'<'col-12 col-md-6'l><'col-12 col-md-6 d-flex justify-content-md-end justify-content-start'f>>" +
                   "<'row'<'col-12'tr>>" +
                   "<'row mt-3 align-items-center'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
            "language": {
                "search": "{{ __('Quick search...') }}",
                "lengthMenu": "{{ __('Tampilkan _MENU_ baris') }}",
                "info": "{{ __('Menampilkan _START_ - _END_ dari _TOTAL_ aset') }}",
                "infoEmpty": "{{ __('Tidak ada data aset') }}",
                "infoFiltered": "({{ __('disaring dari total _MAX_ data') }})",
                "paginate": {
                    "first": "{{ __('Awal') }}",
                    "last": "{{ __('Akhir') }}",
                    "next": "{{ __('Maju') }} <i class='fas fa-chevron-right ms-1'></i>",
                    "previous": "<i class='fas fa-chevron-left me-1'></i> {{ __('Mundur') }}"
                }
            }
        });

        // SweetAlert Confirmation for Accept Handover
        $(document).on('click', '.btn-trigger-accept', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            const noSurat = $(this).data('nosurat');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '{{ __("Konfirmasi Penerimaan Aset?") }}',
                    text: '{{ __("Dengan mengonfirmasi, Anda menyatakan telah menerima unit aset kerja ini dari IT.") }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3874ff',
                    cancelButtonColor: '#6e7891',
                    confirmButtonText: '<i class="fas fa-check me-1"></i> {{ __("Ya, Saya Terima Aset") }}',
                    cancelButtonText: '{{ __("Batal") }}',
                    reverseButtons: true,
                    customClass: {
                        popup: 'phoenix-swal-modal'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('Konfirmasi penerimaan aset?')) {
                    form.submit();
                }
            }
        });
    });
</script>
@endpush