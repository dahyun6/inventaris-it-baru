@extends('layout')

@section('title', __('Edit Data Aset'))

@section('header_actions')
<a href="{{ route('barang.index') }}" class="btn btn-phoenix-secondary btn-sm">
    <i class="fas fa-arrow-left me-1"></i> {{ __('Kembali ke Data Aset') }}
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-10 col-xl-9 mx-auto">
        <div class="phoenix-card">
            <div class="phoenix-card-header">
                <h6 class="phoenix-card-title">
                    <i class="fas fa-pen-to-square text-primary"></i>
                    {{ __('Update Data Aset') }}
                </h6>
            </div>
            <form action="{{ route('barang.update', $barang->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="phoenix-card-body">
                    <div class="form-section-divider">1. {{ __('Klasifikasi & Identitas') }}</div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="no_aset_local">{{ __('No Aset Local') }}</label>
                            <input type="text" id="no_aset_local" name="no_aset_local" class="form-control font-monospace bg-light text-muted" readonly style="cursor: not-allowed;" value="{{ $barang->no_aset_local }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger" for="category_id">{{ __('Jenis / Kategori') }} *</label>
                            <select id="category_id" name="category_id" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $barang->category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger" for="model">{{ __('Model Perangkat') }} *</label>
                            <input type="text" id="model" name="model" class="form-control" required value="{{ $barang->model }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="serial_number">{{ __('Serial Number (SN)') }}</label>
                            <input type="text" id="serial_number" name="serial_number" class="form-control font-monospace" value="{{ $barang->serial_number }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="hostname">{{ __('Hostname PC/Laptop') }}</label>
                            <input type="text" id="hostname" name="hostname" class="form-control font-monospace" value="{{ $barang->hostname }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-danger" for="status">{{ __('Status Kondisi') }} *</label>
                            <select id="status" name="status" class="form-select" required>
                                <option value="Tersedia" {{ $barang->status == 'Tersedia' ? 'selected' : '' }}>{{ __('Tersedia') }}</option>
                                <option value="Dipinjam" {{ $barang->status == 'Dipinjam' ? 'selected' : '' }}>{{ __('Dipinjam') }}</option>
                                <option value="Rusak" {{ $barang->status == 'Rusak' ? 'selected' : '' }}>{{ __('Rusak') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold" for="type_spec">{{ __('Spesifikasi (Type Spec)') }}</label>
                        <textarea id="type_spec" name="type_spec" class="form-control" rows="2">{{ $barang->type_spec }}</textarea>
                    </div>

                    <div class="form-section-divider">2. {{ __('Distribusi & Pengguna') }}</div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="pengguna">{{ __('Nama Pengguna') }}</label>
                            <input type="text" id="pengguna" name="pengguna" class="form-control" value="{{ $barang->pengguna }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="position_user">{{ __('Jabatan (Position User)') }}</label>
                            <input type="text" id="position_user" name="position_user" class="form-control" value="{{ $barang->position_user }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="dept">{{ __('Departemen') }}</label>
                            <input type="text" id="dept" name="dept" class="form-control" value="{{ $barang->dept }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="unit_loc">{{ __('Lokasi Unit (Unit Loc)') }}</label>
                            <input type="text" id="unit_loc" name="unit_loc" class="form-control" value="{{ $barang->unit_loc }}">
                        </div>
                    </div>

                    <div class="form-section-divider">3. {{ __('Pembelian & Catatan') }}</div>
                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="buy_date">{{ __('Tanggal Beli (Buy Date)') }}</label>
                            <input type="date" id="buy_date" name="buy_date" class="form-control" value="{{ $barang->buy_date }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold" for="vendor">{{ __('Vendor / Toko') }}</label>
                            <select id="vendor" name="vendor" class="form-select">
                                <option value="">-- {{ __('Pilih Vendor') }} --</option>
                                @php $vendorMatched = false; @endphp
                                @foreach($vendors as $ven)
                                    @if($barang->vendor === $ven->nama_vendor)
                                        @php $vendorMatched = true; @endphp
                                    @endif
                                    <option value="{{ $ven->nama_vendor }}" {{ $barang->vendor === $ven->nama_vendor ? 'selected' : '' }}>
                                        {{ $ven->nama_vendor }}
                                    </option>
                                @endforeach
                                @if(!$vendorMatched && !empty($barang->vendor))
                                    <option value="{{ $barang->vendor }}" selected>{{ $barang->vendor }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold" for="note">{{ __('Catatan Internal (Note)') }}</label>
                        <textarea id="note" name="note" class="form-control" rows="2">{{ $barang->note }}</textarea>
                    </div>
                </div>

                <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                    <a href="{{ route('barang.index') }}" class="btn btn-phoenix-secondary btn-sm">{{ __('Batal') }}</a>
                    <button type="submit" class="btn btn-phoenix-primary btn-sm px-4">
                        <i class="fas fa-save me-1"></i> {{ __('Update Data') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection