<?php

namespace App\Http\Controllers\adm;

use App\Http\Controllers\Controller;
use App\Models\adm\RegisterSurat;
use App\Models\adm\KategoriSurat;
use App\Models\LogTransaksi;
use App\Models\MasterOption;
use App\Models\Notifications;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Google\Client;
use Http;

class RegisterSuratController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $surat = RegisterSurat::with('kategori_surat', 'signer', 'statusSurat')->get();
            return response()->json($surat);
        }
        return view('adm.register_surat.index');
    }

    public function create()
    {
        $kategoriSurat = KategoriSurat::all();
        $users = User::all();
        $status = MasterOption::where('type', 'status_surat')->get();
        return view('adm.register_surat.create', compact('kategoriSurat', 'users', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_surat' => 'required|string|in:Surat Masuk,Surat Keluar',
            'kategori_surat_id' => 'required|exists:kategori_surat,id',
            'perihal' => 'required|string|max:255',
            // 'isi_ringkas' => 'nullable|string',
            'isi_surat' => 'required|string',
            'tujuan' => 'required|string|max:255',
            'pengirim' => 'nullable|string|max:255',
            'signer_id' => 'required|exists:users,id',
            'tanggal_surat' => 'required|date',
            'tanggal_diterima' => 'nullable|date',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'status' => 'required|string|in:1,2,3,4',    
            'keterangan' => 'nullable|string'
        ]);

        $noSurat = generateNoSurat();
        $request->merge(['nomor_surat' => $noSurat]);

        $data = $request->except('lampiran');

        if ($request->hasFile('lampiran')) {
            $file = $request->file('lampiran');
            $filename = time() . '_' . Str::slug($noSurat) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/surat/lampiran', $filename);
            $data['lampiran'] = $filename;
        }

        $surat = RegisterSurat::create($data);

        LogTransaksi::insertLog('surat', $surat->id, 'create', 'Surat berhasil ditambahkan.');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Surat berhasil ditambahkan',
                'data' => $surat
            ]);
        }

        return redirect()
            ->route('adm.register_surat.index')
            ->with('success', 'Surat berhasil ditambahkan.');
    }

    public function edit(RegisterSurat $surat)
    {
        $kategoriSurat = KategoriSurat::all();
        $status = MasterOption::where('type', 'status_surat')->get();
        $users = User::all();
        return view('adm.register_surat.edit', compact('surat', 'kategoriSurat', 'status', 'users'));
    }

    public function update(Request $request, RegisterSurat $surat)
    {
        $request->validate([
            // 'nomor_surat' => 'required|string|max:255|unique:register_surat,nomor_surat,' . $surat->id,
            'jenis_surat' => 'required|string|in:Surat Masuk,Surat Keluar',
            'perihal' => 'required|string|max:255',
            'isi_ringkas' => 'nullable|string',
            'isi_surat' => 'required|string',
            'tujuan' => 'required|string|max:255',
            'pengirim' => 'nullable|string|max:255',
            'tanggal_surat' => 'required|date',
            'signer_id' => 'required|exists:users,id',
            'tanggal_diterima' => 'nullable|date',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'status' => 'required|string|in:1,2,3,4',
            'keterangan' => 'nullable|string'
        ]);

        $data = $request->except('lampiran');

        if ($request->hasFile('lampiran')) {
            // Delete old file if exists
            if ($surat->lampiran) {
                Storage::delete('public/surat/lampiran/' . $surat->lampiran);
            }

            $file = $request->file('lampiran');
            $filename = time() . '_' . Str::slug($request->nomor_surat) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/surat/lampiran', $filename);
            $data['lampiran'] = $filename;
        }

        $surat->update($data);

        LogTransaksi::insertLog('surat', $surat->id, 'update', 'Surat berhasil diperbarui.');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Surat berhasil diperbarui',
                'data' => $surat
            ]);
        }

        return redirect()
            ->route('adm.register_surat.index')
            ->with('success', 'Surat berhasil diperbarui.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $surat = RegisterSurat::with('kategori_surat', 'signer', 'statusSurat')
        ->where('nomor_surat', 'like', '%' . $search . '%')
        ->orWhere('perihal', 'like', '%' . $search . '%')
        ->orWhereHas('kategori_surat', function ($query) use ($search) {
            $query->where('nama', 'like', '%' . $search . '%');
        })
        ->orWhere('tujuan', 'like', '%' . $search . '%')
        ->orWhere('pengirim', 'like', '%' . $search . '%')
        ->get();
        return response()->json($surat);
    }

    public function sign(RegisterSurat $surat)
    {
        \Log::info('Proses Penandatanganan Surat.');
        $surat->update(['status' => 3]);
        LogTransaksi::insertLog('surat', $surat->id, 'update', 'Surat berhasil ditandatangani.');

        $surat = RegisterSurat::with('layanan')->find($surat->id);
        \Log::info('Surat: ' . $surat);
        $layanan = $surat->layanan;
        \Log::info('Layanan: ' . $layanan);
        $jenis_surat = $surat->jenis_surat;
        // Kirim notifikasi ke user
        $user = User::find($layanan->user_id);
        // Kirim notifikasi ke user
        try {
            $notification = Notifications::create([
                'user_id' => $layanan->user_id,
                'title' => 'Surat Sudah Ditandatangani',
                'message' => "Surat [{$jenis_surat}] Anda, Nomor {$surat->nomor_surat} telah ditandatangani",
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
                'user_id' => $layanan->user_id,
                'jenis_surat' => $jenis_surat,
                'error' => $e->getMessage()
            ]);
            // Lanjutkan eksekusi meski notifikasi gagal
        }

        return response()->json(['success' => true, 'message' => 'Surat berhasil ditandatangani.']);
    }

    public function revisi(RegisterSurat $surat, $description)
    {

        $surat->update(['status' => 2]);
        LogTransaksi::insertLog('surat', $surat->id, 'update', 'Surat berhasil dikembalikan. [' . $description . ']');
        return response()->json(['success' => true, 'message' => 'Surat berhasil dikembalikan.']);
    }

    public function approve(RegisterSurat $surat)
    {
        $surat->update(['status' => 2]);
        LogTransaksi::insertLog('surat', $surat->id, 'update', 'Surat berhasil disetujui.');
        return response()->json(['success' => true, 'message' => 'Surat berhasil disetujui.']);
    }

    public function destroy(RegisterSurat $surat)
    {
        if ($surat->lampiran) {
            Storage::delete('public/surat/lampiran/' . $surat->lampiran);
        }

        $surat->delete();
        LogTransaksi::insertLog('surat', $surat->id, 'delete', 'Surat berhasil dihapus.');
        return response()->json(['message' => 'Surat berhasil dihapus']);
    }

    public function print(RegisterSurat $surat)
    {
        // $pdf = Pdf::loadView('adm.register_surat.print', compact('surat'))
        //     ->setPaper('a4')
        //     ->setWarnings(false)
        //     ->setOptions([
        //         'isHtml5ParserEnabled' => true,
        //         'isRemoteEnabled' => true,
        //         'defaultFont' => 'sans-serif'
        //     ]);

        // return $pdf->stream('surat-' . $surat->nomor_surat . '.pdf');

        $pdf = PDF::loadView('adm.register_surat.print', [
            'surat' => $surat
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('Surat_' . $surat->perihal . '.pdf');
    }
} 