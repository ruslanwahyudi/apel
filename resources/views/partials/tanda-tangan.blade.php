{{--
    Komponen Tanda Tangan yang dapat digunakan berulang
    
    Parameters:
    - $position: 'left', 'center', 'right' (default: 'right')
    - $width: lebar area tanda tangan dalam px (default: '250px')
    - $marginTop: margin atas (default: '40px')
    - $marginBottom: margin bawah (default: '30px')
    - $spacingTtd: jarak untuk tanda tangan (default: '150px')
    - $showTempat: tampilkan tempat/tanggal (default: true)
    - $showJabatan: tampilkan jabatan (default: true)
    - $customTempat: tempat custom (optional)
    - $customTanggal: tanggal custom (optional)
    - $customJabatan: jabatan custom (optional)
    - $customNama: nama custom (optional)
    - $customNip: NIP custom (optional)
    - $showTte: tampilkan TTE digital signature (default: true)
--}}

@php
    use App\Models\KopSuratConfig;
    $kopConfig = KopSuratConfig::getActiveConfig();
    
    // Set default values untuk semua parameter
    $position = $position ?? 'right';
    $width = '500px';
    $marginTop = $marginTop ?? '40px';
    $marginBottom = $marginBottom ?? '30px';
    $spacingTtd = '120px';
    $showTempat = $showTempat ?? true;
    $showJabatan = $showJabatan ?? true;
    $showTte = $showTte ?? true;
    
    // Set default values untuk custom parameters
    $customTempat = $customTempat ?? null;
    $customTanggal = $customTanggal ?? null;
    $customJabatan = $customJabatan ?? null;
    $customNama = $customNama ?? null;
    $customNip = $customNip ?? null;
    
    // Set default value untuk data array
    $data = $data ?? [];
    
    // Determine alignment based on position
    $textAlign = 'right';
    if ($position === 'left') {
        $textAlign = 'left';
    } elseif ($position === 'center') {
        $textAlign = 'center';
    }
    
    // Get data values with fallbacks
    $tempat = $customTempat ?? $kopConfig->desa ?? 'Pamekasan';
    
    // Format tanggal dengan format Indonesia "31 Januari 2025"
    if (!empty($customTanggal)) {
        $tanggal = $customTanggal;
    } elseif (isset($data['tanggal']) && !empty($data['tanggal'])) {
        $tanggal = \Carbon\Carbon::parse($data['tanggal'])->locale('id')->translatedFormat('j F Y');
    } else {
        // Default ke tanggal hari ini dengan format Indonesia
        $tanggal = \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y');
    }
    
    $jabatan = $customJabatan ?? "Pj. Kepala Desa " . ($kopConfig->desa ?? 'Banyupelle');
    $nama = $customNama ?? $kopConfig->kepala_desa ?? 'SYAMSUL SE';
    $nip = $customNip ?? $kopConfig->nip_kepala_desa ?? '196020520101016';
    
    // Path untuk TTE image
    $ttePath = public_path('assets/images/tte_kades.png');
    $tteExists = file_exists($ttePath);
@endphp

<div style="text-align: {{ $textAlign }}; margin-top: {{ $marginTop }}; margin-bottom: {{ $marginBottom }};">
    <div style="display: inline-block; text-align: center; width: {{ $width }};">
        @if($showTempat)
            <div style="margin-bottom: 10px;">
                {{ $tempat }}, {{ $tanggal }}
            </div>
        @endif
        
        @if($showJabatan)
            <div>{{ $jabatan }}</div>
        @endif
        
        {{-- Area Tanda Tangan dengan TTE --}}
        <div style="margin: 20px 0 10px 0; position: relative; height: {{ $spacingTtd }};">
            @if($showTte && $tteExists)
                {{-- TTE Digital Signature --}}
                <div style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); z-index: 1;">
                    <img src="{{ asset('assets/images/tte_kades.png') }}" 
                         alt="Tanda Tangan Elektronik" 
                         style="max-width: 180px; max-height: 120px; opacity: 0.8;">
                </div>
            @endif
        </div>
        
        <div style="font-weight: bold; text-decoration: underline;">{{ $nama }}</div>
        
        @if($showJabatan && $nip)
            <div style="margin-top: 5px;">NIP. {{ $nip }}</div>
        @endif
    </div>
</div> 