@extends('layouts.frontend')

@section('konten')
    <!-- Page Header -->
    <header class="page-header" style="background-image: url('{{ asset($profil->foto_kantor) }}')">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Galeri</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Galeri</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="container">
            <!-- Filter Buttons -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="filter-buttons">
                        <button class="btn btn-filter active" data-filter="all">Semua</button>
                        <button class="btn btn-filter" data-filter="kegiatan">Kegiatan</button>
                        <button class="btn btn-filter" data-filter="wisata">Wisata</button>
                        <button class="btn btn-filter" data-filter="produk">Produk</button>
                    </div>
                </div>
            </div>

            <!-- Gallery Grid -->
            <div class="row gallery-grid">
                @forelse($galeri ?? [] as $gallery)
                    <div class="col-lg-4 col-md-6 gallery-item {{ $gallery->category ?? 'all' }}" data-aos="fade-up">
                        <div class="gallery-card">
                            <div class="gallery-image">
                                <img src="{{ asset('storage/'.$gallery->image_path) }}" 
                                     alt="{{ $gallery->title }}" class="img-fluid">
                                <div class="gallery-overlay">
                                    <div class="gallery-info">
                                        <h4>{{ $gallery->title }}</h4>
                                        <p>{{ $gallery->description }}</p>
                                        <a href="{{ asset('storage/'.$gallery->image_path) }}" class="gallery-popup">
                                            <i class="fas fa-search-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            Belum ada foto yang ditambahkan.
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Load More Button -->
            @if(isset($galleries) && count($galleries) >= 9)
                <div class="row mt-5">
                    <div class="col-12 text-center">
                        <button class="btn btn-primary btn-load-more">
                            Muat Lebih Banyak
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('styles')
<style>
    .page-header {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                    url('{{ asset("assets/images/gallery-header.jpg") }}') no-repeat center center;
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

    .gallery-section {
        padding: 0 0 80px;
    }

    .filter-buttons {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .btn-filter {
        padding: 10px 25px;
        border-radius: 50px;
        background: transparent;
        color: #2c3e50;
        border: 2px solid #e9ecef;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-filter:hover,
    .btn-filter.active {
        background: #3498db;
        color: white;
        border-color: #3498db;
    }

    .gallery-grid {
        margin-top: 40px;
    }

    .gallery-card {
        margin-bottom: 30px;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
    }

    .gallery-card:hover {
        transform: translateY(-5px);
    }

    .gallery-image {
        position: relative;
        overflow: hidden;
        padding-bottom: 75%; /* 4:3 Aspect Ratio */
    }

    .gallery-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(52, 152, 219, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .gallery-card:hover .gallery-overlay {
        opacity: 1;
    }

    .gallery-card:hover .gallery-image img {
        transform: scale(1.1);
    }

    .gallery-info {
        text-align: center;
        color: white;
        padding: 20px;
        transform: translateY(20px);
        transition: all 0.3s ease;
    }

    .gallery-card:hover .gallery-info {
        transform: translateY(0);
    }

    .gallery-info h4 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .gallery-info p {
        font-size: 0.9rem;
        margin-bottom: 15px;
        opacity: 0.9;
    }

    .gallery-popup {
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #3498db;
        font-size: 1.25rem;
        transition: all 0.3s ease;
    }

    .gallery-popup:hover {
        background: #2c3e50;
        color: white;
    }

    .btn-load-more {
        padding: 12px 35px;
        border-radius: 50px;
        font-weight: 500;
        background: #3498db;
        color: white;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-load-more:hover {
        background: #2c3e50;
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 120px 0 60px;
            margin-bottom: 40px;
        }

        .gallery-section {
            padding: 0 0 40px;
        }

        .filter-buttons {
            margin-bottom: 20px;
        }

        .btn-filter {
            padding: 8px 20px;
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Filter functionality
    document.querySelectorAll('.btn-filter').forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.btn-filter').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Add active class to clicked button
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            // Filter gallery items
            document.querySelectorAll('.gallery-item').forEach(item => {
                if (filter === 'all' || item.classList.contains(filter)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Initialize lightbox for gallery popups
    document.addEventListener('DOMContentLoaded', function() {
        const lightbox = GLightbox({
            selector: '.gallery-popup',
            touchNavigation: true,
            loop: true,
            autoplayVideos: true
        });
    });
</script>
@endpush
