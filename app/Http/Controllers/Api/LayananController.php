<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\adm\KategoriSurat;
use App\Models\adm\RegisterSurat;
use App\Models\Dusun;
use App\Models\Layanan\JenisLayanan;
use App\Models\Layanan\Pelayanan;
use App\Models\Layanan\DataIdentitasPemohon;
use App\Models\Layanan\DokumenPengajuan;
use App\Models\Layanan\JenisPelayanan;
use App\Models\MasterOption;
use App\Models\Notifications;
use App\Models\User;
use Google\Client;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LayananController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userId = $request->user_id;
            $user = User::findOrFail($userId);

            // Query dasar dengan relasi
            $query = Pelayanan::with(['jenisPelayanan', 'dataIdentitas', 'dokumenPengajuan', 'statusLayanan'])
                ->select('*'); // Pastikan semua kolom termasuk signed_document_path diambil

            // Filter berdasarkan role
            if ($user->role === 'user') {
                $query->where('user_id', $userId);
            } else if ($user->role === 'Kepala Dusun'){
                // $query->where('status_layanan', '=', '6');
                $query->where('user_id', $userId);
            } else if ($user->role === 'Kepala Desa'){
                $query->where('status_layanan', '=', '7')
                ->orWhere('status_layanan', '=', '8');
            } else { // admin
                $query->where('status_layanan', '<>', '5');
            }

            // Filter berdasarkan catatan/keterangan
            if ($request->has('q')) {
                $search = $request->q;
                // Menggunakan whereRaw dengan parameter binding untuk LIKE
                $query->whereRaw('catatan LIKE ?', ['%' . $search . '%']);
            }

            // Filter berdasarkan jenis layanan
            if ($request->has('jenis_pelayanan_id')) {
                $query->where('jenis_pelayanan_id', $request->jenis_pelayanan_id);
            }

            // Filter berdasarkan range tanggal
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            // Urutkan berdasarkan created_at terbaru
            $query->orderBy('created_at', 'desc');

            // Ambil data
            $layanan = $query->get();

            Log::info('Layanan');
            Log::info($layanan);

            return response()->json([
                'success' => true,
                'message' => 'Daftar Layanan '. $user->role,
                'data' => $layanan,
                'query' => $query->toSql(),
                'bindings' => $query->getBindings() // Menampilkan parameter binding
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function show($id)
    {
        $layanan = Pelayanan::with([
            'jenisPelayanan',
            'jenisPelayanan.identitasPemohon',
            'jenisPelayanan.identitasPemohon.dataIdentitas' => function($query) use ($id) {
                $query->where('pelayanan_id', $id);
            },
            'jenisPelayanan.syaratDokumen',
            'jenisPelayanan.syaratDokumen.dokumenPengajuan' => function($query) use ($id) {
                $query->where('pelayanan_id', $id);
            },
            'statusLayanan',
            'surat'
        ])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'message' => 'Detail Layanan',
            'data' => $layanan
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_pelayanan_id' => 'required|exists:duk_jenis_pelayanan,id',
            'catatan' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'id' => 'nullable|exists:duk_pelayanan,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $statusLayanan = MasterOption::where(['value' => 'Draft', 'type' => 'status_layanan'])->first();
        $request->merge(['status_layanan' => $statusLayanan->id]);

        $layanan = Pelayanan::updateOrCreate(
            [
                'id' => $request->id,
                'user_id' => $request->user_id,
            ],
            [
                'jenis_pelayanan_id' => $request->jenis_pelayanan_id,
                'catatan' => $request->catatan,
                'status_layanan' => $request->status_layanan,
            ]
        );
        

        $layanan->load('jenisPelayanan', 'dataIdentitas', 'dokumenPengajuan', 'statusLayanan');

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil disimpan',
            'data' => $layanan
        ], 201);
    }

    public function uploadIdentitas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identitas_pemohon_id.*' => 'required|exists:duk_identitas_pemohon,id',
            'nilai.*' => 'nullable|string',
            'pelayanan_id' => 'required|exists:duk_pelayanan,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        foreach ($request->identitas_pemohon_id as $key => $value) {
            $dataIdentitas = DataIdentitasPemohon::updateOrCreate(
                ['identitas_pemohon_id' => $value, 'pelayanan_id' => $request->pelayanan_id],
                ['nilai' => $request->nilai[$key]]
            );
        }

        $identitas = DataIdentitasPemohon::where('pelayanan_id', $request->pelayanan_id)->get();


        return response()->json([
            'success' => true,
            'message' => 'Data identitas berhasil diperbarui',
            'data' => $identitas
        ], 201);
    }

    public function uploadDokumen(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'syarat_dokumen_id' => 'required|array',
            'syarat_dokumen_id.*' => 'required|exists:duk_syarat_dokumen,id',
            'path' => 'nullable|array',
            'path.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'pelayanan_id' => 'required|exists:duk_pelayanan,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->syarat_dokumen_id as $key => $dokumenId) {
                // Jika ada file yang diupload
                if ($request->hasFile("path.{$key}")) {
                    $file = $request->file("path.{$key}");
                    $filename = time() . '_' . $dokumenId . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('dokumen_pengajuan', $filename, 'public');

                    // Update atau create dokumen
                    DokumenPengajuan::updateOrCreate(
                        [
                            'syarat_dokumen_id' => $dokumenId,
                            'pelayanan_id' => $request->pelayanan_id,
                        ],
                        [
                            'path_dokumen' => $path,
                            'uploaded_at' => now(),
                        ]
                    );
                } else {
                    // Jika tidak ada file, tetap create record tanpa path
                    DokumenPengajuan::updateOrCreate(
                        [
                            'syarat_dokumen_id' => $dokumenId,
                            'pelayanan_id' => $request->pelayanan_id,
                        ],
                        [
                            'uploaded_at' => now(),
                        ]
                    );
                }
            }

            // Ambil semua dokumen untuk pelayanan ini
            $dokumen = DokumenPengajuan::with('syaratDokumen')
                ->where('pelayanan_id', $request->pelayanan_id)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil disimpan',
                'data' => $dokumen
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan dokumen',
                'error' => $e->getMessage()
            ], 500);
        }
    }   

    public function finalisasi(Request $request, $id)
    {
        // $validator = Validator::make($request->all(), [
        //     'id' => 'required|exists:duk_pelayanan,id',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

        \Log::info('Finalisasi method called', [
            'received_id' => $id,
            'id_type' => gettype($id),
            'request_all' => $request->all(),
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl()
        ]);

        $layanan = Pelayanan::findOrFail($id);
        $user = User::find($layanan->user_id);

        $statusLayanan = MasterOption::where(['value' => 'Belum Diproses', 'type' => 'status_layanan'])->first();

        $upd = $layanan->update(['status_layanan' => $statusLayanan->id]);
        
        $layanan->load(['jenisPelayanan', 'dataIdentitas', 'dokumenPengajuan', 'statusLayanan']);

        if($upd){
            // Kirim notifikasi ke kepala dusun
            try {
                // get kepala dusun id
                $kepalaDusun = Dusun::where('id', $user->dusun_id)->first()->user_id;
                $kepalaDusun = User::find($kepalaDusun);

                $notification = Notifications::create([
                    'user_id' => $kepalaDusun->id,
                    'title' => 'Layanan Baru',
                    'message' => "Pengajuan layanan telah dikirim ke akun Anda",
                    'type' => 'auto'
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
            'message' => 'Layanan berhasil di finalisasi',
            'data' => $layanan
        ], 200);
    }

    public function approve(Request $request, $id)
    {
        // Debug logging
        \Log::info('Approve method called', [
            'received_id' => $id,
            'id_type' => gettype($id),
            'request_all' => $request->all(),
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl()
        ]);
        
        try {
            // Validasi ID
            if (!is_numeric($id) || $id <= 0) {
                \Log::warning('Invalid ID received in approve method', [
                    'received_id' => $id,
                    'id_type' => gettype($id)
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'ID layanan tidak valid'
                ], 400);
            }

            // Cari layanan dengan error handling yang lebih baik
            $layanan = Pelayanan::with('jenisPelayanan')->find($id);
            
            if (!$layanan) {
                \Log::warning('Layanan not found', [
                    'requested_id' => $id,
                    'available_ids' => Pelayanan::pluck('id')->toArray()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan dengan ID ' . $id . ' tidak ditemukan'
                ], 404);
            }

            \Log::info('Layanan found successfully', [
                'layanan_id' => $layanan->id,
                'current_status' => $layanan->status_layanan,
                'jenis_pelayanan' => $layanan->jenisPelayanan ? $layanan->jenisPelayanan->nama_pelayanan : 'null'
            ]);

            // Cek apakah layanan sudah diproses sebelumnya
            // if ($layanan->status_layanan && $layanan->status_layanan != 1) {
            //     $currentStatus = MasterOption::find($layanan->status_layanan);
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Layanan sudah diproses dengan status: ' . ($currentStatus ? $currentStatus->value : 'Unknown')
            //     ], 422);
            // }

            // $statusLayanan = MasterOption::where(['value' => 'Sedang Diproses', 'type' => 'status_layanan'])->first();
            
            // if (!$statusLayanan) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Status layanan "Sedang Diproses" tidak ditemukan di database'
            //     ], 500);
            // }

            $upd = $layanan->update(['status_layanan' => '6']);
            
            if($upd){
                 // Ambil semua kategori surat untuk jenis pelayanan ini
                $kategori_surat_list = KategoriSurat::where('jenis_pelayanan_id', $layanan->jenis_pelayanan_id)
                    ->where('tipe_surat', 'layanan')
                    ->get();
                
                // Array untuk menyimpan semua surat_id yang dibuat
                $createdSuratIds = [];
                
                // Jika tidak ada kategori surat khusus, gunakan kategori default "Layanan"
                if ($kategori_surat_list->isEmpty()) {
                    $kategori_default = KategoriSurat::where('nama', 'Layanan')->first();
                    if ($kategori_default) {
                        $no_surat = generateNoSurat();
                        $jenis_surat = $layanan->jenisPelayanan->nama_pelayanan;
                        $perihal = $layanan->jenisPelayanan->nama_pelayanan;
                        $tanggal_surat = now();
                        $status_surat = MasterOption::where(['value' => 'Proses', 'type' => 'status_surat'])->first()->id;
                        $signed_by = User::where('role', 'Kepala Desa')->first()->id;

                        $ins_reg_surat = RegisterSurat::create([
                            'nomor_surat' => $no_surat,
                            'kategori_surat_id' => $kategori_default->id,
                            'jenis_surat' => $jenis_surat,
                            'perihal' => $perihal,
                            'tanggal_surat' => $tanggal_surat,
                            'status' => $status_surat,
                            'signer_id' => $signed_by,
                        ]);

                        if($ins_reg_surat){
                            $createdSuratIds[] = $ins_reg_surat->id;
                        }
                    }
                } else {
                    // Loop untuk setiap kategori surat
                    foreach ($kategori_surat_list as $kategori_surat) {
                        $no_surat = generateNoSurat();
                        $jenis_surat = $layanan->jenisPelayanan->nama_pelayanan . ' - ' . $kategori_surat->nama;
                        $perihal = $kategori_surat->nama;
                        $tanggal_surat = now();
                        $status_surat = MasterOption::where(['value' => 'Proses', 'type' => 'status_surat'])->first()->id;
                        $signed_by = User::where('role', 'Kepala Desa')->first()->id;
            
                        $ins_reg_surat = RegisterSurat::create([
                            'nomor_surat' => $no_surat,
                            'kategori_surat_id' => $kategori_surat->id,
                            'jenis_surat' => $jenis_surat,
                            'perihal' => $perihal,
                            'tanggal_surat' => $tanggal_surat,
                            'status' => $status_surat,
                            'signer_id' => $signed_by,
                        ]);
                        
                        if($ins_reg_surat){
                            $createdSuratIds[] = $ins_reg_surat->id;
                        }
                        
                        \Log::info('Register surat created in approve', [
                            'layanan_id' => $layanan->id,
                            'kategori_surat_id' => $kategori_surat->id,
                            'kategori_nama' => $kategori_surat->nama,
                            'register_surat_id' => $ins_reg_surat->id,
                            'nomor_surat' => $no_surat
                        ]);
                    }
                }

                // Update layanan dengan array semua surat_id yang dibuat
                if (!empty($createdSuratIds)) {
                    $layanan->surat_id = $createdSuratIds;
                    $layanan->save();
                    
                    \Log::info('Updated layanan with multiple surat_ids', [
                        'layanan_id' => $layanan->id,
                        'surat_ids' => $createdSuratIds,
                        'count' => count($createdSuratIds)
                    ]);
                }

                // Kirim notifikasi ke user
                try {
                    $notification = Notifications::create([
                        'user_id' => $layanan->user_id,
                        'title' => 'Layanan Disetujui',
                        'message' => "Pengajuan layanan " . 
                            ($layanan->jenisPelayanan ? $layanan->jenisPelayanan->nama_pelayanan : '') . 
                            " Anda telah disetujui",
                        'type' => 'layanan'
                    ]);

                    $user = User::find($layanan->user_id);
                    
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

                        \Log::info('FCM Response for layanan approval:', [
                            'layanan_id' => $layanan->id,
                            'user_id' => $layanan->user_id,
                            'status' => $response->status(),
                            'body' => $response->json()
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error sending notification for layanan approval:', [
                        'layanan_id' => $layanan->id,
                        'error' => $e->getMessage()
                    ]);
                    // Lanjutkan eksekusi meski notifikasi gagal
                }

            $kades = User::where('role', 'Kepala Desa')->first();
            try {
                $notification = Notifications::create([
                    'user_id' => $kades->id,
                    'title' => 'Layanan Menunggu Tandatangan',
                    'message' => "Pengajuan layanan " . 
                        ($layanan->jenisPelayanan ? $layanan->jenisPelayanan->nama_pelayanan : '') . 
                        " telah disetujui, silahkan cek di halaman layanan untuk tandatangan",
                    'type' => 'layanan'
                ]);

                if ($kades && $kades->fcm_token) {
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
                            'token' => $kades->fcm_token,
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

                    \Log::info('FCM Response for layanan signing:', [
                        'layanan_id' => $layanan->id,
                        'user_id' => $kades->id,
                        'status' => $response->status(),
                        'body' => $response->json()
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error sending notification to kades for layanan signing:', [
                    'layanan_id' => $layanan->id,
                    'error' => $e->getMessage()
                ]);
                // Lanjutkan eksekusi meski notifikasi gagal
            }
            }

            $layanan->load(['jenisPelayanan', 'dataIdentitas', 'dokumenPengajuan', 'statusLayanan']);

            return response()->json([
                'success' => true,
                'message' => 'Layanan berhasil di approve dan menunggu tandatangan',
                'data' => $layanan
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Error in approve method:', [
                'layanan_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses approval: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:duk_pelayanan,id',
            'reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            // return response()->json($validator->errors(), 422);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $layanan = Pelayanan::with('jenisPelayanan')->findOrFail($request->service_id);
        \Log::info($layanan);
        $layanan->status_layanan = 5;
        $layanan->catatan = $request->reason;
        $upd = $layanan->save();

        if($upd){
            // Kirim notifikasi ke user
            try {
                $notification = Notifications::create([
                    'user_id' => $layanan->user_id,
                    'title' => 'Layanan Ditolak',
                    'message' => "Pengajuan layanan " . 
                        ($layanan->jenisPelayanan ? $layanan->jenisPelayanan->nama_pelayanan : '') . 
                        " Anda telah ditolak, karena " . $request->reason.", Silahkan lengkapi kembali",
                    'type' => 'layanan'
                ]);

                $user = User::find($layanan->user_id);
                
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

                    \Log::info('FCM Response for layanan approval:', [
                        'layanan_id' => $layanan->id,
                        'user_id' => $layanan->user_id,
                        'status' => $response->status(),
                        'body' => $response->json()
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error sending notification for layanan approval:', [
                    'layanan_id' => $layanan->id,
                    'error' => $e->getMessage()
                ]);
                // Lanjutkan eksekusi meski notifikasi gagal
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil di kembalikan ke user',
            'data' => $layanan
        ], 200);
    }

    public function tandatangan(Request $request, $id)
    {
        $layanan = Pelayanan::with('jenisPelayanan')->findOrFail($id);

        $statusLayanan = MasterOption::where(['value' => 'Selesai', 'type' => 'status_layanan'])->first();

        $upd = $layanan->update(['status_layanan' => $statusLayanan->id]);
        
        if($upd){
            // Kirim notifikasi ke user
            try {
                $notification = Notifications::create([
                    'user_id' => $layanan->user_id,
                    'title' => 'Layanan Selesai',
                    'message' => "Pengajuan layanan " . 
                        ($layanan->jenisPelayanan ? $layanan->jenisPelayanan->nama_pelayanan : '') . 
                        " Anda telah selesai di tandatangani, silahkan ambil surat di kantor",
                    'type' => 'layanan'
                ]);

                $user = User::find($layanan->user_id);
                \Log::info($user);
                
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

                    \Log::info('FCM Response for layanan tandatangan:', [
                        'layanan_id' => $layanan->id,
                        'user_id' => $layanan->user_id,
                        'status' => $response->status(),
                        'body' => $response->json()
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error sending notification for layanan tandatangan:', [
                    'layanan_id' => $layanan->id,
                    'error' => $e->getMessage()
                ]);
                // Lanjutkan eksekusi meski notifikasi gagal
            }
        }

        $layanan->load(['jenisPelayanan', 'dataIdentitas', 'dokumenPengajuan', 'statusLayanan']);

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil di tandatangani',
            'data' => $layanan
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'persyaratan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $layanan = Pelayanan::findOrFail($id);
        $layanan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil diupdate',
            'data' => $layanan
        ]);
    }

    public function destroy($id)
    {
        $layanan = Pelayanan::findOrFail($id);
        $layanan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Layanan berhasil dihapus'
        ]);
    }

    public function getJenisLayanan()
    {
        $jenisLayanan = JenisPelayanan::get();
        
        return response()->json([
            'success' => true,
            'message' => 'List Jenis Layanan',
            'data' => $jenisLayanan
        ]);
    }

    /**
     * Download dokumen yang sudah ditandatangani untuk user Android
     */
    public function downloadSignedDocument($id)
    {
        try {
            $pelayanan = Pelayanan::with('jenisPelayanan')->findOrFail($id);

            // Cek apakah user berhak download (hanya user yang mengajukan atau admin)
            $user = auth()->user();
            if ($user->role !== 'admin' && $pelayanan->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berhak mengakses dokumen ini'
                ], 403);
            }

            if (!$pelayanan->signed_document_path || !\Storage::disk('public')->exists($pelayanan->signed_document_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen yang sudah ditandatangani tidak ditemukan'
                ], 404);
            }

            // Return URL download
            $downloadUrl = url('/storage/' . $pelayanan->signed_document_path);
            
            return response()->json([
                'success' => true,
                'message' => 'Link download dokumen yang sudah ditandatangani',
                'data' => [
                    'download_url' => $downloadUrl,
                    'file_name' => 'Dokumen_Ditandatangani_' . $pelayanan->jenisPelayanan->nama_pelayanan . '_' . $pelayanan->id . '.' . pathinfo($pelayanan->signed_document_path, PATHINFO_EXTENSION),
                    'layanan_id' => $pelayanan->id,
                    'jenis_layanan' => $pelayanan->jenisPelayanan->nama_pelayanan
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
} 