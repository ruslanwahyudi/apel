@extends('layouts.admin')

@section('css')
<style>
    .template-form {
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 5px;
        background-color: #f9f9f9;
        margin-bottom: 20px;
    }
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
                    @if($kategori->template_variables && count($kategori->template_variables) > 0)
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
                        <a href="{{ route('adm.kategori-surat.edit', $kategori) }}" class="btn btn-warning btn-block">
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
                    <h5><i class="fa fa-file-text"></i> Hasil Template</h5>
                </div>
                <div class="card-body">
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
                    
                    @if($kategori->template_surat)
                        <div class="mt-3">
                            <button type="button" class="btn btn-success" id="printTemplate">
                                <i class="fa fa-print"></i> Print
                            </button>
                            <button type="button" class="btn btn-info" id="copyTemplate">
                                <i class="fa fa-copy"></i> Copy
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- Hidden data elements -->
<div id="template-data" 
     data-template="{{ base64_encode($kategori->template_surat ?? '') }}"
     data-variables="{{ base64_encode(json_encode($kategori->template_variables ?? [])) }}"
     style="display: none;"></div>

<script>
$(document).ready(function() {
    // Get template data from hidden element
    var templateData = $('#template-data');
    var originalTemplate = atob(templateData.data('template'));
    var templateVariables = JSON.parse(atob(templateData.data('variables')));
    
    // Function to format template preserving original spacing
    function formatTemplate(template) {
        // Simply preserve all whitespace and convert newlines to <br>
        return template.replace(/\n/g, '<br>');
    }
    
    // Generate template with user input
    $('#generateTemplate').click(function() {
        var template = originalTemplate;
        var formData = new FormData($('#templateForm')[0]);
        
        // Replace variables with form values
        if (templateVariables && templateVariables.length > 0) {
            templateVariables.forEach(function(variable) {
                var value = formData.get(variable.name) || '[' + variable.label + ']';
                var regex = new RegExp('\\{\\{' + variable.name + '\\}\\}', 'g');
                template = template.replace(regex, value);
            });
        }
        
                                $('#templateResult').html(formatTemplate(template));
    });
    
    // Auto-generate on input change
    $('#templateForm input, #templateForm textarea').on('input change', function() {
        $('#generateTemplate').click();
    });
    
    // Print functionality
    $('#printTemplate').click(function() {
        // Get the original template with variables replaced
        var template = originalTemplate;
        var formData = new FormData($('#templateForm')[0]);
        
        // Replace variables with form values
        if (templateVariables && templateVariables.length > 0) {
            templateVariables.forEach(function(variable) {
                var value = formData.get(variable.name) || '[' + variable.label + ']';
                var regex = new RegExp('\\{\\{' + variable.name + '\\}\\}', 'g');
                template = template.replace(regex, value);
            });
        }
        
        var printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>{{ $kategori->nama }}</title>
                    <style>
                        body { 
                            font-family: 'Times New Roman', serif; 
                            line-height: 1.6; 
                            margin: 20px;
                            font-size: 14px;
                            white-space: pre;
                        }
                        @media print {
                            body { 
                                margin: 15px;
                                white-space: pre;
                            }
                        }
                    </style>
                </head>
                <body>${template}</body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    });
    
    // Copy functionality
    $('#copyTemplate').click(function() {
        // Get the original template with variables replaced
        var template = originalTemplate;
        var formData = new FormData($('#templateForm')[0]);
        
        // Replace variables with form values
        if (templateVariables && templateVariables.length > 0) {
            templateVariables.forEach(function(variable) {
                var value = formData.get(variable.name) || '[' + variable.label + ']';
                var regex = new RegExp('\\{\\{' + variable.name + '\\}\\}', 'g');
                template = template.replace(regex, value);
            });
        }
        
        // Copy the template with original spacing preserved
        navigator.clipboard.writeText(template).then(function() {
            alert('Template berhasil disalin ke clipboard!');
        }).catch(function() {
            // Fallback for older browsers
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
    
    // Initial load - show formatted template
    if (originalTemplate && originalTemplate.trim() !== '') {
        $('#templateResult').html(formatTemplate(originalTemplate));
    }
    
    // Initial generation if form has values
    if ($('#templateForm input, #templateForm textarea').length > 0) {
        $('#generateTemplate').click();
    }
});
</script>
@endsection 