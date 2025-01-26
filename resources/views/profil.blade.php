@extends('layouts.frontend')

@section('konten')
<!-- Page Header -->
<header class="page-header" style="background-image: url('{{ asset($profil->foto_kantor) }}')">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>Profil Desa</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profil</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</header>

<!-- Profile Section -->
<section class="profile-section">
    <div class="container">
        <!-- Overview -->
        <div class="row mb-5">
            <div class="col-lg-4">
                <div class="profile-content text-justify">
                    <h2 class="section-title">Sejarah Desa</h2>
                    <div class="content-text text-justify">
                        {!! $profil->sejarah !!}
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <!-- Vision & Mission -->
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="vision-mission-box">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="vision-box">
                                        <h3><i class="fas fa-eye"></i> Visi</h3>
                                        <p>{!! $profil->visi !!}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mission-box">
                                        <h3><i class="fas fa-bullseye"></i> Misi</h3>
                                        <div class="mission-list">
                                            @if(isset($profil->misi))
                                            {!! $profil->misi !!}
                                            @else
                                            <p>Misi desa belum ditambahkan</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Statistics -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="section-title text-center mb-4">Data Statistik Desa</h2>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <i class="fas fa-users"></i>
                    <h4>Total Penduduk</h4>
                    <div class="stat-number">{{ number_format($profil->jumlah_penduduk ?? 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <i class="fas fa-male"></i>
                    <h4>Laki-laki</h4>
                    <div class="stat-number">{{ number_format($profil->jumlah_laki ?? 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <i class="fas fa-female"></i>
                    <h4>Perempuan</h4>
                    <div class="stat-number">{{ number_format($profil->jumlah_perempuan ?? 0) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box">
                    <i class="fas fa-home"></i>
                    <h4>Jumlah KK</h4>
                    <div class="stat-number">{{ number_format($profil->jumlah_kk ?? 0) }}</div>
                </div>
            </div>
        </div>

        <!-- Village Structure -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="section-title text-center mb-4">Struktur Pemerintahan Desa</h2>
                <div class="structure-box">
                    <img src="{{ asset($profil->struktur_organisasi ?? 'assets/images/default-structure.jpg') }}"
                        alt="Struktur Organisasi" class="img-fluid">
                </div>
            </div>
        </div>

        
    </div>
</section>
@endsection

@push('styles')
<style>
    .page-header {
        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('/assets/images/profile-header.jpg');
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        padding: 150px 0 80px;
        margin-bottom: 60px;
        position: relative;
        color: #fff;
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: #fff;
    }

    .page-header .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .page-header .breadcrumb-item,
    .page-header .breadcrumb-item a {
        color: #fff;
    }

    .page-header .breadcrumb-item.active {
        color: rgba(255, 255, 255, 0.8);
    }

    .page-header .breadcrumb-item+.breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.8);
    }

    .profile-section {
        padding: 0 0 80px;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 30px;
        color: #2c3e50;
    }

    .profile-content {
        padding-right: 30px;
    }

    .content-text {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #555;
    }

    .profile-image img {
        width: 100%;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .vision-mission-box {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .vision-box,
    .mission-box {
        padding: 30px;
        height: 100%;
    }

    .vision-box h3,
    .mission-box h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    .vision-box i,
    .mission-box i {
        color: #3498db;
        margin-right: 10px;
    }

    .mission-list ul {
        padding-left: 20px;
    }

    .mission-list li {
        margin-bottom: 10px;
        color: #555;
    }

    .stat-box {
        background: white;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
    }

    .stat-box:hover {
        transform: translateY(-5px);
    }

    .stat-box i {
        font-size: 2.5rem;
        color: #3498db;
        margin-bottom: 15px;
    }

    .stat-box h4 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: #2c3e50;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #3498db;
    }

    .structure-box {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .map-box {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
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
        }

        .profile-content {
            padding-right: 0;
            margin-bottom: 30px;
        }

        .vision-box,
        .mission-box {
            margin-bottom: 30px;
        }

        .stat-box {
            margin-bottom: 30px;
        }
    }
</style>
@endpush