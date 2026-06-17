@extends('layout')

@section('title', 'Detail & Riwayat Aset')

@section('content')
<style>
    .card-admin { background: #fff; border-radius: 4px; box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2); margin-bottom: 20px; border-top: 3px solid var(--primary-blue); }
    .card-header-admin { padding: 15px 20px; border-bottom: 1px solid rgba(0,0,0,.125); font-weight: 600; color: #343a40; }
    
    .badge-admin { padding: 5px 10px; font-weight: 500; font-size: 12px; border-radius: 4px; }
    .badge-active { background-color: #28a745; color: white; }
    .badge-invited { background-color: #17a2b8; color: white; }
    .badge-suspended { background-color: #6c757d; color: white; }
    
    .table-admin thead th { border-bottom: 2px solid #dee2e6; color: #343a40; font-weight: 600; font-size: 14px; }
    .table-admin tbody td { vertical-align: middle; border-bottom: 1px solid #dee2e6; color: #495057; font-size: 14px; }
</style>

<div class="mb-3">
    <a href="{{ route('barang.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Kembali ke Data Aset</a>
    
    <a href="{{ route('barang.handover', $barang->uuid) }}" class="btn btn-info btn-sm text-white ms-2"><i class="fas fa-exchange-alt me-1"></i> Form Handover</a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card-admin">
            <div class="card-header-admin">Informasi Perangkat</div>
            <div class="card-body p-4 text-center">
                <div class="display-4 text-primary mb-3"><i class="fas fa-microchip"></i></div>
                <h5 class="fw-bold mb-1">{{ $barang->nama_barang }}</h5>
                <p class="text-muted font-monospace mb-4">{{ $barang->serial_number }}</p>
                
                <ul class="list-group list-group-flush text-start">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Kategori</span>
                        <span class="fw-semibold">{{ $barang->category->nama_kategori ?? '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Status Saat Ini</span>
                        @if($barang->status == 'Tersedia')
                            <span class="badge-admin badge-active">Active</span>
                        @elseif($barang->status == 'Dipinjam')
                            <span class="badge-admin badge-invited">Invited</span>
                        @else
                            <span class="badge-admin badge-suspended">Suspended</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted">Tanggal Input</span>
                        <span class="fw-semibold">{{ $barang->created_at->format('d M Y') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card-admin h-100">
            <div class="card-header-admin"><i class="fas fa-history me-2"></i> Log Serah Terima (Handover)</div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-admin table-hover mb-0">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th class="ps-3">Tanggal</th>
                            <th>Pengguna Terakhir</th>
                            <th>Lokasi</th>
                            <th>Catatan Kondisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barang->riwayat as $log)
                        <tr>
                            <td class="ps-3 text-nowrap">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}</td>
                            <td>
                                @if($log->user)
                                    <span class="fw-semibold text-primary"><i class="fas fa-user-circle me-1"></i> {{ $log->user->name }}</span>
                                @else
                                    <span class="fw-semibold text-success"><i class="fas fa-warehouse me-1"></i> Gudang IT</span>
                                @endif
                            </td>
                            <td>{{ $log->lokasi }}</td>
                            <td class="text-muted small">{{ $log->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fs-2 mb-3 opacity-50"></i><br>
                                Belum ada riwayat pergerakan untuk aset ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection