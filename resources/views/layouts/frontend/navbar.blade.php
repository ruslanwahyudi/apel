<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="{{ asset('storage/'.$settings->app_logo ?? 'assets/images/logo.png') }}" alt="Logo" class="brand-logo me-2">
            <span>{{ $settings->app_name ?? 'Desa Banyupelle' }}</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profil') }}">Profil Desa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('berita') }}">Berita</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('pengumuman') }}">Pengumuman</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('galeri') }}">Galeri</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('kontak') }}">Kontak</a>
                </li>
                @if (Auth::check())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="user-profile">
                                <img src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" 
                                     alt="{{ Auth::user()->name }}">
                                <div class="user-info d-none d-lg-block">
                                    <p class="user-name">{{ Auth::user()->name }}</p>
                                    <p class="user-role">{{ Auth::user()->role }}</p>
                                </div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <div class="user-profile d-block d-lg-none">
                                    <img src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" 
                                         alt="{{ Auth::user()->name }}">
                                    <div class="user-info">
                                        <p class="user-name">{{ Auth::user()->name }}</p>
                                        <p class="user-role">{{ Auth::user()->role }}</p>
                                    </div>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider d-block d-lg-none"></li>
                            <!-- <li><a class="dropdown-item" href="#">
                                <i class="fas fa-user-circle me-2"></i>Profil Saya
                            </a></li> -->
                            @if (Auth::user()->role !== 'user') 
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); this.closest('form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout 
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login <i class="fas fa-sign-in-alt"></i></a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav> 