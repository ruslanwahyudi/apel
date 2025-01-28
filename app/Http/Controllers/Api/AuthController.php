<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dusun;
use App\Models\Notifications;
use App\Models\Roles_type;
use App\Models\User;
use App\Models\UserProfile;
use Google\Client;
use Http;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required_without:email|string|unique:users,phone',
            'email' => 'required_without:phone|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            'fcm_token' => 'nullable|string' // untuk push notification
        ]);

        if ($validator->fails()) {
            // return response()->json($validator->errors(), 422);
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'role_id' => Roles_type::where('name', 'User')->first()->id,
            'password' => Hash::make($request->password),
            'fcm_token' => $request->fcm_token
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        if($user){
            UserProfile::create([
                'user_id' => $user->id,
                'nik' => '',
                'nama_lengkap' => $request->name,
                'tanggal_lahir' => '2000-01-01',
                'tempat_lahir' => 'Pamekasan',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Pamekasan',
                'no_hp' => '',
                'foto' => 'default.png',   
                'status_pernikahan' => '',
                'pekerjaan' => '',
                'kewarganegaraan' => '',
                'agama' => ''
            ]);
        }   

        $profile = UserProfile::where('user_id', $user->id)->first();
        if(!$profile){
            $profile = new UserProfile([
                'user_id' => $user->id,
                'nik' => '',
                'nama' => '',
                'tempat_lahir' => '',
                'tanggal_lahir' => '',
                'jenis_kelamin' => '',
                'alamat' => '',
                'no_hp' => '',
                'foto' => '',
                'status_pernikahan' => '',
                'pekerjaan' => '',
                'kewarganegaraan' => '',
                'agama' => ''
            ]);
        }
        $user->profile = $profile;

        // Kirim email verifikasi
        $user->notify(new \App\Notifications\CustomVerifyEmail);

        return response()->json([
            'success' => true,
            'message' => 'Register successful',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ], 201);
    }

    public function resendEmailVerificationNotification(Request $request)
    {
        $request->user()->notify(new \App\Notifications\CustomVerifyEmail);
        return response()->json([
            'success' => true,
            'message' => 'Email verification berhasil dikirim, silahkan cek email anda'
        ]);
    }

    public function verifyEmail(Request $request)
    {
        try {
            $user = User::findOrFail($request->route('id'));
            
            if ($user->email_verified_at) {
                // return response()->json([
                //     'success' => false,
                //     'message' => 'Email already verified'
                // ]);
                // tampilkan return pesan dengan format html , bukan json
                return '<h1>Email sudah terverifikasi</h1>';
            }

            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }

            // return response()->json([
            //     'success' => true,
            //     'message' => 'Email has been verified successfully'
            // ]);
            return '<h1>Email berhasil terverifikasi</h1>';

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error verifying email: ' . $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required_without:email|string',
            'email' => 'required_without:phone|email',
            'password' => 'required',
            'fcm_token' => 'nullable|string' // untuk push notification
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check if login using phone or email
        $credentials = $request->has('email') 
            ? ['email' => $request->email, 'password' => $request->password]
            : ['phone' => $request->phone, 'password' => $request->password];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = Auth::user();
        
        // Update FCM token jika ada
        if ($request->fcm_token) {
            $user->update(['fcm_token' => $request->fcm_token]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        

        $profile = UserProfile::where('user_id', $user->id)->first();
        if (!$profile) {
            $profile = new UserProfile([
                'user_id' => $user->id,
                'nik' => '',
                'nama_lengkap' => $user->name,
                'tempat_lahir' => 'Pamekasan',
                'tanggal_lahir' => '2000-01-01',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Pamekasan',
                'no_hp' => '',  
                'foto' => 'default.png',
                'status_pernikahan' => '',
                'pekerjaan' => '',
                'kewarganegaraan' => '',
                'agama' => ''
            ]);
            $profile->save();
        }

        $user->profile = $profile;

        $user->profile->foto = $profile->getFotoUrlAttribute();
        

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }

    
    public function loadUser(Request $request)
    {
        $user = $request->user();
        $profile = UserProfile::where('user_id', $user->id)->first();
        if (!$profile) {
            $profile = new UserProfile([
                'user_id' => $user->id,
                'nik' => '',
                'nama_lengkap' => $user->name,
                'tempat_lahir' => 'Pamekasan',
                'tanggal_lahir' => '2000-01-01',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Pamekasan',
                'no_hp' => '',  
                'foto' => 'default.png',
                'status_pernikahan' => '',
                'pekerjaan' => '',
                'kewarganegaraan' => '',
                'agama' => ''
            ]);
            $profile->save();
        }

        $user->profile = $profile;

        $user->profile->foto = $profile->getFotoUrlAttribute();
        

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dimuat',
            'data' => [
                'user' => $user,
            ]
        ]);

    }

    public function logout(Request $request)
    {
        // Clear FCM token saat logout
        $request->user()->update(['fcm_token' => null]);
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    public function uploadSelfie(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'selfie' => 'required|image|mimes:jpeg,png,jpg|max:500',
            'dusun_id' => 'required|exists:dusun,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $request->user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        if ($request->hasFile('selfie')) {
            // Hapus foto lama jika ada
            if ($profile->foto) {
                Storage::delete('public/selfies/' . $profile->foto);
            }

            $file = $request->file('selfie');
            $filename = 'selfies/' . time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/selfies', $filename);

            $profile->update([
                'foto_selfie' => $filename
            ]);

            // Update user profile
            $user->update(['status' => 'S', 'dusun_id' => $request->dusun_id]);

            // Kirim push notification
            try {
                // get kepala dusun id
                $kepalaDusun = Dusun::where('id', $request->dusun_id)->first()->user_id;
                $kepalaDusun = User::find($kepalaDusun);

                $notification = Notifications::create([
                    'user_id' => $kepalaDusun->id,
                    'title' => 'Verifikasi Registrasi Selfie',
                    'message' => "Pengajuan selfie telah dikirim",
                    'type' => 'selfie'
                ]);

                $user = User::find($kepalaDusun->id);
                
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

                    \Log::info('FCM Response for selfie approval:', [
                        'user_id' => $kepalaDusun->id,
                        'status' => $response->status(),
                        'body' => $response->json()
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error sending notification for selfie approval:', [
                    'user_id' => $kepalaDusun->id,
                    'error' => $e->getMessage()
                ]);
                // Lanjutkan eksekusi meski notifikasi gagal
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto selfie berhasil diupload, menunggu verifikasi admin',
            'data' => [
                'user' => $user,
                'profile' => $profile
            ]
        ]);
    }


    public function verifyUser(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Y,N',
            'keterangan' => 'required_if:status,rejected|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::findOrFail($userId);

        $user->update([
            'status' => $request->status === 'Y' ? 'Y' : 'N'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status verifikasi user berhasil diupdate',
            'data' => [
                'user' => $user
            ]
        ]);
    }

    public function checkVerificationStatus(Request $request)
    {
        $user = $request->user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Status verifikasi user',
            'data' => [
                'status' => $user->status
            ]
        ]);
    }

    public function loginWithGoogle(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'token_id' => 'required|string',
                'fcm_token' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Log request data
            \Log::info('Google login request', [
                'fcm_token' => $request->fcm_token
            ]);

            // Verify token with Google
            $client = new \GuzzleHttp\Client();
            $response = $client->get('https://oauth2.googleapis.com/tokeninfo', [
                'query' => ['id_token' => $request->token_id]
            ]);

            $payload = json_decode((string) $response->getBody());

            // Check if email is verified
            if (!$payload->email_verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not verified with Google'
                ], 401);
            }

            // Find or create user
            $user = User::where('email', $payload->email)->first();

            if (!$user) {
                \Log::info('Creating new user', [
                    'email' => $payload->email,
                    'fcm_token' => $request->fcm_token
                ]);

                try {
                    // Buat user dengan DB transaction
                    DB::beginTransaction();

                    $userData = [
                        'name' => $payload->name,
                        'email' => $payload->email,
                        'password' => Hash::make(Str::random(24)),
                        'role_id' => Roles_type::where('name', 'User')->first()->id,
                        'email_verified_at' => now(),
                        'fcm_token' => $request->fcm_token ?? null // Pastikan tidak string kosong
                    ];

                    // Log data sebelum create
                    \Log::info('User data before create:', $userData);

                    $user = User::create($userData);

                    // Log hasil create
                    \Log::info('User after create:', [
                        'user_id' => $user->id,
                        'fcm_token' => $user->fcm_token,
                        'raw_attributes' => $user->getAttributes()
                    ]);

                    // Create user profile
                    UserProfile::create([
                        'user_id' => $user->id,
                        'nama_lengkap' => $payload->name,
                        'foto' => $payload->picture ?? 'default.png',
                        'tanggal_lahir' => '2000-01-01',
                        'tempat_lahir' => 'Pamekasan',
                        'jenis_kelamin' => 'Laki-laki',
                        'alamat' => 'Pamekasan',
                    ]);

                    DB::commit();

                    // Refresh user data
                    $user = $user->fresh();

                    \Log::info('User after refresh:', [
                        'user_id' => $user->id,
                        'fcm_token' => $user->fcm_token
                    ]);

                    if($request->fcm_token){
                        $user->fcm_token = $request->fcm_token;
                        $user->save();
                    }

                } catch (\Exception $e) {
                    DB::rollback();
                    \Log::error('Error creating user:', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            } else {
                \Log::info('Updating existing user', [
                    'user_id' => $user->id,
                    'old_fcm_token' => $user->fcm_token,
                    'new_fcm_token' => $request->fcm_token
                ]);

                // Update user menggunakan save() daripada update()
                $user->fcm_token = $request->fcm_token;
                $user->save();

                \Log::info('User updated', [
                    'user_id' => $user->id,
                    'updated_fcm_token' => $user->fresh()->fcm_token
                ]);
            }

            // Refresh user data dari database
            $user = $user->fresh();

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Get profile
            $profile = $user->profile;
            if (!$profile) {
                $profile = UserProfile::create([
                    'user_id' => $user->id,
                    'nama_lengkap' => $user->name,
                    'foto' => 'default.png',
                    'tanggal_lahir' => '2000-01-01',
                    'tempat_lahir' => 'Pamekasan',
                    'jenis_kelamin' => 'Laki-laki',
                    'alamat' => 'Pamekasan',
                ]);
            }

            // $user->profile = $profile;
            // $user->profile->foto = $profile->getFotoUrlAttribute();

            // Cek apakah foto sudah berupa URL lengkap
            if ($profile && $profile->foto) {
                if (filter_var($profile->foto, FILTER_VALIDATE_URL)) {
                    // Jika sudah URL lengkap, gunakan langsung
                    $profile->foto = $profile->foto;
                } else {
                    // Jika bukan URL lengkap, gunakan getFotoUrlAttribute
                    $user->profile->foto = $user->profile->getFotoUrlAttribute();
                }
            }   

            // Log untuk debugging
            \Log::info('Google login success', [
                'user_id' => $user->id,
                'fcm_token' => $request->fcm_token,
                'updated_fcm_token' => $user->fcm_token
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login with Google successful',
                'data' => [
                    'user' => $user->fresh(), // Pastikan data terbaru
                    'profile' => $profile,
                    'token' => $token
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Google login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error during Google login: ' . $e->getMessage()
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'success' => true,
                    'message' => 'Reset password link has been sent to your email'
                ]);
            }

            // Log error untuk debugging
            \Log::error('Failed to send password reset email', [
                'email' => $request->email,
                'status' => $status,
                'error' => error_get_last()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to send reset link',
                'debug' => config('app.debug') ? error_get_last() : null
            ], 400);

        } catch (\Exception $e) {
            \Log::error('Exception when sending password reset email', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error sending reset link: ' . $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid reset token'
        ], 400);
    }
} 