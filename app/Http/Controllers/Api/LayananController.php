<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\adm\KategoriSurat;
use App\Models\adm\RegisterSurat;
use Barryvdh\DomPDF\Facade\Pdf;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LayananController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userId = $request->user_id;
            $user = User::findOrFail($userId);

            // Query dasar dengan relasi
            $query = Pelayanan::with(['jenisPelayanan', 'dataIdentitas', 'dokumenPengajuan', 'statusLayanan','surat'])
                ->select('*'); // Pastikan semua kolom termasuk signed_document_path diambil

            // Filter berdasarkan role
            if ($user->role === 'user') {
                $query->where('user_id', $userId);
            } else if ($user->role === 'Kepala Dusun'){
                // $query->where('user_id', $userId);
                $query->where(function($q) use ($userId) {
                    $q->where('user_id', $userId)
                      ->orWhere(function($subQ) {
                          $subQ->where('status_layanan', '6')
                          ->orWhere('status_layanan', '7')
                          ->orWhere('status_layanan', '8');
                      });
                });
                // ->where('user_id', $userId);
            } else if ($user->role === 'Kepala Desa'){
                $query->where(function($q) {
                    $q->where('status_layanan', '7')
                      ->orWhere('status_layanan', '8');
                });
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
            'jenisPelayanan.identitasPemohon.klasifikasi',
            'jenisPelayanan.identitasPemohon.dataIdentitas' => function($query) use ($id) {
                $query->where('pelayanan_id', $id);
            },
            'jenisPelayanan.syaratDokumen',
            'jenisPelayanan.syaratDokumen.dokumenPengajuan' => function($query) use ($id) {
                $query->where('pelayanan_id', $id);
            },
            'statusLayanan'
        ])->findOrFail($id);
        
        // Ambil data surat berdasarkan temp_surat_id (Array)
        $suratData = [];
        if (!empty($layanan->temp_surat_id) && is_array($layanan->temp_surat_id)) {
            $registerSuratList = \App\Models\adm\RegisterSurat::whereIn('id', $layanan->temp_surat_id)
                ->with(['kategori_surat', 'signer'])
                ->get();
            
            foreach ($registerSuratList as $surat) {
                $suratData[] = [
                    'id' => $surat->id,
                    'nomor_surat' => $surat->nomor_surat,
                    'jenis_surat' => $surat->jenis_surat,
                    'perihal' => $surat->perihal,
                    'tanggal_surat' => $surat->tanggal_surat,
                    'status' => $surat->status,
                    'kategori_surat' => $surat->kategori_surat ? [
                        'id' => $surat->kategori_surat->id,
                        'nama' => $surat->kategori_surat->nama
                    ] : null,
                    'signer' => $surat->signer ? [
                        'id' => $surat->signer->id,
                        'name' => $surat->signer->name
                    ] : null,
                    // URL file surat: jika status = 3 (ditandatangani) dan ada signed_pdf_path, berikan URL
                    'signed_pdf_url' => ($surat->status == 3 && $surat->signed_pdf_path) 
                        ? Storage::url($surat->signed_pdf_path) 
                        : null,
                    'signed_pdf_path' => $surat->signed_pdf_path,
                    'time_signed_pdf_path' => $surat->time_signed_pdf_path_formatted
                ];
            }
        }
        
        // Tambahkan data surat ke response
        $layanan->surat = $suratData;
        
        // Kelompokkan identitas pemohon berdasarkan klasifikasi untuk memudahkan frontend
        if ($layanan->jenisPelayanan && $layanan->jenisPelayanan->identitasPemohon) {
            $identitasPemohon = $layanan->jenisPelayanan->identitasPemohon;
            
            // Kelompokkan berdasarkan klasifikasi
            $groupedIdentitas = $identitasPemohon->groupBy('klasifikasi_id');
            
            $klasifikasiGroups = [];
            foreach ($groupedIdentitas as $klasifikasiId => $fields) {
                $klasifikasi = $fields->first()->klasifikasi;
                $klasifikasiGroups[] = [
                    'klasifikasi_id' => $klasifikasiId,
                    'klasifikasi_nama' => $klasifikasi ? $klasifikasi->nama_klasifikasi : 'Tidak Berkategori',
                    'klasifikasi_deskripsi' => $klasifikasi ? $klasifikasi->deskripsi : '',
                    'klasifikasi_urutan' => $klasifikasi ? $klasifikasi->urutan : 999,
                    'fields' => $fields->map(function ($field) use ($id) {
                        // Ambil nilai data identitas untuk field ini
                        $dataIdentitas = $field->dataIdentitas->where('pelayanan_id', $id)->first();
                        return [
                            'id' => $field->id,
                            'nama_field' => $field->nama_field,
                            'label' => $field->label,
                            'tipe_field' => $field->tipe_field,
                            'required' => $field->required,
                            'readonly' => $field->readonly,
                            'nilai' => $dataIdentitas ? $dataIdentitas->nilai : null,
                            'data_identitas_id' => $dataIdentitas ? $dataIdentitas->id : null
                        ];
                    })->toArray()
                ];
            }
            
            // Urutkan berdasarkan urutan klasifikasi
            usort($klasifikasiGroups, function($a, $b) {
                return $a['klasifikasi_urutan'] <=> $b['klasifikasi_urutan'];
            });
            
            // Tambahkan data yang sudah dikelompokkan ke response
            $layanan->identitas_pemohon_grouped = $klasifikasiGroups;
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Detail Layanan',
            'data' => $layanan
        ]);
    }

    public function showOld($id)
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
                            // 'notification' => [
                            //     'title' => $notification->title,
                            //     'body' => $notification->message
                            // ],
                            'data' => [
                                'notification_id' => (string)$notification->id,
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                'route' => '/service-detail',
                                'pelayanan_id' => (string)$id, 
                                'title' => $notification->title,
                                'body' => $notification->message
                            ],
                            // 'android' => [
                            //     'notification' => [
                            //         'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                            //     ]
                            // ],
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

            $upd = $layanan->update(['status_layanan' => '7']);
            
            if($upd){
                 // Ambil semua kategori surat untuk jenis pelayanan ini
                $kategori_surat_list = KategoriSurat::where('jenis_pelayanan_id', $layanan->jenis_pelayanan_id)
                    ->where('tipe_surat', 'layanan')
                    ->get();
                
                // Array untuk menyimpan semua temp_surat_id yang dibuat
                $createdSuratIds = [];
                
                // Jika tidak ada kategori surat khusus, gunakan kategori default "Layanan"
                if ($kategori_surat_list->isEmpty()) {
                    $kategori_default = KategoriSurat::where('nama', 'Layanan')->first();
                    if ($kategori_default) {
                        $no_surat = generateNoSurat();
                        $urut_register = generateUrutRegister();
                        $jenis_surat = $layanan->jenisPelayanan->nama_pelayanan;
                        $perihal = $layanan->jenisPelayanan->nama_pelayanan;
                        $tanggal_surat = now();
                        $status_surat = MasterOption::where(['value' => 'Proses', 'type' => 'status_surat'])->first()->id;
                        $signed_by = User::where('role', 'Kepala Desa')->first()->id;

                        $ins_reg_surat = RegisterSurat::create([
                            'nomor_surat' => $no_surat,
                            'urut_register' => $urut_register,  
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
                        $urut_register = generateUrutRegister();
                        $jenis_surat = $layanan->jenisPelayanan->nama_pelayanan . ' - ' . $kategori_surat->nama;
                        $perihal = $kategori_surat->nama;
                        $tanggal_surat = now();
                        $status_surat = MasterOption::where(['value' => 'Proses', 'type' => 'status_surat'])->first()->id;
                        $signed_by = User::where('role', 'Kepala Desa')->first()->id;
            
                        $ins_reg_surat = RegisterSurat::create([
                            'nomor_surat' => $no_surat,
                            'urut_register' => $urut_register,
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

                // Update layanan dengan array semua temp_surat_id yang dibuat
                if (!empty($createdSuratIds)) {
                    $layanan->temp_surat_id = $createdSuratIds;
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
                                // 'notification' => [
                                //     'title' => $notification->title,
                                //     'body' => $notification->message
                                // ],
                                'data' => [
                                    'notification_id' => (string)$notification->id,
                                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                    'route' => '/service-detail',
                                    'pelayanan_id' => (string)$id, 
                                    'title' => $notification->title,
                                    'body' => $notification->message
                                ],
                                // 'android' => [
                                //     'notification' => [
                                //         'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                                //     ]
                                // ],
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
                            // 'notification' => [
                            //     'title' => $notification->title,
                            //     'body' => $notification->message
                            // ],
                            'data' => [
                                'notification_id' => (string)$notification->id,
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                'route' => '/service-detail',
                                'pelayanan_id' => (string)$id, 
                                'title' => $notification->title,
                                'body' => $notification->message
                            ],
                            // 'android' => [
                            //     'notification' => [
                            //         'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                            //     ]
                            // ],
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
                            // 'notification' => [
                            //     'title' => $notification->title,
                            //     'body' => $notification->message
                            // ],
                            'data' => [
                                'notification_id' => (string)$notification->id,
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                'route' => '/service-detail',
                                'pelayanan_id' => (string)$layanan->id, 
                                'title' => $notification->title,
                                'body' => $notification->message
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
            // Generate dan simpan PDF untuk semua surat yang terkait dengan layanan ini
            try {
                $this->generateAndSavePdfsForLayanan($layanan);
            } catch (\Exception $e) {
                \Log::error('Error generating PDFs for layanan tandatangan:', [
                    'layanan_id' => $layanan->id,
                    'error' => $e->getMessage()
                ]);
                // Lanjutkan eksekusi meski generate PDF gagal
            }

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
                            // 'notification' => [
                            //     'title' => $notification->title,
                            //     'body' => $notification->message
                            // ],
                            'data' => [
                                'notification_id' => (string)$notification->id,
                                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                                'route' => '/service-detail',
                                'pelayanan_id' => (string)$id, 
                                'title' => $notification->title,
                                'body' => $notification->message
                            ],
                            // 'android' => [
                            //     'notification' => [
                            //         'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                            //     ]
                            // ],
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
     * Get klasifikasi identitas pemohon untuk jenis layanan tertentu
     */
    public function getKlasifikasiIdentitas($jenisPelayananId)
    {
        try {
            $klasifikasi = \App\Models\Layanan\KlasifikasiIdentitasPemohon::whereHas('identitasPemohon', function($query) use ($jenisPelayananId) {
                $query->where('jenis_pelayanan_id', $jenisPelayananId);
            })->orderBy('urutan')->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Daftar klasifikasi identitas pemohon',
                'data' => $klasifikasi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate dan simpan PDF untuk semua surat yang terkait dengan layanan
     */
    private function generateAndSavePdfsForLayanan($layanan)
    {
        try {
            // Ambil semua surat yang terkait dengan layanan ini
            if (empty($layanan->temp_surat_id) || !is_array($layanan->temp_surat_id)) {
                \Log::info('No surat found for layanan', [
                    'layanan_id' => $layanan->id,
                    'temp_surat_id' => $layanan->temp_surat_id
                ]);
                return;
            }

            $registerSuratList = RegisterSurat::whereIn('id', $layanan->temp_surat_id)
                ->with(['kategori_surat', 'signer'])
                ->get();

            \Log::info('Generating PDFs for layanan', [
                'layanan_id' => $layanan->id,
                'surat_count' => $registerSuratList->count(),
                'surat_ids' => $layanan->temp_surat_id
            ]);

            foreach ($registerSuratList as $surat) {
                try {
                    // Update status surat menjadi ditandatangani (status = 3)
                    $surat->update(['status' => 3]);

                    // Generate dan simpan PDF
                    $pdfPath = $this->generateAndSaveSignedPdf($surat, $layanan);
                    
                    if ($pdfPath) {
                        // Update register surat dengan path PDF yang sudah ditandatangani dan timestamp
                        $surat->update([
                            'signed_pdf_path' => $pdfPath,
                            'time_signed_pdf_path' => now_local()
                        ]);
                        
                        \Log::info('PDF generated and saved successfully for surat', [
                            'layanan_id' => $layanan->id,
                            'surat_id' => $surat->id,
                            'pdf_path' => $pdfPath,
                            'time_signed_pdf_path' => now_local()
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error generating PDF for individual surat', [
                        'layanan_id' => $layanan->id,
                        'surat_id' => $surat->id,
                        'error' => $e->getMessage()
                    ]);
                    // Lanjutkan ke surat berikutnya meski ada error
                }
            }

        } catch (\Exception $e) {
            \Log::error('Error in generateAndSavePdfsForLayanan:', [
                'layanan_id' => $layanan->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Generate dan simpan PDF yang sudah ditandatangani (copy dari RegisterSuratController)
     */
    private function generateAndSaveSignedPdf($surat, $layanan = null)
    {
        try {
            // Load kategori surat dengan relasi
            $surat->load([
                'kategori_surat', 
                'signer'
            ]);
            
            // Load pelayanan relationships if layanan exists
            if ($layanan) {
                $layanan->load([
                    'user',
                    'jenisPelayanan', 
                    'dataIdentitas.identitasPemohon'
                ]);
                
                \Log::info('Generate signed PDF: Pelayanan found and loaded', [
                    'surat_id' => $surat->id,
                    'pelayanan_id' => $layanan->id,
                    'temp_surat_ids' => $layanan->temp_surat_id,
                    'contains_surat' => in_array($surat->id, $layanan->temp_surat_id ?? [])
                ]);
            }

            $kategoriSurat = $surat->kategori_surat;

            \Log::info('Kategori Surat: ' . ($kategoriSurat ? $kategoriSurat->nama : 'null'));
            \Log::info('Kategori Surat: ' . ($kategoriSurat ? $kategoriSurat->hasBladeTemplate() : 'null'));
            
            // Cek apakah kategori surat memiliki template blade
            if ($kategoriSurat && $kategoriSurat->hasBladeTemplate()) {
                // Gunakan template blade dari kategori surat
                $templatePath = $kategoriSurat->getBladeTemplatePath();
                
                // Siapkan data template - menggunakan logika yang sama dengan method print()
                $templateData = [
                    'kategori' => $kategoriSurat,
                    'generated_at' => now(),
                    'nomor_surat' => $surat->nomor_surat
                ];

                // Jika ini adalah surat layanan, ambil data dari layanan
                if ($kategoriSurat->isLayanan() && $layanan) {
                    // Gunakan method getVariables dari kategori surat seperti di KategoriSuratController
                    $dukVariables = $kategoriSurat->getVariables($layanan->id);
                    
                    // Merge dengan data form (dalam hal ini dari register surat)
                    $formData = [
                        'nomor_surat' => $surat->nomor_surat,
                        'tanggal_surat' => $surat->tanggal_surat ? $surat->tanggal_surat->format('d F Y') : '',
                        'perihal' => $surat->perihal,
                        'isi_surat' => $surat->isi_surat,
                        'tujuan' => $surat->tujuan,
                        'pengirim' => $surat->pengirim,
                        'signer_name' => $surat->signer->name ?? '',
                        'signer_role' => $surat->signer->role ?? '',
                        'tanggal' => $surat->tanggal_surat ? $surat->tanggal_surat->format('Y-m-d') : now()->format('Y-m-d')
                    ];
                    
                    $mergedData = array_merge($dukVariables, $formData);
                    $templateData['data'] = $mergedData;
                    
                    \Log::info('Signed PDF: Using DUK data for layanan surat', [
                        'kategori_id' => $kategoriSurat->id,
                        'surat_id' => $surat->id,
                        'layanan_id' => $layanan->id,
                        'duk_variables_count' => count($dukVariables),
                        'form_data_count' => count($formData),
                        'merged_data_count' => count($mergedData),
                        'merged_keys' => array_keys($mergedData)
                    ]);
                } else {
                    // Untuk surat non-layanan, gunakan data dari surat langsung
                    $templateData['data'] = [
                        'nomor_surat' => $surat->nomor_surat,
                        'tanggal_surat' => $surat->tanggal_surat ? $surat->tanggal_surat->format('d F Y') : '',
                        'perihal' => $surat->perihal,
                        'isi_surat' => $surat->isi_surat,
                        'tujuan' => $surat->tujuan,
                        'pengirim' => $surat->pengirim,
                        'signer_name' => $surat->signer->name ?? '',
                        'signer_role' => $surat->signer->role ?? '',
                        'tanggal' => $surat->tanggal_surat ? $surat->tanggal_surat->format('Y-m-d') : now()->format('Y-m-d')
                    ];
                    
                    \Log::info('Signed PDF: Using manual data for non-layanan surat', [
                        'kategori_id' => $kategoriSurat->id,
                        'surat_id' => $surat->id,
                        'layanan_id' => $layanan ? $layanan->id : null,
                        'manual_data_count' => count($templateData['data'])
                    ]);
                }

                \Log::info('Generate signed PDF using blade template:', [
                    'surat_id' => $surat->id,
                    'template_path' => $templatePath,
                    'kategori_surat' => $kategoriSurat->nama,
                    'is_layanan' => $kategoriSurat->isLayanan(),
                    'has_layanan_data' => $layanan ? true : false,
                    'template_data_keys' => array_keys($templateData['data'] ?? [])
                ]);

                // Generate HTML dari Blade template
                $html = view($templatePath, $templateData)->render();
                
                \Log::info('Signed PDF: HTML generated successfully', [
                    'surat_id' => $surat->id,
                    'html_length' => strlen($html),
                    'template_path' => $templatePath
                ]);

                // Generate PDF menggunakan DomPDF dengan setting yang sama seperti di method print()
                $pdf = Pdf::loadHTML($html)
                    ->setPaper('a4', 'portrait')
                    ->setOptions([
                        'defaultFont' => 'Times-Roman',
                        'isRemoteEnabled' => true,
                        'isHtml5ParserEnabled' => true,
                        'dpi' => 150,
                        'defaultPaperSize' => 'a4',
                        'chroot' => public_path(),
                        'debugKeepTemp' => false,
                        'debugCss' => false,
                        'debugLayout' => false,
                        'debugLayoutLines' => false,
                        'debugLayoutBlocks' => false,
                        'debugLayoutInline' => false,
                        'debugLayoutPaddingBox' => false
                    ]);

                // Generate filename dan path untuk menyimpan
                $filename = 'signed_surat_' . $this->sanitizeFileName($surat->nomor_surat) . '_' . now()->format('YmdHis') . '.pdf';
                $path = 'surat/signed-pdfs/' . $filename;
                
                // Simpan PDF ke storage
                $pdfOutput = $pdf->output();
                Storage::disk('public')->put($path, $pdfOutput);
                
                \Log::info('Signed PDF saved successfully', [
                    'surat_id' => $surat->id,
                    'filename' => $filename,
                    'path' => $path,
                    'file_size' => strlen($pdfOutput)
                ]);
                
                return $path;
                
            } else {
                \Log::info('Using default template for signed PDF:', [
                    'surat_id' => $surat->id,
                    'kategori_surat' => $kategoriSurat ? $kategoriSurat->nama : 'No kategori',
                    'has_blade_template' => $kategoriSurat ? $kategoriSurat->hasBladeTemplate() : false
                ]);
                
                // Fallback ke template default jika tidak ada template blade
                $pdf = Pdf::loadView('adm.register_surat.print', [
                    'surat' => $surat
                ])->setPaper('a4', 'portrait');

                // Generate filename dan path untuk menyimpan
                $filename = 'signed_surat_' . $this->sanitizeFileName($surat->nomor_surat) . '_' . now()->format('YmdHis') . '.pdf';
                $path = 'surat/signed-pdfs/' . $filename;
                
                // Simpan PDF ke storage
                $pdfOutput = $pdf->output();
                Storage::disk('public')->put($path, $pdfOutput);
                
                \Log::info('Signed PDF (default template) saved successfully', [
                    'surat_id' => $surat->id,
                    'filename' => $filename,
                    'path' => $path,
                    'file_size' => strlen($pdfOutput)
                ]);
                
                return $path;
            }

        } catch (\Exception $e) {
            \Log::error('Error in generateAndSaveSignedPdf:', [
                'surat_id' => $surat->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Sanitize nama file untuk menghindari karakter yang tidak valid
     */
    private function sanitizeFileName($filename)
    {
        // Remove atau replace karakter yang tidak diinginkan
        $sanitized = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        $sanitized = preg_replace('/_+/', '_', $sanitized); // Replace multiple underscores with single
        $sanitized = trim($sanitized, '_'); // Remove leading/trailing underscores
        
        return $sanitized;
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