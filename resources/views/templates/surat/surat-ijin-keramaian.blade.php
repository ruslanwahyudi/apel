<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Ijin Keramaian</title>
    <style>
        @page {
            margin: 0.5cm 1.5cm 1.5cm 1.5cm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .surat-content {
            margin: 0;
            padding: 0;
        }
        
        .header-info {
            margin: 8px 0 15px 0;
            font-size: 10pt;
            display: table;
            width: 100%;
        }
        
        .header-left {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 48%;
            text-align: right;
            vertical-align: top;
        }
        
        .nomor-info {
            margin: 5px 0;
            line-height: 1.4;
        }
        
        .judul-surat {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 10px 0 10px 0;
            text-decoration: underline;
            /* text-transform: uppercase; */
        }
        
        .isi-surat {
            text-align: justify;
            margin: 15px 0;
            line-height: 1.4;
        }
        
        .isi-surat p {
            margin: 12px 0;
            text-indent: 0;
        }
        
        .data-kegiatan {
            margin: 15px 0;
            margin-left: 0;
        }
        
        .data-kegiatan table {
            border-collapse: collapse;
            width: 100%;
        }
        
        .data-kegiatan td {
            padding: 3px 0;
            vertical-align: top;
        }
        
        .data-kegiatan td:first-child {
            width: 150px;
        }
        
        .data-kegiatan td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .peraturan-section {
            margin: 20px 0;
        }
        
        .peraturan-section ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .peraturan-section ol li {
            margin: 5px 0;
            line-height: 1.4;
        }
        
        .syarat-section {
            margin: 20px 0;
        }
        
        .syarat-section ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .syarat-section ul li {
            margin: 5px 0;
            line-height: 1.4;
        }
        
        .penutup-section {
            margin: 20px 0;
        }
        
        .kepada-section {
            margin: 5px 0;
            line-height: 1.4;
        }
        
        /* Fallback footer jika @page tidak bekerja */
        .footer-fallback {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
            color: #666;
            background: white;
            border-top: 1px solid #ccc;
            padding: 3px;
            margin: 0;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            
            .surat-content {
                max-width: none;
                margin: 0;
                padding: 0;
            }
            
            .header-info {
                margin: 8px 0 15px 0;
                font-size: 11pt;
                display: table;
                width: 100%;
            }
            
            .header-left {
                display: table-cell;
                width: 48%;
                vertical-align: top;
            }
            
            .header-right {
                display: table-cell;
                width: 48%;
                text-align: right;
                vertical-align: top;
            }
            
            .nomor-info {
                margin: 5px 0;
                line-height: 1.4;
            }
            
            .kepada-section {
                margin: 5px 0;
                line-height: 1.4;
            }
            
            .judul-surat {
                margin: 15px 0 8px 0;
            }
            
            .isi-surat {
                margin: 12px 0;
            }
        }
    </style>
</head>
<body>
    <div class="surat-content">
        {{-- Include Kop Surat --}}
        @include('partials.kop-surat')
        
        {{-- Header Info --}}
        <div class="header-info">
            @php
                use App\Models\KopSuratConfig;
                $kopConfig = KopSuratConfig::getActiveConfig();
                
                // Format tanggal dengan format Indonesia
                $tanggalSurat = isset($data['tanggal']) && !empty($data['tanggal']) 
                    ? \Carbon\Carbon::parse($data['tanggal'])->locale('id')->translatedFormat('j F Y')
                    : \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y');
            @endphp
            
            <div class="header-left">
                <div class="nomor-info">
                    
                </div>
                
                <div class="nomor-info">
                    <strong>Nomor</strong> : {{ $data['nomor'] ?? $nomor_surat ?? '..........................................' }}
                </div>
                <div class="nomor-info">
                    <strong>Sifat</strong> : {{ $data['sifat'] ?? 'Penting' }}
                </div>
                <div class="nomor-info">
                    <strong>Lampiran</strong> : {{ $data['lampiran'] ?? '1 (satu)' }}
                </div>
                <div class="nomor-info">
                    <strong>Perihal</strong> : <u>{{ $data['perihal'] ?? 'Permohonan Izin Keramaian' }}</u>
                </div>
            </div>
            
            <div class="header-right">
                <div class="kepada-section">
                    <div class="nomor-info">
                        Pamekasan, {{ $tanggalSurat }}
                    </div>
                    
                    <div>Kepada Yth :</div>
                    <div>1. Bapak Camat Palengaan</div>
                    <div>2. Kapolsek Palengaan</div>
                    <div>3. Danramil Palengaan</div>
                </div>
            </div>
        </div>
        
        {{-- Judul Surat --}}
        <div class="judul-surat">
            Permohonan Izin Keramaian
        </div>
        
        {{-- Isi Surat --}}
        <div class="isi-surat">
            <p>Berdasarkan hasil evaluasi, koordinasi, Musyawarah dan pertimbangan bersama warga dan tokoh masyarakat sekitar maka dengan ini pemerintah desa banyupelle memohon agar sekiranya mendapatkan izin keramaian kepada <strong>{{ $data['nama_kegiatan'] ?? 'Warga Kampung' }}</strong> dalam rangka <strong>{{ $data['nama_acara'] ?? $data['nama_egiatan'] ?? 'Nama Acara/Kegiatan' }}</strong> :</p>
            
            <div class="data-kegiatan">
                <table>
                    <tr>
                        <td><strong>Hari Tanggal</strong></td>
                        <td>:</td>
                        <td>
                            @php
                                $tanggalKegiatan = $data['tanggal'] ?? null;
                                
                                if (!empty($tanggalKegiatan)) {
                                    try {
                                        // Get day name and formatted date
                                        $carbon = \Carbon\Carbon::parse($tanggalKegiatan);
                                        $dayName = $carbon->locale('id')->translatedFormat('l');
                                        $formattedDate = $carbon->locale('id')->translatedFormat('j F Y');
                                        $finalDate = $dayName . ', ' . $formattedDate;
                                    } catch (\Exception $e) {
                                        $finalDate = $tanggalKegiatan;
                                    }
                                } else {
                                    $finalDate = '..........................................';
                                }
                            @endphp
                            {{ $finalDate }}
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Jam</strong></td>
                        <td>:</td>
                        <td>{{ $data['jam'] ?? '..........................................' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tempat</strong></td>
                        <td>:</td>
                        <td>{{ $data['tempat'] ?? '..........................................' }}</td>
                    </tr>
                </table>
            </div>
            
            <p>Demi lancarnya dan suksesnya acara tersebut diatas maka pemerintah desa banyupelle mohon bersama personal pengamanan yang terdiri dari 2 orang dari Kepolisian (Polsek), 1 orang dari koramil (Babinsa) dan 1 orang dari Kecamatan (Satpol PP).</p>
            
            
            
            <div class="penutup-section">
                <p>Demikian surat permohonan izin ini disampaikan, atas perhatian dan kerjasama yang diberikan kami ucapkan terima kasih.</p>
            </div>
        </div>
        
        {{-- Tanda Tangan Kepala Desa --}}
        @include('partials.tanda-tangan', [
            'position' => 'right',
            'width' => '250px',
            'marginTop' => '30px',
            'marginBottom' => '30px',
            'spacingTtd' => '120px',
            'customJabatan' => 'Pj. Kepala Desa Banyupelle'
        ])
    </div>

    {{-- Fallback Footer --}}
    <div class="footer-fallback">
        <strong>Dokumen ini telah ditandatangani secara elektronik</strong> | menggunakan sertifikat elektronik BSrE, Badan Siber dan Sandi Negara
    </div>
</body>
</html> 