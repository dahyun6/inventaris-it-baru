@extends('layout')

@section('title', 'Tambah User Baru')

@section('header_actions')
<a href="{{ route('users.index') }}" class="btn btn-phoenix-secondary btn-sm">
    <i class="fas fa-arrow-left me-1"></i> Kembali ke Master User
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 col-xl-7 mx-auto">
        <div class="phoenix-card">
            <div class="phoenix-card-header">
                <h6 class="phoenix-card-title">
                    <i class="fas fa-user-plus text-primary"></i>
                    Form Registrasi User Baru
                </h6>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="phoenix-card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger" for="name">Nama Lengkap *</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Masukkan nama pengguna" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger" for="email">Alamat Email *</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="contoh: user@company.com" value="{{ old('email') }}" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger" for="password">Password *</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Minimal 8 karakter" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger" for="password_confirmation">Konfirmasi Password *</label>
                            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Ketik ulang password" required>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                    <a href="{{ route('users.index') }}" class="btn btn-phoenix-secondary btn-sm">Batal</a>
                    <button type="submit" class="btn btn-phoenix-primary btn-sm px-4">
                        <i class="fas fa-save me-1"></i> Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection