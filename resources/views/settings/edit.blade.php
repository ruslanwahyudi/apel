@extends('layouts.admin')

@section('css')
<style>
    .preview-image {
        max-width: 200px;
        margin-top: 10px;
        border-radius: 5px;
    }

    .preview-favicon {
        max-width: 32px;
        margin-top: 10px;
    }
</style>
<link href="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Pengaturan Umum</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Pengaturan</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fa fa-cogs"></i> Pengaturan Aplikasi</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-4">Informasi Dasar</h4>
                                
                                <div class="form-group">
                                    <label for="app_name">Nama Aplikasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('app_name') is-invalid @enderror" 
                                           id="app_name" name="app_name" value="{{ old('app_name', $setting->app_name) }}" required>
                                    @error('app_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="app_logo">Logo Aplikasi</label>
                                    <input type="file" class="form-control @error('app_logo') is-invalid @enderror" 
                                           id="app_logo" name="app_logo" accept="image/*">
                                    <small class="form-text text-muted">
                                        Format: JPG, JPEG, PNG, GIF. Maksimal 2MB.
                                    </small>
                                    @if($setting->logo_url)
                                        <img src="{{ $setting->logo_url }}" alt="Logo" class="preview-image" id="logo-preview">
                                    @endif
                                    @error('app_logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="app_favicon">Favicon</label>
                                    <input type="file" class="form-control @error('app_favicon') is-invalid @enderror" 
                                           id="app_favicon" name="app_favicon" accept="image/x-icon,image/png">
                                    <small class="form-text text-muted">
                                        Format: ICO, PNG. Maksimal 1MB.
                                    </small>
                                    @if($setting->favicon_url)
                                        <img src="{{ $setting->favicon_url }}" alt="Favicon" class="preview-favicon" id="favicon-preview">
                                    @endif
                                    @error('app_favicon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="footer_text">Teks Footer</label>
                                    <textarea class="form-control @error('footer_text') is-invalid @enderror" 
                                              id="footer_text" name="footer_text" rows="3">{{ old('footer_text', $setting->footer_text) }}</textarea>
                                    @error('footer_text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h4 class="mb-4">Informasi Kontak</h4>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $setting->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="phone">Nomor Telepon</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $setting->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="address">Alamat</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3">{{ old('address', $setting->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <h4 class="mb-4 mt-5">Media Sosial</h4>

                                <div class="form-group">
                                    <label for="facebook_url">Facebook URL</label>
                                    <input type="url" class="form-control @error('facebook_url') is-invalid @enderror" 
                                           id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $setting->facebook_url) }}">
                                    @error('facebook_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="instagram_url">Instagram URL</label>
                                    <input type="url" class="form-control @error('instagram_url') is-invalid @enderror" 
                                           id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $setting->instagram_url) }}">
                                    @error('instagram_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="youtube_url">Youtube URL</label>
                                    <input type="url" class="form-control @error('youtube_url') is-invalid @enderror" 
                                           id="youtube_url" name="youtube_url" value="{{ old('youtube_url', $setting->youtube_url) }}">
                                    @error('youtube_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-4">Setting Penomoran Surat</h4>

                                <div class="form-group">
                                    <label for="no_surat">Nomor Surat</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Register</span>
                                        </div>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">/</span>
                                        </div>
                                        <input type="text" class="form-control @error('no_surat') is-invalid @enderror" 
                                               id="no_surat" name="no_surat" value="{{ old('no_surat', $setting->no_surat) }}">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">/</span>
                                        </div>
                                               <div class="input-group-append">
                                            <span class="input-group-text">Bulan</span>
                                        </div>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">/</span>
                                        </div>
                                        <div class="input-group-append">
                                            <span class="input-group-text">Tahun</span>
                                        </div>
                                    </div>  
                                    @error('no_surat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Tambahkan section baru untuk Disclaimer dan FAQ -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h4 class="mb-4">Disclaimer & FAQ</h4>
                                
                                <div class="form-group">
                                    <label for="disclaimer">Disclaimer Text</label>
                                    <textarea class="form-control editor @error('disclaimer') is-invalid @enderror" 
                                              id="disclaimer" name="disclaimer">{{ old('disclaimer', $setting->disclaimer) }}</textarea>
                                    <small class="form-text text-muted">
                                        Teks disclaimer yang akan ditampilkan di halaman privacy policy
                                    </small>
                                    @error('disclaimer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="faq">FAQ Text</label>
                                    <textarea class="form-control editor @error('faq') is-invalid @enderror" 
                                              id="faq" name="faq">{{ old('faq', $setting->faq) }}</textarea>
                                    <small class="form-text text-muted">
                                        Gunakan format yang sesuai untuk FAQ
                                    </small>
                                    @error('faq')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="privacy_policy">Privacy Policy Text</label>
                                    <textarea class="form-control editor @error('privacy_policy') is-invalid @enderror" 
                                              id="privacy_policy" name="privacy_policy">{{ old('privacy_policy', $setting->privacy_policy) }}</textarea>
                                    @error('privacy_policy')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
$(document).ready(function() {
    // Logo preview
    $('#app_logo').change(function() {
        const file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function(event) {
                $('#logo-preview').attr('src', event.target.result);
            }
            reader.readAsDataURL(file);
        }
    });

    // Favicon preview
    $('#app_favicon').change(function() {
        const file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function(event) {
                $('#favicon-preview').attr('src', event.target.result);
            }
            reader.readAsDataURL(file);
        }
    });

    // Initialize CKEditor
    $('.editor').each(function() {
        CKEDITOR.replace(this, {
            height: 300,
            removeButtons: 'Image',
            toolbarGroups: [
                { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
                { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
                { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
                { name: 'forms', groups: [ 'forms' ] },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
                { name: 'links', groups: [ 'links' ] },
                '/',
                { name: 'styles', groups: [ 'styles' ] },
                { name: 'colors', groups: [ 'colors' ] },
                { name: 'tools', groups: [ 'tools' ] },
                { name: 'others', groups: [ 'others' ] }
            ]
        });
    });
});
</script>
@endsection 