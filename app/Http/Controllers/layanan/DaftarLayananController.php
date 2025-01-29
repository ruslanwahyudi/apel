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
            ->orderBy('id', 'desc')
            ->get();

            return response()->json($layanan);
        }

        // Return view untuk request normal/non-ajax
        return view('layanan.daftar.index');
    }

    public function create()
    {
        $jenis = JenisLayanan::where('status', true)->get();
        $identitas = IdentitasLayanan::where('status', true)->get();
        $persyaratan = PersyaratanDokumen::where('status', true)->get();

        return view('layanan.daftar.create', compact('jenis', 'identitas', 'persyaratan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_layanan_id' => 'required|exists:jenis_layanan,id',
            'identitas_layanan_id' => 'required|exists:identitas_layanan,id',
            'persyaratan_dokumen' => 'required|array',
            'persyaratan_dokumen.*' => 'exists:persyaratan_dokumen,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file_pendukung' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'status' => 'required|boolean'
        ]);

        try {
            if ($request->hasFile('file_pendukung')) {
                $file = $request->file('file_pendukung');
                $nama_file = 'layanan-' . Str::slug($request->nama) . '-' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/layanan', $nama_file);
                $path = str_replace('public/', 'storage/', $path);
            }

            $layanan = Pelayanan::create([
                'jenis_layanan_id' => $request->jenis_layanan_id,
                'identitas_layanan_id' => $request->identitas_layanan_id,
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'file_pendukung' => $path ?? null,
                'status' => $request->status,
                'user_id' => auth()->id()
            ]);

            $layanan->persyaratan()->attach($request->persyaratan_dokumen);

            return redirect()->route('layanan.daftar')
                ->with('success', 'Layanan berhasil ditambahkan!');
        } catch (\Exception $e) {
            if (isset($path)) {
                Storage::delete(str_replace('storage/', 'public/', $path));
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Pelayanan $layanan)
    {
        $layanan->load(['jenis', 'identitas', 'persyaratan']);
        return response()->json($layanan);
    }

    public function edit(Pelayanan $layanan)
    {
        $layanan->load(['jenisPelayanan', 'dataIdentitas', 'dokumenPengajuan']);
        $jenis = JenisLayanan::where('status', true)->get();
        $identitas = IdentitasLayanan::where('status', true)->get();
        $persyaratan = PersyaratanDokumen::where('status', true)->get();
        $selected_persyaratan = $layanan->persyaratan->pluck('id')->toArray();

        return view('layanan.daftar.edit', compact('layanan', 'jenis', 'identitas', 'persyaratan', 'selected_persyaratan'));
    }

    public function update(Request $request, Pelayanan $layanan)
    {
        $request->validate([
            'jenis_layanan_id' => 'required|exists:jenis_layanan,id',
            'identitas_layanan_id' => 'required|exists:identitas_layanan,id',
            'persyaratan_dokumen' => 'required|array',
            'persyaratan_dokumen.*' => 'exists:persyaratan_dokumen,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'file_pendukung' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'status' => 'required|boolean'
        ]);

        try {
            $old_file = $layanan->file_pendukung;

            if ($request->hasFile('file_pendukung')) {
                $file = $request->file('file_pendukung');
                $nama_file = 'layanan-' . Str::slug($request->nama) . '-' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/layanan', $nama_file);
                $path = str_replace('public/', 'storage/', $path);

                if ($old_file) {
                    Storage::delete(str_replace('storage/', 'public/', $old_file));
                }
            }

            $layanan->update([
                'jenis_layanan_id' => $request->jenis_layanan_id,
                'identitas_layanan_id' => $request->identitas_layanan_id,
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'file_pendukung' => $path ?? $old_file,
                'status' => $request->status
            ]);

            $layanan->persyaratan()->sync($request->persyaratan_dokumen);

            return redirect()->route('layanan.daftar')
                ->with('success', 'Layanan berhasil diperbarui!');
        } catch (\Exception $e) {
            if (isset($path)) {
                Storage::delete(str_replace('storage/', 'public/', $path));
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Pelayanan $layanan)
    {
        try {
            if ($layanan->file_pendukung) {
                Storage::delete(str_replace('storage/', 'public/', $layanan->file_pendukung));
            }

            $layanan->persyaratan()->detach();
            $layanan->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Layanan berhasil dihapus!'
                ]);
            }

            return redirect()->route('layanan.daftar')
                ->with('success', 'Layanan berhasil dihapus!');
        } catch (\Exception $e) {
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
        $layanan = Pelayanan::with(['jenis', 'identitas', 'persyaratan'])
            ->where('nama', 'like', "%{$search}%")
            ->orWhere('deskripsi', 'like', "%{$search}%")
            ->orWhereHas('jenis', function($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%");
            })
            ->orWhereHas('identitas', function($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%");
            })
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
            $no_surat = generateNoSurat();
            $kategori_surat = KategoriSurat::where('nama', 'Layanan')->first()->id;
            $jenis_surat = $layanan->jenisPelayanan->nama_pelayanan;
            $perihal = $layanan->jenisPelayanan->nama_pelayanan;
            $tanggal_surat = now();
            $status_surat = MasterOption::where(['value' => 'Proses', 'type' => 'status_surat'])->first()->id;
            $signed_by = User::where('role', 'Kepala')->first()->id;

            $ins_reg_surat = RegisterSurat::create([
                'nomor_surat' => $no_surat,
                'kategori_surat_id' => $kategori_surat,
                'jenis_surat' => $jenis_surat,
                'perihal' => $perihal,
                'tanggal_surat' => $tanggal_surat,
                'status' => $status_surat,
                'signer_id' => $signed_by,
            ]);

            if($ins_reg_surat){
                $layanan->surat_id = $ins_reg_surat->id;
                $layanan->save();
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
} 