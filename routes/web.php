<?php

use App\Http\Controllers\adm\KategoriSuratController;
use App\Http\Controllers\blog\LabelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\blog\KategoriController;
use App\Http\Controllers\blog\PostController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\informasi\ProfilDesaController;
use App\Http\Controllers\informasi\RKPDESController;
use App\Http\Controllers\informasi\PerdesController;
use App\Http\Controllers\informasi\ProdukController;
use App\Http\Controllers\informasi\WisataController;
use App\Http\Controllers\layanan\DaftarLayananController;
use App\Http\Controllers\layanan\IdentitasLayananController;
use App\Http\Controllers\layanan\JenisLayananController;
use App\Http\Controllers\layanan\PersyaratanDokumenController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\SettingController;
use App\Models\blog\Posts;
use App\Models\Gallery;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\blog\AnnouncementCategoryController;
use App\Http\Controllers\blog\AnnouncementController;
use App\Models\blog\Announcement;
use App\Models\blog\Kategori;
use App\Models\informasi\ProfilDesa;
use App\Models\Layanan\JenisLayanan;
use App\Http\Controllers\adm\RegisterSuratController;
use App\Http\Controllers\adm\DusunController;
use App\Http\Controllers\Admin\KegiatanPembangunanController;
use App\Http\Controllers\Admin\ProgresPembangunanController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\layanan\PengajuanPenggunaController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\layanan\KlasifikasiIdentitasPemohonController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $berita = Posts::with('kategori', 'labels', 'images')->get();
    $galleries = Gallery::all();
    $pengaturan = Setting::all();
    $services = JenisLayanan::all();
        
    $profil = ProfilDesa::all();
    return view('welcome', compact('berita', 'galleries', 'pengaturan', 'services', 'profil'));
})->name('home');

Route::get('/profil', function () {
    $profil = ProfilDesa::first();
    return view('profil', compact('profil'));
})->name('profil');

Route::get('/berita', function () {
    $profil = ProfilDesa::first();
    $kategori = Kategori::all();

    $berita = Posts::with('kategori', 'labels', 'images')
        ->when(request('search'), function($query) {
            return $query->where('title', 'like', '%' . request('search') . '%')
                        ->orWhere('content', 'like', '%' . request('search') . '%');
        })
        ->when(request('category'), function($query) {
            return $query->where('kategori_id', request('category'));
        })
        ->latest()
        ->paginate(9);
    return view('berita', compact('profil', 'berita', 'kategori'));
})->name('berita');

Route::get('/berita/{slug}', function ($slug) {
    $profil = ProfilDesa::first();
    $kategori = Kategori::all();
    $berita = Posts::with('kategori', 'labels', 'images')->where('slug', $slug)->first();
    return view('berita_detail', compact('profil', 'kategori', 'berita'));
})->name('berita.show');

Route::get('/pengumuman', function () {
    $profil = ProfilDesa::first();
    $kategori = Kategori::all();
    $pengumuman = Announcement::with('kategori')
        ->when(request('search'), function($query) {
            return $query->where('title', 'like', '%' . request('search') . '%')
                        ->orWhere('content', 'like', '%' . request('search') . '%');
        })
        ->when(request('category'), function($query) {
            return $query->where('category_id', request('category'));
        })
        ->latest()
        ->paginate(9);
    return view('pengumuman', compact('profil', 'pengumuman', 'kategori'));
})->name('pengumuman');

Route::get('/pengumuman/{slug}', function ($slug) {
    $profil = ProfilDesa::first();
    $kategori = Kategori::all();
    $pengumuman = Announcement::where('slug', $slug)->first();
    return view('pengumuman_detail', compact('profil', 'pengumuman', 'kategori'));
})->name('pengumuman.show');

Route::get('/galeri', function () {
    $profil = ProfilDesa::first();
    $galeri = Gallery::all();
    return view('galeri', compact('profil', 'galeri'));
})->name('galeri');

Route::get('/kontak', function () {
    $profil = ProfilDesa::first();
    return view('kontak', compact('profil'));
})->name('kontak');

Route::post('/kontak', [KontakController::class, 'store'])->name('kontak.store');

