@extends('layouts.frontend')

@section('konten')
    <!-- Page Header -->
    <header class="page-header" style="background-image: url('{{ asset($profil->foto_kantor) }}')">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('pengumuman') }}">Pengumuman</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $pengumuman->title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- Announcement Content -->
    <section class="announcement-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Main Announcement -->
                    <article class="main-announcement">
                        <h1 class="announcement-title">{{ $pengumuman->title }}</h1>
                        
                        <div class="announcement-meta">
                            <span class="date"><i class="far fa-calendar-alt"></i> {{ $pengumuman->created_at->format('d M Y') }}</span>
                            @if($pengumuman->kategori)
                                <span class="category"><i class="far fa-folder"></i> {{ $pengumuman->kategori->nama }}</span>
                            @endif
                        </div>

                        <div class="announcement-content">
                            {!! $pengumuman->content !!}
                        </div>

                        <!-- Social Share -->
                        <div class="social-share">
                            <h5>Bagikan:</h5>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="btn btn-facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($pengumuman->title) }}" target="_blank" class="btn btn-twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($pengumuman->title . ' ' . request()->url()) }}" target="_blank" class="btn btn-whatsapp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </article>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Recent Announcements -->
                    <div class="sidebar-widget recent-announcements">
                        <h4>Pengumuman Terbaru</h4>
                        <div class="recent-announcements-list">
                            @foreach(App\Models\blog\Announcement::latest()->take(5)->get() as $recent)
                                <div class="recent-announcement-item">
                                    <div class="recent-announcement-info">
                                        <h6><a href="{{ route('pengumuman.show', $recent->slug) }}">{{ $recent->title }}</a></h6>
                                        <span class="date">{{ $recent->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="sidebar-widget categories">
                        <h4>Kategori</h4>
                        <ul class="category-list">
                            @foreach($kategori ?? [] as $category)
                                <li>
                                    <a href="{{ route('pengumuman', ['category' => $category->id]) }}">
                                        {{ $category->nama }}
                                        <span class="count">({{ $category->announcements->count() }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .page-header {
        height: 400px;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        position: relative;
        color: #fff;
        margin-bottom: 60px;
        display: flex;
        align-items: flex-end;
        padding-bottom: 30px;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.7));
    }

    .page-header .container {
        position: relative;
        z-index: 1;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item a {
        color: #fff;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: rgba(255, 255, 255, 0.8);
    }

    .announcement-section {
        padding-bottom: 80px;
    }

    .main-announcement {
        background: #fff;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .announcement-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    .announcement-meta {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .announcement-meta span {
        margin-right: 20px;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .announcement-meta i {
        margin-right: 5px;
    }

    .announcement-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #444;
        margin-bottom: 30px;
    }

    .social-share {
        padding-top: 30px;
        border-top: 1px solid #eee;
    }

    .social-share h5 {
        display: inline-block;
        margin-right: 15px;
        margin-bottom: 0;
        vertical-align: middle;
    }

    .social-share .btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        color: #fff;
        transition: transform 0.3s ease;
    }

    .social-share .btn:hover {
        transform: translateY(-3px);
    }

    .btn-facebook {
        background: #3b5998;
    }

    .btn-twitter {
        background: #1da1f2;
    }

    .btn-whatsapp {
        background: #25d366;
    }

    .sidebar-widget {
        background: #fff;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .sidebar-widget h4 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #eee;
        color: #2c3e50;
    }

    .recent-announcement-item {
        padding: 15px 0;
        border-bottom: 1px solid #eee;
    }

    .recent-announcement-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .recent-announcement-info h6 {
        margin-bottom: 5px;
        line-height: 1.4;
    }

    .recent-announcement-info h6 a {
        color: #2c3e50;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .recent-announcement-info h6 a:hover {
        color: #3498db;
    }

    .recent-announcement-info .date {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .category-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .category-list li {
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .category-list li:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .category-list a {
        color: #2c3e50;
        text-decoration: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: color 0.3s ease;
    }

    .category-list a:hover {
        color: #3498db;
    }

    .category-list .count {
        background: #f8f9fa;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.85rem;
        color: #6c757d;
    }

    @media (max-width: 768px) {
        .page-header {
            height: 300px;
        }

        .announcement-title {
            font-size: 2rem;
        }

        .main-announcement {
            padding: 20px;
        }

        .announcement-meta span {
            display: block;
            margin-bottom: 10px;
        }
    }
</style>
@endpush 