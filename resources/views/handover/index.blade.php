@extends('layout')

@section('title', 'Riwayat Handover')

@section('content')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<style>
    .card-admin { background: #fff; border-radius: 4px; box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2); margin-bottom: 20px; border-top: 3px solid #0d6efd; }
    .card-header-admin { padding: 15px 20px; border-bottom: 1px solid rgba(0,0,0,.125); display: flex; justify-content: space-between; align-items: center; }
    .table-admin { margin-bottom: 0; font-size: 13.5px; width: 100% !important; }
    .table-admin thead th { border-bottom: 2px solid #dee2e6; color: #343a40; font-weight: 600; white-space: nowrap; background: #f8f9fa; padding: 12px 30px 12px 15px !important; }
    .table-admin tbody td { vertical-align: middle; border-bottom: 1px solid #dee2e6; color: #495057; white-space: nowrap; padding: 10px 15px !important; }
    .filter-label { font-size: 12px; font-weight: 700; color: #495057; text-transform: uppercase; margin-bottom: 8px; display: block; }
    .filter-input { font-size: 13.5px; padding: 8px 10px; border-radius: 4px; border: 1px solid #ced4da; width: 100%; background-color: #fff; }
</style>

<div class="container-fluid">
    <div class="card-admin">
        <div class="card-header-admin bg-white">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-history me-2 text-primary"></i>Global Handover History</h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="tableHandover" class="table table-admin table-hover table-striped">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>TANGGAL</th>
                            <th>NO ASET LOCAL</th>
                            <th>KATEGORI</th>
                            <th>PENGGUNA TERAKHIR</th>
                            <th>LOKASI</th>
                            <th>CATATAN KONDISI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($handovers as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y, H:i') }}</td>
                            <td class="fw-bold">{{ $row->kode_aset }}</td>
                            <td>{{ $row->kategori ?? '-' }}</td>
                            <td>{{ $row->pengguna_terakhir ?? '-' }}</td>
                            <td>{{ $row->lokasi ?? '-' }}</td>
                            <td>{{ $row->catatan ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
            "scrollX": true
        });
    });
</script>
@endpush