@extends('layouts.admin')

@section('css')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Tambah Register Surat</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Tambah Register Surat</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3><i class="fa fa-check-square-o"></i> Edit Register Surat</h3>
                </div>

                <div class="card-body">
                    <form id="registerSuratForm" action="{{ route('adm.register_surat.update', $surat->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nomor_surat" class="form-label">Nomor Surat <span class="text-danger"></span></label>
                                    <input type="text" readonly class="form-control @error('nomor_surat') is-invalid @enderror" id="nomor_surat" name="nomor_surat" value="{{ $surat->nomor_surat }}">
                                    @error('nomor_surat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="kategori_surat_id" class="form-label">Kategori Surat <span class="text-danger">*</span></label>
                                    <select class="form-control" id="kategori_surat_id" name="kategori_surat_id" required>
                                        <option value="">Pilih Kategori Surat</option>
                                        @foreach($kategoriSurat as $kategori)
                                            <option value="{{ $kategori->id }}" {{ $surat->kategori_surat_id == $kategori->id ? 'selected' : '' }}>
                                                {{ $kategori->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3 d-none">
                                    <label for="jenis_surat" class="form-label">Jenis Surat <span class="text-danger">*</span></label>
                                    <select class="form-control @error('jenis_surat') is-invalid @enderror" id="jenis_surat" name="jenis_surat" required>
                                        <option value="">Pilih Jenis Surat</option>
                                        <option value="Surat Masuk">Surat Masuk</option>
                                        <option value="Surat Keluar" selected>Surat Keluar</option>
                                    </select>
                                    @error('jenis_surat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="perihal" class="form-label">Perihal <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('perihal') is-invalid @enderror" id="perihal" name="perihal" value="{{ $surat->perihal }}" required>
                                    @error('perihal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 d-none">
                                    <label for="isi_ringkas" class="form-label">Isi Ringkas <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('isi_ringkas') is-invalid @enderror" id="isi_ringkas" name="isi_ringkas" rows="4">{{ old('isi_ringkas') }}</textarea>
                                    @error('isi_ringkas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="tujuan" class="form-label">Tujuan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('tujuan') is-invalid @enderror" id="tujuan" name="tujuan" value="{{ $surat->tujuan }}" required>
                                    @error('tujuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tanggal_surat" class="form-label">Tanggal Surat <span class="text-danger">*</span></label> {{ $surat->tanggal_surat->format('d-m-Y') }}
                                    <input type="date" class="form-control @error('tanggal_surat') is-invalid @enderror" id="tanggal_surat" name="tanggal_surat" value="{{ $surat->tanggal_surat->format('Y-m-d') }}" required>
                                    @error('tanggal_surat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 d-none">
                                    <label for="tanggal_diterima" class="form-label">Tanggal Diterima <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_diterima') is-invalid @enderror" id="tanggal_diterima" name="tanggal_diterima" value="{{ old('tanggal_diterima') }}">
                                    @error('tanggal_diterima')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="lampiran" class="form-label">Lampiran (PDF/DOC/DOCX, max 2MB)</label>
                                    <input type="file" class="form-control @error('lampiran') is-invalid @enderror" id="lampiran" name="lampiran" accept=".pdf,.doc,.docx" value="{{ $surat->lampiran }}">
                                    @error('lampiran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

									@if($surat->lampiran)	
									<a href="{{ Storage::url('surat/lampiran/') }}/{{ $surat->lampiran }}" class="btn btn-primary btn-sm" target="_blank">
                                        Lampiran <i class="fa fa-file"></i>
                                    </a>
									@endif
                                </div>

                                <div class="form-group mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                        @foreach($status as $status)
                                            <option value="{{ $status->id }}" {{ $surat->status_id == $status->id ? 'selected' : '' }}>
                                                {{ $status->value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 d-none">
                                    <label for="keterangan" class="form-label">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
                                    @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="signer_id" class="form-label">Penandatangan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="signer_id" name="signer_id" required>
                                        <option value="">Pilih Penandatangan</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}"  {{ old('signer_id') == $user->id || $user->role == 'kepala' ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="isi_surat" class="form-label">Isi Surat <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('isi_surat') is-invalid @enderror" id="isi_surat" name="isi_surat" rows="10" required>{{ $surat->isi_surat }}</textarea>
                            @error('isi_surat')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan <i class="fa fa-save"></i></button>
                        <a href="{{ route('adm.register_surat.index') }}" class="btn btn-secondary">Kembali <i class="fa fa-arrow-left"></i></a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#signer_id').select2({
            theme: 'bootstrap4',
            placeholder: "Pilih Penandatangan",
            allowClear: true,
            width: '100%'
        });

        $('#kategori_surat_id').select2({
            theme: 'bootstrap4',
            placeholder: "Pilih Kategori Surat",
            allowClear: true,
            width: '100%'
        });

        // Handle form submission
        $('#registerSuratForm').on('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    // Disable submit button and show loading state
                    $('button[type="submit"]').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...'
                    );
                },
                success: function(response) {
                    // Show success message
                    swal({
                        title: "Berhasil!",
                        text: response.message,
                        icon: "success",
                        button: "OK",
                    }).then((value) => {
                        // Redirect to index page
                        window.location.href = "{{ route('adm.register_surat.index') }}";
                    });
                },
                error: function(xhr) {
                    // Enable submit button
                    $('button[type="submit"]').prop('disabled', false).html('Simpan <i class="fa fa-save"></i>');
                    
                    // Show error messages
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            // Add invalid class and feedback
                            $('#' + key).addClass('is-invalid')
                                .after('<div class="invalid-feedback">' + value[0] + '</div>');
                        });
                    } else {
                        swal({
                            title: "Error!",
                            text: "Terjadi kesalahan saat menyimpan data",
                            icon: "error",
                            button: "OK",
                        });
                    }
                }
            });
        });

        // Clear validation errors when input changes
        $('input, select, textarea').on('input change', function() {
            $(this).removeClass('is-invalid')
                .siblings('.invalid-feedback').remove();
        });
    });
</script>
@endsection