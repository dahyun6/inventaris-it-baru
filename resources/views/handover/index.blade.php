@extends('layout')

@section('title', __('Riwayat & Surat Serah Terima (Handover)'))

@section('header_actions')
<a href="{{ route('handover.create') }}" class="btn btn-phoenix-primary btn-sm">
    <i class="fas fa-file-signature me-1"></i> {{ __('Terbitkan Surat Tanda Terima') }}
</a>
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
                {{ __('Dokumen Berita Acara & Log Handover') }}
            </h6>
            <small class="text-muted">{{ __('Total transaksi serah terima:') }} <strong>{{ $handovers->count() }}</strong> {{ __('log tercatat') }}</small>
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
                        <th>{{ __('NO ASET LOCAL') }}</th>
                        <th>{{ __('KATEGORI') }}</th>
                        <th>{{ __('PENGGUNA / PENERIMA') }}</th>
                        <th>{{ __('LOKASI') }}</th>
                        <th>{{ __('CATATAN KONDISI') }}</th>
                        <th class="text-center">{{ __('DOKUMEN') }}</th>
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
                        <td class="font-monospace">{{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y, H:i') }}</td>
                        <td class="fw-bold text-dark font-monospace">{{ $row->kode_aset }}</td>
                        <td>{{ $row->kategori ?? '-' }}</td>
                        <td>
                            <span class="fw-semibold text-dark">{{ $row->pengguna_terakhir ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary border">
                                <i class="fas fa-location-dot me-1 text-primary"></i>{{ $row->lokasi ?? '-' }}
                            </span>
                        </td>
                        <td>{{ Str::limit($row->catatan ?? '-', 35) }}</td>
                        <td class="text-center text-nowrap">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('handover.receipt', $row->no_surat ?: $row->id) }}" target="_blank" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Preview') }}">
                                    <i class="fas fa-eye text-primary me-1"></i> {{ __('Preview') }}
                                </a>
                                <a href="{{ route('handover.receipt', $row->no_surat ?: $row->id) }}?print=1" target="_blank" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('PDF') }}">
                                    <i class="fas fa-print text-secondary"></i> {{ __('PDF') }}
                                </a>
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
    });
</script>
@endpush