<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GaleriController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SuratController;
use App\Http\Controllers\Api\KategoriSuratController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\LayananController;
use App\Http\Controllers\Api\SettingHomeController;
use App\Http\Controllers\Api\KontakController;
use App\Http\Controllers\Api\DusunController;
use App\Http\Controllers\Api\PembangunanController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\WisataController;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('login/google', [AuthController::class, 'loginWithGoogle']);

// Storage route - tambahkan di atas route lainnya
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

// Public Content routes
Route::get('galeri', [GaleriController::class, 'index']);
Route::get('galeri/{id}', [GaleriController::class, 'show']);
Route::get('berita', [PostController::class, 'index']);
Route::get('berita/{id}', [PostController::class, 'show']);
Route::get('settinghome', [SettingHomeController::class, 'index']);
Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->name('verification.verify');
Route::get('layanan/jenis', [LayananController::class, 'getJenisLayanan']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // resend email verification
    Route::post('email/verification-notification', [AuthController::class, 'resendEmailVerificationNotification']);

    // Verification routes
    Route::post('upload-selfie', [AuthController::class, 'uploadSelfie']);
    
    Route::get('verification-status', [AuthController::class, 'checkVerificationStatus']);
    Route::get('profile/load', [AuthController::class, 'loadUser']);
    Route::get('profile/daftar-pengajuan-user', [AuthController::class, 'getDaftarPengajuanUser']);
    Route::get('profile/detail-pengajuan-user/{id}', [AuthController::class, 'getDetailPengajuanUser']);
    Route::post('profile/approve-pengajuan-user', [AuthController::class, 'approvePengajuanUser']);
    
    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        Route::post('verify-user/{userId}', [AuthController::class, 'verifyUser']);
    });
    
    // Profile routes
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile/update', [ProfileController::class, 'update']);
    Route::post('profile/password', [ProfileController::class, 'updatePassword']);
    Route::post('profile/photo', [ProfileController::class, 'updatePhotoProfile']);
    
    
    // Layanan routes
    Route::post('layanan', [LayananController::class, 'index']);
    
    Route::get('layanan/{id}', [LayananController::class, 'show']);
    Route::get('layanan_old/{id}', [LayananController::class, 'showOld']);
    Route::post('layanan/store', [LayananController::class, 'store']);
    Route::post('layanan/upload-identitas', [LayananController::class, 'uploadIdentitas']);
    Route::post('layanan/upload-dokumen', [LayananController::class, 'uploadDokumen']);
    Route::get('layanan/finalisasi/{id}', [LayananController::class, 'finalisasi']);
    Route::get('layanan/approve/{id}', [LayananController::class, 'approve']);
    Route::get('layanan/tandatangan/{id}', [LayananController::class, 'tandatangan']);
    Route::get('layanan/download-signed-document/{id}', [LayananController::class, 'downloadSignedDocument']);
    Route::get('layanan/klasifikasi-identitas/{jenisPelayananId}', [LayananController::class, 'getKlasifikasiIdentitas']);
    Route::post('layanan/reject', [LayananController::class, 'reject']);

    
    Route::delete('layanan/{id}', [LayananController::class, 'destroy']);
    
    // Notifikasi routes
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications/read', [NotificationController::class, 'markAsRead']);
    
    // Surat routes
    Route::apiResource('surat', SuratController::class);
    Route::apiResource('kategori-surat', KategoriSuratController::class);

    // Test notification route
    Route::post('notifications/test-send', [NotificationController::class, 'sendTestNotification']);
    Route::post('notifications/test-send-all', [NotificationController::class, 'sendTestNotificationAll']);

    // Dusun routes
    
});

// Tambahkan route ini sementara untuk debugging
Route::get('/debug/logs', function() {
    $logFile = storage_path('logs/laravel.log');
    $contents = file_exists($logFile) ? file_get_contents($logFile) : 'Log file empty';
    
    // Ambil 100 baris terakhir
    $lines = array_slice(explode("\n", $contents), -100);
    
    return response()->json([
        'logs' => $lines
    ]);
});

// Password Reset Routes
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

// Settings & Pages Routes
Route::get('/settings/faq', [SettingHomeController::class, 'getFaq']);
Route::get('/settings/disclaimer', [SettingHomeController::class, 'getDisclaimer']);
Route::get('/settings/privacy-policy', [SettingHomeController::class, 'getPrivacyPolicy']);
Route::get('/settings/contact', [SettingHomeController::class, 'getContact']);
Route::get('/settings/about', [SettingHomeController::class, 'getAbout']);
Route::get('/settings/lokasi', [SettingHomeController::class, 'getLokasi']);

Route::get('daftardusun', [SettingHomeController::class, 'getDaftarDusun']);

// Pembangunan API Routes
Route::get('pembangunan', [PembangunanController::class, 'index']);
Route::get('pembangunan/{id}', [PembangunanController::class, 'show']);

// Produk API Routes
Route::get('produk', [ProdukController::class, 'index']);
Route::get('produk/{id}', [ProdukController::class, 'show']);

// Wisata API Routes  
Route::get('wisata', [WisataController::class, 'index']);
Route::get('wisata/{id}', [WisataController::class, 'show']);

// API route untuk mendapatkan pelayanan berdasarkan jenis layanan (untuk multiple print)
Route::get('pelayanan/by-jenis/{jenisLayananId}', function($jenisLayananId) {
    try {
        // Ambil data pelayanan berdasarkan jenis layanan
        $pelayananList = \DB::table('duk_pelayanan as dp')
            ->join('duk_jenis_pelayanan as djp', 'dp.jenis_pelayanan_id', '=', 'djp.id')
            ->where('dp.jenis_pelayanan_id', $jenisLayananId)
            ->where('dp.status', 'selesai') // Hanya ambil yang sudah selesai
            ->select('dp.id', 'dp.nama', 'dp.nik', 'dp.created_at', 'djp.nama as jenis_layanan')
            ->orderBy('dp.created_at', 'desc')
            ->limit(100) // Batasi untuk performa
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pelayananList,
            'count' => $pelayananList->count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
})->name('api.pelayanan.by-jenis');
