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
                            <li class="breadcrumb-item"><a href="{{ route('berita') }}">Berita</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $berita->title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- Article Content -->
    <section class="article-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Main Article -->
                    <article class="main-article">
                        <h1 class="article-title">{{ $berita->title }}</h1>
                        
                        <div class="article-meta">
                            <span class="date"><i class="far fa-calendar-alt"></i> {{ $berita->created_at->format('d M Y') }}</span>
                            @if($berita->kategori)
                                <span class="category"><i class="far fa-folder"></i> {{ $berita->kategori->nama }}</span>
                            @endif
                            @if($berita->labels)
                                <span class="tags">
                                    <i class="fas fa-tags"></i>
                                    @foreach($berita->labels as $label)
                                        <a href="#">{{ $label->nama }}</a>{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </span>
                            @endif
                        </div>

                        @if($berita->images->isNotEmpty())
                            <div class="article-image">
                                <img src="{{ asset($berita->images->first()->image) }}" alt="{{ $berita->title }}" class="img-fluid">
                            </div>
                        @endif

                        <div class="article-content">
                            {!! $berita->content !!}
                        </div>

                        <!-- Social Share -->
                        <div class="social-share">
                            <h5>Bagikan:</h5>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="btn btn-facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($berita->title) }}" target="_blank" class="btn btn-twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($berita->title . ' ' . request()->url()) }}" target="_blank" class="btn btn-whatsapp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </article>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Recent Posts -->
                    <div class="sidebar-widget recent-posts">
                        <h4>Berita Terbaru</h4>
                        <div class="recent-posts-list">
                            @foreach(App\Models\blog\Posts::with('kategori')->latest()->take(5)->get() as $recent)
                                <div class="recent-post-item">
                                    <div class="recent-post-image">
                                        @if($recent->images->isNotEmpty())
                                            <img src="{{ asset($recent->images->first()->image) }}" alt="{{ $recent->title }}">
                                        @else
                                            <img src="{{ asset('assets/images/default-news.jpg') }}" alt="{{ $recent->title }}">
                                        @endif
                                    </div>
                                    <div class="recent-post-info">
                                        <h6><a href="{{ route('berita.show', $recent->slug) }}">{{ $recent->title }}</a></h6>
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
                            @foreach($kategori as $category)
                                <li>
                                    <a href="{{ route('berita', ['category' => $category->id]) }}">
                                        {{ $category->nama }}
                                        <span class="count">({{ $category->posts->count() }})</span>
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
        background-image: url('{{ asset($berita->images->isNotEmpty() ? $berita->images->first()->image : "assets/images/default-news.jpg") }}');
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

    .article-section {
        padding-bottom: 80px;
    }

    .main-article {
        background: #fff;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .article-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    .article-meta {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .article-meta span {
        margin-right: 20px;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .article-meta i {
        margin-right: 5px;
    }

    .article-meta .tags a {
        color: #3498db;
        text-decoration: none;
    }

    .article-image {
        margin-bottom: 30px;
    }

    .article-image img {
        border-radius: 10px;
        width: 100%;
    }

    .article-content {
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

    .recent-post-item {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .recent-post-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .recent-post-image {
        width: 80px;
        height: 80px;
        margin-right: 15px;
        border-radius: 10px;
        overflow: hidden;
    }

    .recent-post-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .recent-post-info h6 {
        margin-bottom: 5px;
        line-height: 1.4;
    }

    .recent-post-info h6 a {
        color: #2c3e50;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .recent-post-info h6 a:hover {
        color: #3498db;
    }

    .recent-post-info .date {
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

        .article-title {
            font-size: 2rem;
        }

        .main-article {
            padding: 20px;
        }

        .article-meta span {
            display: block;
            margin-bottom: 10px;
        }
    }
</style>
@endpush 