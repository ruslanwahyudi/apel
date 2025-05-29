@extends('layouts.admin')

@section('css')
<style>
    .variable-item {
        border: 1px solid #ddd;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 5px;
        background-color: #f9f9f9;
    }
    .template-preview {
        border: 1px solid #ddd;
        padding: 15px;
        background-color: #fff;
        min-height: 200px;
        white-space: pre-wrap;
    }
    .variable-tag {
        background-color: #007bff;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 12px;
        cursor: pointer;
        margin: 2px;
        display: inline-block;
    }
    .variable-tag:hover {
        background-color: #0056b3;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Tambah Kategori Surat</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item">Kategori Surat</li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3><i class="fa fa-check-square-o"></i> Tambah Kategori Surat</h3>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('adm.kategori-surat.store') }}" method="post" id="kategoriForm">
                        @csrf
                        
                        <!-- Nama Kategori -->
                        <div class="form-group">
                            <label for="nama">Nama Kategori Surat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                   id="nama" name="nama" value="{{ old('nama') }}" 
                                   placeholder="Masukkan nama kategori surat" required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tipe Surat -->
                        <div class="form-group">
                            <label for="tipe_surat">Tipe Surat <span class="text-danger">*</span></label>
                            <select class="form-control @error('tipe_surat') is-invalid @enderror" 
                                    id="tipe_surat" name="tipe_surat" required>
                                <option value="">Pilih Tipe Surat</option>
                                <option value="non_layanan" {{ old('tipe_surat') == 'non_layanan' ? 'selected' : '' }}>
                                    Non-Layanan (Input Manual)
                                </option>
                                <option value="layanan" {{ old('tipe_surat') == 'layanan' ? 'selected' : '' }}>
                                    Layanan (Data dari DUK)
                                </option>
                            </select>
                            <small class="form-text text-muted">
                                <strong>Non-Layanan:</strong> Data diisi manual saat generate surat<br>
                                <strong>Layanan:</strong> Data diambil otomatis dari database DUK (Daftar Usulan Kegiatan)
                            </small>
                            @error('tipe_surat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Jenis Pelayanan (only show when layanan is selected) -->
                        <div class="form-group" id="jenis-pelayanan-section" style="display: none;">
                            <label for="jenis_pelayanan_id">Jenis Pelayanan</label>
                            <select class="form-control @error('jenis_pelayanan_id') is-invalid @enderror" 
                                    id="jenis_pelayanan_id" name="jenis_pelayanan_id">
                                <option value="">Pilih Jenis Pelayanan (Opsional)</option>
                                @php
                                    $jenisPelayanan = \App\Models\Layanan\JenisPelayanan::all();
                                @endphp
                                @foreach($jenisPelayanan as $jenis)
                                    <option value="{{ $jenis->id }}" {{ old('jenis_pelayanan_id') == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                Pilih jenis pelayanan untuk kategorisasi surat layanan (opsional)
                            </small>
                            @error('jenis_pelayanan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Template Type -->
                        <div class="form-group">
                            <label for="template_type">Jenis Template <span class="text-danger">*</span></label>
                            <select class="form-control @error('template_type') is-invalid @enderror" 
                                    id="template_type" name="template_type" required>
                                <option value="">Pilih Jenis Template</option>
                                <option value="blade" {{ old('template_type') == 'blade' ? 'selected' : '' }}>
                                    Blade Template (Recommended)
                                </option>
                                <option value="text" {{ old('template_type') == 'text' ? 'selected' : '' }}>
                                    Text Template
                                </option>
                                <option value="pdf" {{ old('template_type') == 'pdf' ? 'selected' : '' }}>
                                    PDF Template
                                </option>
                                <option value="docx" {{ old('template_type') == 'docx' ? 'selected' : '' }}>
                                    DOCX Template
                                </option>
                            </select>
                            <small class="form-text text-muted">
                                <strong>Blade Template:</strong> Template menggunakan Blade engine Laravel (Fleksibel, mudah maintenance)<br>
                                <strong>Text Template:</strong> Template teks sederhana dengan variabel<br>
                                <strong>PDF Template:</strong> Upload file PDF sebagai template<br>
                                <strong>DOCX Template:</strong> Upload file DOCX sebagai template
                            </small>
                            @error('template_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Blade Template Name (only show when blade is selected) -->
                        <div class="form-group" id="blade-template-section" style="display: none;">
                            <label for="blade_template_name">Nama File Template Blade</label>
                            <input type="text" class="form-control @error('blade_template_name') is-invalid @enderror" 
                                   id="blade_template_name" name="blade_template_name" value="{{ old('blade_template_name') }}" 
                                   placeholder="contoh: surat-keterangan-domisili">
                            <small class="form-text text-muted">
                                File akan disimpan di: <code>resources/views/templates/surat/[nama-file].blade.php</code><br>
                                Template yang tersedia: <strong>surat-keterangan-domisili</strong>, <strong>surat-keterangan-usaha</strong>
                            </small>
                            @error('blade_template_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Template Variables Section -->
                        <div class="form-group template-variables-section">
                            <label>Variabel Template</label>
                            <small class="form-text text-muted mb-3">
                                Definisikan variabel yang akan digunakan dalam template surat. Variabel akan ditampilkan sebagai @{{nama_variabel}} dalam template.
                            </small>
                            
                            <div id="variables-container">
                                <!-- Variables will be added here -->
                            </div>
                            
                            <button type="button" class="btn btn-sm btn-success" id="add-variable">
                                <i class="fa fa-plus"></i> Tambah Variabel
                            </button>
                        </div>

                        <!-- Template Surat Section -->
                        <div class="form-group template-surat-section">
                            <label for="template_surat">Template Surat</label>
                            <small class="form-text text-muted mb-2">
                                Gunakan variabel dengan format @{{nama_variabel}}. Klik pada tag variabel di bawah untuk menambahkannya ke template.
                            </small>
                            
                            <!-- Variable Tags -->
                            <div id="variable-tags" class="mb-2">
                                <!-- Variable tags will be displayed here -->
                            </div>
                            
                            <textarea class="form-control @error('template_surat') is-invalid @enderror" 
                                      id="template_surat" name="template_surat" rows="15" 
                                      placeholder="Masukkan template surat...">{{ old('template_surat') }}</textarea>
                            @error('template_surat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Preview Section -->
                        <div class="form-group template-surat-section">
                            <label>Preview Template</label>
                            <div class="template-preview" id="template-preview">
                                Template preview akan muncul di sini...
                            </div>
                            <button type="button" class="btn btn-sm btn-info mt-2" id="preview-template">
                                <i class="fa fa-eye"></i> Preview Template
                            </button>
                        </div>

                        <!-- Blade Template Variables Section (for Blade templates) -->
                        <div class="form-group blade-variables-section" style="display: none;">
                            <label>Variabel Template Blade</label>
                            <small class="form-text text-muted mb-3">
                                Definisikan variabel yang akan digunakan dalam template Blade. Variabel ini akan menjadi input form saat generate surat.
                            </small>
                            
                            <div id="blade-variables-container">
                                <!-- Blade variables will be added here -->
                            </div>
                            
                            <button type="button" class="btn btn-sm btn-success" id="add-blade-variable">
                                <i class="fa fa-plus"></i> Tambah Variabel Blade
                            </button>
                        </div>

                        <!-- PDF/DOCX Template Info Section -->
                        <div class="form-group pdf-docx-info-section" style="display: none;">
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Informasi Template PDF/DOCX</h5>
                                <p>Untuk template PDF dan DOCX, Anda perlu:</p>
                                <ol>
                                    <li>Simpan kategori surat ini terlebih dahulu</li>
                                    <li>Edit kategori surat untuk upload file template</li>
                                    <li>Konfigurasi field dan posisi variabel</li>
                                </ol>
                                <p class="mb-0"><strong>Catatan:</strong> Template PDF/DOCX memerlukan konfigurasi lanjutan yang akan tersedia di halaman edit.</p>
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
    let variableIndex = 0;

    // Template type change handler
    $('#template_type').change(function() {
        var selectedType = $(this).val();
        
        // Hide all sections first
        $('.blade-template-section, .blade-variables-section, .template-surat-section, .template-variables-section, .pdf-docx-info-section').hide();
        
        if (selectedType === 'blade') {
            $('.blade-template-section, .blade-variables-section').show();
        } else if (selectedType === 'text') {
            $('.template-surat-section, .template-variables-section').show();
        } else if (selectedType === 'pdf' || selectedType === 'docx') {
            $('.pdf-docx-info-section').show();
        }
    });

    // Tipe surat change handler
    $('#tipe_surat').change(function() {
        var selectedTipe = $(this).val();
        
        if (selectedTipe === 'layanan') {
            $('#jenis-pelayanan-section').show();
            $('#jenis_pelayanan_id').attr('required', true);
        } else {
            $('#jenis-pelayanan-section').hide();
            $('#jenis_pelayanan_id').attr('required', false);
            $('#jenis_pelayanan_id').val('');
        }
    });

    // Initialize on page load
    $('#template_type').trigger('change');
    $('#tipe_surat').trigger('change');

    // Add variable function
    function addVariable(name = '', label = '', type = 'text', required = false) {
        var selectedText = (type === 'text') ? 'selected' : '';
        var selectedTextarea = (type === 'textarea') ? 'selected' : '';
        var selectedDate = (type === 'date') ? 'selected' : '';
        var selectedNumber = (type === 'number') ? 'selected' : '';
        var checkedRequired = required ? 'checked' : '';
        
        var variableHtml = '<div class="variable-item" data-index="' + variableIndex + '">' +
            '<div class="row">' +
                '<div class="col-md-3">' +
                    '<label>Nama Variabel</label>' +
                    '<input type="text" class="form-control variable-name" ' +
                           'name="template_variables[' + variableIndex + '][name]" ' +
                           'value="' + name + '" placeholder="nama_variabel">' +
                '</div>' +
                '<div class="col-md-3">' +
                    '<label>Label</label>' +
                    '<input type="text" class="form-control variable-label" ' +
                           'name="template_variables[' + variableIndex + '][label]" ' +
                           'value="' + label + '" placeholder="Label untuk form">' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Tipe</label>' +
                    '<select class="form-control variable-type" name="template_variables[' + variableIndex + '][type]">' +
                        '<option value="text" ' + selectedText + '>Text</option>' +
                        '<option value="textarea" ' + selectedTextarea + '>Textarea</option>' +
                        '<option value="date" ' + selectedDate + '>Date</option>' +
                        '<option value="number" ' + selectedNumber + '>Number</option>' +
                    '</select>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Required</label>' +
                    '<div class="form-check">' +
                        '<input type="checkbox" class="form-check-input variable-required" ' +
                               'name="template_variables[' + variableIndex + '][required]" ' +
                               'value="1" ' + checkedRequired + '>' +
                        '<label class="form-check-label">Wajib</label>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>&nbsp;</label>' +
                    '<button type="button" class="btn btn-danger btn-sm d-block remove-variable">' +
                        '<i class="fa fa-trash"></i> Hapus' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        $('#variables-container').append(variableHtml);
        variableIndex++;
        updateVariableTags();
    }

    // Update variable tags
    function updateVariableTags() {
        var tags = $('#variable-tags');
        tags.empty();
        
        $('.variable-name').each(function() {
            var name = $(this).val();
            console.log('Variable name:', name); // Debug
            if (name && name.trim() !== '') {
                var tagHtml = '<span class="variable-tag" data-variable="' + name + '">{' + '{' + name + '}' + '}</span>';
                console.log('Tag HTML:', tagHtml); // Debug
                tags.append(tagHtml);
            }
        });
        console.log('Tags container content:', tags.html()); // Debug
    }

    // Add variable button click
    $('#add-variable').click(function() {
        addVariable();
    });

    // Remove variable
    $(document).on('click', '.remove-variable', function() {
        $(this).closest('.variable-item').remove();
        updateVariableTags();
    });

    // Update tags when variable name changes
    $(document).on('input', '.variable-name', function() {
        updateVariableTags();
    });

    // Insert variable tag into template
    $(document).on('click', '.variable-tag', function() {
        var variable = $(this).data('variable');
        var template = $('#template_surat');
        var cursorPos = template[0].selectionStart;
        var textBefore = template.val().substring(0, cursorPos);
        var textAfter = template.val().substring(cursorPos);
        var insertText = '{' + '{' + variable + '}' + '}';
        
        template.val(textBefore + insertText + textAfter);
        
        // Set cursor position after inserted text
        var newPos = cursorPos + insertText.length;
        template[0].setSelectionRange(newPos, newPos);
        template.focus();
    });

    // Preview template
    $('#preview-template').click(function() {
        var template = $('#template_surat').val();
        var preview = template;
        
        // Replace variables with sample data
        $('.variable-item').each(function() {
            var name = $(this).find('.variable-name').val();
            var label = $(this).find('.variable-label').val();
            if (name && label) {
                var searchPattern = '{' + '{' + name + '}' + '}';
                var regex = new RegExp(searchPattern.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g');
                preview = preview.replace(regex, '[' + label + ']');
            }
        });
        
        $('#template-preview').html(preview || 'Template kosong...');
    });

    // Auto preview on template change
    $('#template_surat').on('input', function() {
        $('#preview-template').click();
    });

    // Blade variable functions
    let bladeVariableIndex = 0;

    function addBladeVariable(name = '', label = '', type = 'text', required = false, defaultValue = '') {
        var selectedText = (type === 'text') ? 'selected' : '';
        var selectedTextarea = (type === 'textarea') ? 'selected' : '';
        var selectedDate = (type === 'date') ? 'selected' : '';
        var selectedNumber = (type === 'number') ? 'selected' : '';
        var checkedRequired = required ? 'checked' : '';
        
        var variableHtml = '<div class="variable-item" data-index="' + bladeVariableIndex + '">' +
            '<div class="row">' +
                '<div class="col-md-2">' +
                    '<label>Nama Variabel</label>' +
                    '<input type="text" class="form-control blade-variable-name" ' +
                           'name="blade_template_variables[' + bladeVariableIndex + '][name]" ' +
                           'value="' + name + '" placeholder="nama_variabel">' +
                '</div>' +
                '<div class="col-md-3">' +
                    '<label>Label</label>' +
                    '<input type="text" class="form-control blade-variable-label" ' +
                           'name="blade_template_variables[' + bladeVariableIndex + '][label]" ' +
                           'value="' + label + '" placeholder="Label untuk form">' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Tipe</label>' +
                    '<select class="form-control blade-variable-type" name="blade_template_variables[' + bladeVariableIndex + '][type]">' +
                        '<option value="text" ' + selectedText + '>Text</option>' +
                        '<option value="textarea" ' + selectedTextarea + '>Textarea</option>' +
                        '<option value="date" ' + selectedDate + '>Date</option>' +
                        '<option value="number" ' + selectedNumber + '>Number</option>' +
                    '</select>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Default Value</label>' +
                    '<input type="text" class="form-control blade-variable-default" ' +
                           'name="blade_template_variables[' + bladeVariableIndex + '][default_value]" ' +
                           'value="' + defaultValue + '" placeholder="Nilai default">' +
                '</div>' +
                '<div class="col-md-1">' +
                    '<label>Required</label>' +
                    '<div class="form-check">' +
                        '<input type="checkbox" class="form-check-input blade-variable-required" ' +
                               'name="blade_template_variables[' + bladeVariableIndex + '][required]" ' +
                               'value="1" ' + checkedRequired + '>' +
                        '<label class="form-check-label">Wajib</label>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>&nbsp;</label>' +
                    '<button type="button" class="btn btn-danger btn-sm d-block remove-blade-variable">' +
                        '<i class="fa fa-trash"></i> Hapus' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        $('#blade-variables-container').append(variableHtml);
        bladeVariableIndex++;
    }

    // Add blade variable button click
    $('#add-blade-variable').click(function() {
        addBladeVariable();
    });

    // Remove blade variable
    $(document).on('click', '.remove-blade-variable', function() {
        $(this).closest('.variable-item').remove();
    });

    // Add default variables
    addVariable('nama_pemohon', 'Nama Pemohon', 'text', true);
    addVariable('alamat_pemohon', 'Alamat Pemohon', 'textarea', true);
    addVariable('tanggal_surat', 'Tanggal Surat', 'date', true);

    // Add default blade variables
    addBladeVariable('nama_lengkap', 'Nama Lengkap', 'text', true);
    addBladeVariable('nik', 'NIK', 'text', true);
    addBladeVariable('tempat_lahir', 'Tempat Lahir', 'text', true);
    addBladeVariable('tanggal_lahir', 'Tanggal Lahir', 'date', true);
    addBladeVariable('alamat', 'Alamat', 'textarea', true);
    addBladeVariable('keperluan', 'Keperluan', 'text', true);
});
</script>
@endsection

