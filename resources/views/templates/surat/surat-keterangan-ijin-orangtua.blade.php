<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Usaha</title>
    <style>
        @page {
            margin: 0.5cm 1.5cm 0.5cm 1.5cm;
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
        
        .nomor-tanggal {
            text-align: center;
            margin: 10px 0 15px 0;
            font-size: 10pt;
        }
        
        .judul-surat {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 20px 0 10px 0;
            text-decoration: underline;
            /* text-transform: uppercase; */
        }
        
        .isi-surat {
            text-align: justify;
            margin: 15px 0;
            line-height: 1.5;
        }
        
        .isi-surat p {
            margin: 12px 0;
            text-indent: 0;
        }
        
        .data-pemohon {
            margin: 15px 0;
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
            width: 20px;
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
            margin-bottom: 10px;
        }
        
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }
        
        .ttd-jabatan {
            margin-top: 5px;
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
                margin: 8px 0 12px 0;
            }
            
            .judul-surat {
                margin: 15px 0 8px 0;
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
        @include('partials.kop-surat')
        
        {{-- Judul Surat --}}
        <div class="judul-surat">
            SURAT KETERANGAN IJIN ORANG TUA
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
                        <td style="width: 280px;">Nama</td>
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
                        <td>Kepala Desa {{ $kopConfig->desa ?? 'Banyupelle' }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="menerangkan-section">
                <div class="menerangkan-title">Menerangkan bahwa :</div>
                <div class="menerangkan-content">
                    <table>
                        <tr>
                            <td style="width: 280px;">Nama</td>
                            <td style="width: 20px; text-align: center;">:</td>
                            <td>{{ $data['nama'] ?? '_______________' }}</td>
                        </tr>
                        <tr>
                            <td>NIK</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $data['nik'] ?? $data['no_ktp'] ?? '_______________' }}</td>
                        </tr>
                        <tr>
                            <td>Tempat Tanggal Lahir</td>
                            <td style="text-align: center;">:</td>
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
            
            <p>Bahwa orang tersebut diatas benar-benar penduduk desa banyupelle kecamatan palengaan kabupaten pamekasan dan yang bersangkutan diatas benra-benar telah mndapatkan izin / restu dari orang tuanya <strong>{{ $data['tujuan_ijin'] ?? '_______________' }}</strong>.</p>
            
            <p>Demikian surat keterangan ini dibuat dengan sebenarnya dan dapat dipergunakan sebagaimana mestinya.</p>
        </div>
        
        {{-- Tanda Tangan Kepala Desa - menggunakan komponen --}}
        @include('partials.tanda-tangan', [
            'position' => 'right',
            'width' => '500px',
            'marginTop' => '40px',
            'marginBottom' => '30px',
            'spacingTtd' => '150px'
        ])
    </div>

    {{-- Fallback Footer --}}
    <div class="footer-fallback">
        <strong>Dokumen ini telah ditandatangani secara elektronik</strong> | menggunakan sertifikat elektronik BSrE, Badan Siber dan Sandi Negara
    </div>
</body>
</html> 