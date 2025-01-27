@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Edit Daftar Layanan</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item">Layanan</li>
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
                    <h3><i class="fa fa-edit"></i> Edit Daftar Layanan</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('layanan.daftar.update', $daftar->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama">Nama Pemohon <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $daftar->nama) }}" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="nik">NIK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik', $daftar->nik) }}" required>
                                    @error('nik')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="jenis_layanan_id">Jenis Layanan <span class="text-danger">*</span></label>
                                    <select class="form-control @error('jenis_layanan_id') is-invalid @enderror" id="jenis_layanan_id" name="jenis_layanan_id" required>
                                        <option value="">Pilih Jenis Layanan</option>
                                        @foreach($jenisLayanan as $jenis)
                                            <option value="{{ $jenis->id }}" {{ old('jenis_layanan_id', $daftar->jenis_layanan_id) == $jenis->id ? 'selected' : '' }}>{{ $jenis->nama_pelayanan }}</option>
                                        @endforeach
                                    </select>
                                    @error('jenis_layanan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="4">{{ old('keterangan', $daftar->keterangan) }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="file_persyaratan">File Persyaratan</label>
                                    @if($daftar->file_persyaratan)
                                        <div class="mb-2">
                                            <a href="{{ asset($daftar->file_persyaratan) }}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fa fa-file-pdf"></i> Lihat File Saat Ini
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control-file @error('file_persyaratan') is-invalid @enderror" id="file_persyaratan" name="file_persyaratan">
                                    <small class="form-text text-muted">Format: PDF. Ukuran maksimal: 5MB. Biarkan kosong jika tidak ingin mengubah file.</small>
                                    @error('file_persyaratan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                        <option value="pending" {{ old('status', $daftar->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="proses" {{ old('status', $daftar->status) == 'proses' ? 'selected' : '' }}>Proses</option>
                                        <option value="selesai" {{ old('status', $daftar->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="ditolak" {{ old('status', $daftar->status) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('layanan.daftar') }}" class="btn btn-secondary">Kembali</a>
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
    CKEDITOR.replace('keterangan');
</script>
@endsection 