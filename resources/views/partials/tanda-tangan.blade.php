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
--}}

@php
    use App\Models\KopSuratConfig;
    $kopConfig = KopSuratConfig::getActiveConfig();
    
    // Set default values untuk semua parameter
    $position = $position ?? 'right';
    $width = $width ?? '250px';
    $marginTop = $marginTop ?? '40px';
    $marginBottom = $marginBottom ?? '30px';
    $spacingTtd = $spacingTtd ?? '150px';
    $showTempat = $showTempat ?? true;
    $showJabatan = $showJabatan ?? true;
    
    // Set default values untuk custom parameters
    $customTempat = $customTempat ?? null;
    $customTanggal = $customTanggal ?? null;
    $customJabatan = $customJabatan ?? null;
    $customNama = $customNama ?? null;
    $customNip = $customNip ?? null;
    
    // Set default value untuk data array
    $data = $data ?? [];
    
    // Determine alignment based on position
    $textAlign = match($position) {
        'left' => 'left',
        'center' => 'center',
        'right' => 'right',
        default => 'right'
    };
    
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
        
        <div style="margin: {{ $spacingTtd }} 0 10px 0;"></div>
        
        <div style="font-weight: bold; text-decoration: underline;">{{ $nama }}</div>
        
        @if($showJabatan && $nip)
            <div style="margin-top: 5px;">NIP. {{ $nip }}</div>
        @endif
    </div>
</div> 