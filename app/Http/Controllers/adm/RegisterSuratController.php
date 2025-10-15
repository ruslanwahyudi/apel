<?php

namespace App\Http\Controllers\adm;

use App\Http\Controllers\Controller;
use App\Models\adm\RegisterSurat;
use App\Models\adm\KategoriSurat;
use App\Models\Layanan\Pelayanan;
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
            // Check if this is a DataTables request
            if (request()->has('draw')) {
                return $this->getDataTablesData();
            }
            
            // Fallback for non-DataTables AJAX requests
            $surat = RegisterSurat::with('kategori_surat', 'signer', 'statusSurat')
                ->orderBy('id', 'desc')
                ->get();
            
            return response()->json($surat);
        }
        return view('adm.register_surat.index');
    }

    /**
     * Handle DataTables server-side processing
     */
    private function getDataTablesData()
    {
        $query = RegisterSurat::with([
            'kategori_surat', 
            'signer', 
            'statusSurat'
        ]);

        // Get DataTables parameters
        $draw = request()->get('draw');
        $start = request()->get('start', 0);
        $length = request()->get('length', 10);
        $searchValue = request()->get('search')['value'] ?? '';
        $orderColumn = request()->get('order')[0]['column'] ?? 0;
        $orderDir = request()->get('order')[0]['dir'] ?? 'desc';

        // Define column mapping for ordering
        $columns = [
            0 => 'id',
            1 => 'nomor_surat',
            2 => 'perihal',
            3 => 'tanggal_surat',
            4 => 'status'
        ];

        $orderColumnName = $columns[$orderColumn] ?? 'id';

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('nomor_surat', 'like', "%{$searchValue}%")
                  ->orWhere('perihal', 'like', "%{$searchValue}%")
                  ->orWhere('tujuan', 'like', "%{$searchValue}%")
                  ->orWhere('pengirim', 'like', "%{$searchValue}%")
                  ->orWhereHas('kategori_surat', function($subQ) use ($searchValue) {
                      $subQ->where('nama', 'like', "%{$searchValue}%");
                  })
                  ->orWhereHas('signer', function($subQ) use ($searchValue) {
                      $subQ->where('name', 'like', "%{$searchValue}%");
                  });
            });
        }

        // Get total records count
        $totalRecords = RegisterSurat::count();
        $filteredRecords = $query->count();

        // Apply ordering and pagination
        $data = $query->orderBy($orderColumnName, $orderDir)
                     ->offset($start)
                     ->limit($length)
                     ->get();

        // Format data for DataTables
        $formattedData = [];
        foreach ($data as $index => $surat) {
            // Format tanggal
            $tanggalFormatted = $surat->tanggal_surat ? 
                $surat->tanggal_surat->format('d F Y') : '-';

            // Format data layanan
            $dataLayanan = '-';
            try {
                // Get layanan data using the accessor
                $layananData = $surat->layanan_data;
                if ($layananData) {
                    $layananInfo = [];
                    
                    // User information
                    if ($layananData->user && $layananData->user->name) {
                        $layananInfo[] = '<strong>User:</strong> ' . $layananData->user->name;
                    }
                    
                    // Jenis pelayanan information
                    if ($layananData->jenisPelayanan && $layananData->jenisPelayanan->nama_pelayanan) {
                        $layananInfo[] = '<strong>Jenis:</strong> ' . $layananData->jenisPelayanan->nama_pelayanan;
                    }
                    
                    // Cari nama dan NIK dari data identitas
                    if ($layananData->dataIdentitas && $layananData->dataIdentitas->count() > 0) {
                        // Cari nama
                        $namaPriorities = ['nama', 'nama_pemohon', 'nama_anak', 'nama_bayi', 'nama_suami', 'nama_istri', 'nama_ayah', 'nama_ibu', 'nama_ahliwaris'];
                        $namaField = null;
                        foreach ($namaPriorities as $priority) {
                            $namaField = $layananData->dataIdentitas->where('identitasPemohon.nama_field', $priority)->first();
                            if ($namaField) break;
                        }
                        if ($namaField && $namaField->nilai) {
                            $layananInfo[] = '<strong>Pemohon:</strong> ' . $namaField->nilai;
                        }
                        
                        // Cari NIK
                        $nikPriorities = ['nik', 'nik_pemohon', 'nik_anak', 'nik_bayi', 'nik_suami', 'nik_istri', 'nik_ayah', 'nik_ibu', 'nik_ahliwaris'];
                        $nikField = null;
                        foreach ($nikPriorities as $priority) {
                            $nikField = $layananData->dataIdentitas->where('identitasPemohon.nama_field', $priority)->first();
                            if ($nikField) break;
                        }
                        if ($nikField && $nikField->nilai) {
                            $layananInfo[] = '<strong>NIK:</strong> ' . $nikField->nilai;
                        }
                    }
                    
                    $dataLayanan = count($layananInfo) > 0 ? implode('<br>', $layananInfo) : '-';
                }
            } catch (\Exception $e) {
                $dataLayanan = 'Error processing data: ' . $e->getMessage();
            }

            // Generate action buttons
            $actions = '<div class="d-flex flex-column" style="gap: 0.5rem;">';
            
            // Edit button
            if ($surat->status != '3') {
                $actions .= '<a href="/adm/register_surat/' . $surat->id . '/edit" class="btn btn-warning btn-sm">Edit <i class="fa fa-edit"></i></a>';
            }
            
            // Revisi button
            if ($surat->status == '3') {
                $actions .= '<button class="btn btn-danger btn-sm register_surat-revisi" data-id="' . $surat->id . '">Revisi <i class="fa fa-undo"></i></button>';
            }
            
            // Delete button
            if ($surat->status != '3') {
                $actions .= '<button class="btn btn-danger btn-sm delete-register_surat" data-id="' . $surat->id . '">Hapus <i class="fa fa-trash"></i></button>';
            }
            
            // Print button
            if ($surat->status == '3') {
                $actions .= '<a href="/adm/register-surat/print/' . $surat->id . '" class="btn btn-info btn-sm" target="_blank">Print <i class="fa fa-print"></i></a>';
            }
            
            // Download PDF button
            if ($surat->status == '3' && $surat->signed_pdf_path) {
                $actions .= '<a href="/adm/register-surat/download-signed-pdf/' . $surat->id . '" class="btn btn-success btn-sm" download>Download PDF <i class="fa fa-download"></i></a>';
            }
            
            $actions .= '</div>';

            // Status buttons
            $statusButtons = '<div class="d-flex flex-column" style="gap: 0.5rem;">';
            $statusButtons .= '<span class="badge ' . $this->getStatusBadgeClass($surat->status_surat->value ?? '') . '">' . ($surat->status_surat->description ?? '-') . '</span>';
            $statusButtons .= 'Status : ' . $surat->status;
            
            if ($surat->status == '2') {
                $statusButtons .= '<button class="btn btn-info btn-sm register_surat-sign" data-id="' . $surat->id . '">Tanda Tangani <i class="fa fa-barcode"></i></button>';
            }
            if ($surat->status == '1') {
                $statusButtons .= '<button class="btn btn-secondary btn-sm register_surat-approve" data-id="' . $surat->id . '">Setujui <i class="fa fa-check"></i></button>';
            }
            
            $statusButtons .= '</div>';

            $formattedData[] = [
                $start + $index + 1, // No
                '<strong>Nomor:</strong> ' . ($surat->nomor_surat ?: '-') . '<br>' .
                '<strong>Tanggal:</strong> ' . $tanggalFormatted . '<br>' .
                '<strong>Perihal:</strong> ' . ($surat->perihal ?: '-') . '<br>' .
                '<strong>Kategori:</strong> ' . ($surat->kategori_surat ? $surat->kategori_surat->nama : '-'),
                '<button type="button" class="btn btn-secondary btn-sm view-isi-surat" ' .
                'data-toggle="modal" data-target="#isiSuratModal" ' .
                'data-content="' . htmlspecialchars($surat->isi_surat ?: '') . '">' .
                '<i class="fa fa-book"></i> Lihat</button>',
                '<div style="font-size: 12px;">' . $dataLayanan . '</div>',
                $surat->signer ? $surat->signer->name : '-',
                $surat->lampiran ? 
                    '<a href="/storage/surat/lampiran/' . $surat->lampiran . '" class="btn btn-primary btn-sm" download>Lampiran <i class="fa fa-file"></i></a>' : 
                    '<span class="badge bg-secondary text-white">Tidak ada lampiran</span>',
                $statusButtons,
                $actions
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $formattedData
        ]);
    }

    /**
     * Get status badge class
     */
    private function getStatusBadgeClass($status)
    {
        switch ($status) {
            case 'Proses': return 'bg-warning';
            case 'Selesai': return 'bg-success';
            case 'Draft': return 'bg-light';
            case 'Ditolak': return 'bg-danger';
            default: return 'bg-info';
        }
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
        // This method is kept for backward compatibility but is no longer used
        // since DataTables handles search through server-side processing
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

        // Mencari pelayanan yang memiliki register surat ini dalam array temp_surat_id
        $layanan = $surat->getPelayanan();
        
        \Log::info('Register Surat ID: ' . $surat->id);
        \Log::info('Layanan found: ' . ($layanan ? $layanan->id : 'null'));
        
        if (!$layanan) {
            \Log::warning('No layanan found for register surat ID: ' . $surat->id);
            // Update status pelayanan untuk surat yang sudah ditandatangani (jika ditemukan)
            $statusSelesai = MasterOption::where(['value' => 'Selesai', 'type' => 'status_layanan'])->first();
            if ($statusSelesai) {
                // Coba cari pelayanan dengan cara alternatif jika perlu
                $pelayananAlternatif = \App\Models\Layanan\Pelayanan::whereRaw(
                    'JSON_SEARCH(temp_surat_id, "one", ?) IS NOT NULL', 
                    [$surat->id]
                )->first();
                
                if ($pelayananAlternatif) {
                    $pelayananAlternatif->update(['status_layanan' => $statusSelesai->id]);
                    \Log::info('Updated pelayanan status via alternative search: ' . $pelayananAlternatif->id);
                }
            }
            
            return response()->json(['success' => true, 'message' => 'Surat berhasil ditandatangani.']);
        }
        
        $jenis_surat = $surat->jenis_surat;
        \Log::info('Jenis Surat: ' . $jenis_surat);
        $kategori_surat = $surat->kategori_surat;
        \Log::info('Kategori Surat: ' . ($kategori_surat ? $kategori_surat->nama : 'null'));
        
        // Update status pelayanan menjadi selesai setelah surat ditandatangani
        $statusSelesai = MasterOption::where(['value' => 'Selesai', 'type' => 'status_layanan'])->first();
        if ($statusSelesai) {
            $layanan->update(['status_layanan' => $statusSelesai->id]);
            \Log::info('Updated pelayanan status to Selesai: ' . $layanan->id);
        }

        // Generate dan simpan PDF setelah surat ditandatangani
        try {
            $pdfPath = $this->generateAndSaveSignedPdf($surat, $layanan);
            if ($pdfPath) {
                // Update register surat dengan path PDF yang sudah ditandatangani dan timestamp
                $surat->update([
                    'signed_pdf_path' => $pdfPath,
                    'time_signed_pdf_path' => now_local()
                ]);
                \Log::info('PDF signed surat saved successfully', [
                    'register_surat_id' => $surat->id,
                    'pdf_path' => $pdfPath,
                    'time_signed_pdf_path' => now_local()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to generate signed PDF', [
                'register_surat_id' => $surat->id,
                'error' => $e->getMessage()
            ]);
            // Lanjutkan eksekusi meski generate PDF gagal
        }
        
        // Kirim notifikasi ke user
        try {
            $notification = Notifications::create([
                'user_id' => $layanan->user_id,
                'title' => 'Surat Sudah Ditandatangani',
                'message' => "Surat [{$jenis_surat}] Anda, Nomor {$surat->nomor_surat} telah ditandatangani",
                'type' => 'layanan'
            ]);

            $user = User::find($layanan->user_id);
            \Log::info('User: ' . ($user ? $user->name : 'null'));
            
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

                \Log::info('FCM Response for surat signing:', [
                    'register_surat_id' => $surat->id,
                    'layanan_id' => $layanan->id,
                    'user_id' => $layanan->user_id,
                    'status' => $response->status(),
                    'body' => $response->json()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error sending notification for surat signing:', [
                'register_surat_id' => $surat->id,
                'layanan_id' => $layanan ? $layanan->id : null,
                'user_id' => $layanan ? $layanan->user_id : null,
                'jenis_surat' => $jenis_surat,
                'error' => $e->getMessage()
            ]);
            // Lanjutkan eksekusi meski notifikasi gagal
        }

        return response()->json(['success' => true, 'message' => 'Surat berhasil ditandatangani.']);
    }

    /**
     * Generate dan simpan PDF yang sudah ditandatangani
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
                    'temp_surat_id' => $surat->id,
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
                        'temp_surat_id' => $surat->id,
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
                        'temp_surat_id' => $surat->id,
                        'layanan_id' => $layanan ? $layanan->id : null,
                        'manual_data_count' => count($templateData['data'])
                    ]);
                }

                \Log::info('Generate signed PDF using blade template:', [
                    'temp_surat_id' => $surat->id,
                    'template_path' => $templatePath,
                    'kategori_surat' => $kategoriSurat->nama,
                    'is_layanan' => $kategoriSurat->isLayanan(),
                    'has_layanan_data' => $layanan ? true : false,
                    'template_data_keys' => array_keys($templateData['data'] ?? [])
                ]);

                // Generate HTML dari Blade template
                $html = view($templatePath, $templateData)->render();
                
                \Log::info('Signed PDF: HTML generated successfully', [
                    'temp_surat_id' => $surat->id,
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
                    'temp_surat_id' => $surat->id,
                    'filename' => $filename,
                    'path' => $path,
                    'file_size' => strlen($pdfOutput)
                ]);
                
                return $path;
                
            } else {
                \Log::info('Using default template for signed PDF:', [
                    'temp_surat_id' => $surat->id,
                    'kategori_surat' => $kategoriSurat ? $kategoriSurat->nama : 'No kategori',
                    'has_blade_template' => $kategoriSurat ? $kategoriSurat->hasBladeTemplate() : false
                ]);
                
                // Fallback ke template default jika tidak ada template blade
                $pdf = PDF::loadView('adm.register_surat.print', [
                    'surat' => $surat
                ])->setPaper('a4', 'portrait');

                // Generate filename dan path untuk menyimpan
                $filename = 'signed_surat_' . $this->sanitizeFileName($surat->nomor_surat) . '_' . now()->format('YmdHis') . '.pdf';
                $path = 'surat/signed-pdfs/' . $filename;
                
                // Simpan PDF ke storage
                $pdfOutput = $pdf->output();
                Storage::disk('public')->put($path, $pdfOutput);
                
                \Log::info('Signed PDF (default template) saved successfully', [
                    'temp_surat_id' => $surat->id,
                    'filename' => $filename,
                    'path' => $path,
                    'file_size' => strlen($pdfOutput)
                ]);
                
                return $path;
            }

        } catch (\Exception $e) {
            \Log::error('Error in generateAndSaveSignedPdf:', [
                'temp_surat_id' => $surat->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Download PDF yang sudah ditandatangani
     */
    public function downloadSignedPdf(RegisterSurat $surat)
    {
        try {
            if (!$surat->signed_pdf_path || !Storage::disk('public')->exists($surat->signed_pdf_path)) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PDF yang sudah ditandatangani tidak ditemukan'
                    ], 404);
                }
                
                return redirect()->back()->with('error', 'PDF yang sudah ditandatangani tidak ditemukan');
            }

            $filePath = storage_path('app/public/' . $surat->signed_pdf_path);
            $fileName = 'Surat_Ditandatangani_' . $this->sanitizeFileName($surat->nomor_surat) . '.pdf';

            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            \Log::error('Error downloading signed PDF:', [
                'register_surat_id' => $surat->id,
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

        $surat->forceDelete();
        LogTransaksi::insertLog('surat', $surat->id, 'delete', 'Surat berhasil dihapus.');
        // hapus semua data temp_surat_id dari layanan
        $layanan = $surat->getPelayanan();
        if ($layanan) {
            $layanan->temp_surat_id = null;
            $layanan->save();
        }
        return response()->json(['message' => 'Surat berhasil dihapus']);
    }

    public function print(RegisterSurat $surat)
    {
        try {
            // Cek apakah sudah ada PDF yang sudah ditandatangani
            if ($surat->signed_pdf_path && Storage::disk('public')->exists($surat->signed_pdf_path)) {
                \Log::info('Using existing signed PDF for print', [
                    'register_surat_id' => $surat->id,
                    'signed_pdf_path' => $surat->signed_pdf_path
                ]);
                
                $filePath = storage_path('app/public/' . $surat->signed_pdf_path);
                $fileName = 'Surat_' . $this->sanitizeFileName($surat->nomor_surat) . '.pdf';
                
                return response()->download($filePath, $fileName);
            }
            
            // Load kategori surat dengan relasi
            $surat->load([
                'kategori_surat', 
                'signer'
            ]);
            
            // Get pelayanan using the reliable method
            $layanan = $surat->getPelayanan();
            
            // Load pelayanan relationships if layanan exists
            if ($layanan) {
                $layanan->load([
                    'user',
                    'jenisPelayanan', 
                    'dataIdentitas.identitasPemohon'
                ]);
                
                \Log::info('Print surat: Pelayanan found and loaded', [
                    'temp_surat_id' => $surat->id,
                    'pelayanan_id' => $layanan->id,
                    'temp_surat_ids' => $layanan->temp_surat_id,
                    'contains_surat' => in_array($surat->id, $layanan->temp_surat_id ?? [])
                ]);
            } else {
                \Log::info('Print surat: No pelayanan found for register surat', [
                    'temp_surat_id' => $surat->id
                ]);
            }

            $kategoriSurat = $surat->kategori_surat;

            \Log::info('Kategori Surat: ' . ($kategoriSurat ? $kategoriSurat->nama : 'null'));
            \Log::info('Kategori Surat: ' . ($kategoriSurat ? $kategoriSurat->hasBladeTemplate() : 'null'));
            
            // Cek apakah kategori surat memiliki template blade
            if ($kategoriSurat && $kategoriSurat->hasBladeTemplate()) {
                // Gunakan template blade dari kategori surat
                $templatePath = $kategoriSurat->getBladeTemplatePath();
                
                // Siapkan data template - menggunakan logika yang sama dengan KategoriSuratController
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
                    
                    \Log::info('Register Surat PDF: Using DUK data for layanan surat', [
                        'kategori_id' => $kategoriSurat->id,
                        'temp_surat_id' => $surat->id,
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
                    
                    \Log::info('Register Surat PDF: Using manual data for non-layanan surat', [
                        'kategori_id' => $kategoriSurat->id,
                        'temp_surat_id' => $surat->id,
                        'layanan_id' => $layanan ? $layanan->id : null,
                        'manual_data_count' => count($templateData['data'])
                    ]);
                }

                \Log::info('Print surat using blade template:', [
                    'temp_surat_id' => $surat->id,
                    'template_path' => $templatePath,
                    'kategori_surat' => $kategoriSurat->nama,
                    'is_layanan' => $kategoriSurat->isLayanan(),
                    'has_layanan_data' => $layanan ? true : false,
                    'template_data_keys' => array_keys($templateData['data'] ?? [])
                ]);

                // Generate HTML dari Blade template
                $html = view($templatePath, $templateData)->render();
                
                \Log::info('Register Surat PDF: HTML generated successfully', [
                    'temp_surat_id' => $surat->id,
                    'html_length' => strlen($html),
                    'template_path' => $templatePath
                ]);

                // Generate PDF menggunakan DomPDF dengan setting yang sama seperti di KategoriSuratController
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

                $filename = 'Surat_' . $this->sanitizeFileName($surat->nomor_surat) . '_' . now()->format('YmdHis') . '.pdf';
                
                // Return PDF sebagai stream untuk preview di browser
                $pdfOutput = $pdf->output();
                return response($pdfOutput)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="' . $this->sanitizeFileName($filename) . '"')
                    ->header('Content-Length', strlen($pdfOutput))
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
                
            } else {
                \Log::info('Using default print template:', [
                    'temp_surat_id' => $surat->id,
                    'kategori_surat' => $kategoriSurat ? $kategoriSurat->nama : 'No kategori',
                    'has_blade_template' => $kategoriSurat ? $kategoriSurat->hasBladeTemplate() : false
                ]);
                
                // Fallback ke template default jika tidak ada template blade
                $pdf = PDF::loadView('adm.register_surat.print', [
                    'surat' => $surat
                ])->setPaper('a4', 'portrait');

                return $pdf->stream('Surat_' . $this->sanitizeFileName($surat->perihal) . '.pdf');
            }

        } catch (\Exception $e) {
            \Log::error('Error in print surat:', [
                'temp_surat_id' => $surat->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback ke template default jika terjadi error
            $pdf = PDF::loadView('adm.register_surat.print', [
                'surat' => $surat
            ])->setPaper('a4', 'portrait');

            return $pdf->stream('Surat_' . $surat->perihal . '.pdf');
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
} 