<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Domisili</title>
    <style>
        @page {
            margin: 1.5cm 2cm 2cm 2cm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .surat-content {
            margin: 0;
            padding: 0;
        }
        
        .nomor-tanggal {
            text-align: right;
            margin: 10px 0 15px 0;
            font-size: 12pt;
        }
        
        .judul-surat {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin: 20px 0;
            text-decoration: underline;
            text-transform: uppercase;
        }
        
        .isi-surat {
            text-align: justify;
            margin: 15px 0;
            line-height: 1.8;
        }
        
        .isi-surat p {
            margin: 12px 0;
            text-indent: 30px;
        }
        
        .data-pemohon {
            margin: 15px 0;
            margin-left: 50px;
        }
        
        .data-pemohon table {
            border-collapse: collapse;
        }
        
        .data-pemohon td {
            padding: 3px 0;
            vertical-align: top;
        }
        
        .data-pemohon td:first-child {
            width: 150px;
        }
        
        .data-pemohon td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .ttd-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .ttd-kiri, .ttd-kanan {
            width: 45%;
            text-align: center;
        }
        
        .ttd-tempat-tanggal {
            margin-bottom: 70px;
        }
        
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }
        
        .ttd-jabatan {
            margin-top: 5px;
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
                margin: 8px 0 12px 0;
            }
            
            .judul-surat {
                margin: 15px 0;
            }
            
            .isi-surat {
                margin: 12px 0;
            }
            
            .ttd-section {
                margin-top: 35px;
            }
            
            .ttd-tempat-tanggal {
                margin-bottom: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="surat-content">
        {{-- Include Kop Surat Dynamic --}}
        @include('partials.kop-surat-dynamic')
        
        {{-- Nomor dan Tanggal Surat --}}
        <div class="nomor-tanggal">
            <div>Nomor: {{ $data['nomor'] ?? $nomor_surat ?? generateNoSurat() }}</div>
            <div>Tanggal: 
                @php
                    $tanggal = $data['tanggal'] ?? null;
                    
                    if (!empty($tanggal)) {
                        try {
                            $formattedDate = \Carbon\Carbon::parse($tanggal)->format('d F Y');
                        } catch (\Exception $e) {
                            // Not a valid date, use raw value or current date
                            $formattedDate = $tanggal;
                        }
                    } else {
                        $formattedDate = date('d F Y');
                    }
                @endphp
                {{ $formattedDate }}
            </div>
        </div>
        
        {{-- Judul Surat --}}
        <div class="judul-surat">
            SURAT KETERANGAN DOMISILI
        </div>
        
        {{-- Isi Surat --}}
        <div class="isi-surat">
            @php
                use App\Models\KopSuratConfig;
                $kopConfig = KopSuratConfig::getActiveConfig();
            @endphp
            <p>Yang bertanda tangan di bawah ini, Kepala {{ $kopConfig->desa }}, Kecamatan {{ $kopConfig->kecamatan }}, Kabupaten {{ $kopConfig->kabupaten }}, dengan ini menerangkan bahwa:</p>
            
            <div class="data-pemohon">
                <table>
                    <tr>
                        <td>Nama Lengkap</td>
                        <td>:</td>
                        <td><strong>{{ $data['nama_lengkap'] ?? $data['nama'] ?? '_______________' }}</strong></td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ $data['nik'] ?? $data['no_ktp'] ?? '_______________' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat, Tanggal Lahir</td>
                        <td>:</td>
                        <td>{{ $data['tempat_lahir'] ?? '_______________' }}, 
                            @php
                                $tanggalLahir = $data['tanggal_lahir'] ?? null;
                                
                                if (!empty($tanggalLahir)) {
                                    try {
                                        $formattedDate = \Carbon\Carbon::parse($tanggalLahir)->format('d F Y');
                                    } catch (\Exception $e) {
                                        // Not a valid date, just use the raw value
                                        $formattedDate = $tanggalLahir;
                                    }
                                } else {
                                    $formattedDate = '_______________';
                                }
                            @endphp
                            {{ $formattedDate }}
                        </td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $data['jenis_kelamin'] ?? '_______________' }}</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ $data['agama'] ?? '_______________' }}</td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>{{ $data['pekerjaan'] ?? '_______________' }}</td>
                    </tr>
                    <tr>
                        <td>Status Perkawinan</td>
                        <td>:</td>
                        <td>{{ $data['status_kawin'] ?? $data['status_perkawinan'] ?? '_______________' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $data['alamat'] ?? '_______________' }}</td>
                    </tr>
                </table>
            </div>

            <p>Adalah benar-benar penduduk {{ $kopConfig->desa }}, Kecamatan {{ $kopConfig->kecamatan }}, Kabupaten {{ $kopConfig->kabupaten }}, dan berdomisili di alamat tersebut di atas.</p>

            <p>Surat keterangan ini dibuat untuk keperluan <strong>{{ $data['keperluan'] ?? '_______________' }}</strong> dan dapat digunakan sebagaimana mestinya.</p>

            <p>Demikian surat keterangan ini dibuat dengan sebenarnya dan dapat dipertanggungjawabkan.</p>
        </div>
        
        {{-- Tanda Tangan - menggunakan komponen --}}
        @include('partials.tanda-tangan', [
            'position' => 'right',
            'width' => '250px',
            'marginTop' => '40px',
            'marginBottom' => '30px',
            'spacingTtd' => '80px'
        ])
    </div>
</body>
</html> 