@extends('layouts.frontend')

@section('konten')
    <!-- Hero Section -->
    <section class="hero" id="beranda">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-indicators">
                @foreach($galleries as $key => $gallery)
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $key }}" 
                            class="{{ $key == 0 ? 'active' : '' }}" aria-current="true" 
                            aria-label="Slide {{ $key + 1 }}"></button>
                @endforeach
            </div>
            <div class="carousel-inner">
                @foreach($galleries as $key => $gallery)
                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                        <div class="hero-slide" style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                             url('{{ $gallery->image_url }}') no-repeat center center; background-size: cover;">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-12 text-center">
                                        <h1>{{ $gallery->title }}</h1>
                                        <p class="lead">{{ $gallery->description }}</p>
                                        <div class="d-flex justify-content-center gap-3 mb-4">
                                            <a href="#layanan" class="btn btn-custom btn-primary-custom">Layanan Online</a>
                                            <a href="#profil" class="btn btn-custom btn-outline-light">Pelajari Lebih Lanjut</a>
                                        </div>
                                        <div class="app-download">
                                            <a href="{{ $settings->playstore_url ?? 'https://play.google.com/store/apps/details?id=com.banyupele_app' }}" target="_blank" class="playstore-button">
                                                <div class="playstore-content">
                                                    <div class="playstore-icon">
                                                        <i class="fab fa-google-play"></i>
                                                    </div>
                                                    <div class="playstore-text">
                                                        <span class="small-text">GET IT ON</span>
                                                        <span class="big-text">Google Play</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="layanan">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-12">
                    <h2>Layanan Kami</h2>
                    <p class="lead">Berbagai layanan untuk memudahkan masyarakat</p>
                </div>
            </div>
            <div class="row">
                @foreach($services as $service)
                <div class="col-lg-3">
                    <div class="feature-box">
                        <i class="fas fa-id-card feature-icon"></i>
                        <h5>{{ $service->nama_pelayanan }}</h5>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section class="news-section" id="berita">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-lg-12">
                    <h2>Berita Terbaru</h2>
                    <p class="lead">Informasi terkini seputar Desa Banyupelle</p>
                </div>
            </div>
            <div class="row">
                @foreach($berita as $item)
                <div class="col-lg-4 mb-4">
                    <div class="card news-card">
                        <img src="{{ asset($item->images[0]->image ?? 'images/no-image.jpg') }}" class="card-img-top" alt="{{ $item->title }}">
                        <div class="card-body">
                            <p class="card-meta">
                                <span class="badge bg-secondary">{{ $item->kategori->nama }}</span>
                                @foreach($item->labels as $label)
                                    <span class="badge bg-info">{{ $label->nama }}</span>
                                @endforeach
                                
                            </p>
                            <!-- <span class="text-muted">{{ $item->created_at->format('d M Y') }}</span> -->
                            <h5 class="card-title">{{ $item->title }}</h5>
                            <p class="card-text">{{ strip_tags($item->content) }}</p>
                            
                            <a href="#" class="btn btn-primary btn-sm">Baca Selengkapnya</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Navbar color change on scroll
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            document.querySelector('.navbar').style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
        } else {
            document.querySelector('.navbar').style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
        }
    });
</script>
@endpush
