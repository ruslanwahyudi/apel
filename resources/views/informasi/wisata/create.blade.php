@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Tambah Wisata</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item">Wisata</li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fa fa-plus"></i> Tambah Wisata</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('info.wisata.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama">Nama Wisata <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="lokasi">Lokasi <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('lokasi') is-invalid @enderror" id="lokasi" name="lokasi" rows="3" required>{{ old('lokasi') }}</textarea>
                                    @error('lokasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="4" required>{{ old('deskripsi') }}</textarea>
                                    @error('deskripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="fasilitas">Fasilitas</label>
                                    <textarea class="form-control @error('fasilitas') is-invalid @enderror" id="fasilitas" name="fasilitas" rows="3">{{ old('fasilitas') }}</textarea>
                                    @error('fasilitas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jam_buka">Jam Buka</label>
                                    <input type="time" class="form-control @error('jam_buka') is-invalid @enderror" id="jam_buka" name="jam_buka" value="{{ old('jam_buka') }}">
                                    @error('jam_buka')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="jam_tutup">Jam Tutup</label>
                                    <input type="time" class="form-control @error('jam_tutup') is-invalid @enderror" id="jam_tutup" name="jam_tutup" value="{{ old('jam_tutup') }}">
                                    @error('jam_tutup')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="harga_tiket">Harga Tiket</label>
                                    <input type="number" step="0.01" class="form-control @error('harga_tiket') is-invalid @enderror" id="harga_tiket" name="harga_tiket" value="{{ old('harga_tiket') }}">
                                    @error('harga_tiket')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="kontak">Kontak</label>
                                    <input type="text" class="form-control @error('kontak') is-invalid @enderror" id="kontak" name="kontak" value="{{ old('kontak') }}">
                                    @error('kontak')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="petunjuk_arah">Petunjuk Arah</label>
                                    <textarea class="form-control @error('petunjuk_arah') is-invalid @enderror" id="petunjuk_arah" name="petunjuk_arah" rows="3">{{ old('petunjuk_arah') }}</textarea>
                                    @error('petunjuk_arah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="gambar">Gambar Wisata</label>
                                    <input type="file" class="form-control-file @error('gambar') is-invalid @enderror" id="gambar" name="gambar">
                                    <small class="form-text text-muted">Format: JPG, PNG. Ukuran maksimal: 2MB</small>
                                    @error('gambar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="status">Aktif</label>
                                    </div>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('info.wisata') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('deskripsi');
    CKEDITOR.replace('fasilitas');
    CKEDITOR.replace('petunjuk_arah');
</script>
@endsection 