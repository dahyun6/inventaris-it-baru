@extends('layout')

@section('title', 'Dashboard')

@section('content')

<style>
    /* =========================================================
       STYLE KHUSUS WIDGET ADMINLTE (Small Boxes)
       ========================================================= */
    .small-box {
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,.125), 0 1px 2px rgba(0,0,0,.2);
        display: block;
        margin-bottom: 20px;
        position: relative;
        color: #fff;
        overflow: hidden;
    }
    .small-box > .inner {
        padding: 15px;
        position: relative;
        z-index: 2;
    }
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0 0 5px;
        white-space: nowrap;
        padding: 0;
    }
    .small-box p {
        font-size: 1.05rem;
        margin-bottom: 0;
    }
    .small-box .icon {
        color: rgba(0,0,0,.15);
        z-index: 1;
        position: absolute;
        right: 15px;
        top: 15px;
        transition: transform .3s linear;
    }
    .small-box .icon i {
        font-size: 70px;
    }
    .small-box:hover .icon {
        transform: scale(1.1);
    }
    .small-box > .small-box-footer {
        background-color: rgba(0,0,0,.1);
        color: rgba(255,255,255,.8);
        display: block;
        padding: 3px 0;
        position: relative;
        text-align: center;
        text-decoration: none;
        z-index: 10;
        font-size: 14px;
    }
    .small-box > .small-box-footer:hover {
        color: #fff;
        background-color: rgba(0,0,0,.15);
    }
    
    /* Warna Solid AdminLTE */
    .bg-info-admin { background-color: #0d6efd !important; } /* Biru */
    .bg-success-admin { background-color: #198754 !important; } /* Hijau */
    .bg-warning-admin { background-color: #ffc107 !important; color: #212529 !important; } /* Kuning */
    .bg-warning-admin .icon { color: rgba(0,0,0,.1) !important; }
    .bg-warning-admin .small-box-footer { color: rgba(0,0,0,.6) !important; }
    .bg-warning-admin .small-box-footer:hover { color: #000 !important; background-color: rgba(0,0,0,.1) !important; }
    .bg-danger-admin { background-color: #dc3545 !important; } /* Merah */

    /* Card Umum AdminLTE */
    .card-admin { background: #fff; border-radius: 4px; box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2); margin-bottom: 20px; }
    .card-header-admin { padding: 12px 20px; border-bottom: 1px solid rgba(0,0,0,.125); display: flex; justify-content: space-between; align-items: center; font-weight: 500; }
    
    /* Style Chat Widget (Meniru UI Direct Chat) */
    .direct-chat-messages { padding: 10px; height: 350px; overflow-y: auto; }
    .direct-chat-msg { margin-bottom: 15px; }
    .direct-chat-info { display: block; margin-bottom: 2px; font-size: 12px; }
    .direct-chat-name { font-weight: 600; }
    .direct-chat-timestamp { color: #697582; }
    .direct-chat-img { border-radius: 50%; float: left; height: 40px; width: 40px; }
    .direct-chat-text { border-radius: 4px; background: #d2d6de; border: 1px solid #d2d6de; color: #444; margin: 5px 0 0 50px; padding: 5px 10px; position: relative; }
    .direct-chat-text::after { border-color: transparent #d2d6de transparent transparent; border-width: 6px; content: " "; position: absolute; top: 15px; left: -12px; height: 0; width: 0; pointer-events: none; border-style: solid; }
</style>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info-admin">
            <div class="inner">
                <h3>{{ $total_aset }}</h3>
                <p>Total Aset Terdaftar</p>
            </div>
            <div class="icon"><i class="fas fa-boxes"></i></div>
            <a href="{{ route('barang.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right ms-1"></i></a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success-admin">
            <div class="inner">
                <h3>{{ $aset_tersedia }}</h3>
                <p>Aset Tersedia (Gudang)</p>
            </div>
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <a href="{{ route('barang.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right ms-1"></i></a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning-admin">
            <div class="inner">
                <h3>{{ $aset_dipinjam }}</h3>
                <p>Aset Sedang Dipinjam</p>
            </div>
            <div class="icon"><i class="fas fa-handshake"></i></div>
            <a href="{{ route('barang.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right ms-1"></i></a>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger-admin">
            <div class="inner">
                <h3>{{ $aset_rusak }}</h3>
                <p>Aset Rusak / Suspended</p>
            </div>
            <div class="icon"><i class="fas fa-tools"></i></div>
            <a href="{{ route('barang.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right ms-1"></i></a>
        </div>
    </div>
</div>

<div class="row">
    
    <div class="col-lg-7">
        <div class="card-admin">
            <div class="card-header-admin">
                <span><i class="fas fa-chart-line me-2"></i> Trend Penambahan Aset (Tahun Ini)</span>
            </div>
            <div class="card-body">
                <canvas id="assetTrendChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-admin">
            <div class="card-header-admin">
                <span><i class="far fa-comments me-2"></i> Log Handover Terbaru</span>
                <span class="badge bg-primary rounded-pill">{{ $recent_handovers->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="direct-chat-messages">
                    
                    @forelse($recent_handovers as $log)
                    <div class="direct-chat-msg">
                        <div class="direct-chat-info clearfix">
                            <span class="direct-chat-name float-start">{{ $log->user ? $log->user->name : 'Gudang IT' }}</span>
                            <span class="direct-chat-timestamp float-end">{{ \Carbon\Carbon::parse($log->tanggal_serah_terima)->format('d M h:i a') }}</span>
                        </div>
                        <img class="direct-chat-img" src="https://ui-avatars.com/api/?name={{ urlencode($log->user ? $log->user->name : 'Gudang') }}&background=random" alt="User Image">
                        <div class="direct-chat-text">
                            <strong>{{ $log->barang->nama_barang }}</strong> diserahterimakan. <br>
                            <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> {{ $log->lokasi }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted mt-5">
                        <i class="fas fa-sleep fs-2 opacity-50 mb-2"></i><br>
                        Belum ada aktivitas serah terima.
                    </div>
                    @endforelse

                </div>
            </div>
            <div class="card-footer bg-light border-0 p-2 text-center">
                <a href="{{ route('barang.index') }}" class="text-decoration-none text-muted small">Lihat Semua Aset</a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Konfigurasi Area Chart (Smooth Line Chart) meniru "Sales Value"
        const ctx = document.getElementById('assetTrendChart').getContext('2d');
        
        // Gradient Warna untuk Area Bawah Grafik
        let gradientBlue = ctx.createLinearGradient(0, 0, 0, 400);
        gradientBlue.addColorStop(0, 'rgba(13, 110, 253, 0.5)'); // Biru transparan
        gradientBlue.addColorStop(1, 'rgba(13, 110, 253, 0.05)');

        let gradientGreen = ctx.createLinearGradient(0, 0, 0, 400);
        gradientGreen.addColorStop(0, 'rgba(40, 167, 69, 0.5)'); // Hijau transparan
        gradientGreen.addColorStop(1, 'rgba(40, 167, 69, 0.05)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels_grafik) !!},
                datasets: [
                    {
                        label: 'Total Aset Masuk',
                        data: {!! json_encode($data_aset_masuk) !!},
                        borderColor: '#0d6efd',
                        backgroundColor: gradientBlue,
                        borderWidth: 3,
                        pointBackgroundColor: '#0d6efd',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#0d6efd',
                        fill: true,
                        tension: 0.4 // Membuat garis melengkung halus (smooth)
                    },
                    {
                        label: 'Aset Selesai Diperbaiki',
                        data: {!! json_encode($data_aset_diperbaiki) !!},
                        borderColor: '#28a745',
                        backgroundColor: gradientGreen,
                        borderWidth: 3,
                        pointBackgroundColor: '#28a745',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#28a745',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { boxWidth: 10, usePointStyle: true }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#e9ecef' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endpush