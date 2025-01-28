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
    Route::apiResource('daftardusun', SettingHomeController::class);
    Route::get('verification-status', [AuthController::class, 'checkVerificationStatus']);
    Route::get('profile/load', [AuthController::class, 'loadUser']);
    
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
    Route::post('layanan/store', [LayananController::class, 'store']);
    Route::post('layanan/upload-identitas', [LayananController::class, 'uploadIdentitas']);
    Route::post('layanan/upload-dokumen', [LayananController::class, 'uploadDokumen']);
    Route::post('layanan/finalisasi/{id}', [LayananController::class, 'finalisasi']);
    Route::post('layanan/approve/{id}', [LayananController::class, 'approve']);
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
