<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\blog\Announcement;
use App\Models\User;
use App\Models\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Google\Client;
use Str;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notifications::where('user_id', $request->user()->id)
            ->latest()
            ->get();
        
        $announcements = Announcement::latest()->get();

        // Convert announcements ke array dengan format yang sesuai
        $formattedAnnouncements = $announcements->map(function ($announcement) {
            return (object)[
                'id' => $announcement->id,
                'title' => $announcement->title,
                'message' => $announcement->content,
                'type' => 'Pengumuman',
                'is_read' => 1,
                'user_id' => 213133,
                'created_at' => $announcement->created_at,
                'updated_at' => $announcement->updated_at
            ];
        });

        // Gabungkan collections menggunakan concat
        $mergedNotifications = $notifications->concat($formattedAnnouncements);
        
        // Urutkan berdasarkan created_at
        $sortedNotifications = $mergedNotifications->sortByDesc('created_at')->values();
        
        return response()->json([
            'success' => true,
            'message' => 'List Notifikasi',
            'data' => [
                'unread_count' => $notifications->where('is_read', 0)->count(),
                'notifications' => $sortedNotifications
            ]
        ]);
    }

    public function markAsRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $notification = Notifications::findOrFail($request->id);
        
        $update = $notification->update([
            'is_read' => 1,
            'updated_at' => now()
        ]);

        if ($update) {
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil ditandai telah dibaca',
                'data' => $notification
            ]);
        }
                

    }

    public function sendTestNotification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'title' => 'required|string',
                'message' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::find($request->user_id);
            
            if (!$user->fcm_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak memiliki FCM token'
                ], 400);
            }

            // Simpan notifikasi ke database
            $notification = Notifications::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'message' => $request->message,
                'type' => 'manual'
            ]);

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

            // Kirim ke FCM HTTP v1
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/v1/projects/'.config('services.firebase.project_id').'/messages:send', [
                'message' => [
                    'token' => $user->fcm_token,
                    'notification' => [
                        'title' => $request->title,
                        'body' => $request->message
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

            \Log::info('FCM Response:', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notifikasi berhasil dikirim',
                    'data' => [
                        'notification' => $notification,
                        'fcm_response' => $response->json()
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim notifikasi',
                'error' => $response->json()
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Error sending notification:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendTestNotificationAll(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'message' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $slug = Str::slug($request->title.'-'.now()->timestamp);
            \Log::info('Slug: '.$slug);

            $announcement = Announcement::create([
                'category_id' => 1,
                'title' => $request->title,
                'slug' => 'announcement-'.$slug,
                'content' => $request->message,
                'created_at' => now(),
                'updated_at' => now()
            ]);

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

            // Kirim ke FCM HTTP v1 dengan format yang benar untuk topic
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/v1/projects/'.config('services.firebase.project_id').'/messages:send', [
                'message' => [
                    'topic' => 'all_users',
                    'notification' => [
                        'title' => $request->title,
                        'body' => $request->message
                    ],
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'default_sound' => true,
                            'default_vibrate_timings' => true,
                            'default_light_settings' => true
                        ]
                    ],
                    'apns' => [
                        'headers' => [
                            'apns-priority' => '10'
                        ],
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                                'content-available' => 1
                            ]
                        ]
                    ],
                    'data' => [
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'type' => 'broadcast',
                        'title' => $request->title,
                        'message' => $request->message
                    ]
                ]
            ]);

            \Log::info('FCM Response All:', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notifikasi broadcast berhasil dikirim',
                    'data' => [
                        'fcm_response' => $response->json()
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim notifikasi broadcast',
                'error' => $response->json()
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Error sending broadcast notification:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
} 