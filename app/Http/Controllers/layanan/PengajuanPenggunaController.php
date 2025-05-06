<?php

namespace App\Http\Controllers\layanan;

use App\Http\Controllers\Controller;
use App\Models\Notifications;
use App\Models\User;
use App\Models\UserProfile;
use Google\Client;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PengajuanPenggunaController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $pengajuan = User::with(['profile', 'dusun'])
                ->where('status', 'S')
                ->orderBy('id', 'desc')
                ->get();

            return response()->json($pengajuan);
        }

        // Return view untuk request normal/non-ajax
        return view('layanan.pengajuan_pengguna.index');
    }

    public function show($id)
    {
        $pengajuan = User::with(['profile', 'dusun'])->find($id);
        
        if (!$pengajuan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan tidak ditemukan'
            ], 404);
        }
        
        return response()->json($pengajuan);
    }

    public function approve(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::find($request->user_id);
            $user->status = 'Y';
            $saved = $user->save();

            if ($saved) {
                // kirim push notification
                try {
                    $notification = Notifications::create([
                        'user_id' => $user->id,
                        'title' => 'Registrasi Disetujui',
                        'message' => "Registrasi Anda telah disetujui",
                        'type' => 'auto'
                    ]);
    
                    if ($user && $user->fcm_token) {
                        // Generate access token
                        $client = new Client();
                        $client->setAuthConfig([
                            "type" => "service_account",
                            "project_id" => config('services.firebase.project_id'),
                            "private_key_id" => "private_key_id",
                            "private_key" => config('services.firebase.private_key'),
                            "client_email" => config('services.firebase.client_email'),
                            "client_id" => "client_id",
                            "auth_uri" => "https://accounts.google.com/o/oauth2/auth",
                            "token_uri" => "https://oauth2.googleapis.com/token",
                            "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
                            "client_x509_cert_url" => "https://www.googleapis.com/robot/v1/metadata/x509/".config('services.firebase.client_email')
                        ]);
                        
                        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
                        $client->fetchAccessTokenWithAssertion();
                        $accessToken = $client->getAccessToken()['access_token'];
    
                        // Kirim ke FCM
                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $accessToken,
                            'Content-Type' => 'application/json',
                        ])->post('https://fcm.googleapis.com/v1/projects/'.config('services.firebase.project_id').'/messages:send', [
                            'message' => [
                                'token' => $user->fcm_token,
                                'notification' => [
                                    'title' => $notification->title,
                                    'body' => $notification->message
                                ],
                                'data' => [
                                    'notification_id' => (string)$notification->id,
                                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                                ],
                                'android' => [
                                    'notification' => [
                                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                                    ]
                                ],
                                'apns' => [
                                    'payload' => [
                                        'aps' => [
                                            'sound' => 'default'
                                        ]
                                    ]
                                ]
                            ]
                        ]);
    
                        \Log::info('FCM Response for user approval:', [
                            'user_id' => $user->id,
                            'status' => $response->status(),
                            'body' => $response->json()
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error sending notification for user approval:', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                    // Lanjutkan eksekusi meski notifikasi gagal
                }
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pengajuan user berhasil diapprove',
                    'data' => $user
                ]);
            }

            return redirect()->route('layanan.pengajuan_pengguna')
                ->with('success', 'Pengajuan user berhasil diapprove');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error approving user: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::find($request->user_id);
            $user->status = 'N';
            $saved = $user->save();

            if ($saved) {
                // Kirim notifikasi penolakan
                try {
                    $notification = Notifications::create([
                        'user_id' => $user->id,
                        'title' => 'Registrasi Ditolak',
                        'message' => "Registrasi Anda ditolak dengan alasan: " . $request->reason,
                        'type' => 'auto'
                    ]);

                    // Implementasi pengiriman notifikasi FCM bisa ditambahkan disini
                } catch (\Exception $e) {
                    \Log::error('Error sending rejection notification:', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pengajuan user berhasil ditolak',
                    'data' => $user
                ]);
            }

            return redirect()->route('layanan.pengajuan_pengguna')
                ->with('success', 'Pengajuan user berhasil ditolak');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error rejecting user: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function search($search)
    {
        $pengajuan = User::with(['profile', 'dusun'])
            ->where('status', 'S')
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('profile', function($q) use ($search) {
                        $q->where('nama_lengkap', 'like', "%{$search}%")
                          ->orWhere('nik', 'like', "%{$search}%");
                    });
            })
            ->get();

        return response()->json($pengajuan);
    }
} 