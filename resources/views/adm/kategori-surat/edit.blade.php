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

						<!-- Template Tabs -->
						<div class="form-group">
							<ul class="nav nav-tabs" id="templateTabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" id="header-tab" data-toggle="tab" href="#header" role="tab">Header Surat</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="body-tab" data-toggle="tab" href="#body" role="tab">Body Surat</a>
								</li>
							</ul>
							
							<div class="tab-content" id="templateTabContent">
								<!-- Header Template Tab -->
								<div class="tab-pane fade show active" id="header" role="tabpanel">
									<div class="mt-3">
										<!-- Header Type Selection -->
										<div class="form-group">
											<label>Tipe Header</label>
											<select class="form-control" name="header_type" id="header_type">
												<option value="simple" {{ old('header_type', $kategori->header_type) == 'simple' ? 'selected' : '' }}>Simple (Nomor + Nama Surat)</option>
												<option value="full" {{ old('header_type', $kategori->header_type) == 'full' ? 'selected' : '' }}>Full (Nomor, Tujuan, Perihal, Sifat, dll)</option>
											</select>
										</div>

										<!-- Header Variables -->
										<div class="form-group">
											<label>Variabel Header</label>
											<small class="form-text text-muted mb-3">
												Definisikan variabel untuk header surat (nomor, tujuan, perihal, dll).
											</small>
											
											<div id="header-variables-container">
												<!-- Header variables will be added here -->
											</div>
											
											<button type="button" class="btn btn-sm btn-success" id="add-header-variable">
												<i class="fa fa-plus"></i> Tambah Variabel Header
											</button>
										</div>

										<!-- Header Template -->
										<div class="form-group">
											<label for="header_template">Template Header</label>
											<small class="form-text text-muted mb-2">
												Template untuk header surat. Gunakan variabel dengan format @{{nama_variabel}}.
											</small>
											
											<div id="header-variable-tags" class="mb-2">
												<!-- Header variable tags will be displayed here -->
											</div>
											
											<textarea class="form-control" 
													  id="header_template" name="header_template" rows="8" 
													  placeholder="Masukkan template header surat...">{{ old('header_template', $kategori->header_template) }}</textarea>
										</div>
									</div>
								</div>

								<!-- Body Template Tab -->
								<div class="tab-pane fade" id="body" role="tabpanel">
									<div class="mt-3">
										<!-- Template Variables Section -->
										<div class="form-group">
											<label>Variabel Template Body</label>
											<small class="form-text text-muted mb-3">
												Definisikan variabel yang akan digunakan dalam isi surat.
											</small>
											
											<div id="variables-container">
												<!-- Variables will be added here -->
											</div>
											
											<button type="button" class="btn btn-sm btn-success" id="add-variable">
												<i class="fa fa-plus"></i> Tambah Variabel Body
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Template Surat Section -->
						<div class="form-group">
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
									  placeholder="Masukkan template surat...">{{ old('template_surat', $kategori->template_surat) }}</textarea>
							@error('template_surat')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<!-- Preview Section -->
						<div class="form-group">
							<label>Preview Template</label>
							<div class="template-preview" id="template-preview">
								Template preview akan muncul di sini...
							</div>
							<button type="button" class="btn btn-sm btn-info mt-2" id="preview-template">
								<i class="fa fa-eye"></i> Preview Template
							</button>
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
// Global variables
window.existingVariables = @json($kategori->template_variables ?? []);

