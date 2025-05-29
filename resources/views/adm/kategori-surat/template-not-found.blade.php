@extends('layouts.app')

@section('title', 'Template Tidak Ditemukan - ' . $kategori->nama)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa fa-exclamation-triangle text-warning"></i> Template Tidak Ditemukan
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('adm.kategori-surat') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <div class="card-body text-center">
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle fa-3x mb-3"></i>
                        <h4>Template Blade Tidak Ditemukan</h4>
                        <p>Template untuk kategori surat <strong>{{ $kategori->nama }}</strong> belum dibuat.</p>
                        
                        @if($kategori->blade_template_name)
                            <p class="mb-0">
                                <strong>File yang diharapkan:</strong><br>
                                <code>resources/views/templates/surat/{{ $kategori->blade_template_name }}.blade.php</code>
                            </p>
                        @else
                            <p class="mb-0">
                                <strong>Nama template belum dikonfigurasi.</strong>
                            </p>
                        @endif
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('adm.kategori-surat.edit', $kategori) }}" class="btn btn-primary btn-lg">
                            <i class="fa fa-edit"></i> Edit Kategori & Buat Template
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 