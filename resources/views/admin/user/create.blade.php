@extends('layouts.app') {{-- Sesuaikan dengan nama file layout Anda (misal: layouts.master atau layouts.app) --}}

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Tambah User Baru</h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Form Data User</h3>
            </div>
            
            <form action="{{ route('users.store') }}" method="POST">
                @csrf <div class="card-body">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="Masukkan nama" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="Masukkan email" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Ketik ulang password" required>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan User</button>
                    <a href="{{ route('users.index') }}" class="btn btn-default float-right">Batal</a>
                </div>
            </form>
            </div>
    </div>
</section>
@endsection