Route::get('/privacy-policy', function () {
    $profil = ProfilDesa::first();
    $setting = Setting::first();
    return view('privacy-policy', compact('profil', 'setting'));
})->name('privacy-policy');

Route::get('/disclaimer', function () {
    $profil = ProfilDesa::first();
    $setting = Setting::first();
    return view('disclaimer', compact('profil', 'setting'));
})->name('disclaimer');

Route::get('/faq', function () {
    $profil = ProfilDesa::first();  
    $setting = Setting::first();
    return view('faq', compact('profil', 'setting'));
})->name('faq');


Auth::routes();

Route::get('/debug-session', function () {
    return session()->all();
});

Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout.get');

// Admin Routes
Route::middleware(['auth', 'role:admin,kepala'])->group(function () {

    Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Role Routes
    Route::get('admin/roles', [RoleController::class, 'index'])->name('admin.roles');
    Route::get('admin/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
    Route::post('admin/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::get('admin/roles/edit/{role}', [RoleController::class, 'edit'])->name('admin.roles.edit');
    Route::put('admin/roles/update/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
    Route::delete('admin/roles/destroy/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
    Route::get('admin/roles/search/{search}', [RoleController::class, 'search'])->name('admin.roles.search');

    // Module Routes
    Route::get('admin/modules', [ModuleController::class, 'index'])->name('admin.modules');
    Route::get('admin/modules/create', [ModuleController::class, 'create'])->name('admin.modules.create');
    Route::post('admin/modules', [ModuleController::class, 'store'])->name('admin.modules.store');
    Route::get('admin/modules/edit/{module}', [ModuleController::class, 'edit'])->name('admin.modules.edit');
    Route::put('admin/modules/update/{module}', [ModuleController::class, 'update'])->name('admin.modules.update');
    Route::delete('admin/modules/destroy/{module}', [ModuleController::class, 'destroy'])->name('admin.modules.destroy');
    Route::get('admin/modules/search/{search}', [ModuleController::class, 'search'])->name('admin.modules.search');

    // Menu Routes
    Route::get('admin/menus', [MenuController::class, 'index'])->name('admin.menus');
    Route::get('admin/menus/create', [MenuController::class, 'create'])->name('admin.menus.create');
    Route::post('admin/menus', [MenuController::class, 'store'])->name('admin.menus.store');
    Route::get('admin/menus/edit/{menu}', [MenuController::class, 'edit'])->name('admin.menus.edit');
    Route::put('admin/menus/update/{menu}', [MenuController::class, 'update'])->name('admin.menus.update');
    Route::delete('admin/menus/destroy/{menu}', [MenuController::class, 'destroy'])->name('admin.menus.destroy');
    Route::get('admin/menus/search/{search}', [MenuController::class, 'search'])->name('admin.menus.search');

    // Permission Routes
    Route::get('admin/permissions', [PermissionController::class, 'index'])->name('admin.permissions');
    Route::get('admin/permissions/create', [PermissionController::class, 'create'])->name('admin.permissions.create');
    Route::post('admin/permissions', [PermissionController::class, 'store'])->name('admin.permissions.store');
    Route::get('admin/permissions/getPrivileges', [PermissionController::class, 'getPrivileges'])->name('admin.permissions.getPrivileges');
    Route::get('admin/permissions/edit/{permission}', [PermissionController::class, 'edit'])->name('admin.permissions.edit');
    Route::put('admin/permissions/update/{permission}', [PermissionController::class, 'update'])->name('admin.permissions.update');
    Route::delete('admin/permissions/destroy/{permission}', [PermissionController::class, 'destroy'])->name('admin.permissions.destroy');
    Route::get('admin/permissions/search/{search}', [PermissionController::class, 'search'])->name('admin.permissions.search');

    // User Routes
    Route::get('admin/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('admin/users/edit/{user}', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('admin/users/update/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('admin/users/destroy/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('admin/users/search/{search}', [UserController::class, 'search'])->name('admin.users.search');

    // kategori
    Route::get('blog/kategori', [KategoriController::class, 'index'])->name('blog.kategori');
    Route::get('blog/kategori/create', [KategoriController::class, 'create'])->name('blog.kategori.create');
    Route::post('blog/kategori', [KategoriController::class, 'store'])->name('blog.kategori.store');
    Route::get('blog/kategori/edit/{kategori}', [KategoriController::class, 'edit'])->name('blog.kategori.edit');
    Route::put('blog/kategori/update/{kategori}', [KategoriController::class, 'update'])->name('blog.kategori.update');
    Route::delete('blog/kategori/destroy/{kategori}', [KategoriController::class, 'destroy'])->name('blog.kategori.destroy');
    Route::get('blog/kategori/search/{search}', [KategoriController::class, 'search'])->name('blog.kategori.search');
    // label
    Route::get('blog/label', [LabelController::class, 'index'])->name('blog.label');
    Route::get('blog/label/create', [LabelController::class, 'create'])->name('blog.label.create');
    Route::post('blog/label', [LabelController::class, 'store'])->name('blog.label.store');
    Route::get('blog/label/edit/{label}', [LabelController::class, 'edit'])->name('blog.label.edit');
    Route::put('blog/label/update/{label}', [LabelController::class, 'update'])->name('blog.label.update');
    Route::delete('blog/label/destroy/{label}', [LabelController::class, 'destroy'])->name('blog.label.destroy');
    Route::get('blog/label/search/{search}', [LabelController::class, 'search'])->name('blog.label.search');

    // Post Routes
    Route::get('blog/posts', [PostController::class, 'index'])->name('blog.posts');
    Route::get('blog/posts/create', [PostController::class, 'create'])->name('blog.posts.create');
    Route::post('blog/posts', [PostController::class, 'store'])->name('blog.posts.store');
    Route::get('blog/posts/edit/{post}', [PostController::class, 'edit'])->name('blog.posts.edit');
    Route::put('blog/posts/update/{post}', [PostController::class, 'update'])->name('blog.posts.update');
    Route::delete('blog/posts/destroy/{post}', [PostController::class, 'destroy'])->name('blog.posts.destroy');
    Route::get('blog/posts/search/{search}', [PostController::class, 'search'])->name('blog.posts.search');

    // Announcement Routes
    Route::get('blog/announcements', [AnnouncementController::class, 'index'])->name('blog.announcements');
    Route::get('blog/announcements/create', [AnnouncementController::class, 'create'])->name('blog.announcements.create');
    Route::post('blog/announcements', [AnnouncementController::class, 'store'])->name('blog.announcements.store');
    Route::get('blog/announcements/edit/{announcement}', [AnnouncementController::class, 'edit'])->name('blog.announcements.edit');
    Route::put('blog/announcements/update/{announcement}', [AnnouncementController::class, 'update'])->name('blog.announcements.update');
    Route::delete('blog/announcements/destroy/{announcement}', [AnnouncementController::class, 'destroy'])->name('blog.announcements.destroy');
    Route::get('blog/announcements/search/{search}', [AnnouncementController::class, 'search'])->name('blog.announcements.search');


    // informasi desa
    // profil desa
    Route::get('informasi/profil', [ProfilDesaController::class, 'index'])->name('info.profil');
    Route::get('informasi/profil/create', [ProfilDesaController::class, 'create'])->name('info.profil.create');
    Route::post('informasi/profil', [ProfilDesaController::class, 'store'])->name('info.profil.store');
    Route::get('informasi/profil/edit/{profil}', [ProfilDesaController::class, 'edit'])->name('info.profil.edit');
    Route::put('informasi/profil/update/{profil}', [ProfilDesaController::class, 'update'])->name('info.profil.update');
    Route::delete('informasi/profil/destroy/{profil}', [ProfilDesaController::class, 'destroy'])->name('info.profil.destroy');
    Route::get('informasi/profil/search/{search}', [ProfilDesaController::class, 'search'])->name('info.profil.search');
    

    // dusun
    Route::get('admin/dusun', [DusunController::class, 'index'])->name('admin.dusun');
    Route::get('admin/dusun/create', [DusunController::class, 'create'])->name('admin.dusun.create');
    Route::post('admin/dusun', [DusunController::class, 'store'])->name('admin.dusun.store');
    Route::get('admin/dusun/edit/{dusun}', [DusunController::class, 'edit'])->name('admin.dusun.edit');
    Route::put('admin/dusun/update/{dusun}', [DusunController::class, 'update'])->name('admin.dusun.update');
    Route::delete('admin/dusun/destroy/{dusun}', [DusunController::class, 'destroy'])->name('admin.dusun.destroy');
    Route::get('admin/dusun/search/{search}', [DusunController::class, 'search'])->name('admin.dusun.search');

    // RKPDES
    Route::get('informasi/rkpdes', [RKPDESController::class, 'index'])->name('info.rkpdes');
    Route::get('informasi/rkpdes/create', [RKPDESController::class, 'create'])->name('info.rkpdes.create');
    Route::post('informasi/rkpdes', [RKPDESController::class, 'store'])->name('info.rkpdes.store');
    Route::get('informasi/rkpdes/edit/{rkpdes}', [RKPDESController::class, 'edit'])->name('info.rkpdes.edit');
    Route::put('informasi/rkpdes/update/{rkpdes}', [RKPDESController::class, 'update'])->name('info.rkpdes.update');
    Route::delete('informasi/rkpdes/destroy/{rkpdes}', [RKPDESController::class, 'destroy'])->name('info.rkpdes.destroy');
    Route::get('informasi/rkpdes/search/{search}', [RKPDESController::class, 'search'])->name('info.rkpdes.search');

    // Perdes
    Route::get('informasi/perdes', [PerdesController::class, 'index'])->name('info.perdes');
    Route::get('informasi/perdes/create', [PerdesController::class, 'create'])->name('info.perdes.create');
    Route::post('informasi/perdes', [PerdesController::class, 'store'])->name('info.perdes.store');
    Route::get('informasi/perdes/edit/{perdes}', [PerdesController::class, 'edit'])->name('info.perdes.edit');
    Route::put('informasi/perdes/update/{perdes}', [PerdesController::class, 'update'])->name('info.perdes.update');
    Route::delete('informasi/perdes/destroy/{perdes}', [PerdesController::class, 'destroy'])->name('info.perdes.destroy');
    Route::get('informasi/perdes/search/{search}', [PerdesController::class, 'search'])->name('info.perdes.search');

    // produk
    Route::get('informasi/produk', [ProdukController::class, 'index'])->name('info.produk');
    Route::get('informasi/produk/create', [ProdukController::class, 'create'])->name('info.produk.create');
    Route::post('informasi/produk', [ProdukController::class, 'store'])->name('info.produk.store');
    Route::get('informasi/produk/edit/{produk}', [ProdukController::class, 'edit'])->name('info.produk.edit');
    Route::put('informasi/produk/update/{produk}', [ProdukController::class, 'update'])->name('info.produk.update');
    Route::delete('informasi/produk/destroy/{produk}', [ProdukController::class, 'destroy'])->name('info.produk.destroy');
    Route::get('informasi/produk/search/{search}', [ProdukController::class, 'search'])->name('info.produk.search');

    // wisata
    Route::get('informasi/wisata', [WisataController::class, 'index'])->name('info.wisata');
    Route::get('informasi/wisata/create', [WisataController::class, 'create'])->name('info.wisata.create');
    Route::post('informasi/wisata', [WisataController::class, 'store'])->name('info.wisata.store');
    Route::get('informasi/wisata/edit/{wisata}', [WisataController::class, 'edit'])->name('info.wisata.edit');
    Route::put('informasi/wisata/update/{wisata}', [WisataController::class, 'update'])->name('info.wisata.update');
    Route::delete('informasi/wisata/destroy/{wisata}', [WisataController::class, 'destroy'])->name('info.wisata.destroy');
    Route::get('informasi/wisata/search/{search}', [WisataController::class, 'search'])->name('info.wisata.search');

    // Layanan
    // Jenis Layanan		
    Route::get('layanan/jenis-layanan', [JenisLayananController::class, 'index'])->name('layanan.jenis');
    Route::get('layanan/jenis-layanan/create', [JenisLayananController::class, 'create'])->name('layanan.jenis.create');
    Route::post('layanan/jenis-layanan', [JenisLayananController::class, 'store'])->name('layanan.jenis.store');
    Route::get('layanan/jenis-layanan/edit/{jenis}', [JenisLayananController::class, 'edit'])->name('layanan.jenis.edit');
    Route::put('layanan/jenis-layanan/update/{jenis}', [JenisLayananController::class, 'update'])->name('layanan.jenis.update');
    Route::delete('layanan/jenis-layanan/destroy/{jenis}', [JenisLayananController::class, 'destroy'])->name('layanan.jenis.destroy');
    Route::get('layanan/jenis-layanan/search/{search}', [JenisLayananController::class, 'search'])->name('layanan.jenis.search');
    
    // Persyaratan Dokumen						
    Route::get('layanan/persyaratan-dokumen', [PersyaratanDokumenController::class, 'index'])->name('layanan.syarat_dokumen');
    Route::get('layanan/persyaratan-dokumen/create', [PersyaratanDokumenController::class, 'create'])->name('layanan.syarat_dokumen.create');
    Route::post('layanan/persyaratan-dokumen', [PersyaratanDokumenController::class, 'store'])->name('layanan.syarat_dokumen.store');
    Route::get('layanan/persyaratan-dokumen/edit/{persyaratan}', [PersyaratanDokumenController::class, 'edit'])->name('layanan.syarat_dokumen.edit');
    Route::put('layanan/persyaratan-dokumen/update/{persyaratan}', [PersyaratanDokumenController::class, 'update'])->name('layanan.syarat_dokumen.update');
    Route::delete('layanan/persyaratan-dokumen/destroy/{persyaratan}', [PersyaratanDokumenController::class, 'destroy'])->name('layanan.syarat_dokumen.destroy');
    Route::get('layanan/persyaratan-dokumen/show/{persyaratan}', [PersyaratanDokumenController::class, 'show'])->name('layanan.syarat_dokumen.show');
    Route::get('layanan/persyaratan-dokumen/search/{search}', [PersyaratanDokumenController::class, 'search'])->name('layanan.syarat_dokumen.search');
    
    // Identitas Layanan						
    Route::get('layanan/identitas-layanan', [IdentitasLayananController::class, 'index'])->name('layanan.identitas_pemohon');
    Route::get('layanan/identitas-layanan/create', [IdentitasLayananController::class, 'create'])->name('layanan.identitas_pemohon.create');
    Route::post('layanan/identitas-layanan', [IdentitasLayananController::class, 'store'])->name('layanan.identitas_pemohon.store');
    Route::get('layanan/identitas-layanan/edit/{identitas}', [IdentitasLayananController::class, 'edit'])->name('layanan.identitas_pemohon.edit');
    Route::put('layanan/identitas-layanan/update/{identitas}', [IdentitasLayananController::class, 'update'])->name('layanan.identitas_pemohon.update');
    Route::delete('layanan/identitas-layanan/destroy/{identitas}', [IdentitasLayananController::class, 'destroy'])->name('layanan.identitas_pemohon.destroy');
    Route::get('layanan/identitas-layanan/show/{identitas}', [IdentitasLayananController::class, 'show'])->name('layanan.identitas_pemohon.show');
    Route::get('layanan/identitas-layanan/search/{search}', [IdentitasLayananController::class, 'search'])->name('layanan.identitas_pemohon.search');
    
    // Klasifikasi Identitas Pemohon
    Route::get('layanan/klasifikasi-identitas', [KlasifikasiIdentitasPemohonController::class, 'index'])->name('layanan.klasifikasi');
    Route::get('layanan/klasifikasi-identitas/create', [KlasifikasiIdentitasPemohonController::class, 'create'])->name('layanan.klasifikasi.create');
    Route::post('layanan/klasifikasi-identitas', [KlasifikasiIdentitasPemohonController::class, 'store'])->name('layanan.klasifikasi.store');
    Route::get('layanan/klasifikasi-identitas/edit/{klasifikasi}', [KlasifikasiIdentitasPemohonController::class, 'edit'])->name('layanan.klasifikasi.edit');
    Route::put('layanan/klasifikasi-identitas/update/{klasifikasi}', [KlasifikasiIdentitasPemohonController::class, 'update'])->name('layanan.klasifikasi.update');
    Route::delete('layanan/klasifikasi-identitas/destroy/{klasifikasi}', [KlasifikasiIdentitasPemohonController::class, 'destroy'])->name('layanan.klasifikasi.destroy');
    Route::get('layanan/klasifikasi-identitas/show/{klasifikasi}', [KlasifikasiIdentitasPemohonController::class, 'show'])->name('layanan.klasifikasi.show');
    Route::get('layanan/klasifikasi-identitas/search/{search}', [KlasifikasiIdentitasPemohonController::class, 'search'])->name('layanan.klasifikasi.search');
    
    // Daftar Layanan
    Route::get('layanan/daftar-layanan', [DaftarLayananController::class, 'index'])->name('layanan.daftar');
    Route::get('layanan/daftar-layanan/create', [DaftarLayananController::class, 'create'])->name('layanan.daftar.create');
    Route::post('layanan/daftar-layanan', [DaftarLayananController::class, 'store'])->name('layanan.daftar.store');
    Route::get('layanan/daftar-layanan/get-form-fields/{jenisLayananId}', [DaftarLayananController::class, 'getFormFields'])->name('layanan.daftar.get-form-fields');
    Route::get('layanan/daftar-layanan/preview-surat/{id}', [DaftarLayananController::class, 'previewSurat'])->name('layanan.daftar.preview-surat');
    Route::get('layanan/daftar-layanan/edit/{daftar}', [DaftarLayananController::class, 'edit'])->name('layanan.daftar.edit');
    Route::put('layanan/daftar-layanan/update/{daftar}', [DaftarLayananController::class, 'update'])->name('layanan.daftar.update');
    Route::delete('layanan/daftar-layanan/destroy/{daftar}', [DaftarLayananController::class, 'destroy'])->name('layanan.daftar.destroy');
    Route::get('layanan/daftar-layanan/search/{search}', [DaftarLayananController::class, 'search'])->name('layanan.daftar.search');
    Route::post('layanan/daftar-layanan/approve/{id}', [DaftarLayananController::class, 'approve'])->name('layanan.approve');
    Route::post('layanan/daftar-layanan/upload-signed-document/{id}', [DaftarLayananController::class, 'uploadSignedDocument'])->name('layanan.daftar.upload-signed-document');
    Route::get('layanan/daftar-layanan/download-signed-document/{id}', [DaftarLayananController::class, 'downloadSignedDocument'])->name('layanan.daftar.download-signed-document');

    // Pengajuan Pengguna
    Route::get('layanan/pengajuan-pengguna', [PengajuanPenggunaController::class, 'index'])->name('layanan.pengajuan_pengguna');
    Route::get('layanan/pengajuan-pengguna/show/{id}', [PengajuanPenggunaController::class, 'show'])->name('layanan.pengajuan_pengguna.show');
    Route::post('layanan/pengajuan-pengguna/approve', [PengajuanPenggunaController::class, 'approve'])->name('layanan.pengajuan_pengguna.approve');
    Route::post('layanan/pengajuan-pengguna/reject', [PengajuanPenggunaController::class, 'reject'])->name('layanan.pengajuan_pengguna.reject');
    Route::get('layanan/pengajuan-pengguna/search/{search}', [PengajuanPenggunaController::class, 'search'])->name('layanan.pengajuan_pengguna.search');

    // Gallery Routes
    // Route::resource('gallery', GalleryController::class);
    Route::get('gallery', [GalleryController::class, 'index'])->name('info.gallery');
    Route::get('gallery/create', [GalleryController::class, 'create'])->name('info.gallery.create');
    Route::post('gallery', [GalleryController::class, 'store'])->name('info.gallery.store');
    Route::get('gallery/edit/{gallery}', [GalleryController::class, 'edit'])->name('info.gallery.edit');
    Route::put('gallery/update/{gallery}', [GalleryController::class, 'update'])->name('info.gallery.update');
    Route::delete('gallery/destroy/{gallery}', [GalleryController::class, 'destroy'])->name('info.gallery.destroy');
    
    // Settings Routes
    Route::get('admin/settings', [SettingController::class, 'edit'])->name('admin.settings');
    Route::put('admin/settings', [SettingController::class, 'update'])->name('admin.settings.update');

    // Administrasi Umum

    // Kategori Surat
    Route::get('adm/kategori-surat', [KategoriSuratController::class, 'index'])->name('adm.kategori-surat');
    Route::get('adm/kategori-surat/create', [KategoriSuratController::class, 'create'])->name('adm.kategori-surat.create');
    Route::post('adm/kategori-surat', [KategoriSuratController::class, 'store'])->name('adm.kategori-surat.store');
    Route::get('adm/kategori-surat/{kategori}/edit', [KategoriSuratController::class, 'edit'])->name('adm.kategori-surat.edit');
    Route::get('adm/kategori-surat/{kategori}/edit-simple', [KategoriSuratController::class, 'editSimple'])->name('adm.kategori-surat.edit-simple');
    Route::put('adm/kategori-surat/{kategori}', [KategoriSuratController::class, 'update'])->name('adm.kategori-surat.update');
    Route::delete('adm/kategori-surat/{kategori}', [KategoriSuratController::class, 'destroy'])->name('adm.kategori-surat.destroy');
    Route::get('adm/kategori-surat/search/{search}', [KategoriSuratController::class, 'search'])->name('adm.kategori-surat.search');
    Route::post('adm/kategori-surat/{kategori}/preview-template', [KategoriSuratController::class, 'previewTemplate'])->name('adm.kategori-surat.preview-template');
    Route::get('adm/kategori-surat/{kategori}/template', [KategoriSuratController::class, 'showTemplate'])->name('adm.kategori-surat.template');
    Route::post('adm/kategori-surat/{kategori}/generate-pdf', [KategoriSuratController::class, 'generatePdf'])->name('adm.kategori-surat.generate-pdf');
    Route::post('adm/kategori-surat/{kategori}/generate-blade', [KategoriSuratController::class, 'generateBladeTemplate'])->name('adm.kategori-surat.generate-blade');
    Route::get('adm/kategori-surat/{kategori}/preview-blade', [KategoriSuratController::class, 'previewBladeTemplate'])->name('adm.kategori-surat.preview-blade');
    Route::post('adm/kategori-surat/{kategori}/detect-positions', [KategoriSuratController::class, 'detectVariablePositionsApi'])->name('adm.kategori-surat.detect-positions');
    Route::post('adm/kategori-surat/{kategori}/test-replacement', [KategoriSuratController::class, 'testVariableReplacement'])->name('adm.kategori-surat.test-replacement');
    
    // Register Surat Routes
    Route::get('adm/register-surat', [RegisterSuratController::class, 'index'])->name('adm.register_surat.index');
    Route::get('adm/register-surat/create', [RegisterSuratController::class, 'create'])->name('adm.register_surat.create');
    Route::post('adm/register-surat', [RegisterSuratController::class, 'store'])->name('adm.register_surat.store');
    Route::get('adm/register-surat/edit/{surat}', [RegisterSuratController::class, 'edit'])->name('adm.register_surat.edit');
    Route::put('adm/register-surat/update/{surat}', [RegisterSuratController::class, 'update'])->name('adm.register_surat.update');
    Route::get('adm/register-surat/sign/{surat}', [RegisterSuratController::class, 'sign'])->name('adm.register_surat.sign');
    Route::get('adm/register-surat/approve/{surat}', [RegisterSuratController::class, 'approve'])->name('adm.register_surat.approve');
    Route::get('adm/register-surat/revisi/{surat}/{description}', [RegisterSuratController::class, 'revisi'])->name('adm.register_surat.revisi');
    Route::delete('adm/register-surat/{surat}', [RegisterSuratController::class, 'destroy'])->name('adm.register_surat.destroy');
    Route::get('adm/register-surat/print/{surat}', [RegisterSuratController::class, 'print'])->name('adm.register_surat.print');
    Route::get('adm/register-surat/search/{search}', [RegisterSuratController::class, 'search'])->name('adm.register_surat.search');

    // Log Transaksi

    // data desa
    Route::get('data/data-kependudukan', ['', 'index'])->name('data.kependudukan');

    // Pembangunan routes
    Route::get('admin/pembangunan', [KegiatanPembangunanController::class, 'index'])->name('admin.pembangunan');
    Route::get('admin/pembangunan/create', [KegiatanPembangunanController::class, 'create'])->name('admin.pembangunan.create');
    Route::post('admin/pembangunan', [KegiatanPembangunanController::class, 'store'])->name('admin.pembangunan.store');
    Route::get('admin/pembangunan/show/{kegiatan}', [KegiatanPembangunanController::class, 'show'])->name('admin.pembangunan.show');
    Route::get('admin/pembangunan/edit/{kegiatan}', [KegiatanPembangunanController::class, 'edit'])->name('admin.pembangunan.edit');
    Route::put('admin/pembangunan/update/{kegiatan}', [KegiatanPembangunanController::class, 'update'])->name('admin.pembangunan.update');
    Route::get('admin/pembangunan/{kegiatan}', [KegiatanPembangunanController::class, 'destroy'])->name('admin.pembangunan.destroy');
    

    // Progres Pembangunan Routes
    Route::get('admin/pembangunan/progres/create/{kegiatan}', [ProgresPembangunanController::class, 'create'])
        ->name('admin.pembangunan.progres.create');
    Route::post('admin/pembangunan/progres/store/{kegiatan}', [ProgresPembangunanController::class, 'store'])
        ->name('admin.pembangunan.progres.store');
});

Route::get('test_generate_no_surat', function () {
    return generateNoSurat();
});

// Kepala Routes
Route::middleware(['role:kepala'])->group(function () {
    Route::get('/kepala/dashboard', function () {
        return view('kepala.dashboard');
    })->name('kepala.dashboard');
});

// Operator Routes
Route::middleware(['role:operator'])->group(function () {
    Route::get('/operator/dashboard', function () {
        return view('operator.dashboard');
    })->name('operator.dashboard');
});

// User Routes
Route::middleware(['role:user'])->group(function () {
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');
});
// multiple role
Route::middleware(['role:admin,operator'])->group(function () {
    Route::get('/shared-route', function () {
        return 'Accessible by Admin and Operator';
    });
});

Route::get('storage/{path}', function($path) {
    $filePath = storage_path('app/public/' . $path);
    
    if (!file_exists($filePath)) {
        abort(404);
    }

    return response()->file($filePath, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET',
        'Access-Control-Allow-Headers' => 'Content-Type',
        'Content-Type' => mime_content_type($filePath)
    ]);
})->where('path', '.*');

// Test FPDI functionality
Route::get('/test-fpdi-simple', function() {
    try {
        // Include FPDF first, then FPDI
        require_once base_path('vendor/setasign/fpdf/fpdf.php');
        require_once base_path('vendor/setasign/fpdi/src/autoload.php');
        
        $pdf = new \setasign\Fpdi\Fpdi();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);
        $pdf->Text(50, 50, 'FPDI is working correctly!');
        $pdf->Text(50, 70, 'Date: ' . date('Y-m-d H:i:s'));
        // $pdf->Text(50, 90, 'FPDF Version: ' . (defined('FPDF_VERSION') ? FPDF_VERSION : 'Unknown'));
        
        return response($pdf->Output('S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="fpdi-test.pdf"');
    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
});

// API routes for AJAX requests
Route::get('/api/pemohon/{id}', function($id) {
    try {
        // Ambil data dari duk_pelayanan dan join dengan data identitas
        $pelayanan = \DB::table('duk_pelayanan as dp')
            ->where('dp.id', $id)
            ->first();
            
        if (!$pelayanan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pelayanan tidak ditemukan'
            ], 404);
        }
        
        // Ambil data identitas pemohon untuk pelayanan ini
        $dataIdentitas = \DB::table('duk_data_identitas_pemohon as ddip')
            ->join('duk_identitas_pemohon as dip', 'ddip.identitas_pemohon_id', '=', 'dip.id')
            ->where('ddip.pelayanan_id', $id)
            ->select('dip.nama_field', 'ddip.nilai', 'dip.tipe_field')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'pelayanan' => $pelayanan,
                'identitas' => $dataIdentitas
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
})->name('api.pemohon.show');

// Test route for debugging pelayanan data
Route::get('/test-pelayanan/{id}', [\App\Http\Controllers\TestController::class, 'testPelayananData']);




