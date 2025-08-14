<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Ahli Waris</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 2cm 1.5cm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1;
            color: #000;
            margin: 0;
            padding: 0;
            padding-bottom: 60px; /* Space for footer */
        }
        
        .surat-content {
            margin: 0;
            padding: 0;
            width: 100%;
        }
        
        .judul-surat {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 30px 0 25px 0;
            text-decoration: underline;
            /* text-transform: uppercase; */
            letter-spacing: 1px;
            clear: both;
        }
        
        .nomor-tanggal {
            text-align: center;
            margin: 5px 0 10px 0;
            font-size: 10pt;
            clear: both;
            width: 100%;
        }
        
        .nomor-tanggal .nomor-line {
            margin-bottom: 5px;
        }
        
        .isi-surat {
            text-align: justify;
            margin: 20px 0;
            line-height: 1;
        }
        
        .isi-surat p {
            margin: 15px 0;
            text-indent: 30px;
        }
        
        .data-pemohon {
            margin: 20px 0 20px 50px;
        }
        
        .data-pemohon table {
            border-collapse: collapse;
            width: 100%;
        }
        
        .data-pemohon td {
            padding: 4px 0;
            vertical-align: top;
        }
        
        .data-pemohon td:first-child {
            width: 180px;
        }
        
        .data-pemohon td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .data-pemohon td:last-child {
            font-weight: normal;
        }
        
        .nama-bold {
            font-weight: bold;
        }
        
        .data-almarhum {
            margin: 20px 0 20px 50px;
        }
        
        .data-almarhum table {
            border-collapse: collapse;
            width: 100%;
        }
        
        .data-almarhum td {
            padding: 4px 0;
            vertical-align: top;
        }
        
        .data-almarhum td:first-child {
            width: 180px;
        }
        
        .data-almarhum td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .data-ahliwaris {
            margin: 20px 0 20px 50px;
        }
        
        .data-ahliwaris table {
            border-collapse: collapse;
            width: 100%;
        }
        
        .data-ahliwaris td {
            padding: 4px 0;
            vertical-align: top;
        }
        
        .data-ahliwaris td:first-child {
            width: 180px;
        }
        
        .data-ahliwaris td:nth-child(2) {
            width: 20px;
            text-align: center;
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
            
            .nomor-tanggal {
                margin: 20px 0 25px 0;
            }
            
            .judul-surat {
                margin: 25px 0 22px 0;
            }
            
            .isi-surat {
                margin: 18px 0;
            }
            
            .data-pemohon, .data-almarhum, .data-ahliwaris {
                margin: 18px 0 18px 45px;
            }
        }
    </style>
</head>
<body>
    <div class="surat-content">
        {{-- Include Kop Surat Dynamic --}}
        @include('partials.kop-surat-dynamic')
        
        {{-- Judul Surat --}}
        <div class="judul-surat">
            SURAT KETERANGAN AHLI WARIS
        </div>
        
        {{-- Nomor Surat --}}
        <div class="nomor-tanggal">
            <div class="nomor-line">Nomor: {{ $data['nomor'] ?? $nomor_surat ?? 'XXX/XX/XX/2025' }}</div>
        </div>
        
        {{-- Isi Surat --}}
        <div class="isi-surat">
            @php
                use App\Models\KopSuratConfig;
                $kopConfig = KopSuratConfig::getActiveConfig();
            @endphp
            
            <p>Yang bertanda tangan dibawah ini :</p>
            
            <div class="data-pemohon">
                <table>
                    <tr>
                        <td style="width: 280px;">Nama</td>
                        <td>:</td>
                        <td><span class="nama-bold">{{ $kopConfig->kepala_desa ?? 'SYAMSUL SE' }}</span></td>
                    </tr>
                    <tr>
                        <td>NIP</td>
                        <td>:</td>
                        <td>{{ $kopConfig->nip_kepala_desa ?? '196020520101016' }}</td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td>Kepala Desa {{ $kopConfig->desa ?? 'Banyupelle' }}</td>
                    </tr>
                </table>
            </div>

            <p>Menerangkan bahwa :</p>
            
            <div class="data-almarhum">
                <table>
                    <tr>
                        <td style="width: 280px;">Nama</td>
                        <td>:</td>
                        <td><span class="nama-bold">{{ $data['nama'] ?? '................................' }}</span></td>
                    </tr>
                    <tr>
                        <td style="width: 180px;">NIK</td>
                        <td>:</td>
                        <td>{{ $data['nik'] ?? '................................' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Tanggal Lahir</td>
                        <td>:</td>
                        <td>{{ $data['tempat_lahir'] ?? '................................' }}, 
                            @php
                                $tanggalLahir = $data['tanggal_lahir'] ?? null;
                                
                                if (!empty($tanggalLahir)) {
                                    try {
                                        $formattedDate = \Carbon\Carbon::parse($tanggalLahir)->locale('id')->translatedFormat('d F Y');
                                    } catch (\Exception $e) {
                                        $formattedDate = $tanggalLahir;
                                    }
                                } else {
                                    $formattedDate = '................................';
                                }
                            @endphp
                            {{ $formattedDate }}
                        </td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>{{ $data['pekerjaan'] ?? '................................' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $data['alamat'] ?? '................................' }}</td>
                    </tr>
                </table>
            </div>

            <p>Benar-benar AHLI WARIS dari :</p>
            
            <div class="data-ahliwaris">
                <table>
                    <tr>
                        <td style="width: 280px;">Nama</td>
                        <td>:</td>
                        <td><span class="nama-bold">{{ $data['nama_ahliwaris'] ?? '................................' }}</span></td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ $data['nik_ahliwaris'] ?? '................................' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Tanggal Lahir</td>
                        <td>:</td>
                        <td>{{ $data['tempat_lahir_ahliwaris'] ?? '................................' }}, 
                            @php
                                $tanggalLahirAhliWaris = $data['tanggal_lahir_ahliwaris'] ?? null;
                                
                                if (!empty($tanggalLahirAhliWaris)) {
                                    try {
                                        $formattedDateAhliWaris = \Carbon\Carbon::parse($tanggalLahirAhliWaris)->locale('id')->translatedFormat('d F Y');
                                    } catch (\Exception $e) {
                                        $formattedDateAhliWaris = $tanggalLahirAhliWaris;
                                    }
                                } else {
                                    $formattedDateAhliWaris = '................................';
                                }
                            @endphp
                            {{ $formattedDateAhliWaris }}
                        </td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $data['alamat_ahliwaris'] ?? '................................' }}</td>
                    </tr>
                </table>
            </div>

            <p>Demikian surat keterangan ini dibuat dengan sebenarnya dan dapat dipergunakan sebagaimana mestinya.</p>
        </div>
        
        {{-- Include Tanda Tangan --}}
        @include('partials.tanda-tangan', [
            'position' => 'right',
            'width' => '300px',
            'marginTop' => '50px',
            'marginBottom' => '30px',
            'spacingTtd' => '80px'
        ])
    </div>
    
    {{-- Fallback Footer --}}
    <div class="footer-fallback">
        <strong>Dokumen ini telah ditandatangani secara elektronik</strong> | menggunakan sertifikat elektronik BSrE, Badan Siber dan Sandi Negara
    </div>
</body>
</html> 