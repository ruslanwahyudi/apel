@extends('layouts.frontend')

@section('konten')
    <!-- Page Header -->
    <header class="page-header" style="background-image: url('{{ asset($profil->foto_kantor) }}')">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Berita Terkini</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Berita</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- News Section -->
    <section class="news-section">
        <div class="container">
            <!-- Search and Filter -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="search-box">
                        <form action="{{ route('berita') }}" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Cari berita..." name="search" value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-4">
                    <form action="{{ route('berita') }}" method="GET" id="categoryForm">
                        <select class="form-select" name="category" onchange="document.getElementById('categoryForm').submit()">
                            <option value="">Semua Kategori</option>
                            @foreach($kategori ?? [] as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->nama }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <!-- News Grid -->
            <div class="row">
                @forelse($berita as $item)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="news-card">
                            <div class="news-image">
                                @if($item->images->isNotEmpty())
                                    <img src="{{ asset($item->images->first()->image) }}" alt="{{ $item->title }}" class="img-fluid">
                                @else
                                    <img src="{{ asset('assets/images/default-news.jpg') }}" alt="{{ $item->title }}" class="img-fluid">
                                @endif
                            </div>
                            <div class="news-content">
                                <div class="news-meta">
                                    <span class="date"><i class="far fa-calendar-alt"></i> {{ $item->created_at->format('d M Y') }}</span>
                                    @if($item->kategori)
                                        <span class="category"><i class="far fa-folder"></i> {{ $item->kategori->nama }}</span>
                                    @endif
                                </div>
                                <h3 class="news-title">
                                    <a href="{{ route('berita.show', $item->slug) }}">{{ $item->title }}</a>
                                </h3>
                                <p class="news-excerpt">{{ Str::limit(strip_tags($item->content), 100) }}</p>
                                <a href="{{ route('berita.show', $item->slug) }}" class="read-more">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            Belum ada berita yang ditambahkan.
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($berita instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="row">
                    <div class="col-12">
                        <div class="pagination-wrapper">
                            {{ $berita->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@push('styles')
<style>
    .page-header {
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        padding: 150px 0 80px;
        position: relative;
        color: #fff;
        margin-bottom: 60px;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
    }

    .page-header .container {
        position: relative;
        z-index: 1;
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

    .news-section {
        padding-bottom: 80px;
    }

    .news-card {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        height: 100%;
        transition: transform 0.3s ease;
    }

    .news-card:hover {
        transform: translateY(-5px);
    }

    .news-image {
        position: relative;
        padding-top: 60%;
        overflow: hidden;
    }

    .news-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .news-content {
        padding: 20px;
    }

    .news-meta {
        margin-bottom: 15px;
        font-size: 0.9rem;
        color: #6c757d;
    }

    .news-meta span {
        margin-right: 15px;
    }

    .news-meta i {
        margin-right: 5px;
    }

    .news-title {
        font-size: 1.25rem;
        margin-bottom: 15px;
        line-height: 1.4;
    }

    .news-title a {
        color: #2c3e50;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .news-title a:hover {
        color: #3498db;
    }

    .news-excerpt {
        color: #6c757d;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .read-more {
        color: #3498db;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    .read-more i {
        margin-left: 5px;
        transition: transform 0.3s ease;
    }

    .read-more:hover i {
        transform: translateX(5px);
    }

    .search-box .form-control {
        border-radius: 50px 0 0 50px;
        padding-left: 20px;
    }

    .search-box .btn {
        border-radius: 0 50px 50px 0;
        padding: 10px 20px;
    }

    .form-select {
        border-radius: 50px;
        padding: 10px 20px;
    }

    .pagination-wrapper {
        margin-top: 40px;
        display: flex;
        justify-content: center;
    }

    .pagination {
        margin: 0;
    }

    .page-link {
        padding: 10px 20px;
        border-radius: 50px;
        margin: 0 5px;
        color: #3498db;
        border: none;
        background: #f8f9fa;
    }

    .page-item.active .page-link {
        background: #3498db;
        color: #fff;
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 100px 0 60px;
        }

        .news-card {
            margin-bottom: 30px;
        }
    }
</style>
@endpush
