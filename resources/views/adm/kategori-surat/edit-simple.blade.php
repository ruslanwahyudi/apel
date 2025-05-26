@extends('layouts.admin')

@section('css')
<style>
    .template-option {
        border: 2px solid #ddd;
        padding: 20px;
        margin: 10px 0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .template-option:hover {
        border-color: #007bff;
        background-color: #f8f9fa;
    }
    .template-option.active {
        border-color: #007bff;
        background-color: #e3f2fd;
    }
    .template-option i {
        font-size: 2em;
        margin-bottom: 10px;
        color: #007bff;
    }
    .pdf-upload-area {
        border: 2px dashed #ddd;
        padding: 40px;
        text-align: center;
        border-radius: 8px;
        margin: 20px 0;
    }
    .pdf-upload-area.dragover {
        border-color: #007bff;
        background-color: #f8f9fa;
    }
    .field-item {
        border: 1px solid #ddd;
        padding: 15px;
        margin: 10px 0;
        border-radius: 5px;
        background-color: #f9f9f9;
    }
    .variable-input {
        margin-bottom: 15px;
    }
    .position-helper {
        border: 1px solid #ddd;
        padding: 15px;
        background-color: #f8f9fa;
        margin-bottom: 20px;
    }
    .coordinate-info {
        font-size: 12px;
        color: #666;
        margin-top: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Edit Kategori Surat</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item">Kategori Surat</li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3><i class="fa fa-check-square-o"></i> Edit Kategori Surat</h3>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('adm.kategori-surat.update', ['kategori' => $kategori->id]) }}" method="post" enctype="multipart/form-data" id="kategoriForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Nama Kategori -->
                        <div class="form-group">
                            <label for="nama">Nama Kategori Surat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                   id="nama" name="nama" value="{{ old('nama', $kategori->nama) }}" 
                                   placeholder="Masukkan nama kategori surat" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Template Type Selection -->
                        <div class="form-group">
                            <label>Pilih Jenis Template</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="template-option" data-type="text" id="text-template-option">
                                        <div class="text-center">
                                            <i class="fa fa-file-text-o"></i>
                                            <h5>Template Text</h5>
                                            <p class="text-muted">Buat template menggunakan text editor dengan variabel dinamis</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="template-option" data-type="pdf" id="pdf-template-option">
                                        <div class="text-center">
                                            <i class="fa fa-file-pdf-o"></i>
                                            <h5>Template PDF</h5>
                                            <p class="text-muted">Upload file PDF dengan form fields yang bisa diisi otomatis</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="template-option" data-type="docx" id="docx-template-option">
                                        <div class="text-center">
                                            <i class="fa fa-file-word-o"></i>
                                            <h5>Template DOCX ⭐</h5>
                                            <p class="text-muted"><strong>RECOMMENDED:</strong> Akurasi 100% untuk penggantian variabel</p>
                                            <span class="badge badge-success">BEST CHOICE</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="template_type" id="template_type" value="{{ old('template_type', $kategori->template_type ?? 'text') }}">
                        </div>

                        <!-- Text Template Section -->
                        <div id="text-template-section" style="display: none;">
                            <div class="form-group">
                                <label for="template_surat">Template Surat</label>
                                <small class="form-text text-muted mb-2">
                                    Gunakan variabel dengan format @{{nama_variabel}}. Contoh: @{{nama_pemohon}}, @{{tanggal_surat}}
                                </small>
                                <textarea class="form-control" id="template_surat" name="template_surat" rows="15" 
                                          placeholder="Masukkan template surat...">{{ old('template_surat', $kategori->template_surat) }}</textarea>
                            </div>

                            <!-- Simple Variables -->
                            <div class="form-group">
                                <label>Variabel Template</label>
                                <small class="form-text text-muted mb-3">
                                    Definisikan variabel yang akan digunakan dalam template. Pisahkan dengan koma.
                                </small>
                                <input type="text" class="form-control" id="simple_variables" 
                                       placeholder="nama_pemohon, alamat_pemohon, tanggal_surat, perihal"
                                       value="{{ old('simple_variables', implode(', ', array_column($kategori->template_variables ?? [], 'name'))) }}">
                                <small class="form-text text-muted">
                                    Contoh: nama_pemohon, alamat_pemohon, tanggal_surat, perihal
                                </small>
                            </div>
                        </div>

                        <!-- DOCX Template Section -->
                        <div id="docx-template-section" style="display: none;">
                            <div class="alert alert-success">
                                <h5><i class="fa fa-star"></i> Template DOCX - Pilihan Terbaik!</h5>
                                <p><strong>Keunggulan DOCX Template:</strong></p>
                                <ul>
                                    <li>✅ <strong>Akurasi 100%</strong> - Variabel pasti terganti dengan tepat</li>
                                    <li>✅ <strong>Mudah dibuat</strong> - Gunakan Microsoft Word biasa</li>
                                    <li>✅ <strong>Format terjaga</strong> - Layout dan styling tetap sempurna</li>
                                    <li>✅ <strong>Tidak perlu koordinat</strong> - Sistem otomatis mengganti variabel</li>
                                </ul>
                            </div>
                            
                            <div class="form-group">
                                <label>Upload Template DOCX</label>
                                <div class="docx-upload-area" id="docx-upload-area">
                                    <i class="fa fa-file-word-o fa-3x text-primary mb-3"></i>
                                    <h5>Drag & Drop file DOCX atau klik untuk browse</h5>
                                    <p class="text-muted">Buat template di Microsoft Word dengan variabel format: ${nomor}, ${nama}, ${tanggal}</p>
                                    <input type="file" id="docx_template" name="docx_template" accept=".docx" style="display: none;">
                                    <button type="button" class="btn btn-primary" onclick="$('#docx_template').click()">
                                        <i class="fa fa-folder-open"></i> Pilih File DOCX
                                    </button>
                                </div>
                                
                                @if($kategori->docx_template_path)
                                    <div class="alert alert-info mt-3">
                                        <i class="fa fa-file-word-o"></i> 
                                        Template DOCX saat ini: <strong>{{ basename($kategori->docx_template_path) }}</strong>
                                        <a href="{{ asset('storage/' . $kategori->docx_template_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ml-2">
                                            <i class="fa fa-download"></i> Download
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- DOCX Variable Guide -->
                            <div class="docx-guide">
                                <h6><i class="fa fa-lightbulb-o"></i> Cara Membuat Template DOCX</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="guide-step">
                                            <h6>1. Buat dokumen di Microsoft Word</h6>
                                            <p>Tulis surat seperti biasa dengan format yang diinginkan</p>
                                        </div>
                                        <div class="guide-step">
                                            <h6>2. Tambahkan variabel</h6>
                                            <p>Gunakan format: <code>${nama_variabel}</code></p>
                                            <p>Contoh: <code>${nomor}</code>, <code>${nama}</code>, <code>${tanggal}</code></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="guide-step">
                                            <h6>3. Simpan sebagai .docx</h6>
                                            <p>Pastikan format file adalah .docx (bukan .doc)</p>
                                        </div>
                                        <div class="guide-step">
                                            <h6>4. Upload ke sistem</h6>
                                            <p>Sistem akan otomatis mengganti semua variabel dengan data yang diinput</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DOCX Form Fields -->
                            <div class="form-group">
                                <label>Variabel DOCX</label>
                                <small class="form-text text-muted mb-3">
                                    Definisikan variabel yang ada di template DOCX Anda
                                </small>
                                <div id="docx-fields-container">
                                    <!-- DOCX fields will be added here -->
                                </div>
                                <button type="button" class="btn btn-sm btn-success" id="add-docx-field">
                                    <i class="fa fa-plus"></i> Tambah Variabel
                                </button>
                            </div>
                        </div>

                        <!-- PDF Template Section -->
                        <div id="pdf-template-section" style="display: none;">
                            <div class="form-group">
                                <label>Upload Template PDF</label>
                                <div class="pdf-upload-area" id="pdf-upload-area">
                                    <i class="fa fa-cloud-upload fa-3x text-muted mb-3"></i>
                                    <h5>Drag & Drop file PDF atau klik untuk browse</h5>
                                    <p class="text-muted">File PDF harus memiliki form fields yang bisa diisi</p>
                                    <input type="file" id="pdf_template" name="pdf_template" accept=".pdf" style="display: none;">
                                    <button type="button" class="btn btn-primary" onclick="$('#pdf_template').click()">
                                        <i class="fa fa-folder-open"></i> Pilih File PDF
                                    </button>
                                </div>
                                
                                @if($kategori->pdf_template_path)
                                    <div class="alert alert-info mt-3">
                                        <i class="fa fa-file-pdf-o"></i> 
                                        Template PDF saat ini: <strong>{{ basename($kategori->pdf_template_path) }}</strong>
                                        <a href="{{ asset('storage/' . $kategori->pdf_template_path) }}" target="_blank" class="btn btn-sm btn-outline-primary ml-2">
                                            <i class="fa fa-eye"></i> Lihat
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Position Helper -->
                            <div class="position-helper">
                                <h6><i class="fa fa-info-circle"></i> Panduan Posisi Variabel</h6>
                                <p>Untuk hasil yang akurat, pastikan posisi variabel sesuai dengan template PDF Anda:</p>
                                <ul>
                                    <li><strong>Nomor Surat:</strong> Biasanya di kanan atas (x: 100-150, y: 50-70)</li>
                                    <li><strong>Tanggal:</strong> Di bawah nomor surat (x: 100-150, y: 70-90)</li>
                                    <li><strong>Nama/Data Pemohon:</strong> Di bagian tengah (x: 100-120, y: 120-200)</li>
                                </ul>
                                <div class="coordinate-info">
                                    <strong>Tips:</strong> Koordinat dimulai dari kiri atas (0,0). X = jarak dari kiri, Y = jarak dari atas.
                                </div>
                                
                                <!-- Auto Detection Button -->
                                <div class="mt-3">
                                    <button type="button" class="btn btn-info btn-sm" id="auto-detect-positions">
                                        <i class="fa fa-magic"></i> Deteksi Posisi Otomatis
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm ml-2" id="test-replacement">
                                        <i class="fa fa-flask"></i> Test Penggantian Variabel
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm ml-2" id="test-precise-replacement">
                                        <i class="fa fa-bullseye"></i> Test Precise Replacement
                                    </button>
                                    <small class="form-text text-muted">
                                        Sistem akan mencoba mendeteksi posisi variabel secara otomatis. Gunakan "Test Precise" untuk hasil yang lebih akurat.
                                    </small>
                                </div>
                            </div>

                            <!-- PDF Form Fields -->
                            <div class="form-group">
                                <label>Form Fields PDF</label>
                                <small class="form-text text-muted mb-3">
                                    Definisikan nama field di PDF yang akan diisi otomatis
                                </small>
                                <div id="pdf-fields-container">
                                    <!-- PDF fields will be added here -->
                                </div>
                                <button type="button" class="btn btn-sm btn-success" id="add-pdf-field">
                                    <i class="fa fa-plus"></i> Tambah Field
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                            <a href="{{ route('adm.kategori-surat') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                        </div>
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
    // Set CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var pdfFieldIndex = 0;
    var docxFieldIndex = 0;
    
    // Initialize template type
    var currentType = $('#template_type').val();
    selectTemplateType(currentType);
    
    // Template type selection
    $('.template-option').click(function() {
        var type = $(this).data('type');
        selectTemplateType(type);
    });
    
    function selectTemplateType(type) {
        $('.template-option').removeClass('active');
        $('#' + type + '-template-option').addClass('active');
        $('#template_type').val(type);
        
        // Hide all sections first
        $('#text-template-section').hide();
        $('#pdf-template-section').hide();
        $('#docx-template-section').hide();
        
        // Show selected section
        if (type === 'text') {
            $('#text-template-section').show();
        } else if (type === 'pdf') {
            $('#pdf-template-section').show();
        } else if (type === 'docx') {
            $('#docx-template-section').show();
        }
    }
    
    // PDF Upload handling
    $('#pdf_template').change(function() {
        var file = this.files[0];
        if (file) {
            $('#pdf-upload-area h5').text('File dipilih: ' + file.name);
        }
    });
    
    // Drag and drop for PDF
    $('#pdf-upload-area').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    }).on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    }).on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0 && files[0].type === 'application/pdf') {
            $('#pdf_template')[0].files = files;
            $(this).find('h5').text('File dipilih: ' + files[0].name);
        }
    });
    
    // DOCX Upload handling
    $('#docx_template').change(function() {
        var file = this.files[0];
        if (file) {
            $('#docx-upload-area h5').text('File dipilih: ' + file.name);
        }
    });
    
    // Drag and drop for DOCX
    $('#docx-upload-area').on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    }).on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    }).on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0 && files[0].type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            $('#docx_template')[0].files = files;
            $(this).find('h5').text('File dipilih: ' + files[0].name);
        }
    });
    
    // Add PDF field
    function addPdfField(fieldName = '', fieldLabel = '', fieldType = 'text', positionX = 100, positionY = 100) {
        var fieldHtml = '<div class="field-item">' +
            '<div class="row">' +
                '<div class="col-md-3">' +
                    '<label>Nama Field di PDF</label>' +
                    '<input type="text" class="form-control" name="pdf_form_fields[' + pdfFieldIndex + '][field_name]" ' +
                           'value="' + fieldName + '" placeholder="nama_pemohon">' +
                '</div>' +
                '<div class="col-md-3">' +
                    '<label>Label untuk Form</label>' +
                    '<input type="text" class="form-control" name="pdf_form_fields[' + pdfFieldIndex + '][label]" ' +
                           'value="' + fieldLabel + '" placeholder="Nama Pemohon">' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Tipe</label>' +
                    '<select class="form-control" name="pdf_form_fields[' + pdfFieldIndex + '][type]">' +
                        '<option value="text" ' + (fieldType === 'text' ? 'selected' : '') + '>Text</option>' +
                        '<option value="textarea" ' + (fieldType === 'textarea' ? 'selected' : '') + '>Textarea</option>' +
                        '<option value="date" ' + (fieldType === 'date' ? 'selected' : '') + '>Date</option>' +
                        '<option value="number" ' + (fieldType === 'number' ? 'selected' : '') + '>Number</option>' +
                    '</select>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Posisi X</label>' +
                    '<input type="number" class="form-control" name="pdf_form_fields[' + pdfFieldIndex + '][position_x]" ' +
                           'value="' + positionX + '" placeholder="100" min="0" max="600">' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Posisi Y</label>' +
                    '<input type="number" class="form-control" name="pdf_form_fields[' + pdfFieldIndex + '][position_y]" ' +
                           'value="' + positionY + '" placeholder="100" min="0" max="800">' +
                '</div>' +
            '</div>' +
            '<div class="row mt-2">' +
                '<div class="col-md-12 text-right">' +
                    '<button type="button" class="btn btn-danger btn-sm remove-pdf-field">' +
                        '<i class="fa fa-trash"></i> Hapus Field' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        $('#pdf-fields-container').append(fieldHtml);
        pdfFieldIndex++;
    }
    
    $('#add-pdf-field').click(function() {
        addPdfField();
    });
    
    $(document).on('click', '.remove-pdf-field', function() {
        $(this).closest('.field-item').remove();
    });
    
    // Add DOCX field
    function addDocxField(fieldName = '', fieldLabel = '', fieldType = 'text', required = false) {
        var fieldHtml = '<div class="field-item">' +
            '<div class="row">' +
                '<div class="col-md-4">' +
                    '<label>Nama Variabel</label>' +
                    '<input type="text" class="form-control" name="docx_form_fields[' + docxFieldIndex + '][field_name]" ' +
                           'value="' + fieldName + '" placeholder="nomor">' +
                    '<small class="text-muted">Tanpa ${}, contoh: nomor, nama, tanggal</small>' +
                '</div>' +
                '<div class="col-md-4">' +
                    '<label>Label untuk Form</label>' +
                    '<input type="text" class="form-control" name="docx_form_fields[' + docxFieldIndex + '][label]" ' +
                           'value="' + fieldLabel + '" placeholder="Nomor Surat">' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Tipe</label>' +
                    '<select class="form-control" name="docx_form_fields[' + docxFieldIndex + '][type]">' +
                        '<option value="text" ' + (fieldType === 'text' ? 'selected' : '') + '>Text</option>' +
                        '<option value="textarea" ' + (fieldType === 'textarea' ? 'selected' : '') + '>Textarea</option>' +
                        '<option value="date" ' + (fieldType === 'date' ? 'selected' : '') + '>Date</option>' +
                        '<option value="number" ' + (fieldType === 'number' ? 'selected' : '') + '>Number</option>' +
                    '</select>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Required</label>' +
                    '<div class="form-check">' +
                        '<input type="checkbox" class="form-check-input" name="docx_form_fields[' + docxFieldIndex + '][required]" ' +
                               'value="1" ' + (required ? 'checked' : '') + '>' +
                        '<label class="form-check-label">Wajib diisi</label>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            '<div class="row mt-2">' +
                '<div class="col-md-12 text-right">' +
                    '<button type="button" class="btn btn-danger btn-sm remove-docx-field">' +
                        '<i class="fa fa-trash"></i> Hapus Variabel' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        $('#docx-fields-container').append(fieldHtml);
        docxFieldIndex++;
    }
    
    $('#add-docx-field').click(function() {
        addDocxField();
    });
    
    $(document).on('click', '.remove-docx-field', function() {
        $(this).closest('.field-item').remove();
    });
    
    // Load existing PDF fields
    var existingPdfFields = [];
    try {
        var pdfFieldsData = '{{ json_encode($kategori->pdf_form_fields ?? []) }}';
        existingPdfFields = JSON.parse(pdfFieldsData.replace(/&quot;/g, '"'));
    } catch(e) {
        existingPdfFields = [];
    }
    
    if (existingPdfFields && existingPdfFields.length > 0) {
        existingPdfFields.forEach(function(field) {
            addPdfField(
                field.field_name, 
                field.label, 
                field.type, 
                field.position_x || 100, 
                field.position_y || 100
            );
        });
    } else if (currentType === 'pdf') {
        // Add default PDF fields with default positions
        addPdfField('nomor_surat', 'Nomor Surat', 'text', 150, 60);
        addPdfField('tanggal_surat', 'Tanggal Surat', 'date', 150, 80);
        addPdfField('nama_pemohon', 'Nama Pemohon', 'text', 100, 120);
        addPdfField('alamat_pemohon', 'Alamat Pemohon', 'textarea', 100, 140);
    }
    
    // Load existing DOCX fields
    var existingDocxFields = [];
    try {
        var docxFieldsData = '{{ json_encode($kategori->docx_form_fields ?? []) }}';
        existingDocxFields = JSON.parse(docxFieldsData.replace(/&quot;/g, '"'));
    } catch(e) {
        existingDocxFields = [];
    }
    
    if (existingDocxFields && existingDocxFields.length > 0) {
        existingDocxFields.forEach(function(field) {
            addDocxField(
                field.field_name, 
                field.label, 
                field.type, 
                field.required || false
            );
        });
    } else if (currentType === 'docx') {
        // Add default DOCX fields
        addDocxField('nomor', 'Nomor Surat', 'text', true);
        addDocxField('tanggal', 'Tanggal Surat', 'date', true);
        addDocxField('nama', 'Nama Pemohon', 'text', true);
        addDocxField('alamat', 'Alamat Pemohon', 'textarea', true);
        addDocxField('keperluan', 'Keperluan', 'textarea', false);
    }
    
    // Auto-detect positions functionality
    $('#auto-detect-positions').click(function() {
        var button = $(this);
        var originalText = button.html();
        
        // Check if PDF template is uploaded
        var hasUploadedFile = $('#pdf_template')[0].files.length > 0;
        var hasExistingTemplate = '{{ $kategori->pdf_template_path ?? "" }}' !== '';
        
        if (!hasUploadedFile && !hasExistingTemplate) {
            alert('Silakan upload template PDF terlebih dahulu');
            return;
        }
        
        // Show loading
        button.html('<i class="fa fa-spinner fa-spin"></i> Mendeteksi...');
        button.prop('disabled', true);
        
        // Call detection API
        $.ajax({
            url: '/adm/kategori-surat/{{ $kategori->id }}/detect-positions',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update positions in existing fields
                    $('.field-item').each(function(index) {
                        var fieldNameInput = $(this).find('input[name*="[field_name]"]');
                        var positionXInput = $(this).find('input[name*="[position_x]"]');
                        var positionYInput = $(this).find('input[name*="[position_y]"]');
                        
                        var fieldName = fieldNameInput.val();
                        if (fieldName && response.positions[fieldName]) {
                            positionXInput.val(response.positions[fieldName].x);
                            positionYInput.val(response.positions[fieldName].y);
                            
                            // Add visual feedback
                            positionXInput.addClass('border-success');
                            positionYInput.addClass('border-success');
                            setTimeout(function() {
                                positionXInput.removeClass('border-success');
                                positionYInput.removeClass('border-success');
                            }, 2000);
                        }
                    });
                    
                    // Show success message with debug info
                    var detectedCount = Object.keys(response.positions).length;
                    var debugMessage = 'Berhasil mendeteksi ' + detectedCount + ' posisi variabel!\n\n' + 
                          'Posisi yang terdeteksi:\n' + 
                          Object.keys(response.positions).map(function(key) {
                              return '• ' + key + ': (' + response.positions[key].x + ', ' + response.positions[key].y + ')';
                          }).join('\n');
                    
                    // Add debug info if available
                    if (response.debug_info) {
                        debugMessage += '\n\nDebug Info:\n';
                        Object.keys(response.debug_info).forEach(function(fieldName) {
                            var info = response.debug_info[fieldName];
                            debugMessage += '• ' + fieldName + ': ';
                            if (info.detected_position) {
                                debugMessage += 'Detected (' + info.detected_position.x + ', ' + info.detected_position.y + ')';
                            } else {
                                debugMessage += 'Not detected';
                            }
                            debugMessage += '\n';
                        });
                    }
                    
                    alert(debugMessage);
                    
                    // Show PDF text content for reference
                    if (response.pdf_text) {
                        console.log('PDF Text Content:', response.pdf_text);
                    }
                    
                    // Show debug info in console
                    if (response.debug_info) {
                        console.log('Debug Info:', response.debug_info);
                    }
                } else {
                    alert('Gagal mendeteksi posisi: ' + response.message);
                    if (response.trace) {
                        console.error('Error trace:', response.trace);
                    }
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan saat mendeteksi posisi variabel');
                console.error('AJAX Error:', xhr.responseText);
            },
            complete: function() {
                // Restore button
                button.html(originalText);
                button.prop('disabled', false);
            }
        });
    });
    
    // Test replacement functionality
    $('#test-replacement').click(function() {
        var button = $(this);
        var originalText = button.html();
        
        // Check if PDF template is uploaded
        var hasUploadedFile = $('#pdf_template')[0].files.length > 0;
        var hasExistingTemplate = '{{ $kategori->pdf_template_path ?? "" }}' !== '';
        
        if (!hasUploadedFile && !hasExistingTemplate) {
            alert('Silakan upload template PDF terlebih dahulu');
            return;
        }
        
        // Show loading
        button.html('<i class="fa fa-spinner fa-spin"></i> Testing...');
        button.prop('disabled', true);
        
        // Call test API
        $.ajax({
            url: '/adm/kategori-surat/{{ $kategori->id }}/test-replacement',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Show success message with link to test PDF
                    var message = 'Test PDF berhasil di-generate!\n\n' +
                                'Data test yang digunakan:\n' +
                                Object.keys(response.test_data).map(function(key) {
                                    return '• ' + key + ': ' + response.test_data[key];
                                }).join('\n') + '\n\n' +
                                'PDF akan dibuka di tab baru.';
                    
                    alert(message);
                    
                    // Open test PDF in new tab
                    window.open(response.test_pdf_url, '_blank');
                    
                    console.log('Test Data:', response.test_data);
                    console.log('PDF Fields:', response.pdf_fields);
                } else {
                    alert('Test gagal: ' + response.message);
                    if (response.trace) {
                        console.error('Error trace:', response.trace);
                    }
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan saat testing');
                console.error('AJAX Error:', xhr.responseText);
            },
            complete: function() {
                // Restore button
                button.html(originalText);
                button.prop('disabled', false);
            }
        });
    });
    
    // Test PRECISE replacement functionality
    $('#test-precise-replacement').click(function() {
        var button = $(this);
        var originalText = button.html();
        
        // Check if PDF template is uploaded
        var hasUploadedFile = $('#pdf_template')[0].files.length > 0;
        var hasExistingTemplate = '{{ $kategori->pdf_template_path ?? "" }}' !== '';
        
        if (!hasUploadedFile && !hasExistingTemplate) {
            alert('Silakan upload template PDF terlebih dahulu');
            return;
        }
        
        // Show loading
        button.html('<i class="fa fa-spinner fa-spin"></i> Testing Precise...');
        button.prop('disabled', true);
        
        // Call test API with precise flag
        $.ajax({
            url: '/adm/kategori-surat/{{ $kategori->id }}/test-replacement',
            method: 'POST',
            data: {
                precise_mode: true
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Show success message with link to test PDF
                    var message = '✅ PRECISE Test PDF berhasil di-generate!\n\n' +
                                '🎯 Mode: Precise Variable Replacement\n' +
                                '📊 Data test yang digunakan:\n' +
                                Object.keys(response.test_data).map(function(key) {
                                    return '• ' + key + ': ' + response.test_data[key];
                                }).join('\n') + '\n\n' +
                                '📄 PDF akan dibuka di tab baru.\n' +
                                '🔍 Periksa apakah variabel sudah terganti dengan tepat!';
                    
                    alert(message);
                    
                    // Open test PDF in new tab
                    window.open(response.test_pdf_url, '_blank');
                    
                    console.log('PRECISE Test Data:', response.test_data);
                    console.log('PRECISE PDF Fields:', response.pdf_fields);
                    
                    // Show additional info if available
                    if (response.template_analysis) {
                        console.log('Template Analysis:', response.template_analysis);
                    }
                } else {
                    alert('PRECISE Test gagal: ' + response.message);
                    if (response.trace) {
                        console.error('Error trace:', response.trace);
                    }
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan saat PRECISE testing');
                console.error('AJAX Error:', xhr.responseText);
            },
            complete: function() {
                // Restore button
                button.html(originalText);
                button.prop('disabled', false);
            }
        });
    });
    
    // Form submission handling
    $('#kategoriForm').submit(function() {
        if ($('#template_type').val() === 'text') {
            // Convert simple variables to template_variables format
            var simpleVars = $('#simple_variables').val();
            if (simpleVars) {
                var variables = simpleVars.split(',').map(function(item, index) {
                    var name = item.trim();
                    return {
                        name: name,
                        label: name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()),
                        type: 'text',
                        required: false
                    };
                });
                
                // Add hidden inputs for template_variables
                variables.forEach(function(variable, index) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'template_variables[' + index + '][name]',
                        value: variable.name
                    }).appendTo('#kategoriForm');
                    
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'template_variables[' + index + '][label]',
                        value: variable.label
                    }).appendTo('#kategoriForm');
                    
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'template_variables[' + index + '][type]',
                        value: variable.type
                    }).appendTo('#kategoriForm');
                    
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'template_variables[' + index + '][required]',
                        value: variable.required
                    }).appendTo('#kategoriForm');
                });
            }
        }
    });
});
</script>
@endsection 