<?php

namespace App\Http\Controllers\layanan;

use App\Http\Controllers\Controller;
use App\Models\adm\KategoriSurat;
use App\Models\adm\RegisterSurat;
use App\Models\Layanan\Layanan;
use App\Models\Layanan\JenisLayanan;
use App\Models\Layanan\PersyaratanDokumen;
use App\Models\Layanan\IdentitasLayanan;
use App\Models\Layanan\Pelayanan;
use App\Models\MasterOption;
use App\Models\Notifications;
use App\Models\User;
use Google\Client;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Lang;

class DaftarLayananController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $layanan = Pelayanan::with([
                'user',
                'jenisPelayanan',
                'dataIdentitas.identitasPemohon',
                'dokumenPengajuan.syaratDokumen',
                'status'
            ])
            ->select('*') // Pastikan semua kolom termasuk signed_document_path diambil
            ->orderBy('id', 'desc')
            ->get();

            return response()->json($layanan);
        }

        // Return view untuk request normal/non-ajax
        return view('layanan.daftar.index');
    }

    public function create()
    {
        $jenis = \App\Models\Layanan\JenisPelayanan::all();
        $identitas = IdentitasLayanan::all();
        $persyaratan = PersyaratanDokumen::all();

        return view('layanan.daftar.create', compact('jenis', 'identitas', 'persyaratan'));
    }

    /**
     * Mendapatkan form dinamis berdasarkan jenis layanan
     */
    public function getFormFields($jenisLayananId)
    {
        try {
            // Ambil identitas pemohon berdasarkan jenis layanan
            $identitasPemohon = \App\Models\Layanan\IdentitasPemohon::with('klasifikasi')
                ->where('jenis_pelayanan_id', $jenisLayananId)
                ->orderBy('klasifikasi_id')
                ->get();

            // Kelompokkan berdasarkan klasifikasi
            $groupedFields = $identitasPemohon->groupBy('klasifikasi_id');
            
            // Format data untuk response
            $formFields = [];
            foreach ($groupedFields as $klasifikasiId => $fields) {
                $klasifikasi = $fields->first()->klasifikasi;
                $formFields[] = [
                    'klasifikasi_id' => $klasifikasiId,
                    'klasifikasi_nama' => $klasifikasi ? $klasifikasi->nama_klasifikasi : 'Tidak Berkategori',
                    'klasifikasi_deskripsi' => $klasifikasi ? $klasifikasi->deskripsi : '',
                    'fields' => $fields->map(function ($field) {
                        return [
                            'id' => $field->id,
                            'nama_field' => $field->nama_field,
                            'tipe_field' => $field->tipe_field,
                            'required' => $field->required,
                            'readonly' => $field->readonly
                        ];
                    })->toArray()
                ];
            }

            // Ambil syarat dokumen untuk jenis layanan ini (tanpa where status)
            $syaratDokumen = \App\Models\Layanan\SyaratDokumen::where('jenis_pelayanan_id', $jenisLayananId)
                ->get();

            return response()->json([
                'success' => true,
                'form_fields' => $formFields,
                'syarat_dokumen' => $syaratDokumen
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_layanan_id' => 'required|exists:duk_jenis_pelayanan,id',
            'catatan' => 'nullable|string',
            'identitas_data' => 'required|array',
            'identitas_data.*' => 'nullable|string',
            'dokumen_files' => 'nullable|array',
            'dokumen_files.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120'
        ]);

        try {
            \DB::beginTransaction();

            // Ambil status default (Draft)
            $statusLayanan = \App\Models\MasterOption::where(['value' => 'Draft', 'type' => 'status_layanan'])->first();

            // Simpan data pelayanan
            $pelayanan = \App\Models\Layanan\Pelayanan::create([
                'user_id' => auth()->id(),
                'jenis_pelayanan_id' => $request->jenis_layanan_id,
                'catatan' => $request->catatan,
                'status_layanan' => $statusLayanan->id
            ]);

            // Simpan data identitas pemohon
            foreach ($request->identitas_data as $identitasId => $nilai) {
                if (!empty($nilai)) {
                    \App\Models\Layanan\DataIdentitasPemohon::create([
                        'pelayanan_id' => $pelayanan->id,
                        'identitas_pemohon_id' => $identitasId,
                        'nilai' => $nilai
                    ]);
                }
            }

            // Simpan dokumen jika ada
            if ($request->hasFile('dokumen_files')) {
                foreach ($request->file('dokumen_files') as $syaratId => $file) {
                    if ($file) {
                        $fileName = 'dokumen-' . $pelayanan->id . '-' . $syaratId . '-' . time() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('layanan/dokumen', $fileName, 'public');

                        \App\Models\Layanan\DokumenPengajuan::create([
                            'pelayanan_id' => $pelayanan->id,
                            'syarat_dokumen_id' => $syaratId,
                            'path_dokumen' => $path
                        ]);
                    }
                }
            }

            \DB::commit();

            return redirect()->route('layanan.daftar')
                ->with('success', 'Layanan berhasil ditambahkan!');
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Pelayanan $layanan)
    {
        $layanan->load(['jenis', 'identitas', 'persyaratan']);
        return response()->json($layanan);
    }

    public function edit(Pelayanan $daftar)
    {
        $daftar->load(['jenisPelayanan', 'dataIdentitas.identitasPemohon', 'dokumenPengajuan.syaratDokumen']);
        $jenis = \App\Models\Layanan\JenisPelayanan::all();
        
        // Format data identitas untuk form
        $existingData = [];
        foreach ($daftar->dataIdentitas as $data) {
            $existingData[$data->identitas_pemohon_id] = $data->nilai;
        }

        return view('layanan.daftar.edit', compact('daftar', 'jenis', 'existingData'));
    }

    public function update(Request $request, Pelayanan $daftar)
    {
        $request->validate([
            'jenis_layanan_id' => 'required|exists:duk_jenis_pelayanan,id',
            'catatan' => 'nullable|string',
            'identitas_data' => 'required|array',
            'identitas_data.*' => 'nullable|string',
            'dokumen_files' => 'nullable|array',
            'dokumen_files.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120'
        ]);

        try {
            \DB::beginTransaction();

            // Update data pelayanan
            $daftar->update([
                'jenis_pelayanan_id' => $request->jenis_layanan_id,
                'catatan' => $request->catatan
            ]);

            // Hapus data identitas lama
            $daftar->dataIdentitas()->delete();

            // Simpan data identitas baru
            foreach ($request->identitas_data as $identitasId => $nilai) {
                if (!empty($nilai)) {
                    \App\Models\Layanan\DataIdentitasPemohon::create([
                        'pelayanan_id' => $daftar->id,
                        'identitas_pemohon_id' => $identitasId,
                        'nilai' => $nilai
                    ]);
                }
            }

            // Handle dokumen baru (jika ada)
            if ($request->hasFile('dokumen_files')) {
                foreach ($request->file('dokumen_files') as $syaratId => $file) {
                    if ($file) {
                        // Hapus dokumen lama untuk syarat ini jika ada
                        $oldDokumen = $daftar->dokumenPengajuan()->where('syarat_dokumen_id', $syaratId)->first();
                        if ($oldDokumen) {
                            // Hapus file lama
                            if (\Storage::disk('public')->exists($oldDokumen->path_dokumen)) {
                                \Storage::disk('public')->delete($oldDokumen->path_dokumen);
                            }
                            $oldDokumen->delete();
                        }

                        // Simpan file baru
                        $fileName = 'dokumen-' . $daftar->id . '-' . $syaratId . '-' . time() . '.' . $file->getClientOriginalExtension();
                        $path = $file->storeAs('layanan/dokumen', $fileName, 'public');

                        \App\Models\Layanan\DokumenPengajuan::create([
                            'pelayanan_id' => $daftar->id,
                            'syarat_dokumen_id' => $syaratId,
                            'path_dokumen' => $path
                        ]);
                    }
                }
            }

            \DB::commit();

            return redirect()->route('layanan.daftar')
                ->with('success', 'Layanan berhasil diperbarui!');
        } catch (\Exception $e) {
            \DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Pelayanan $daftar)
    {
        try {
            \DB::beginTransaction();

            // Hapus file dokumen yang terkait
            $dokumenPengajuan = $daftar->dokumenPengajuan;
            foreach ($dokumenPengajuan as $dokumen) {
                if (\Storage::disk('public')->exists($dokumen->path_dokumen)) {
                    \Storage::disk('public')->delete($dokumen->path_dokumen);
                }
                $dokumen->delete();
            }

            // Hapus data identitas pemohon
            $daftar->dataIdentitas()->delete();

            // Hapus data pelayanan
            $daftar->delete();

            \DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Layanan berhasil dihapus!'
                ]);
            }

            return redirect()->route('layanan.daftar')
                ->with('success', 'Layanan berhasil dihapus!');
        } catch (\Exception $e) {
            \DB::rollback();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function search($search)
    {
        $layanan = Pelayanan::with([
                'user',
                'jenisPelayanan', 
                'dataIdentitas.identitasPemohon',
                'dokumenPengajuan.syaratDokumen',
                'status'
            ])
            ->whereHas('jenisPelayanan', function($query) use ($search) {
                $query->where('nama_pelayanan', 'like', "%{$search}%");
            })
            ->orWhereHas('user', function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('dataIdentitas', function($query) use ($search) {
                $query->where('nilai', 'like', "%{$search}%");
            })
            ->orWhere('catatan', 'like', "%{$search}%")
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($layanan);
    }

    public function approve($id)
    {
        // $layanan = Pelayanan::find($id);
        // $layanan->update(['status_layanan' => 2]);
        // return redirect()->route('layanan.daftar')->with('success', 'Layanan berhasil disetujui!');
        $layanan = Pelayanan::findOrFail($id);

        $statusLayanan = MasterOption::where(['value' => 'Sedang Diproses', 'type' => 'status_layanan'])->first();

        $upd = $layanan->update(['status_layanan' => $statusLayanan->id]);
        
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
                    'message' => "Pengajuan layanan {$jenis_surat} Anda telah disetujui",
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
                        'layanan_id' => $id,
                        'user_id' => $layanan->user_id,
                        'status' => $response->status(),
                        'body' => $response->json()
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error sending notification for layanan approval:', [
                    'layanan_id' => $id,
                    'user_id' => $layanan->user_id,
                    'jenis_surat' => $jenis_surat,
                    'error' => $e->getMessage()
                ]);
                // Lanjutkan eksekusi meski notifikasi gagal
            }

            // Kirim notifikasi ke Kepala Desa
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
    }

    /**
     * Preview surat untuk pelayanan tertentu
     */
    public function previewSurat($id)
    {
        try {
            $pelayanan = \App\Models\Layanan\Pelayanan::with([
                'jenisPelayanan', 
                'dataIdentitas.identitasPemohon', 
                'user'
            ])->findOrFail($id);

            // Cari kategori surat yang sesuai dengan jenis pelayanan
            $kategoriSurat = \App\Models\adm\KategoriSurat::where('jenis_pelayanan_id', $pelayanan->jenis_pelayanan_id)
                ->where('tipe_surat', 'layanan')
                ->first();

            if (!$kategoriSurat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template surat untuk jenis layanan ini belum tersedia'
                ], 404);
            }

            // Ambil data identitas dalam format key-value
            $dataIdentitas = [];
            foreach ($pelayanan->dataIdentitas as $data) {
                if ($data->identitasPemohon) {
                    $dataIdentitas[$data->identitasPemohon->nama_field] = $data->nilai;
                }
            }

            // Data untuk template
            $templateData = [
                'kategori' => $kategoriSurat,
                'generated_at' => now(),
                'nomor_surat' => 'PREVIEW-' . date('YmdHis'),
                'data' => array_merge($dataIdentitas, [
                    'nama_pemohon' => $pelayanan->user->name,
                    'jenis_layanan' => $pelayanan->jenisPelayanan->nama_pelayanan,
                    'tanggal_pengajuan' => $pelayanan->created_at->format('d F Y'),
                    'catatan' => $pelayanan->catatan
                ])
            ];

            // Cek apakah ada template blade
            if ($kategoriSurat->hasBladeTemplate()) {
                $templatePath = $kategoriSurat->getBladeTemplatePath();
                $html = view($templatePath, $templateData)->render();
                
                return response()->json([
                    'success' => true,
                    'html' => $html,
                    'template_type' => 'blade'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Template surat belum tersedia'
                ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload dokumen yang sudah ditandatangani
     */
    public function uploadSignedDocument(Request $request, $id)
    {
        $request->validate([
            'signed_document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240' // 10MB max
        ]);

        try {
            $pelayanan = \App\Models\Layanan\Pelayanan::findOrFail($id);

            // Pastikan layanan sudah ditandatangani (status Selesai atau yang sesuai)
            if ($pelayanan->status_layanan != 8) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen hanya bisa diupload untuk layanan yang sudah selesai (status = 8)'
                ], 400);
            }

            // Hapus dokumen lama jika ada
            if ($pelayanan->signed_document_path && \Storage::disk('public')->exists($pelayanan->signed_document_path)) {
                \Storage::disk('public')->delete($pelayanan->signed_document_path);
            }

            // Upload dokumen baru
            $file = $request->file('signed_document');
            $fileName = 'signed-doc-' . $pelayanan->id . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('layanan/signed-documents', $fileName, 'public');

            // Update path di database
            $pelayanan->update(['signed_document_path' => $path]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen yang sudah ditandatangani berhasil diupload!'
                ]);
            }

            return redirect()->route('layanan.daftar')
                ->with('success', 'Dokumen yang sudah ditandatangani berhasil diupload!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download dokumen yang sudah ditandatangani
     */
    public function downloadSignedDocument($id)
    {
        try {
            $pelayanan = \App\Models\Layanan\Pelayanan::findOrFail($id);

            if (!$pelayanan->signed_document_path || !\Storage::disk('public')->exists($pelayanan->signed_document_path)) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dokumen yang sudah ditandatangani tidak ditemukan'
                    ], 404);
                }
                
                return redirect()->back()->with('error', 'Dokumen yang sudah ditandatangani tidak ditemukan');
            }

            $filePath = storage_path('app/public/' . $pelayanan->signed_document_path);
            $fileName = 'Dokumen_Ditandatangani_' . $pelayanan->jenisPelayanan->nama_pelayanan . '_' . $pelayanan->id . '.' . pathinfo($filePath, PATHINFO_EXTENSION);

            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Simple approve - increment status_layanan by 1 (only for administrator user id = 3)
     */
    public function simpleApprove($id)
    {
        // Cek apakah user yang login adalah administrator (id = 3)
        if (auth()->id() !== 3) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Hanya administrator yang dapat melakukan approve.'
                ], 403);
            }
            
            return redirect()->back()->with('error', 'Akses ditolak. Hanya administrator yang dapat melakukan approve.');
        }

        try {
            $layanan = Pelayanan::findOrFail($id);

            // Ambil status saat ini
            $currentStatus = $layanan->status_layanan;
            
            // Cari status selanjutnya (increment by 1)
            $nextStatus = MasterOption::where('type', 'status_layanan')
                ->where('id', $currentStatus + 1)
                ->first();

            if (!$nextStatus) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Status sudah maksimal atau tidak ditemukan status selanjutnya.'
                    ], 400);
                }
                
                return redirect()->back()->with('error', 'Status sudah maksimal atau tidak ditemukan status selanjutnya.');
            }

            // Update status
            $upd = $layanan->update(['status_layanan' => $nextStatus->id]);
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

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Status berhasil diubah menjadi: {$nextStatus->description}"
                ]);
            }

            return redirect()->back()->with('success', "Status berhasil diubah menjadi: {$nextStatus->description}");

        } catch (\Exception $e) {
            \Log::error('Error in simpleApprove:', [
                'layanan_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
} 