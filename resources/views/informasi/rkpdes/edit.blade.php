@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Edit RKPDes</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item">RKPDes</li>
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
                    <h3><i class="fa fa-edit"></i> Edit RKPDes</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('info.rkpdes.update', $rkpdes->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nomor">Nomor <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nomor') is-invalid @enderror" id="nomor" name="nomor" value="{{ old('nomor', $rkpdes->nomor) }}" required>
                                    @error('nomor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="tahun_anggaran">Tahun Anggaran <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('tahun_anggaran') is-invalid @enderror" id="tahun_anggaran" name="tahun_anggaran" value="{{ old('tahun_anggaran', $rkpdes->tahun_anggaran) }}" required>
                                    @error('tahun_anggaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="program">Program <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('program') is-invalid @enderror" id="program" name="program" value="{{ old('program', $rkpdes->program) }}" required>
                                    @error('program')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="kegiatan">Kegiatan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('kegiatan') is-invalid @enderror" id="kegiatan" name="kegiatan" value="{{ old('kegiatan', $rkpdes->kegiatan) }}" required>
                                    @error('kegiatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="lokasi">Lokasi <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('lokasi') is-invalid @enderror" id="lokasi" name="lokasi" rows="3" required>{{ old('lokasi', $rkpdes->lokasi) }}</textarea>
                                    @error('lokasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="anggaran">Anggaran <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('anggaran') is-invalid @enderror" id="anggaran" name="anggaran" value="{{ old('anggaran', $rkpdes->anggaran) }}" required>
                                    @error('anggaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="sumber_dana">Sumber Dana <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sumber_dana') is-invalid @enderror" id="sumber_dana" name="sumber_dana" value="{{ old('sumber_dana', $rkpdes->sumber_dana) }}" required>
                                    @error('sumber_dana')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="target">Target</label>
                                    <textarea class="form-control @error('target') is-invalid @enderror" id="target" name="target" rows="3">{{ old('target', $rkpdes->target) }}</textarea>
                                    @error('target')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="sasaran">Sasaran</label>
                                    <textarea class="form-control @error('sasaran') is-invalid @enderror" id="sasaran" name="sasaran" rows="3">{{ old('sasaran', $rkpdes->sasaran) }}</textarea>
                                    @error('sasaran')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="direncanakan" {{ old('status', $rkpdes->status) == 'direncanakan' ? 'selected' : '' }}>Direncanakan</option>
                                        <option value="berlangsung" {{ old('status', $rkpdes->status) == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                                        <option value="selesai" {{ old('status', $rkpdes->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $rkpdes->keterangan) }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="file">File RKPDes</label>
                                    @if($rkpdes->file)
                                        <div class="mb-2">
                                            <a href="{{ asset($rkpdes->file) }}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="fa fa-file-pdf"></i> Lihat File Saat Ini
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control-file @error('file') is-invalid @enderror" id="file" name="file">
                                    <small class="form-text text-muted">Format: PDF. Ukuran maksimal: 5MB. Biarkan kosong jika tidak ingin mengubah file.</small>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('info.rkpdes') }}" class="btn btn-secondary">Kembali</a>
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
    CKEDITOR.replace('target');
    CKEDITOR.replace('sasaran');
    CKEDITOR.replace('keterangan');
</script>
@endsection 