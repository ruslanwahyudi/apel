@extends('layouts.frontend')

@section('konten')
    <!-- Page Header -->
    <header class="page-header" style="background-image: url('{{ asset($profil->foto_kantor) }}')">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Disclaimer</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Disclaimer</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- Disclaimer Section -->
    <section class="disclaimer-section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="disclaimer-content">
                        <h2>Disclaimer Aplikasi Desa Banyupelle</h2>
                        <p class="last-updated">Terakhir diperbarui: {{ date('d F Y') }}</p>

                        {!! $setting->disclaimer !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .page-header {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                    url('{{ asset("assets/images/contact-header.jpg") }}') no-repeat center center;
        background-size: cover;
        padding: 150px 0 80px;
        color: white;
        margin-bottom: 80px;
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
    }

    .breadcrumb-item a {
        color: #fff;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: rgba(255, 255, 255, 0.8);
    }

    .contact-section {
        padding: 0 0 80px;
    }

    .contact-info {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 30px;
    }

    .info-item .icon {
        width: 50px;
        height: 50px;
        background: #3498db;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        color: white;
        font-size: 1.25rem;
    }

    .info-item .content h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 5px;
        color: #2c3e50;
    }

    .info-item .content p {
        margin: 0;
        color: #6c757d;
    }

    .social-links {
        margin-top: 40px;
    }

    .social-links h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    .social-icons {
        display: flex;
        gap: 15px;
    }

    .social-icons a {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }

    .social-icons .facebook {
        background: #3b5998;
    }

    .social-icons .instagram {
        background: #e1306c;
    }

    .social-icons .youtube {
        background: #ff0000;
    }

    .social-icons a:hover {
        transform: translateY(-3px);
    }

    .contact-form {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .contact-form h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 30px;
        color: #2c3e50;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: 500;
        margin-bottom: 8px;
        color: #2c3e50;
    }

    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    .btn-primary {
        padding: 12px 30px;
        border-radius: 50px;
        background: #3498db;
        border: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: #2c3e50;
        transform: translateY(-2px);
    }

    .map-container {
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .map-placeholder {
        background: #f8f9fa;
        padding: 40px;
        text-align: center;
        color: #6c757d;
        border-radius: 10px;
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 120px 0 60px;
            margin-bottom: 40px;
        }

        .contact-section {
            padding: 0 0 40px;
        }

        .contact-info, .contact-form {
            padding: 30px;
        }

        .info-item {
            margin-bottom: 20px;
        }

        .social-links {
            margin-top: 30px;
        }
    }

    .privacy-policy-section {
        padding: 80px 0;
    }

    .privacy-policy-content {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .privacy-policy-content h2 {
        color: #2c3e50;
        margin-bottom: 20px;
        font-weight: 700;
    }

    .last-updated {
        color: #6c757d;
        font-style: italic;
        margin-bottom: 30px;
    }

    .section {
        margin-bottom: 40px;
    }

    .section h3 {
        color: #2c3e50;
        font-size: 1.5rem;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .section p {
        color: #4a5568;
        line-height: 1.7;
        margin-bottom: 15px;
    }

    .section ul {
        padding-left: 20px;
        margin-bottom: 15px;
    }

    .section ul li {
        color: #4a5568;
        margin-bottom: 10px;
        line-height: 1.6;
    }

    .contact-info {
        list-style: none;
        padding: 0;
    }

    .contact-info li {
        margin-bottom: 10px;
        color: #4a5568;
    }

    @media (max-width: 768px) {
        .privacy-policy-section {
            padding: 40px 0;
        }

        .privacy-policy-content {
            padding: 20px;
        }

        .section {
            margin-bottom: 30px;
        }
    }
</style>
@endpush
