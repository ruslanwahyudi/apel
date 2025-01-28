@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Edit Kegiatan Pembangunan</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Edit Kegiatan</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">						
            <div class="card mb-3">
                <div class="card-header">
                    <h3><i class="fa fa-check-square-o"></i> Edit Kegiatan Pembangunan</h3>
                </div>
                    
                <div class="card-body">
                    <form action="{{ route('admin.pembangunan.update', $kegiatan->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="nama_kegiatan">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_kegiatan') is-invalid @enderror" 
                                id="nama_kegiatan" name="nama_kegiatan" value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}" required>
                            @error('nama_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                                id="deskripsi" name="deskripsi" rows="3" required>{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="lokasi">Lokasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('lokasi') is-invalid @enderror" 
                                id="lokasi" name="lokasi" value="{{ old('lokasi', $kegiatan->lokasi) }}" required>
                            @error('lokasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="anggaran">Anggaran <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>
                                <input type="number" class="form-control @error('anggaran') is-invalid @enderror" 
                                    id="anggaran" name="anggaran" value="{{ old('anggaran', $kegiatan->anggaran) }}" required>
                            </div>
                            @error('anggaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="sumber_dana">Sumber Dana <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('sumber_dana') is-invalid @enderror" 
                                id="sumber_dana" name="sumber_dana" value="{{ old('sumber_dana', $kegiatan->sumber_dana) }}" required>
                            @error('sumber_dana')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_mulai">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                        id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', $kegiatan->tanggal_mulai->format('Y-m-d')) }}" required>
                                    @error('tanggal_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_selesai">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                        id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', $kegiatan->tanggal_selesai->format('Y-m-d')) }}" required>
                                    @error('tanggal_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="pelaksana">Pelaksana <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('pelaksana') is-invalid @enderror" 
                                id="pelaksana" name="pelaksana" value="{{ old('pelaksana', $kegiatan->pelaksana) }}" required>
                            @error('pelaksana')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="Belum Dimulai" {{ old('status', $kegiatan->status) == 'Belum Dimulai' ? 'selected' : '' }}>Belum Dimulai</option>
                                <option value="Dalam Pengerjaan" {{ old('status', $kegiatan->status) == 'Dalam Pengerjaan' ? 'selected' : '' }}>Dalam Pengerjaan</option>
                                <option value="Selesai" {{ old('status', $kegiatan->status) == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="Terhenti" {{ old('status', $kegiatan->status) == 'Terhenti' ? 'selected' : '' }}>Terhenti</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update <i class="fa fa-save"></i></button>
                        <a href="{{ route('admin.pembangunan') }}" class="btn btn-secondary">Kembali <i class="fa fa-arrow-left"></i></a>
                    </form>
                </div>														
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Validasi tanggal selesai harus setelah tanggal mulai
        $('#tanggal_mulai, #tanggal_selesai').on('change', function() {
            var mulai = $('#tanggal_mulai').val();
            var selesai = $('#tanggal_selesai').val();
            
            if(mulai && selesai && mulai > selesai) {
                $('#tanggal_selesai').val('');
                alert('Tanggal selesai harus setelah tanggal mulai');
            }
        });
    });
</script>
@endsection 