@extends('layouts.admin')

@section('title', 'Tambah Surat')

@section('content')
<div class="container-fluid">
    <!-- Page header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0">Tambah Surat</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('adm.register-surat.index') }}">Register Surat</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('adm.register_surat.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nomor_surat" class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nomor_surat') is-invalid @enderror" id="nomor_surat" name="nomor_surat" value="{{ old('nomor_surat') }}" required>
                            @error('nomor_surat')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="jenis_surat" class="form-label">Jenis Surat <span class="text-danger">*</span></label>
                            <select class="form-control @error('jenis_surat') is-invalid @enderror" id="jenis_surat" name="jenis_surat" required>
                                <option value="">Pilih Jenis Surat</option>
                                <option value="Surat Masuk" {{ old('jenis_surat') == 'Surat Masuk' ? 'selected' : '' }}>Surat Masuk</option>
                                <option value="Surat Keluar" {{ old('jenis_surat') == 'Surat Keluar' ? 'selected' : '' }}>Surat Keluar</option>
                            </select>
                            @error('jenis_surat')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="perihal" class="form-label">Perihal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('perihal') is-invalid @enderror" id="perihal" name="perihal" value="{{ old('perihal') }}" required>
                            @error('perihal')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="isi_ringkas" class="form-label">Isi Ringkas <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('isi_ringkas') is-invalid @enderror" id="isi_ringkas" name="isi_ringkas" rows="4" required>{{ old('isi_ringkas') }}</textarea>
                            @error('isi_ringkas')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tujuan" class="form-label">Tujuan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('tujuan') is-invalid @enderror" id="tujuan" name="tujuan" value="{{ old('tujuan') }}" required>
                            @error('tujuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="pengirim" class="form-label">Pengirim <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('pengirim') is-invalid @enderror" id="pengirim" name="pengirim" value="{{ old('pengirim') }}" required>
                            @error('pengirim')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tanggal_surat" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_surat') is-invalid @enderror" id="tanggal_surat" name="tanggal_surat" value="{{ old('tanggal_surat') }}" required>
                            @error('tanggal_surat')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_diterima" class="form-label">Tanggal Diterima <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_diterima') is-invalid @enderror" id="tanggal_diterima" name="tanggal_diterima" value="{{ old('tanggal_diterima') }}" required>
                            @error('tanggal_diterima')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="lampiran" class="form-label">Lampiran (PDF/DOC/DOCX, max 2MB)</label>
                            <input type="file" class="form-control @error('lampiran') is-invalid @enderror" id="lampiran" name="lampiran" accept=".pdf,.doc,.docx">
                            @error('lampiran')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                @foreach(getAllStatus() as $value => $label)
                                    <option value="{{ $value }}" {{ old('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                                
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="isi_surat" class="form-label">Isi Surat <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('isi_surat') is-invalid @enderror" id="isi_surat" name="isi_surat" rows="10" required>{{ old('isi_surat') }}</textarea>
                    @error('isi_surat')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <a href="{{ route('adm.register-surat.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<style>
    .form-label {
        font-weight: 500;
    }
    
    .invalid-feedback {
        font-size: 0.875em;
    }
    
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        border: 1px solid #dee2e6;
    }
    
    .select2-container--bootstrap-5 .select2-selection--single {
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }

    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .select2-container--bootstrap-5 .select2-dropdown {
        border-color: #86b7fe;
    }

    .select2-container--bootstrap-5 .select2-dropdown .select2-results__option--highlighted[aria-selected] {
        background-color: #0d6efd;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for all select elements
        $('#jenis_surat, #status').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('body')
        });
    });

    tinymce.init({
        selector: '#isi_surat',
        height: 500,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
    });
</script>
@endpush 