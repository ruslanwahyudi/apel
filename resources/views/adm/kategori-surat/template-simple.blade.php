@extends('layouts.admin')

@section('css')
<style>
    .template-result {
        border: 1px solid #ddd;
        padding: 20px;
        background-color: #fff;
        min-height: 400px;
        white-space: pre;
        font-family: 'Times New Roman', serif;
        line-height: 1.6;
        font-size: 14px;
        overflow-x: auto;
    }
    .variable-input {
        margin-bottom: 15px;
    }
    .pdf-preview {
        border: 1px solid #ddd;
        min-height: 500px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Template Surat: {{ $kategori->nama }}</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item">Kategori Surat</li>
                    <li class="breadcrumb-item active">Template</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fa fa-edit"></i> Input Data</h5>
                </div>
                <div class="card-body">
                    @if(($kategori->template_type === 'pdf' && $kategori->pdf_form_fields) || 
                        ($kategori->template_type === 'docx' && $kategori->docx_form_fields) || 
                        (!empty($kategori->template_surat)))
                        <!-- Form Fields (PDF, DOCX, or Text Template) -->
                        <form id="templateForm">
                            @if($kategori->template_type === 'docx' && $kategori->docx_form_fields)
                                <!-- DOCX Form Fields -->
                                <div class="alert alert-success mb-3">
                                    <i class="fa fa-star"></i> <strong>Template DOCX</strong> - Akurasi 100%!
                                </div>
                                @foreach($kategori->docx_form_fields as $field)
                                    <div class="variable-input">
                                        <label for="{{ $field['field_name'] }}">
                                            {{ $field['label'] }}
                                            @if($field['required'])
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        
                                        @if($field['type'] === 'textarea')
                                            <textarea class="form-control" 
                                                      id="{{ $field['field_name'] }}" 
                                                      name="{{ $field['field_name'] }}"
                                                      rows="3"
                                                      {{ $field['required'] ? 'required' : '' }}
                                                      placeholder="Masukkan {{ strtolower($field['label']) }}"></textarea>
                                        @elseif($field['type'] === 'date')
                                            <input type="date" 
                                                   class="form-control" 
                                                   id="{{ $field['field_name'] }}" 
                                                   name="{{ $field['field_name'] }}"
                                                   {{ $field['required'] ? 'required' : '' }}>
                                        @elseif($field['type'] === 'number')
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="{{ $field['field_name'] }}" 
                                                   name="{{ $field['field_name'] }}"
                                                   {{ $field['required'] ? 'required' : '' }}
                                                   placeholder="Masukkan {{ strtolower($field['label']) }}">
                                        @else
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="{{ $field['field_name'] }}" 
                                                   name="{{ $field['field_name'] }}"
                                                   {{ $field['required'] ? 'required' : '' }}
                                                   placeholder="Masukkan {{ strtolower($field['label']) }}">
                                        @endif
                                    </div>
                                @endforeach
                            @elseif($kategori->template_type === 'pdf' && $kategori->pdf_form_fields)
                                <!-- PDF Form Fields -->
                                @foreach($kategori->pdf_form_fields as $field)
                                    <div class="variable-input">
                                        <label for="{{ $field['field_name'] }}">
                                            {{ $field['label'] }}
                                        </label>
                                        
                                        @if($field['type'] === 'textarea')
                                            <textarea class="form-control" 
                                                      id="{{ $field['field_name'] }}" 
                                                      name="{{ $field['field_name'] }}"
                                                      rows="3"
                                                      placeholder="Masukkan {{ strtolower($field['label']) }}"></textarea>
                                        @elseif($field['type'] === 'date')
                                            <input type="date" 
                                                   class="form-control" 
                                                   id="{{ $field['field_name'] }}" 
                                                   name="{{ $field['field_name'] }}">
                                        @elseif($field['type'] === 'number')
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="{{ $field['field_name'] }}" 
                                                   name="{{ $field['field_name'] }}"
                                                   placeholder="Masukkan {{ strtolower($field['label']) }}">
                                        @else
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="{{ $field['field_name'] }}" 
                                                   name="{{ $field['field_name'] }}"
                                                   placeholder="Masukkan {{ strtolower($field['label']) }}">
                                        @endif
                                    </div>
                                @endforeach
                            @elseif($kategori->template_variables && count($kategori->template_variables) > 0)
                                <!-- Text Template Variables -->
                                @foreach($kategori->template_variables as $variable)
                                    <div class="variable-input">
                                        <label for="{{ $variable['name'] }}">
                                            {{ $variable['label'] }}
                                            @if($variable['required'])
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        
                                        @if($variable['type'] === 'textarea')
                                            <textarea class="form-control" 
                                                      id="{{ $variable['name'] }}" 
                                                      name="{{ $variable['name'] }}"
                                                      rows="3"
                                                      {{ $variable['required'] ? 'required' : '' }}
                                                      placeholder="Masukkan {{ strtolower($variable['label']) }}"></textarea>
                                        @elseif($variable['type'] === 'date')
                                            <input type="date" 
                                                   class="form-control" 
                                                   id="{{ $variable['name'] }}" 
                                                   name="{{ $variable['name'] }}"
                                                   {{ $variable['required'] ? 'required' : '' }}>
                                        @elseif($variable['type'] === 'number')
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="{{ $variable['name'] }}" 
                                                   name="{{ $variable['name'] }}"
                                                   {{ $variable['required'] ? 'required' : '' }}
                                                   placeholder="Masukkan {{ strtolower($variable['label']) }}">
                                        @else
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="{{ $variable['name'] }}" 
                                                   name="{{ $variable['name'] }}"
                                                   {{ $variable['required'] ? 'required' : '' }}
                                                   placeholder="Masukkan {{ strtolower($variable['label']) }}">
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                            
                            <button type="button" class="btn btn-primary btn-block" id="generateTemplate">
                                <i class="fa fa-magic"></i> Generate PDF
                            </button>
                        </form>
                    @elseif($kategori->template_variables && count($kategori->template_variables) > 0)
                        <!-- Text Template Form -->
                        <form id="templateForm">
                            @foreach($kategori->template_variables as $variable)
                                <div class="variable-input">
                                    <label for="{{ $variable['name'] }}">
                                        {{ $variable['label'] }}
                                        @if($variable['required'])
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    
                                    @if($variable['type'] === 'textarea')
                                        <textarea class="form-control" 
                                                  id="{{ $variable['name'] }}" 
                                                  name="{{ $variable['name'] }}"
                                                  rows="3"
                                                  {{ $variable['required'] ? 'required' : '' }}
                                                  placeholder="Masukkan {{ strtolower($variable['label']) }}"></textarea>
                                    @elseif($variable['type'] === 'date')
                                        <input type="date" 
                                               class="form-control" 
                                               id="{{ $variable['name'] }}" 
                                               name="{{ $variable['name'] }}"
                                               {{ $variable['required'] ? 'required' : '' }}>
                                    @elseif($variable['type'] === 'number')
                                        <input type="number" 
                                               class="form-control" 
                                               id="{{ $variable['name'] }}" 
                                               name="{{ $variable['name'] }}"
                                               {{ $variable['required'] ? 'required' : '' }}
                                               placeholder="Masukkan {{ strtolower($variable['label']) }}">
                                    @else
                                        <input type="text" 
                                               class="form-control" 
                                               id="{{ $variable['name'] }}" 
                                               name="{{ $variable['name'] }}"
                                               {{ $variable['required'] ? 'required' : '' }}
                                               placeholder="Masukkan {{ strtolower($variable['label']) }}">
                                    @endif
                                </div>
                            @endforeach
                            
                            <button type="button" class="btn btn-primary btn-block" id="generateTemplate">
                                <i class="fa fa-magic"></i> Generate Template
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                            Template ini belum memiliki variabel yang didefinisikan.
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('adm.kategori-surat.edit-simple', $kategori) }}" class="btn btn-warning btn-block">
                            <i class="fa fa-edit"></i> Edit Template
                        </a>
                        <a href="{{ route('adm.kategori-surat') }}" class="btn btn-secondary btn-block">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fa fa-file-text"></i> 
                        @if($kategori->template_type === 'docx')
                            Hasil DOCX Template ⭐
                        @elseif($kategori->template_type === 'pdf')
                            Hasil PDF Template
                        @else
                            Hasil Template
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($kategori->template_type === 'docx')
                        <!-- DOCX Template Result -->
                        <div class="pdf-preview" id="docxPreview">
                            @if($kategori->docx_template_path)
                                <div class="text-center">
                                    <i class="fa fa-file-word-o fa-3x text-success mb-3"></i>
                                    <h5>Template DOCX ⭐</h5>
                                    <p class="text-muted">Isi form di sebelah kiri untuk generate PDF dari template DOCX</p>
                                    <div class="alert alert-info">
                                        <strong>Keunggulan DOCX:</strong> Variabel akan diganti dengan akurasi 100%!
                                    </div>
                                    <a href="{{ asset('storage/' . $kategori->docx_template_path) }}" target="_blank" class="btn btn-outline-success">
                                        <i class="fa fa-download"></i> Download Template DOCX
                                    </a>
                                </div>
                            @else
                                <div class="text-center text-muted">
                                    <i class="fa fa-file-o fa-3x mb-3"></i>
                                    <p>Template DOCX belum diupload.</p>
                                </div>
                            @endif
                        </div>
                    @elseif($kategori->template_type === 'pdf')
                        <!-- PDF Template Result -->
                        <div class="pdf-preview" id="pdfPreview">
                            @if($kategori->pdf_template_path)
                                <div class="text-center">
                                    <i class="fa fa-file-pdf-o fa-3x text-muted mb-3"></i>
                                    <h5>Template PDF</h5>
                                    <p class="text-muted">Isi form di sebelah kiri untuk generate PDF</p>
                                    <a href="{{ asset('storage/' . $kategori->pdf_template_path) }}" target="_blank" class="btn btn-outline-primary">
                                        <i class="fa fa-eye"></i> Lihat Template Asli
                                    </a>
                                </div>
                            @else
                                <div class="text-center text-muted">
                                    <i class="fa fa-file-o fa-3x mb-3"></i>
                                    <p>Template PDF belum diupload.</p>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Text Template Result -->
                        <div class="template-result" id="templateResult">
                            @if($kategori->template_surat)
                                {{ $kategori->template_surat }}
                            @else
                                <div class="text-center text-muted">
                                    <i class="fa fa-file-o fa-3x mb-3"></i>
                                    <p>Template belum dibuat. Silakan edit kategori surat untuk menambahkan template.</p>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    @if(($kategori->template_type === 'docx' && $kategori->docx_template_path) || 
                        ($kategori->template_type === 'pdf' && $kategori->pdf_template_path) || 
                        ($kategori->template_type === 'text' && $kategori->template_surat))
                        <div class="mt-3">
                            @if($kategori->template_type === 'docx')
                                <button type="button" class="btn btn-success" id="downloadPdf">
                                    <i class="fa fa-download"></i> Generate PDF dari DOCX
                                </button>
                            @elseif($kategori->template_type === 'pdf')
                                <button type="button" class="btn btn-success" id="downloadPdf">
                                    <i class="fa fa-download"></i> Download PDF
                                </button>
                            @else
                                <button type="button" class="btn btn-success" id="printTemplate">
                                    <i class="fa fa-print"></i> Print
                                </button>
                                <button type="button" class="btn btn-info" id="copyTemplate">
                                    <i class="fa fa-copy"></i> Copy
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden data for JavaScript -->
<div id="template-data" 
     data-type="{{ $kategori->template_type ?? 'text' }}"
     data-template="{{ base64_encode($kategori->template_surat ?? '') }}"
     data-variables="{{ base64_encode(json_encode($kategori->template_variables ?? [])) }}"
     data-kategori-nama="{{ $kategori->nama }}"
     data-kategori-id="{{ $kategori->id }}"
     style="display: none;">
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Get data from hidden div
    var templateDataDiv = document.getElementById('template-data');
    var templateType = templateDataDiv.getAttribute('data-type');
    var originalTemplate = atob(templateDataDiv.getAttribute('data-template'));
    var templateVariables = JSON.parse(atob(templateDataDiv.getAttribute('data-variables')));
    var kategoriNama = templateDataDiv.getAttribute('data-kategori-nama');
    var kategoriId = templateDataDiv.getAttribute('data-kategori-id');
    
    if (templateType === 'text') {
        // Text template handling
        function replaceTemplateVariables(template, formData) {
            if (templateVariables && templateVariables.length > 0) {
                templateVariables.forEach(function(variable) {
                    var value = formData.get(variable.name) || '[' + variable.label + ']';
                    var regex = new RegExp('\\{\\{' + variable.name + '\\}\\}', 'g');
                    template = template.replace(regex, value);
                });
            }
            return template;
        }

        $('#generateTemplate').click(function() {
            var template = originalTemplate;
            var formData = new FormData(document.getElementById('templateForm'));
            template = replaceTemplateVariables(template, formData);
            document.getElementById('templateResult').textContent = template;
        });
        
        // Auto-generate on input change
        $('#templateForm input, #templateForm textarea').on('input change', function() {
            $('#generateTemplate').click();
        });
        
        // Print functionality
        $('#printTemplate').click(function() {
            var template = originalTemplate;
            var formData = new FormData(document.getElementById('templateForm'));
            template = replaceTemplateVariables(template, formData);
            
            var printWindow = window.open('', '_blank');
            printWindow.document.write(
                '<html>' +
                    '<head>' +
                        '<title>' + kategoriNama + '</title>' +
                        '<style>' +
                            'body { ' +
                                'font-family: "Times New Roman", serif; ' +
                                'line-height: 1.6; ' +
                                'margin: 20px;' +
                                'font-size: 14px;' +
                                'white-space: pre;' +
                            '}' +
                            '@media print {' +
                                'body { ' +
                                    'margin: 15px;' +
                                    'white-space: pre;' +
                                '}' +
                            '}' +
                        '</style>' +
                    '</head>' +
                    '<body>' + template + '</body>' +
                '</html>'
            );
            printWindow.document.close();
            printWindow.print();
        });
        
        // Copy functionality
        $('#copyTemplate').click(function() {
            var template = originalTemplate;
            var formData = new FormData(document.getElementById('templateForm'));
            template = replaceTemplateVariables(template, formData);
            
            navigator.clipboard.writeText(template).then(function() {
                alert('Template berhasil disalin ke clipboard!');
            }).catch(function() {
                var textArea = document.createElement('textarea');
                textArea.value = template;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('Template berhasil disalin ke clipboard!');
            });
        });
        
        // Set default values for date fields
        $('input[type="date"]').each(function() {
            if (!$(this).val()) {
                $(this).val(new Date().toISOString().split('T')[0]);
            }
        });
        
        // Initial generation
        if ($('#templateForm input, #templateForm textarea').length > 0) {
            $('#generateTemplate').click();
        }
        
    }
    
    // Unified PDF generation for text, PDF, and DOCX templates
    $('#generateTemplate').click(function() {
        var formData = new FormData(document.getElementById('templateForm'));
        
        // Show loading
        var previewElement = templateType === 'docx' ? '#docxPreview' : 
                           templateType === 'pdf' ? '#pdfPreview' : '#templateResult';
        
        var loadingMessage = templateType === 'docx' ? 'Generating PDF from DOCX...' : 'Generating PDF...';
        $(previewElement).html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><p>' + loadingMessage + '</p></div>');
        
        // Send AJAX request to generate PDF
        $.ajax({
            url: '/adm/kategori-surat/' + kategoriId + '/generate-pdf',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('PDF Generation Response:', response);
                if (response.success) {
                    var filledDataHtml = '';
                    if (response.filled_data && response.filled_data.length > 0) {
                        filledDataHtml = '<div class="mt-3"><h6>Data yang diisi:</h6><ul class="list-unstyled">';
                        response.filled_data.forEach(function(item) {
                            if (item && typeof item === 'object') {
                                var status = item.filled ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>';
                                var label = item.label || 'Field';
                                var value = item.value || '[Kosong]';
                                filledDataHtml += '<li>' + status + ' ' + label + ': ' + value + '</li>';
                            }
                        });
                        filledDataHtml += '</ul></div>';
                    }
                    
                    var successHtml = '<div class="text-center">' +
                        '<i class="fa fa-file-pdf-o fa-3x text-success mb-3"></i>' +
                        '<h5>Dokumen Berhasil Di-generate!</h5>' +
                        '<p class="text-muted">' + response.message + '</p>';
                    
                    // Add specific info for DOCX template
                    if (response.template_type === 'docx') {
                        successHtml += '<div class="alert alert-success mt-3">' +
                            '<h6><i class="fa fa-star"></i> Template DOCX Berhasil Diproses - Akurasi 100%!</h6>' +
                            '<p class="mb-2">Sistem telah mengganti variabel dalam template DOCX dengan data yang Anda input:</p>' +
                            '<ul class="text-left">';
                        
                        // Show filled data summary if available
                        if (response.filled_data && response.filled_data.length > 0) {
                            response.filled_data.forEach(function(item) {
                                if (item.filled && item.field && item.value) {
                                    successHtml += '<li>${' + item.field + '} → ' + item.value + '</li>';
                                }
                            });
                        } else {
                            successHtml += '<li>${nomor} → Nomor yang diisi</li>' +
                                '<li>${tanggal} → Tanggal yang dipilih</li>' +
                                '<li>${nama} → Nama yang diinput</li>' +
                                '<li>Dan variabel lainnya...</li>';
                        }
                        
                        successHtml += '</ul>' +
                            '<p class="mb-0"><strong>✅ DOCX Template memberikan hasil terbaik dengan akurasi 100%!</strong></p>' +
                        '</div>';
                    }
                    // Add specific info for PDF template
                    else if (response.template_type === 'pdf' && response.original_template_url) {
                        successHtml += '<div class="alert alert-success mt-3">' +
                            '<h6><i class="fa fa-check-circle"></i> Variabel PDF Berhasil Diganti</h6>' +
                            '<p class="mb-2">Sistem telah mengganti variabel dalam template PDF dengan data yang Anda input:</p>' +
                            '<ul class="text-left">';
                        
                        // Show filled data summary if available
                        if (response.filled_data && response.filled_data.length > 0) {
                            response.filled_data.forEach(function(item) {
                                if (item.filled && item.field && item.value) {
                                    successHtml += '<li>{{' + item.field + '}} → ' + item.value + '</li>';
                                }
                            });
                        } else {
                            successHtml += '<li> → Nilai yang diisi</li>' +
                                '<li> → Tanggal yang dipilih</li>' +
                                '<li> → Nama yang diinput</li>' +
                                '<li>Dan variabel lainnya...</li>';
                        }
                        
                        successHtml += '</ul>' +
                            '<p class="mb-0"><strong>PDF yang di-generate sudah berisi data Anda, bukan lagi variabel kosong.</strong></p>' +
                        '</div>';
                    } else {
                        successHtml += '<p class="text-info"><small>Template diproses dengan variabel yang terisi</small></p>';
                    }
                    
                    successHtml += filledDataHtml +
                        '<div class="mt-3">' +
                            '<a href="' + response.pdf_url + '" target="_blank" class="btn btn-primary">' +
                                '<i class="fa fa-eye"></i> Lihat PDF' +
                            '</a>' +
                            '<a href="' + response.pdf_url + '" download="' + (response.filename || 'dokumen.pdf') + '" class="btn btn-success ml-2">' +
                                '<i class="fa fa-download"></i> Download PDF' +
                            '</a>' +
                        '</div>' +
                        '<div class="mt-2">' +
                            '<small class="text-muted">File: ' + (response.filename || 'dokumen.pdf') + '</small>' +
                        '</div>' +
                    '</div>';
                    
                    $(previewElement).html(successHtml);
                } else {
                    $(previewElement).html(
                        '<div class="text-center text-danger">' +
                            '<i class="fa fa-exclamation-triangle fa-3x mb-3"></i>' +
                            '<h5>Error generating PDF</h5>' +
                            '<p>' + response.message + '</p>' +
                        '</div>'
                    );
                }
            },
            error: function() {
                $(previewElement).html(
                    '<div class="text-center text-danger">' +
                        '<i class="fa fa-exclamation-triangle fa-3x mb-3"></i>' +
                        '<h5>Error generating PDF</h5>' +
                        '<p>Terjadi kesalahan saat generate PDF</p>' +
                    '</div>'
                );
            }
        });
    });
    
    // Download PDF button
    $('#downloadPdf').click(function() {
        $('#generateTemplate').click();
    });
    
    // Set default values for date fields
    $('input[type="date"]').each(function() {
        if (!$(this).val()) {
            $(this).val(new Date().toISOString().split('T')[0]);
        }
    });
});
</script>
@endsection 