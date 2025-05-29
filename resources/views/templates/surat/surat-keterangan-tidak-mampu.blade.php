<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Tidak Mampu</title>
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
            text-align: center;
            margin: 10px 0 1px 0;
            font-size: 10pt;    
        }
        
        .judul-surat {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 10px 0 10px 0;
            text-decoration: underline;
            text-transform: uppercase;
        }
        
        .isi-surat {
            text-align: justify;
            margin: 5px 0;
            line-height: 1.8;
        }
        
        .isi-surat p {
            margin: 1px 0;
            text-indent: 0;
        }
        
        .data-pemohon {
            margin: 1px 0;
            margin-left: 0;
        }
        
        .data-pemohon table {
            border-collapse: collapse;
            width: 100%;
        }
        
        .data-pemohon td {
            padding: 3px 0;
            vertical-align: top;
        }
        
        .data-pemohon td:first-child {
            width: 150px;
        }
        
        .data-pemohon td:nth-child(2) {
            width: 100px;
            text-align: center;
        }
        
        .menerangkan-section {
            margin: 20px 0;
        }
        
        .menerangkan-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .menerangkan-content {
            margin-left: 30px;
        }
        
        .ttd-tempat-tanggal {
            margin-bottom: 5px;
        }
        
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }
        
        .ttd-jabatan {
            margin-top: 5px;
        }
        
        .mengetahui-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .mengetahui-jabatan {
            margin-bottom: 10px;
        }
        
        .mengetahui-nama {
            font-weight: bold;
            text-decoration: underline;
        }
        
        .mengetahui-nip {
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
                margin: 5px 0;
            }
            
            .isi-surat {
                margin: 12px 0;
            }
            
            .ttd-tempat-tanggal {
                margin-bottom: 10px;
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
            SURAT KETERANGAN TIDAK MAMPU
        </div>
        
        {{-- Nomor Surat - dipindah ke bawah judul --}}
        <div class="nomor-tanggal">
            <div>Nomor: {{ $data['nomor'] ?? $nomor_surat ?? generateNoSurat() }}</div>
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
                        <td>Nama</td>
                        <td>:</td>
                        <td><strong>{{ $data['nama_kepala_desa'] ?? $kopConfig->kepala_desa ?? 'SYAMSUL SE' }}</strong></td>
                    </tr>
                    <tr>
                        <td>NIP</td>
                        <td>:</td>
                        <td>{{ $data['nip_kepala_desa'] ?? $kopConfig->nip_kepala_desa ?? '196020520101016' }}</td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td>Pj. Kepala Desa {{ $kopConfig->desa ?? 'Banyupelle' }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="menerangkan-section">
                <div class="menerangkan-title">Menerangkan bahwa :</div>
                <div class="menerangkan-content">
                    <table>
                        <tr>
                            <td style="width: 150px;">Nama</td>
                            <td style="width: 20px; text-align: center;">:</td>
                            <td>{{ $data['nama'] ?? '_______________' }}</td>
                        </tr>
                        <tr>
                            <td>NIK</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $data['nik'] ?? $data['nik'] ?? '_______________' }}</td>
                        </tr>
                        <tr>
                            <td>Tempat Tanggal Lahir</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $data['tempat_lahir'] ?? '_______________' }}, 
                                @php
                                    $tanggalLahir = $data['tanggal_lahir'] ?? null;
                                    $isValidDate = false;
                                    
                                    if (!empty($tanggalLahir)) {
                                        try {
                                            $parsedDate = \Carbon\Carbon::parse($tanggalLahir);
                                            $isValidDate = true;
                                            $formattedDate = $parsedDate->format('d F Y');
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
                            <td>Pekerjaan</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $data['pekerjaan'] ?? '_______________' }}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $data['alamat'] ?? '_______________' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <p>Bahwa orang tersebut benar-benar penduduk desa {{ $kopConfig->desa ?? 'banyupelle' }} kecamatan {{ $kopConfig->kecamatan ?? 'palengaan' }} kabupaten {{ $kopConfig->kabupaten ?? 'pamekasan' }} dan yang bersangkutan tergolong keluarga prasejahtera (tidak mampu).</p>
            
            <p>Demikian surat keterangan ini dibuat dengan sebenarnya dan dapat dipergunakan sebagaimana mestinya.</p>
        </div>
        
        {{-- Tanda Tangan Pj Kepala Desa - menggunakan komponen --}}
        @include('partials.tanda-tangan', [
            'position' => 'right',
            'width' => '500px',
            'marginTop' => '10px',
            'marginBottom' => '10px',
            'spacingTtd' => '100px'
        ])
        
        {{-- Tanda Tangan Camat - menggunakan komponen --}}
        @php
            $kecamatan = $kopConfig->kecamatan ?? 'Palengaan';
        @endphp
        <div style="text-align: center;">
            <div style="margin-bottom: 10px;">
                Mengetahui,
            </div>
        </div>
        @include('partials.tanda-tangan', [
            'position' => 'center',
            'width' => '550px',
            'marginTop' => '10px',
            'marginBottom' => '10px',
            'spacingTtd' => '80px',
            'showTempat' => false,
            'customJabatan' => 'Camat ' . $kecamatan,
            'customNama' => $data['nama_camat'] ?? 'Muzanni, S.H, M.Si',
            'customNip' => '197006151994031008'
        ])
    </div>
</body>
</html> 