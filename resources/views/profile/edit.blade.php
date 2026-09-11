@extends('layout')

@section('title', __('Account & Profile Settings'))

@section('header_actions')
<a href="{{ route('dashboard') }}" class="btn btn-phoenix-secondary btn-sm">
    <i class="fas fa-arrow-left me-1"></i> {{ __('Kembali ke Dashboard') }}
</a>
@endsection

@section('content')

<div class="row g-4">
    <!-- LEFT COLUMN: USER OVERVIEW & BADGE -->
    <div class="col-lg-4">
        <div class="phoenix-card mb-4">
            <div class="phoenix-card-header">
                <h6 class="phoenix-card-title">
                    <i class="fas fa-id-badge text-primary"></i>
                    {{ __('Identitas Pengguna') }}
                </h6>
                <span class="badge-phoenix badge-phoenix-success">Active</span>
            </div>
            <div class="phoenix-card-body text-center p-4">
                <div class="position-relative d-inline-block mb-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=3874ff&color=fff&bold=true&size=128" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle shadow-sm" width="80" height="80">
                    <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle p-2" title="Online"></span>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-3">{{ $user->email }}</p>

                <div class="d-inline-flex align-items-center gap-1 badge-phoenix badge-phoenix-primary mb-3">
                    <i class="fas fa-shield-halved"></i>
                    <span>{{ __('IT Asset Administrator') }}</span>
                </div>

                <div class="border-top pt-3 text-start">
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small font-semibold">{{ __('Status Akun') }}</span>
                        <span class="badge bg-success-subtle text-success fw-bold px-2 py-1" style="font-size: 0.725rem;">{{ __('Terverifikasi') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small font-semibold">{{ __('Role Akses') }}</span>
                        <span class="fw-bold text-dark small">{{ __('Super Administrator') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted small font-semibold">{{ __('Bahasa Pilihan') }}</span>
                        <span class="badge bg-light text-dark border fw-bold px-2 py-1" style="font-size: 0.725rem;">
                            {{ ($user->locale ?? 'id') === 'en' ? '🇺🇸 English' : '🇮🇩 Indonesia' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted small font-semibold">{{ __('Bergabung Sejak') }}</span>
                        <span class="font-monospace text-dark small">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Security Quick Tips -->
        <div class="phoenix-card">
            <div class="phoenix-card-header">
                <h6 class="phoenix-card-title" style="font-size: 0.85rem;">
                    <i class="fas fa-shield-check text-primary"></i>
                    {{ __('Tips Keamanan Akun') }}
                </h6>
            </div>
            <div class="phoenix-card-body p-3">
                <ul class="text-secondary small mb-0 ps-3 space-y-2" style="font-size: 0.8rem; line-height: 1.6;">
                    <li>Use a unique password with at least 8 characters.</li>
                    <li>Do not share your login credentials with others.</li>
                    <li>Always <strong>Log Out</strong> when finishing work on shared computers.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: SETTINGS FORMS -->
    <div class="col-lg-8">
        
        <!-- CARD 1: INFORMASI PROFIL -->
        <div class="phoenix-card mb-4">
            <div class="phoenix-card-header">
                <div>
                    <h6 class="phoenix-card-title">
                        <i class="fas fa-user-pen text-primary"></i>
                        {{ __('Informasi Profil Akun') }}
                    </h6>
                    <small class="text-muted">{{ __('Perbarui nama lengkap dan alamat email yang terdaftar') }}</small>
                </div>
            </div>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="phoenix-card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger" for="profile_name">{{ __('Nama Lengkap') }} *</label>
                        <input type="text" id="profile_name" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $user->name) }}" required autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback font-semibold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger" for="profile_email">{{ __('Alamat Email') }} *</label>
                        <input type="email" id="profile_email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email) }}" required autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback font-semibold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark" for="profile_locale">
                            <i class="fas fa-language text-primary me-1"></i> {{ __('Bahasa Pilihan') }}
                        </label>
                        <select name="locale" id="profile_locale" class="form-select @error('locale') is-invalid @enderror">
                            <option value="id" {{ old('locale', $user->locale ?? 'id') === 'id' ? 'selected' : '' }}>🇮🇩 Bahasa Indonesia (ID)</option>
                            <option value="en" {{ old('locale', $user->locale ?? 'id') === 'en' ? 'selected' : '' }}>🇺🇸 English (EN)</option>
                        </select>
                        @error('locale')
                            <div class="invalid-feedback font-semibold">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="p-3 bg-light border-top d-flex justify-content-end">
                    <button type="submit" class="btn btn-phoenix-primary btn-sm px-4">
                        <i class="fas fa-save me-1"></i> {{ __('Simpan Perubahan Profil') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- CARD 2: GANTI PASSWORD -->
        <div class="phoenix-card mb-4">
            <div class="phoenix-card-header">
                <div>
                    <h6 class="phoenix-card-title">
                        <i class="fas fa-key text-primary"></i>
                        {{ __('Perbarui Kata Sandi') }}
                    </h6>
                    <small class="text-muted">{{ __('Pastikan akun menggunakan kombinasi sandi yang kuat dan aman') }}</small>
                </div>
            </div>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="phoenix-card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-danger" for="current_password">{{ __('Kata Sandi Saat Ini') }} *</label>
                        <input type="password" id="current_password" name="current_password" 
                               class="form-control @if($errors->updatePassword->has('current_password')) is-invalid @endif" 
                               placeholder="******" autocomplete="current-password">
                        @if($errors->updatePassword->has('current_password'))
                            <div class="invalid-feedback font-semibold">{{ $errors->updatePassword->first('current_password') }}</div>
                        @endif
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger" for="password">{{ __('Kata Sandi Baru') }} *</label>
                            <input type="password" id="password" name="password" 
                                   class="form-control @if($errors->updatePassword->has('password')) is-invalid @endif" 
                                   placeholder="Min. 8 chars" autocomplete="new-password">
                            @if($errors->updatePassword->has('password'))
                                <div class="invalid-feedback font-semibold">{{ $errors->updatePassword->first('password') }}</div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger" for="password_confirmation">{{ __('Konfirmasi Sandi Baru') }} *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" 
                                   class="form-control @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif" 
                                   placeholder="Re-type password" autocomplete="new-password">
                            @if($errors->updatePassword->has('password_confirmation'))
                                <div class="invalid-feedback font-semibold">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-light border-top d-flex justify-content-end">
                    <button type="submit" class="btn btn-phoenix-primary btn-sm px-4">
                        <i class="fas fa-lock me-1"></i> {{ __('Perbarui Kata Sandi') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- CARD 3: DANGER ZONE / HAPUS AKUN -->
        <div class="phoenix-card border-danger-subtle">
            <div class="phoenix-card-header bg-danger-subtle text-danger">
                <div>
                    <h6 class="phoenix-card-title text-danger">
                        <i class="fas fa-triangle-exclamation"></i>
                        {{ __('Zona Berbahaya: Hapus Akun') }}
                    </h6>
                    <small style="color: #b91c1c;">{{ __('Tindakan ini permanen dan tidak dapat dibatalkan') }}</small>
                </div>
            </div>
            <div class="phoenix-card-body">
                <p class="text-secondary small mb-3" style="line-height: 1.6;">
                    {{ __('Apakah Anda yakin ingin menghapus akun ini secara permanen?') }}
                </p>
                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalDeleteAccountConfirm">
                    <i class="fas fa-trash-can me-1"></i> {{ __('Hapus Akun Saya') }}
                </button>
            </div>
        </div>

    </div>
</div>

<!-- MODAL KONFIRMASI HAPUS AKUN -->
<div class="modal fade" id="modalDeleteAccountConfirm" tabindex="-1" aria-labelledby="modalDeleteAccountConfirmLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; border: 1px solid var(--phoenix-border-color);">
            <div class="modal-header border-bottom bg-danger text-white">
                <h6 class="modal-title fw-bold" id="modalDeleteAccountConfirmLabel">
                    <i class="fas fa-triangle-exclamation me-2"></i>{{ __('Konfirmasi Hapus Akun') }}
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')

                <div class="modal-body">
                    <p class="text-dark small fw-bold mb-2">
                        {{ __('Apakah Anda yakin ingin menghapus akun ini secara permanen?') }}
                    </p>
                    <p class="text-muted small mb-3">
                        Silakan masukkan kata sandi akun Anda untuk mengonfirmasi.
                    </p>

                    <div class="mb-2">
                        <label class="form-label small fw-bold" for="delete_password">{{ __('Kata Sandi Anda') }}</label>
                        <input type="password" id="delete_password" name="password" 
                               class="form-control form-control-sm @if($errors->userDeletion->has('password')) is-invalid @endif" 
                               placeholder="******" required>
                        @if($errors->userDeletion->has('password'))
                            <div class="invalid-feedback font-semibold">{{ $errors->userDeletion->first('password') }}</div>
                        @endif
                    </div>
                </div>

                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-phoenix-secondary btn-sm" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                    <button type="submit" class="btn btn-danger btn-sm px-3">
                        <i class="fas fa-trash-can me-1"></i> {{ __('Ya, Hapus Akun Permanen') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->userDeletion->isNotEmpty())
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var deleteModal = new bootstrap.Modal(document.getElementById('modalDeleteAccountConfirm'));
        deleteModal.show();
    });
</script>
@endpush
@endif

@endsection
