@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Edit Profil Desa</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item">Profil Desa</li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fa fa-edit"></i> Edit Profil Desa</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('info.profil.update', $profil->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_desa">Nama Desa <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_desa') is-invalid @enderror" id="nama_desa" name="nama_desa" value="{{ old('nama_desa', $profil->nama_desa) }}" required>
                                    @error('nama_desa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="kode_desa">Kode Desa <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('kode_desa') is-invalid @enderror" id="kode_desa" name="kode_desa" value="{{ old('kode_desa', $profil->kode_desa) }}" required>
                                    @error('kode_desa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="kecamatan">Kecamatan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('kecamatan') is-invalid @enderror" id="kecamatan" name="kecamatan" value="{{ old('kecamatan', $profil->kecamatan) }}" required>
                                    @error('kecamatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="kabupaten">Kabupaten <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('kabupaten') is-invalid @enderror" id="kabupaten" name="kabupaten" value="{{ old('kabupaten', $profil->kabupaten) }}" required>
                                    @error('kabupaten')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="provinsi">Provinsi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi" value="{{ old('provinsi', $profil->provinsi) }}" required>
                                    @error('provinsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="kode_pos">Kode Pos <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('kode_pos') is-invalid @enderror" id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $profil->kode_pos) }}" required>
                                    @error('kode_pos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="telepon">Telepon</label>
                                    <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon" name="telepon" value="{{ old('telepon', $profil->telepon) }}">
                                    @error('telepon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $profil->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="alamat">Alamat <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" required>{{ old('alamat', $profil->alamat) }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="sejarah">Sejarah</label>
                                    <textarea class="form-control @error('sejarah') is-invalid @enderror" id="sejarah" name="sejarah" rows="4">{{ old('sejarah', $profil->sejarah) }}</textarea>
                                    @error('sejarah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="visi">Visi</label>
                                    <textarea class="form-control @error('visi') is-invalid @enderror" id="visi" name="visi" rows="3">{{ old('visi', $profil->visi) }}</textarea>
                                    @error('visi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="misi">Misi</label>
                                    <textarea class="form-control @error('misi') is-invalid @enderror" id="misi" name="misi" rows="4">{{ old('misi', $profil->misi) }}</textarea>
                                    @error('misi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    @if($profil->logo)
                                        <div class="mb-2">
                                            <img src="{{ asset($profil->logo) }}" alt="Logo" class="img-thumbnail" style="max-height: 100px">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control-file @error('logo') is-invalid @enderror" id="logo" name="logo">
                                    <small class="form-text text-muted">Format: JPG, PNG. Ukuran maksimal: 2MB. Biarkan kosong jika tidak ingin mengubah logo.</small>
                                    @error('logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="foto_kantor">Foto Kantor</label>
                                    @if($profil->foto_kantor)
                                        <div class="mb-2">
                                            <img src="{{ asset($profil->foto_kantor) }}" alt="Foto Kantor" class="img-thumbnail" style="max-height: 100px">
                                        </div>
                                    @endif
                                    <input type="file" class="form-control-file @error('foto_kantor') is-invalid @enderror" id="foto_kantor" name="foto_kantor">
                                    <small class="form-text text-muted">Format: JPG, PNG. Ukuran maksimal: 2MB. Biarkan kosong jika tidak ingin mengubah foto kantor.</small>
                                    @error('foto_kantor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('info.profil') }}" class="btn btn-secondary">Kembali</a>
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
    CKEDITOR.replace('sejarah');
    CKEDITOR.replace('visi');
    CKEDITOR.replace('misi');
</script>
@endsection 