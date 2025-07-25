<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Kelakuan Baik</title>
    <style>
        @page {
            margin: 0.5cm 1.5cm 0.5cm 1.5cm;
            size: A4;
            @bottom-center {
                content: "Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik BSrE, Badan Siber dan Sandi Negara";
                font-size: 7pt;
                color: #666;
                text-align: center;
                border-top: 1px solid #ccc;
                padding-top: 5px;
            }
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
            width: 100%;
        }
        
        .judul-surat {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 30px 0 2px 0;
            text-decoration: underline;
            /* text-transform: uppercase; */
            letter-spacing: 1px;
            clear: both;
        }
        
        .nomor-tanggal {
            text-align: center;
            margin: 25px 0 30px 0;
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
        
        .keterangan-kelakuan {
            margin: 20px 0;
            text-indent: 30px;
            line-height: 1.6;
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
            
            .data-pemohon, .data-almarhum {
                margin: 18px 0 18px 45px;
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
            SURAT KETERANGAN PENDAPATAN ORANG TUA
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
            
            <p>Yang bertanda tangan dibawah ini Kepala Desa Banyupelle Kecamatan Palengaan Kabupaten Pamekasan, Menerangkan bahwa:</p>
            
            <div class="data-pemohon">
                <table>
                    <tr>
                        <td style="width: 280px;">Nama</td>
                        <td>:</td>
                        <td><span class="nama-bold">{{ $data['nama_orang_tua'] ?? 'SYAMSUL SE' }}</span></td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ $data['nik_orang_tua'] ?? '196020520101016' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Tanggal Lahir</td>
                        <td>:</td>
                        <td>{{ $data['tempat_lahir_orang_tua'] ?? '................................' }}, 
                            @php
                                $tanggalLahir = $data['tanggal_lahir_orang_tua'] ?? null;
                                
                                if (!empty($tanggalLahir)) {
                                    try {
                                        // Coba parse sebagai tanggal jika formatnya benar
                                        if (strlen($tanggalLahir) === 10 && substr_count($tanggalLahir, '-') === 2) {
                                            $formattedDate = \Carbon\Carbon::parse($tanggalLahir)->locale('id')->translatedFormat('d F Y');
                                        } else {
                                            $formattedDate = $tanggalLahir;
                                        }
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
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $data['jenis_kelamin_orang_tua'] ?? '................................' }}</td>
                    </tr>
                    <tr>
                        <td>Status Perkawinan</td>
                        <td>:</td>
                        <td>{{ $data['status_perkawinan_orang_tua'] ?? '................................' }}</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ $data['agama_orang_tua'] ?? '................................' }}</td>
                    </tr>
                    <tr>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>{{ $data['pekerjaan_orang_tua'] ?? '................................' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $data['alamat_orang_tua'] ?? '................................' }}</td>
                    </tr>
                </table>
            </div>

            <p>Adalah Wali/Orang Tua dari :</p>
            
            <div class="data-almarhum">
                <table>
                    <tr>
                        <td style="width: 280px;">Nama</td>
                        <td>:</td>
                        <td><span class="nama-bold">{{ $data['nama'] ?? '................................' }}</span></td>
                    </tr>
                    <tr>
                        <td>NIK</td>
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
                                        // Coba parse sebagai tanggal jika formatnya benar
                                        if (strlen($tanggalLahir) === 10 && substr_count($tanggalLahir, '-') === 2) {
                                            $formattedDate = \Carbon\Carbon::parse($tanggalLahir)->locale('id')->translatedFormat('d F Y');
                                        } else {
                                            $formattedDate = $tanggalLahir;
                                        }
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
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $data['jenis_kelamin'] ?? '................................' }}</td>
                    </tr>
                    <tr>
                        <td>Status Perkawinan</td>
                        <td>:</td>
                        <td>{{ $data['status_kawin'] ?? '................................' }}</td>
                    </tr>
                    <tr>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ $data['agama'] ?? '................................' }}</td>
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

            <div class="keterangan-kelakuan">
                <p>Menerangkan dengan sebenar-benarnya bahwa orang tua tersebut diatas betul-betul penduduk Desa kami dan orang tersebut tergolong ekonomi sangat rendah dengan penghasilan Rp. {{ $data['penghasilan'] ?? '................................' }} / Bulan dan menanggung {{ $data['jumlah_tanggungan'] ?? '................................' }} orang anggota keluarga.</p>
                
                <p>Demikian surat keterangan ini dibuat agar dapat dipergunakan sebagaimana mestinya.</p>
            </div>
        </div>
        
        {{-- Include Tanda Tangan --}}
        @include('partials.tanda-tangan', [
            'position' => 'right',
            'width' => '300px',
            'marginTop' => '50px',
            'marginBottom' => '30px',
            'spacingTtd' => '100px'
        ])
    </div>
    
    {{-- Fallback Footer --}}
    <div class="footer-fallback">
        <strong>Dokumen ini telah ditandatangani secara elektronik</strong> | menggunakan sertifikat elektronik BSrE, Badan Siber dan Sandi Negara
    </div>
</body>
</html> 