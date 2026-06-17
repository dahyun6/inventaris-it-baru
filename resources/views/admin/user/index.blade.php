@extends('layout')

@section('title', 'Master User')

@section('content')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    /* CSS persis seperti Master Aset */
    .card-admin { background: #fff; border-radius: 4px; box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2); margin-bottom: 20px; border-top: 3px solid var(--primary-blue); }
    .card-header-admin { padding: 15px 20px; border-bottom: 1px solid rgba(0,0,0,.125); display: flex; justify-content: space-between; align-items: center; }
    .table-admin { margin-bottom: 0; font-size: 13.5px; width: 100% !important; }
    .table-admin thead th { border-bottom: 2px solid #dee2e6; color: #343a40; font-weight: 600; white-space: nowrap; background: #f8f9fa; padding: 12px 30px 12px 15px !important; }
    .table-admin tbody td { vertical-align: middle; border-bottom: 1px solid #dee2e6; color: #495057; white-space: nowrap; padding: 10px 15px !important; }
    
    table.dataTable thead th.sorting:before, table.dataTable thead th.sorting:after,
    table.dataTable thead th.sorting_asc:before, table.dataTable thead th.sorting_asc:after,
    table.dataTable thead th.sorting_desc:before, table.dataTable thead th.sorting_desc:after { right: 8px !important; bottom: 12px !important; }
    
    .form-section-title { font-size: 13px; font-weight: 700; color: #0d6efd; text-transform: uppercase; margin-top: 15px; margin-bottom: 10px; border-bottom: 1px solid #dee2e6; padding-bottom: 5px; }
    
    div.dataTables_wrapper div.dataTables_length select { width: 70px; display: inline-block; padding: 4px 8px; }
    
    @media (max-width: 768px) {
        div.dataTables_wrapper div.dataTables_filter { text-align: left !important; margin-top: 10px; }
        div.dataTables_wrapper div.dataTables_filter label { display: flex; align-items: center; width: 100%; }
        div.dataTables_wrapper div.dataTables_filter input { flex: 1; margin-left: 10px !important; width: 100% !important; }
    }
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card-admin" style="border-top: 3px solid #0d6efd;">
    <div class="card-header-admin bg-white d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-users me-2 text-primary"></i>Master Data User</h5>
        </div>
        <div class="dt-buttons d-flex gap-2">
            <button class="btn btn-primary btn-sm m-0" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                <i class="fas fa-user-plus me-1"></i> Tambah User Baru
            </button>
        </div>
    </div>

    <div class="card-body p-4">
        <div class="table-responsive">
            <table id="tableUser" class="table table-admin table-hover table-striped w-100">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">NO</th>
                        <th>NAMA LENGKAP</th>
                        <th>ALAMAT EMAIL</th>
                        <th>TANGGAL TERDAFTAR</th>
                        <th width="15%" class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td class="font-monospace">{{ $user->created_at->format('d M Y, H:i') }}</td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <button type="button" class="btn btn-xs btn-light text-warning" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $user->id }}" title="Edit Data"><i class="fas fa-edit"></i></button>
                                
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini secara permanen?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-light text-danger" title="Hapus Data"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEdit{{ $user->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content" style="border-radius: 4px;">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title fs-6 fw-bold">Edit Data User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('users.update', $user->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body bg-light pt-0">
                                        <div class="form-section-title">Informasi Akun</div>
                                        <div class="mb-3">
                                            <label class="form-label text-danger">Nama Lengkap *</label>
                                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-danger">Email *</label>
                                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                        </div>
                                        <hr class="my-3 text-muted">
<div class="form-section-title">Ubah Password (Opsional)</div>
<div class="alert alert-info p-2 text-center" style="font-size: 12px;">
    <i class="fas fa-info-circle"></i> Kosongkan kedua kolom di bawah ini jika tidak ingin mengubah password user.
</div>
<div class="mb-3">
    <label class="form-label">Password Baru</label>
    <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter">
</div>
<div class="mb-3">
    <label class="form-label">Konfirmasi Password Baru</label>
    <input type="password" name="password_confirmation" class="form-control" placeholder="Ketik ulang password baru">
</div>
                                    </div>
                                    <div class="modal-footer bg-white">
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-warning px-4"><i class="fas fa-save me-1"></i> Update Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 4px;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold"><i class="fas fa-user-plus me-2"></i>Entry User Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body bg-light pt-0">
                    <div class="form-section-title">Identitas & Akses Login</div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-danger">Nama Lengkap *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Masukkan nama" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-danger">Alamat Email *</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Masukkan email" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label text-danger">Password *</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-danger">Konfirmasi Password *</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ketik ulang password" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan User</button>
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

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables untuk User (Tanpa scroll horizontal ekstrim karena kolom sedikit)
        $('#tableUser').DataTable({
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "pageLength": 10,
            "dom": "<'row mb-2'<'col-12 col-md-6'l><'col-12 col-md-6 d-flex justify-content-md-end justify-content-start mt-2 mt-md-0'f>>" +
                   "<'row'<'col-12'tr>>" +
                   "<'row mt-2'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
            "language": {
                "search": "🔍 Pencarian Cepat:",
                "lengthMenu": "Tampilkan _MENU_ baris",
                "info": "Menampilkan _START_ s/d _END_ dari _TOTAL_ user",
                "infoEmpty": "Tidak ada data user",
                "infoFiltered": "(disaring dari total _MAX_ user)",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "Maju <i class='fas fa-angle-right ms-1'></i>",
                    "previous": "<i class='fas fa-angle-left me-1'></i> Mundur"
                }
            }
        });
    });
</script>
@endpush