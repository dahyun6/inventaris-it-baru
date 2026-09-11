@extends('layout')

@section('title', __('Master User Management'))

@section('header_actions')
<button class="btn btn-phoenix-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
    <i class="fas fa-user-plus me-1"></i> {{ __('Tambah User Baru') }}
</button>
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
    .user-avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: var(--phoenix-primary-subtle);
        color: var(--phoenix-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
    }
    .form-section-divider {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--phoenix-primary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 1.25rem;
        margin-bottom: 0.75rem;
        padding-bottom: 0.35rem;
        border-bottom: 1px solid var(--phoenix-border-color);
    }
    .form-section-divider:first-child,
    .form-section-divider:first-of-type {
        margin-top: 0;
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
                <i class="fas fa-users-gear text-primary"></i>
                {{ __('Daftar Akun Pengguna & Administrator') }}
            </h6>
            <small class="text-muted">{{ __('Total terdaftar:') }} <strong>{{ $users->count() }}</strong> {{ __('User Management') }}</small>
        </div>
        <div>
            <span class="badge-phoenix badge-phoenix-primary font-monospace">{{ $users->count() }} Active</span>
        </div>
    </div>

    <div class="phoenix-card-body p-0">
        <div class="table-responsive p-3">
            <table id="tableUser" class="table table-phoenix w-100">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">{{ __('NO') }}</th>
                        <th>{{ __('NAMA LENGKAP') }}</th>
                        <th>{{ __('ALAMAT EMAIL') }}</th>
                        <th>{{ __('TANGGAL TERDAFTAR') }}</th>
                        <th width="15%" class="text-center">{{ __('AKSI') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                    <tr>
                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span class="fw-bold text-dark">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-secondary"><i class="far fa-envelope me-1 text-muted"></i>{{ $user->email }}</span>
                        </td>
                        <td class="font-monospace text-muted">{{ $user->created_at->format('d M Y, H:i') }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-phoenix-secondary py-1 px-2 btn-edit-user" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEditUser" 
                                    data-url="{{ route('users.update', $user->id) }}"
                                    data-name="{{ $user->name }}"
                                    data-email="{{ $user->email }}"
                                    title="{{ __('Edit Data') }}">
                                    <i class="fas fa-pen text-warning"></i>
                                </button>
                                
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Hapus user ini secara permanen?') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-phoenix-secondary py-1 px-2" title="{{ __('Hapus Data') }}">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL EDIT USER -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-labelledby="modalEditUserLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; border: 1px solid var(--phoenix-border-color);">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold" id="modalEditUserLabel"><i class="fas fa-user-pen text-primary me-2"></i>{{ __('Edit Data User') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditUser" action="" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-section-divider">{{ __('Informasi Akun') }}</div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger" for="edit_user_name">{{ __('Nama Lengkap') }} *</label>
                        <input type="text" id="edit_user_name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger" for="edit_user_email">{{ __('Alamat Email') }} *</label>
                        <input type="email" id="edit_user_email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-section-divider">{{ __('Ubah Password (Opsional)') }}</div>
                    <div class="alert alert-info border-0 p-2 text-center" style="font-size: 0.75rem; background-color: var(--phoenix-info-subtle); color: #0369a1; border-radius: 8px;">
                        <i class="fas fa-info-circle me-1"></i> Kosongkan jika tidak ingin mengubah password pengguna.
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold" for="edit_user_password">{{ __('Password Baru') }}</label>
                        <input type="password" id="edit_user_password" name="password" class="form-control" placeholder="Minimal 8 karakter">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold" for="edit_user_password_confirmation">{{ __('Konfirmasi Password Baru') }}</label>
                        <input type="password" id="edit_user_password_confirmation" name="password_confirmation" class="form-control" placeholder="Ketik ulang password baru">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-phoenix-secondary btn-sm" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-phoenix-primary btn-sm px-4"><i class="fas fa-save me-1"></i> {{ __('Update Data') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH USER -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: 1px solid var(--phoenix-border-color);">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold" id="modalTambahUserLabel"><i class="fas fa-user-plus text-primary me-2"></i>{{ __('Entry User Baru') }}</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-section-divider">{{ __('Identitas & Akses Login') }}</div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger" for="add_user_name">{{ __('Nama Lengkap') }} *</label>
                            <input type="text" id="add_user_name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger" for="add_user_email">{{ __('Alamat Email') }} *</label>
                            <input type="email" id="add_user_email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Email" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger" for="add_user_password">{{ __('Password Baru') }} *</label>
                            <input type="password" id="add_user_password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger" for="add_user_password_confirmation">{{ __('Konfirmasi Password Baru') }} *</label>
                            <input type="password" id="add_user_password_confirmation" name="password_confirmation" class="form-control" placeholder="Ketik ulang password" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-phoenix-secondary btn-sm" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-phoenix-primary btn-sm px-4"><i class="fas fa-save me-1"></i> {{ __('Simpan User') }}</button>
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
        $(document).on('click', '.btn-edit-user', function() {
            const btn = $(this);
            $('#formEditUser').attr('action', btn.data('url'));
            $('#edit_user_name').val(btn.data('name') || '');
            $('#edit_user_email').val(btn.data('email') || '');
            $('#edit_user_password').val('');
            $('#edit_user_password_confirmation').val('');
        });

        $('#tableUser').DataTable({
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "{{ __('Semua') }}"]],
            "pageLength": 10,
            "dom": "<'row mb-3 align-items-center'<'col-12 col-md-6'l><'col-12 col-md-6 d-flex justify-content-md-end justify-content-start'f>>" +
                   "<'row'<'col-12'tr>>" +
                   "<'row mt-3 align-items-center'<'col-12 col-md-5'i><'col-12 col-md-7'p>>",
            "language": {
                "search": "{{ __('Quick search...') }}",
                "lengthMenu": "{{ __('Tampilkan _MENU_ baris') }}",
                "info": "{{ __('Menampilkan _START_ - _END_ dari _TOTAL_ user') }}",
                "infoEmpty": "{{ __('Tidak ada data user') }}",
                "infoFiltered": "({{ __('disaring dari total _MAX_ user') }})",
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