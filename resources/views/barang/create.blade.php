@extends('layout')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Tambah Barang Baru</h5>
    </div>
    <div class="card-body">
        <!-- Fitur Keamanan: Tampilkan pesan error jika validasi gagal -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('barang.store') }}" method="POST">
            <!-- INI SANGAT PENTING: Kunci keamanan token CSRF Laravel -->
            @csrf 

            <div class="mb-3">
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" required value="{{ old('nama_barang') }}">
            </div>
            <div class="mb-3">
                <label>Kategori (Misal: Laptop, Monitor, Jaringan)</label>
                <input type="text" name="kategori" class="form-control" required value="{{ old('kategori') }}">
            </div>
            <div class="mb-3">
                <label>Serial Number (Harus Unik)</label>
                <input type="text" name="serial_number" class="form-control" required value="{{ old('serial_number') }}">
            </div>
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="Tersedia">Tersedia</option>
                    <option value="Dipinjam">Dipinjam</option>
                    <option value="Rusak">Rusak</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Barang Aman</button>
            <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection