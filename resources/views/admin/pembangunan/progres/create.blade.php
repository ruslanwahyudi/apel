@extends('layouts.admin')

@section('css')
<style>
    .preview-image {
        max-width: 200px;
        margin: 10px;
    }
    .preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Update Progres Pembangunan</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item">Pembangunan</li>
                    <li class="breadcrumb-item active">Update Progres</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">						
            <div class="card mb-3">
                <div class="card-header">
                    <h3><i class="fa fa-check-square-o"></i> Update Progres: {{ $kegiatan->nama_kegiatan }}</h3>
                </div>
                    
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5>Informasi Kegiatan:</h5>
                        <p>
                            <strong>Lokasi:</strong> {{ $kegiatan->lokasi }}<br>
                            <strong>Pelaksana:</strong> {{ $kegiatan->pelaksana }}<br>
                            <strong>Status:</strong> {{ $kegiatan->status }}<br>
                            <strong>Progres Terakhir:</strong> 
                            {{ $kegiatan->latestProgres ? $kegiatan->latestProgres->persentase . '%' : '0%' }}
                        </p>
                    </div>

                    <form action="{{ route('admin.pembangunan.progres.store', $kegiatan->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="tanggal">Tanggal Update <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal') is-invalid @enderror" 
                                id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="persentase">Persentase Progres <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('persentase') is-invalid @enderror" 
                                    id="persentase" name="persentase" value="{{ old('persentase') }}" 
                                    min="0" max="100" step="0.01" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            @error('persentase')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="keterangan">Keterangan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                                id="keterangan" name="keterangan" rows="3" required>{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Foto Dokumentasi <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('fotos.*') is-invalid @enderror" 
                                    id="fotos" name="fotos[]" accept="image/*" multiple required>
                                <label class="custom-file-label" for="fotos">Pilih foto...</label>
                            </div>
                            <small class="form-text text-muted">
                                Dapat memilih lebih dari 1 foto. Format: JPG, JPEG, PNG. Maksimal 2MB per file.
                            </small>
                            @error('fotos.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="preview" class="preview-container"></div>
                        </div>

                        <div id="captions"></div>

                        <button type="submit" class="btn btn-primary">Simpan <i class="fa fa-save"></i></button>
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
    // Preview foto yang dipilih
    $('#fotos').on('change', function() {
        $('#preview').html('');
        $('#captions').html('');
        
        if (this.files) {
            [...this.files].forEach((file, index) => {
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        // Tambah preview image
                        $('#preview').append(`
                            <div class="preview-item">
                                <img src="${e.target.result}" class="preview-image">
                                <div class="form-group">
                                    <input type="text" class="form-control" 
                                        name="captions[]" placeholder="Caption foto ${index + 1}">
                                </div>
                            </div>
                        `);
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    // Update label custom file input
    $('.custom-file-input').on('change', function() {
        let fileName = Array.from(this.files)
            .map(file => file.name)
            .join(', ');
        $(this).next('.custom-file-label').html(fileName);
    });

    // Validasi persentase tidak boleh lebih kecil dari progres terakhir
    // var lastProgress = {!! $kegiatan->latestProgres ? $kegiatan->latestProgres->persentase : 0 !!};
    var lastProgress = 0;
    $('#persentase').on('change', function() {
        var value = parseFloat($(this).val());
        if (value < lastProgress) {
            alert('Persentase tidak boleh lebih kecil dari progres terakhir (' + lastProgress + '%)');
            $(this).val(lastProgress);
        }
    });
});
</script>
@endsection 