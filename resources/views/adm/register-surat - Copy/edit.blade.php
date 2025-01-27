@extends('layouts.app')

@section('title', 'Edit Surat')

@section('content')
<div class="container-fluid">
    <!-- Page header -->
    <div class="card bg-light-primary shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Edit Surat</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a class="text-muted" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a class="text-muted" href="{{ route('adm.register-surat.index') }}">Register Surat</a></li>
                            <li class="breadcrumb-item" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-3">
                    <div class="text-center mb-n5">
                        <img src="{{ asset('assets/images/breadcrumb/mail.png') }}" alt="" class="img-fluid mb-n4">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('adm.register_surat.update', $surat) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nomor_surat" class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nomor_surat') is-invalid @enderror" id="nomor_surat" name="nomor_surat" value="{{ old('nomor_surat', $surat->nomor_surat) }}" required>
                            @error('nomor_surat')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="jenis_surat" class="form-label">Jenis Surat <span class="text-danger">*</span></label>
                            <select class="form-select @error('jenis_surat') is-invalid @enderror" id="jenis_surat" name="jenis_surat" required>
                                <option value="">Pilih Jenis Surat</option>
                                <option value="Surat Masuk" {{ old('jenis_surat', $surat->jenis_surat) == 'Surat Masuk' ? 'selected' : '' }}>Surat Masuk</option>
                                <option value="Surat Keluar" {{ old('jenis_surat', $surat->jenis_surat) == 'Surat Keluar' ? 'selected' : '' }}>Surat Keluar</option>
                            </select>
                            @error('jenis_surat')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="perihal" class="form-label">Perihal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('perihal') is-invalid @enderror" id="perihal" name="perihal" value="{{ old('perihal', $surat->perihal) }}" required>
                            @error('perihal')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="isi_ringkas" class="form-label">Isi Ringkas <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('isi_ringkas') is-invalid @enderror" id="isi_ringkas" name="isi_ringkas" rows="4" required>{{ old('isi_ringkas', $surat->isi_ringkas) }}</textarea>
                            @error('isi_ringkas')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tujuan" class="form-label">Tujuan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('tujuan') is-invalid @enderror" id="tujuan" name="tujuan" value="{{ old('tujuan', $surat->tujuan) }}" required>
                            @error('tujuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="pengirim" class="form-label">Pengirim <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('pengirim') is-invalid @enderror" id="pengirim" name="pengirim" value="{{ old('pengirim', $surat->pengirim) }}" required>
                            @error('pengirim')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tanggal_surat" class="form-label">Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_surat') is-invalid @enderror" id="tanggal_surat" name="tanggal_surat" value="{{ old('tanggal_surat', $surat->tanggal_surat->format('Y-m-d')) }}" required>
                            @error('tanggal_surat')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_diterima" class="form-label">Tanggal Diterima <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_diterima') is-invalid @enderror" id="tanggal_diterima" name="tanggal_diterima" value="{{ old('tanggal_diterima', $surat->tanggal_diterima->format('Y-m-d')) }}" required>
                            @error('tanggal_diterima')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="lampiran" class="form-label">Lampiran (PDF/DOC/DOCX, max 2MB)</label>
                            <input type="file" class="form-control @error('lampiran') is-invalid @enderror" id="lampiran" name="lampiran" accept=".pdf,.doc,.docx">
                            @if($surat->lampiran)
                            <div class="form-text">File saat ini: <a href="{{ $surat->lampiran_url }}" target="_blank">{{ $surat->lampiran }}</a></div>
                            @endif
                            @error('lampiran')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="draft" {{ old('status', $surat->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="diproses" {{ old('status', $surat->status) == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                <option value="selesai" {{ old('status', $surat->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="ditolak" {{ old('status', $surat->status) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $surat->keterangan) }}</textarea>
                            @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="isi_surat" class="form-label">Isi Surat <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('isi_surat') is-invalid @enderror" id="isi_surat" name="isi_surat" rows="10" required>{{ old('isi_surat', $surat->isi_surat) }}</textarea>
                    @error('isi_surat')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <a href="{{ route('adm.register-surat.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-label {
        font-weight: 500;
    }
    
    .invalid-feedback {
        font-size: 0.875em;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #696cff;
        box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.1);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.tiny.cloud/1/YOUR_API_KEY/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
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