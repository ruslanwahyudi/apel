<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Beda Nama</title>
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
        
        .judul-surat {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 20px 0 15px 0;
            text-decoration: underline;
            /* text-transform: uppercase; */
        }
        
        .nomor-surat {
            text-align: center;
            margin: 10px 0 20px 0;
            font-size: 11pt;
        }
        
        .isi-surat {
            text-align: justify;
            margin: 15px 0;
            line-height: 1.8;
        }
        
        .isi-surat p {
            margin: 12px 0;
            text-indent: 0;
        }
        
        .data-pemohon {
            margin: 15px 0 15px 40px;
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
            width: 200px;
        }
        
        .data-pemohon td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .menerangkan-section {
            margin: 20px 0;
        }
        
        .menerangkan-title {
            margin-bottom: 10px;
        }
        
        .menerangkan-content {
            margin-left: 20px;
        }
        
        .data-orang {
            margin: 10px 0 10px 40px;
        }
        
        .data-orang table {
            border-collapse: collapse;
            width: 100%;
        }
        
        .data-orang td {
            padding: 3px 0;
            vertical-align: top;
        }
        
        .data-orang td:first-child {
            width: 200px;
        }
        
        .data-orang td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .dan-section {
            margin: 15px 0 15px 40px;
            font-weight: bold;
        }
        
        .keterangan-section {
            margin: 20px 0;
        }
        
        .penutup-section {
            margin: 20px 0;
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
            
            .judul-surat {
                margin: 15px 0 12px 0;
            }
            
            .nomor-surat {
                margin: 8px 0 15px 0;
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
        
        {{-- Judul Surat --}}
        <div class="judul-surat">
            Surat Keterangan Beda Nama
        </div>
        
        {{-- Nomor Surat --}}
        <div class="nomor-surat">
            Nomor : {{ $data['nomor'] ?? $nomor_surat ?? '..........................................' }}
        </div>
        
        {{-- Isi Surat --}}
        <div class="isi-surat">
            <p>Yang bertanda tangan dibawah ini, Kepala Desa Banyupelle Kecamatan Palengaan Kabupaten Pamekasan menerangkan dengan sebenarnya bahwa:</p>
            
            <div class="data-pemohon">
                @php
                    use App\Models\KopSuratConfig;
                    $kopConfig = KopSuratConfig::getActiveConfig();
                    $namaKepala = $kopConfig->nama_kepala ?? 'SYAMSUL SE';
                    $nipKepala = $kopConfig->nip_kepala ?? '19760205201001 1 016';
                    $jabatanKepala = $kopConfig->jabatan_kepala ?? 'Kepala Desa Banyupelle';
                @endphp
                
                <table>
                    <tr>
                        <td><strong>Nama</strong></td>
                        <td>:</td>
                        <td>{{ $data['nama_pertama'] ?? '..........................................' }}</td>
                    </tr>
                    <tr>
                        <td><strong>No. KTP</strong></td>
                        <td>:</td>
                        <td>{{ $data['nik'] ?? '..........................................' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jenis Kelamin</strong></td>
                        <td>:</td>
                        <td>{{ $data['jenis_kelamin'] ?? '..........................................' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Pekerjaan</strong></td>
                        <td>:</td>
                        <td>{{ $data['pekerjaan'] ?? '..........................................' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Alamat</strong></td>
                                <td>:</td>
                                <td>{{ $data['alamat_pertama'] ?? '..........................................' }}</td>
                            </tr>
                </table>
            </div>
            
            <div class="menerangkan-section">
                <div class="menerangkan-title">adalah satu dengan orang yang sama :</div>
                <div class="menerangkan-content">
                    <div class="data-orang">
                        <table>
                            <tr>
                                <td><strong>Nama</strong></td>
                                <td>:</td>
                                <td>{{ $data['nama_kedua'] ?? '..........................................' }}</td>
                            </tr>
                            <tr>
                                <td><strong>No. KTP</strong></td>
                                    <td>:</td>
                                    <td>{{ $data['nik'] ?? '..........................................' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis Kelamin</strong></td>
                                    <td>:</td>
                                    <td>{{ $data['jenis_kelamin'] ?? '..........................................' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Pekerjaan</strong></td>
                                    <td>:</td>
                                    <td>{{ $data['pekerjaan'] ?? '..........................................' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Alamat</strong></td>
                                            <td>:</td>
                                            <td>{{ $data['alamat_pertama'] ?? '..........................................' }}</td>
                                </tr>
                        </table>
                    </div>
                    
                    
                </div>
            </div>
            
            <div class="keterangan-section">
                <p>Dalam hal ini menerangkan bahwa yang tertera dalam Dokumen Kartu Identitas Kependudukan,
                <strong>Nama : {{ $data['nama_pertama'] ?? '..........................................' }}</strong>,
                dan yang tertera di (<strong>{{ $data['bukti_kepemilikan'] ?? '..........................................' }}</strong>),
                <strong>Nama : {{ $data['nama_kedua'] ?? '..........................................' }}</strong>, Dengan ini bahwa nama tersebut adalah benar satu orang</p>
            </div>
            
            <div class="penutup-section">
                <p>Demikian surat keterangan ini dibuat dengan sebenarnya dan dapat dipergunakan 
                sebagaimana mestinya.</p>
            </div>
        </div>
        
        {{-- Tanda Tangan Kepala Desa --}}
        @include('partials.tanda-tangan', [
            'position' => 'right',
            'width' => '250px',
            'marginTop' => '30px',
            'marginBottom' => '30px',
            'spacingTtd' => '120px'
        ])
    </div>
    {{-- Fallback Footer --}}
    <div class="footer-fallback">
        <strong>Dokumen ini telah ditandatangani secara elektronik</strong> | menggunakan sertifikat elektronik BSrE, Badan Siber dan Sandi Negara
    </div>
</body>
</html> 