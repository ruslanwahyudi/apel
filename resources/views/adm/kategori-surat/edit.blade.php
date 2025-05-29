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
    .blade-template-section {
        background: #f8f9fa;
        border: 2px solid #28a745;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .legacy-template-section {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
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
					<h3><i class="fa fa-edit"></i> Edit Kategori Surat: {{ $kategori->nama }}</h3>
					<div class="card-tools">
						@if($kategori->hasBladeTemplate())
							<a href="{{ route('adm.kategori-surat.preview-blade', $kategori) }}" 
							   class="btn btn-primary btn-sm" target="_blank">
								<i class="fa fa-eye"></i> Preview Template
							</a>
						@endif
					</div>
				</div>
				
				<div class="card-body">
					<form action="{{ route('adm.kategori-surat.update', ['kategori' => $kategori->id]) }}" method="post" id="kategoriForm">
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

						<!-- Tipe Surat -->
						<div class="form-group">
							<label for="tipe_surat">Tipe Surat <span class="text-danger">*</span></label>
							<select class="form-control @error('tipe_surat') is-invalid @enderror" 
									id="tipe_surat" name="tipe_surat" required>
								<option value="">Pilih Tipe Surat</option>
								<option value="non_layanan" {{ old('tipe_surat', $kategori->tipe_surat) == 'non_layanan' ? 'selected' : '' }}>
									Non-Layanan (Input Manual)
								</option>
								<option value="layanan" {{ old('tipe_surat', $kategori->tipe_surat) == 'layanan' ? 'selected' : '' }}>
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
									<option value="{{ $jenis->id }}" {{ old('jenis_pelayanan_id', $kategori->jenis_pelayanan_id) == $jenis->id ? 'selected' : '' }}>
										{{ $jenis->nama_pelayanan }}
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

						<!-- Template Type Selection -->
						<div class="form-group">
							<label>Tipe Template <span class="text-danger">*</span></label>
							<select class="form-control" name="template_type" id="template_type" required>
								<option value="blade" {{ old('template_type', $kategori->template_type) == 'blade' ? 'selected' : '' }}>
									Blade Template (Recommended)
								</option>
								<option value="text" {{ old('template_type', $kategori->template_type) == 'text' ? 'selected' : '' }}>
									Text Template (Legacy)
								</option>
								<option value="pdf" {{ old('template_type', $kategori->template_type) == 'pdf' ? 'selected' : '' }}>
									PDF Template (Legacy)
								</option>
								<option value="docx" {{ old('template_type', $kategori->template_type) == 'docx' ? 'selected' : '' }}>
									DOCX Template (Legacy)
								</option>
							</select>
						</div>

						<!-- Blade Template Section (Recommended) -->
						<div id="blade-template-section" class="blade-template-section" style="display: none;">
							<h4><i class="fa fa-star text-success"></i> Template Blade (Recommended)</h4>
							<p class="text-muted">Template Blade memberikan kontrol penuh atas tampilan dan format surat dengan HTML/CSS.</p>
							
							<!-- Blade Template Name -->
							<div class="form-group">
								<label for="blade_template_name">Nama File Template Blade</label>
								<input type="text" class="form-control" id="blade_template_name" name="blade_template_name" 
									   value="{{ old('blade_template_name', $kategori->blade_template_name) }}"
									   placeholder="contoh: surat-keterangan-domisili">
								<small class="form-text text-muted">
									File akan disimpan di: <code>resources/views/templates/surat/{nama-file}.blade.php</code>
								</small>
							</div>

							<!-- Blade Template Variables -->
							<div class="form-group">
								<label>Variabel Template Blade</label>
								<small class="form-text text-muted mb-3">
									Definisikan variabel yang akan digunakan dalam template Blade.
								</small>
								
								<div id="blade-variables-container">
									<!-- Blade variables will be added here -->
								</div>
								
								<button type="button" class="btn btn-sm btn-success" id="add-blade-variable">
									<i class="fa fa-plus"></i> Tambah Variabel
								</button>
							</div>

							<!-- Template Status -->
							@if($kategori->blade_template_name)
								<div class="alert alert-info">
									<i class="fa fa-info-circle"></i>
									<strong>Status Template:</strong>
									@if($kategori->hasBladeTemplate())
										<span class="text-success">Template file ditemukan ✓</span>
										<br>
										<small>File: <code>resources/views/templates/surat/{{ $kategori->blade_template_name }}.blade.php</code></small>
									@else
										<span class="text-warning">Template file belum dibuat</span>
										<br>
										<small>Silakan buat file: <code>resources/views/templates/surat/{{ $kategori->blade_template_name }}.blade.php</code></small>
									@endif
								</div>
							@endif
						</div>

						<!-- Legacy Template Sections -->
						<div id="legacy-template-section" class="legacy-template-section" style="display: none;">
							<h4><i class="fa fa-exclamation-triangle text-warning"></i> Template Legacy</h4>
							<p class="text-muted">Template legacy masih didukung tetapi disarankan untuk menggunakan Blade Template.</p>
							
							<!-- Text Template Section -->
							<div id="text-template-section" style="display: none;">
								<div class="form-group">
									<label for="template_surat">Template Surat (Text)</label>
									<textarea class="form-control" id="template_surat" name="template_surat" rows="10" 
											  placeholder="Masukkan template surat...">{{ old('template_surat', $kategori->template_surat) }}</textarea>
								</div>
							</div>

							<!-- PDF/DOCX Template Section -->
							<div id="file-template-section" style="display: none;">
								<div class="alert alert-info">
									<i class="fa fa-info-circle"></i>
									Template PDF/DOCX dapat dikelola melalui halaman edit khusus.
									<a href="{{ route('adm.kategori-surat.edit-simple', $kategori) }}" class="btn btn-sm btn-primary ml-2">
										<i class="fa fa-edit"></i> Edit Template PDF/DOCX
									</a>
								</div>
							</div>
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-save"></i> Simpan Perubahan
							</button>
							<a href="{{ route('adm.kategori-surat') }}" class="btn btn-secondary">
								<i class="fa fa-arrow-left"></i> Kembali
							</a>
							@if($kategori->hasBladeTemplate())
								<a href="{{ route('adm.kategori-surat.preview-blade', $kategori) }}" 
								   class="btn btn-info" target="_blank">
									<i class="fa fa-eye"></i> Preview Template
								</a>
							@endif
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
    // Initialize form based on current template type
    var currentTemplateType = $('#template_type').val();
    showTemplateSection(currentTemplateType);
    
    // Load existing blade variables
    loadBladeVariables();
    
    // Template type change handler
    $('#template_type').change(function() {
        var templateType = $(this).val();
        showTemplateSection(templateType);
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
    $('#tipe_surat').trigger('change');
    
    // Add blade variable handler
    $('#add-blade-variable').click(function() {
        addBladeVariable();
    });
    
    // Remove variable handler
    $(document).on('click', '.remove-blade-variable', function() {
        $(this).closest('.variable-item').remove();
    });
    
    function showTemplateSection(templateType) {
        // Hide all sections first
        $('#blade-template-section').hide();
        $('#legacy-template-section').hide();
        $('#text-template-section').hide();
        $('#file-template-section').hide();
        
        if (templateType === 'blade') {
            $('#blade-template-section').show();
        } else {
            $('#legacy-template-section').show();
            
            if (templateType === 'text') {
                $('#text-template-section').show();
            } else if (templateType === 'pdf' || templateType === 'docx') {
                $('#file-template-section').show();
            }
        }
    }
    
    function loadBladeVariables() {
        var variables = @json($kategori->blade_template_variables ?? []);
        
        // Clear container
        $('#blade-variables-container').empty();
        
        // Add existing variables
        if (variables && variables.length > 0) {
            variables.forEach(function(variable) {
                addBladeVariable(variable);
            });
        } else {
            // Add default variables for new templates
            addDefaultBladeVariables();
        }
    }
    
    function addDefaultBladeVariables() {
        var defaultVariables = [
            { name: 'nama_lengkap', label: 'Nama Lengkap', type: 'text', required: true },
            { name: 'nik', label: 'NIK', type: 'text', required: true },
            { name: 'tempat_lahir', label: 'Tempat Lahir', type: 'text', required: false },
            { name: 'tanggal_lahir', label: 'Tanggal Lahir', type: 'date', required: false },
            { name: 'alamat', label: 'Alamat', type: 'textarea', required: true },
            { name: 'keperluan', label: 'Keperluan', type: 'text', required: true },
            { name: 'tanggal_surat', label: 'Tanggal Surat', type: 'date', required: false }
        ];
        
        defaultVariables.forEach(function(variable) {
            addBladeVariable(variable);
        });
    }
    
    function addBladeVariable(variable) {
        if (typeof variable === 'undefined') {
            variable = null;
        }
        
        var index = $('#blade-variables-container .variable-item').length;
        
        var html = '<div class="variable-item">' +
            '<div class="row">' +
                '<div class="col-md-3">' +
                    '<label>Nama Variabel</label>' +
                    '<input type="text" class="form-control" name="blade_template_variables[' + index + '][name]" ' +
                           'value="' + (variable ? variable.name : '') + '" placeholder="nama_field" required>' +
                '</div>' +
                '<div class="col-md-3">' +
                    '<label>Label</label>' +
                    '<input type="text" class="form-control" name="blade_template_variables[' + index + '][label]" ' +
                           'value="' + (variable ? variable.label : '') + '" placeholder="Label Field" required>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Tipe</label>' +
                    '<select class="form-control" name="blade_template_variables[' + index + '][type]">' +
                        '<option value="text"' + (variable && variable.type === 'text' ? ' selected' : '') + '>Text</option>' +
                        '<option value="textarea"' + (variable && variable.type === 'textarea' ? ' selected' : '') + '>Textarea</option>' +
                        '<option value="date"' + (variable && variable.type === 'date' ? ' selected' : '') + '>Date</option>' +
                        '<option value="number"' + (variable && variable.type === 'number' ? ' selected' : '') + '>Number</option>' +
                    '</select>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label>Default Value</label>' +
                    '<input type="text" class="form-control" name="blade_template_variables[' + index + '][default_value]" ' +
                           'value="' + (variable ? (variable.default_value || '') : '') + '" placeholder="Default">' +
                '</div>' +
                '<div class="col-md-1">' +
                    '<label>Required</label>' +
                    '<div class="form-check">' +
                        '<input type="checkbox" class="form-check-input" name="blade_template_variables[' + index + '][required]" ' +
                               'value="1"' + (variable && variable.required ? ' checked' : '') + '>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-1">' +
                    '<label>&nbsp;</label>' +
                    '<button type="button" class="btn btn-danger btn-sm remove-blade-variable d-block">' +
                        '<i class="fa fa-trash"></i>' +
                    '</button>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        $('#blade-variables-container').append(html);
    }
});
</script>
@endsection