$(document).ready(function() {
	var variableIndex = 0;
	var headerVariableIndex = 0;

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

	// Add header variable function
	function addHeaderVariable(name = '', label = '', type = 'text', required = false) {
		var selectedText = (type === 'text') ? 'selected' : '';
		var selectedTextarea = (type === 'textarea') ? 'selected' : '';
		var selectedDate = (type === 'date') ? 'selected' : '';
		var selectedNumber = (type === 'number') ? 'selected' : '';
		var checkedRequired = required ? 'checked' : '';
		
		var variableHtml = '<div class="variable-item" data-index="' + headerVariableIndex + '">' +
			'<div class="row">' +
				'<div class="col-md-3">' +
					'<label>Nama Variabel</label>' +
					'<input type="text" class="form-control header-variable-name" ' +
						   'name="header_variables[' + headerVariableIndex + '][name]" ' +
						   'value="' + name + '" placeholder="nomor_surat">' +
				'</div>' +
				'<div class="col-md-3">' +
					'<label>Label</label>' +
					'<input type="text" class="form-control header-variable-label" ' +
						   'name="header_variables[' + headerVariableIndex + '][label]" ' +
						   'value="' + label + '" placeholder="Nomor Surat">' +
				'</div>' +
				'<div class="col-md-2">' +
					'<label>Tipe</label>' +
					'<select class="form-control header-variable-type" name="header_variables[' + headerVariableIndex + '][type]">' +
						'<option value="text" ' + selectedText + '>Text</option>' +
						'<option value="textarea" ' + selectedTextarea + '>Textarea</option>' +
						'<option value="date" ' + selectedDate + '>Date</option>' +
						'<option value="number" ' + selectedNumber + '>Number</option>' +
					'</select>' +
				'</div>' +
				'<div class="col-md-2">' +
					'<label>Required</label>' +
					'<div class="form-check">' +
						'<input type="checkbox" class="form-check-input header-variable-required" ' +
							   'name="header_variables[' + headerVariableIndex + '][required]" ' +
							   'value="1" ' + checkedRequired + '>' +
						'<label class="form-check-label">Wajib</label>' +
					'</div>' +
				'</div>' +
				'<div class="col-md-2">' +
					'<label>&nbsp;</label>' +
					'<button type="button" class="btn btn-danger btn-sm d-block remove-header-variable">' +
						'<i class="fa fa-trash"></i> Hapus' +
					'</button>' +
				'</div>' +
			'</div>' +
		'</div>';
		
		$('#header-variables-container').append(variableHtml);
		headerVariableIndex++;
		updateHeaderVariableTags();
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

	// Update header variable tags
	function updateHeaderVariableTags() {
		var tags = $('#header-variable-tags');
		tags.empty();
		
		$('.header-variable-name').each(function() {
			var name = $(this).val();
			if (name && name.trim() !== '') {
				var tagHtml = '<span class="variable-tag" data-variable="' + name + '" data-target="header">{' + '{' + name + '}' + '}</span>';
				tags.append(tagHtml);
			}
		});
	}

	// Load existing variables
	if (window.existingVariables && window.existingVariables.length > 0) {
		window.existingVariables.forEach(function(variable) {
			addVariable(variable.name, variable.label, variable.type, variable.required);
		});
	} else {
		// Add default variables if none exist
		addVariable('nama_pemohon', 'Nama Pemohon', 'text', true);
		addVariable('alamat_pemohon', 'Alamat Pemohon', 'textarea', true);
		addVariable('tanggal_surat', 'Tanggal Surat', 'date', true);
	}

	// Load existing header variables
	var existingHeaderVariables = @json($kategori->header_variables ?? []);
	if (existingHeaderVariables && existingHeaderVariables.length > 0) {
		existingHeaderVariables.forEach(function(variable) {
			addHeaderVariable(variable.name, variable.label, variable.type, variable.required);
		});
	} else {
		// Add default header variables based on header type
		var headerType = $('#header_type').val();
		if (headerType === 'full') {
			addHeaderVariable('nomor_surat', 'Nomor Surat', 'text', true);
			addHeaderVariable('tujuan', 'Tujuan', 'text', true);
			addHeaderVariable('perihal', 'Perihal', 'text', true);
			addHeaderVariable('sifat', 'Sifat', 'text', false);
			addHeaderVariable('lampiran', 'Lampiran', 'text', false);
		} else {
			addHeaderVariable('nomor_surat', 'Nomor Surat', 'text', true);
			addHeaderVariable('nama_surat', 'Nama Surat', 'text', true);
		}
	}

	// Add variable button click
	$('#add-variable').click(function() {
		addVariable();
	});

	// Add header variable button click
	$('#add-header-variable').click(function() {
		addHeaderVariable();
	});

	// Remove variable
	$(document).on('click', '.remove-variable', function() {
		$(this).closest('.variable-item').remove();
		updateVariableTags();
	});

	// Remove header variable
	$(document).on('click', '.remove-header-variable', function() {
		$(this).closest('.variable-item').remove();
		updateHeaderVariableTags();
	});

	// Update tags when variable name changes
	$(document).on('input', '.variable-name', function() {
		updateVariableTags();
	});

	// Update header tags when header variable name changes
	$(document).on('input', '.header-variable-name', function() {
		updateHeaderVariableTags();
	});

	// Insert variable tag into template
	$(document).on('click', '.variable-tag', function() {
		var variable = $(this).data('variable');
		var target = $(this).data('target');
		var template = target === 'header' ? $('#header_template') : $('#template_surat');
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

	// Initial preview
	$('#preview-template').click();
});
</script>
@endsection