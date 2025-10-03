<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Domisili Lembaga</title>
    <style>
        @page {
            margin: 1.5cm 2cm 2.5cm 2cm;
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
        
        .nomor-tanggal {
            text-align: center;
            margin: 25px 0 30px 0;
            font-size: 12pt;
            clear: both;
            width: 100%;
        }
        
        .nomor-tanggal .nomor-line {
            margin-bottom: 5px;
        }
        
        .nomor-tanggal .tanggal-line {
            margin-bottom: 5px;
        }
        
        .judul-surat {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin: 30px 0 25px 0;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 1px;
            clear: both;
        }
        
        .isi-surat {
            text-align: justify;
            margin: 20px 0;
            line-height: 1.8;
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
        
        .keperluan {
            font-weight: bold;
            text-decoration: underline;
        }
        
        .nama-bold {
            font-weight: bold;
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
            
            .data-pemohon {
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
            SURAT KETERANGAN DOMISILI
        </div>
        
        {{-- Nomor dan Tanggal Surat - dipindah ke bawah judul --}}
        <div class="nomor-tanggal">
            <div class="nomor-line">Nomor: {{ $data['nomor'] ?? $nomor_surat ?? 'XXX/XX/XX/2025' }}</div>
            
        </div>
        
        {{-- Isi Surat --}}
        <div class="isi-surat">
            @php
                use App\Models\KopSuratConfig;
                $kopConfig = KopSuratConfig::getActiveConfig();
            @endphp
            
            <p>Yang bertanda tangan dibawah ini, Kepala Desa Banyupelle Kecamatan Palengaan Kabupaten Pamekasan menerangkan dengan sebenarnya bahwa :</p>
            

            <p>Menerangkan dengan sebenarnya bahwa :</p>
            
            <div class="data-pemohon">
                <table>
                    <tr>
                        <td style="width: 280px;">Nama</td>
                        <td>:</td>
                        <td><span class="nama-bold">{{ $data['nama_lembaga'] ?? $data['nama_lembaga'] ?? '................................' }}</span></td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $data['alamat'] ?? '................................' }}</td>
                    </tr>
                </table>
            </div>

            <p>Menerangkan dengan sebenarnya bahwa nama lembaga di atas benar benar berdomisili di {{ $data['alamat'] ?? '................................' }}.</p>
            
            <p>Demikian surat keterangan ini dibuat dengan sebenarnya dan dapat 
            dipergunakan sebagaimana mestinya.</p>
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