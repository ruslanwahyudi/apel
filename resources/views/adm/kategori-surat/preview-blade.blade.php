@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    
                </div>
                
                <div class="card-body">
                    @if($kategori->hasBladeTemplate())
                        
                        
                        <!-- Template Preview Container -->
                        <div class="template-preview" style="border: 2px solid #ddd; padding: 20px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                            {!! $template_html !!}
                        </div>
                        
                        
                    @else
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>Template Blade tidak ditemukan!</strong>
                            <br>
                            Template file: <code>{{ $kategori->blade_template_name ?? 'Tidak ada' }}</code>
                            <br>
                            Path: <code>resources/views/templates/surat/{{ $kategori->blade_template_name ?? 'tidak-ada' }}.blade.php</code>
                        </div>
                        
                        <div class="text-center">
                            <a href="{{ route('adm.kategori-surat.edit', $kategori) }}" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Buat Template Blade
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for template preview */
.template-preview {
    font-family: 'Times New Roman', serif;
    font-size: 12pt;
    line-height: 1.5;
    color: #000;
    background: white;
    min-height: 500px;
}

.template-preview img {
    max-width: 100px;
    height: auto;
}

.template-preview table {
    width: 100%;
    border-collapse: collapse;
}

.template-preview table td {
    padding: 5px 10px;
    vertical-align: top;
}

/* Print styles for preview */
@media print {
    .card-header,
    .card-tools,
    .alert,
    .mt-4,
    .btn {
        display: none !important;
    }
    
    .template-preview {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
}
</style>

<script>
$(document).ready(function() {
    // Add print functionality
    $(document).on('keydown', function(e) {
        if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
    });
});
</script>
@endsection 