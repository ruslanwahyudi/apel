@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Edit Pengajuan Layanan</h1>
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
                    <h3><i class="fa fa-edit"></i> Edit Pengajuan Layanan</h3>
                </div>
                <div class="card-body">
                    <!-- Step 1: Pilih Jenis Layanan -->
                    <div id="step-jenis-layanan">
                        <h5 class="mb-3 text-primary">
                            <i class="fa fa-list-alt"></i> Langkah 1: Pilih Jenis Layanan
                        </h5>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="jenis_layanan_id" class="font-weight-bold">Jenis Layanan <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-lg" id="jenis_layanan_id" name="jenis_layanan_id" required>
                                        <option value="">-- Pilih Jenis Layanan --</option>
                                        @foreach($jenis as $j)
                                            <option value="{{ $j->id }}" {{ $j->id == $daftar->jenis_pelayanan_id ? 'selected' : '' }}>
                                                {{ $j->nama_pelayanan }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" class="btn btn-primary btn-lg" id="btn-next-step">
                                    <i class="fa fa-arrow-right"></i> Lanjut ke Pengisian Data
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Form Dinamis -->
                    <div id="step-form-data" style="display: none;">
                        <form action="{{ route('layanan.daftar.update', $daftar->id) }}" method="POST" enctype="multipart/form-data" id="form-layanan">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="jenis_layanan_id" id="hidden_jenis_layanan_id" value="{{ $daftar->jenis_pelayanan_id }}">
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-secondary" id="btn-back-step">
                                        <i class="fa fa-arrow-left"></i> Kembali
                                    </button>
                                </div>
                            </div>

                            <h5 class="mb-3 text-primary">
                                <i class="fa fa-edit"></i> Langkah 2: Edit Data Identitas
                            </h5>
                            
                            <!-- Container untuk form dinamis -->
                            <div id="dynamic-form-container">
                                <!-- Form akan dimuat secara dinamis disini -->
                            </div>

                            <!-- Catatan -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0"><i class="fa fa-sticky-note"></i> Catatan Tambahan</h6>
                                        </div>
                                        <div class="card-body py-2">
                                            <textarea class="form-control" id="catatan" name="catatan" rows="2" placeholder="Tambahkan catatan jika diperlukan...">{{ $daftar->catatan }}</textarea>
                                        </div>
                                </div>
                            </div>
                        </div>

                            <!-- Submit buttons -->
                        <div class="row mt-3">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-success btn-lg px-5">
                                        <i class="fa fa-save"></i> Update Pengajuan
                                    </button>
                                    <a href="{{ route('layanan.daftar') }}" class="btn btn-secondary btn-lg px-5 ml-2">
                                        <i class="fa fa-times"></i> Batal
                                    </a>
                                </div>
                            </div>
                        </form>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Data yang sudah ada untuk pre-fill form
    const existingData = JSON.parse('{!! addslashes(json_encode($existingData)) !!}');
    const currentJenisLayanan = parseInt('{{ $daftar->jenis_pelayanan_id }}');
    
    // Setup CSRF token untuk AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    // Handle pilihan jenis layanan
    $('#jenis_layanan_id').change(function() {
        const jenisLayananId = $(this).val();
        if (jenisLayananId) {
            $('#btn-next-step').prop('disabled', false);
        } else {
            $('#btn-next-step').prop('disabled', true);
        }
    });

    // Handle next step
    $('#btn-next-step').click(function() {
        const jenisLayananId = $('#jenis_layanan_id').val();
        if (jenisLayananId) {
            loadDynamicForm(jenisLayananId);
        }
    });

    // Handle back step
    $('#btn-back-step').click(function() {
        $('#step-form-data').hide();
        $('#step-jenis-layanan').show();
        $('#dynamic-form-container').html('');
    });

    // Auto load form jika jenis layanan sudah dipilih
    if (currentJenisLayanan) {
        $('#btn-next-step').prop('disabled', false);
    }

    // Function untuk load form dinamis
    function loadDynamicForm(jenisLayananId) {
        $.ajax({
            url: "{{ route('layanan.daftar.get-form-fields', ['jenisLayananId' => ':id']) }}".replace(':id', jenisLayananId),
            method: 'GET',
            beforeSend: function() {
                $('#dynamic-form-container').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2">Memuat form...</p></div>');
            },
            success: function(response) {
                if (response.success) {
                    buildDynamicForm(response.form_fields, response.syarat_dokumen);
                    $('#hidden_jenis_layanan_id').val(jenisLayananId);
                    $('#step-jenis-layanan').hide();
                    $('#step-form-data').show();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan saat memuat form');
                console.error(xhr);
            }
        });
    }

    // Function untuk build form dinamis
    function buildDynamicForm(formFields, syaratDokumen) {
        let html = '';

        // Build form berdasarkan klasifikasi dengan tabs
        if (formFields.length > 0) {
            html += '<div class="card">';
            html += '<div class="card-header bg-primary text-white">';
            html += '<h6 class="mb-0"><i class="fa fa-user"></i> Data Identitas Pemohon</h6>';
            html += '</div>';
            html += '<div class="card-body p-0">';
            
            // Nav tabs
            html += '<ul class="nav nav-tabs nav-justified" id="identitasTab" role="tablist">';
            formFields.forEach(function(group, index) {
                const activeClass = index === 0 ? 'active' : '';
                html += '<li class="nav-item">';
                html += '<a class="nav-link ' + activeClass + '" id="tab-' + group.klasifikasi_id + '-tab" data-toggle="tab" href="#tab-' + group.klasifikasi_id + '" role="tab">';
                html += '<strong>' + group.klasifikasi_nama + '</strong>';
                if (group.klasifikasi_deskripsi) {
                    html += '<br><small class="text-muted">' + group.klasifikasi_deskripsi + '</small>';
                }
                html += '</a>';
                html += '</li>';
            });
            html += '</ul>';

            // Tab content
            html += '<div class="tab-content" id="identitasTabContent">';
            formFields.forEach(function(group, index) {
                const activeClass = index === 0 ? 'show active' : '';
                html += '<div class="tab-pane fade ' + activeClass + '" id="tab-' + group.klasifikasi_id + '" role="tabpanel">';
                html += '<div class="p-3">';
                html += '<div class="row">';

                group.fields.forEach(function(field, fieldIndex) {
                    const colClass = 'col-md-6 col-lg-4'; // 3 kolom untuk desktop, 2 untuk tablet
                    const existingValue = existingData[field.id] || '';
                    
                    html += '<div class="' + colClass + '">';
                    html += '<div class="form-group mb-3">';
                    html += '<label for="identitas_' + field.id + '" class="form-label font-weight-semibold">' + field.nama_field;
                    if (field.required) {
                        html += ' <span class="text-danger">*</span>';
                    }
                    html += '</label>';

                    // Generate input berdasarkan tipe field dengan nilai yang ada
                    const inputClass = 'form-control form-control-sm';
                    switch (field.tipe_field) {
                        case 'text':
                        case 'email':
                            html += '<input type="' + field.tipe_field + '" class="' + inputClass + '" id="identitas_' + field.id + '" name="identitas_data[' + field.id + ']" value="' + existingValue + '"' + (field.required ? ' required' : '') + '>';
                            break;
                        case 'number':
                            html += '<input type="number" class="' + inputClass + '" id="identitas_' + field.id + '" name="identitas_data[' + field.id + ']" value="' + existingValue + '"' + (field.required ? ' required' : '') + '>';
                            break;
                        case 'date':
                            html += '<input type="date" class="' + inputClass + '" id="identitas_' + field.id + '" name="identitas_data[' + field.id + ']" value="' + existingValue + '"' + (field.required ? ' required' : '') + '>';
                            break;
                        case 'textarea':
                            html += '<textarea class="' + inputClass + '" id="identitas_' + field.id + '" name="identitas_data[' + field.id + ']" rows="2"' + (field.required ? ' required' : '') + '>' + existingValue + '</textarea>';
                            break;
                        default:
                            html += '<input type="text" class="' + inputClass + '" id="identitas_' + field.id + '" name="identitas_data[' + field.id + ']" value="' + existingValue + '"' + (field.required ? ' required' : '') + '>';
                    }

                    html += '</div>';
                    html += '</div>';
                });

                html += '</div>';
                html += '</div>';
                html += '</div>';
            });
            html += '</div>';
            html += '</div>';
            html += '</div>';
        }

        // Build form upload dokumen
        if (syaratDokumen.length > 0) {
            html += '<div class="card mt-3">';
            html += '<div class="card-header bg-warning text-dark">';
            html += '<h6 class="mb-0"><i class="fa fa-upload"></i> Upload Dokumen Persyaratan</h6>';
            html += '<small>Upload dokumen baru untuk mengganti dokumen yang ada (opsional)</small>';
            html += '</div>';
            html += '<div class="card-body">';
            html += '<div class="row">';

            syaratDokumen.forEach(function(dokumen, index) {
                const colClass = 'col-md-6 col-lg-4';
                html += '<div class="' + colClass + '">';
                html += '<div class="form-group mb-3">';
                html += '<label for="dokumen_' + dokumen.id + '" class="form-label font-weight-semibold">' + dokumen.nama_dokumen;
                html += '</label>';
                if (dokumen.deskripsi) {
                    html += '<div class="text-muted small mb-1">' + dokumen.deskripsi + '</div>';
                }
                html += '<input type="file" class="form-control-file form-control-sm" id="dokumen_' + dokumen.id + '" name="dokumen_files[' + dokumen.id + ']" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">';
                html += '<small class="form-text text-muted">PDF, DOC, DOCX, JPG, PNG (Max: 5MB)<br>Biarkan kosong jika tidak ingin mengubah dokumen</small>';
                html += '</div>';
                html += '</div>';
            });

            html += '</div>';
            html += '</div>';
            html += '</div>';
        }

        $('#dynamic-form-container').html(html);
    }
});
</script>

<style>
.form-label {
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
}

.form-control-sm {
    font-size: 0.875rem;
}

.nav-tabs .nav-link {
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
}

.tab-content {
    min-height: 300px;
}

.card-header h6 {
    margin-bottom: 0;
}

.form-group {
    margin-bottom: 1rem;
}
</style>
@endsection 