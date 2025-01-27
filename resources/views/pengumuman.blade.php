@extends('layouts.frontend')

@section('konten')
    <!-- Page Header -->
    <header class="page-header" style="background-image: url('{{ asset($profil->foto_kantor) }}')">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Pengumuman</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Pengumuman</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- Announcements Section -->
    <section class="announcements-section">
        <div class="container">
            <!-- Search and Filter -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="search-box">
                        <form action="" method="GET">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Cari pengumuman..." name="search">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="category">
                        <option value="">Semua Kategori</option>
                        @foreach($kategori ?? [] as $category)
                            <option value="{{ $category->id }}">{{ $category->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Announcements List -->
            <div class="row">
                <div class="col-12">
                    @forelse($pengumuman ?? [] as $announcement)
                        <div class="announcement-card">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <div class="announcement-date">
                                        <span class="day">{{ $announcement->created_at?->format('d') }}</span>
                                        <span class="month">{{ $announcement->created_at?->format('M') }}</span>
                                        <span class="year">{{ $announcement->created_at?->format('Y') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="announcement-content">
                                        <div class="announcement-meta">
                                            <span class="category">
                                                <i class="fas fa-tag"></i> 
                                                {{ $announcement->kategori->nama ?? 'Uncategorized' }}
                                            </span>
                                            <span class="author">
                                                <i class="fas fa-user"></i> 
                                                {{ $announcement->author ?? 'Admin' }}
                                            </span>
                                        </div>
                                        <h3 class="announcement-title">{{ $announcement->title }}</h3>
                                        <p class="announcement-excerpt">
                                            {{ Str::limit($announcement->content, 150) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center">
                                    <a href="{{ route('pengumuman.show', $announcement->slug) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        Baca Selengkapnya
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info text-center">
                            Belum ada pengumuman yang ditambahkan.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Pagination -->
            @if(isset($announcements) && $announcements->hasPages())
                <div class="row">
                    <div class="col-12">
                        <div class="pagination-wrapper">
                            {{ $announcements->links() }}
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
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                    url('{{ asset("assets/images/announcement-header.jpg") }}') no-repeat center center;
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

    .announcements-section {
        padding: 0 0 80px;
    }

    .search-box .form-control {
        border-radius: 50px 0 0 50px;
        padding: 12px 25px;
    }

    .search-box .btn {
        border-radius: 0 50px 50px 0;
        padding: 12px 25px;
    }

    .form-select {
        padding: 12px 25px;
        border-radius: 50px;
    }

    .announcement-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
    }

    .announcement-card:hover {
        transform: translateY(-5px);
    }

    .announcement-date {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 15px;
        border-radius: 10px;
        background: #f8f9fa;
    }

    .announcement-date .day {
        font-size: 2rem;
        font-weight: 700;
        color: #3498db;
        line-height: 1;
    }

    .announcement-date .month {
        font-size: 1rem;
        font-weight: 600;
        color: #2c3e50;
        text-transform: uppercase;
    }

    .announcement-date .year {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .announcement-content {
        padding: 0 20px;
    }

    .announcement-meta {
        margin-bottom: 10px;
    }

    .announcement-meta span {
        display: inline-flex;
        align-items: center;
        margin-right: 20px;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .announcement-meta i {
        margin-right: 5px;
        color: #3498db;
    }

    .announcement-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: #2c3e50;
    }

    .announcement-excerpt {
        color: #6c757d;
        margin-bottom: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .pagination-wrapper {
        margin-top: 50px;
        display: flex;
        justify-content: center;
    }

    .pagination {
        gap: 5px;
    }

    .page-link {
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3498db;
        border: none;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .page-link:hover {
        background: #3498db;
        color: white;
    }

    .page-item.active .page-link {
        background: #3498db;
        color: white;
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 120px 0 60px;
            margin-bottom: 40px;
        }

        .announcements-section {
            padding: 0 0 40px;
        }

        .search-box {
            margin-bottom: 15px;
        }

        .announcement-date {
            margin-bottom: 20px;
        }

        .announcement-content {
            padding: 0;
            margin-bottom: 20px;
            text-align: center;
        }

        .announcement-meta {
            justify-content: center;
        }
    }
</style>
@endpush
