<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $kategori->nama ?? 'Contoh Surat dengan Kop' }}</title>
    <style>
        @page {
            margin: 1.5cm 2cm 2cm 2cm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #000;
        }
        
        .surat-content {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
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
                            $formattedDate = \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('j F Y');
                        } catch (\Exception $e) {
                            // Not a valid date, use raw value or current date
                            $formattedDate = $tanggal;
                        }
                    } else {
                        $formattedDate = \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y');
                    }
                @endphp
                {{ $formattedDate }}
            </div>
        </div>
        
        {{-- Judul Surat --}}
        <div class="judul-surat">
            {{ $kategori->nama ?? 'CONTOH SURAT DENGAN KOP' }}
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
                        <td>Nama</td>
                        <td>:</td>
                        <td><strong>{{ $data['nama'] ?? '_______________' }}</strong></td>
                    </tr>
                    <tr>
                        <td>Tempat, Tanggal Lahir</td>
                        <td>:</td>
                        <td>{{ $data['tempat_lahir'] ?? '_______________' }}, 
                            @php
                                $tanggalLahir = $data['tanggal_lahir'] ?? null;
                                
                                if (!empty($tanggalLahir)) {
                                    try {
                                        $formattedDate = \Carbon\Carbon::parse($tanggalLahir)->locale('id')->translatedFormat('j F Y');
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
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $data['alamat'] ?? '_______________' }}</td>
                    </tr>
                    <tr>
                        <td>No. KTP</td>
                        <td>:</td>
                        <td>{{ $data['no_ktp'] ?? '_______________' }}</td>
                    </tr>
                </table>
            </div>
            
            <p>Adalah benar-benar penduduk {{ $kopConfig->desa }}, Kecamatan {{ $kopConfig->kecamatan }}, Kabupaten {{ $kopConfig->kabupaten }}.</p>
            
            <p>Surat ini dibuat untuk keperluan <strong>{{ $data['keperluan'] ?? '_______________' }}</strong> dan dapat digunakan sebagaimana mestinya.</p>
            
            <p>Demikian surat ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
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