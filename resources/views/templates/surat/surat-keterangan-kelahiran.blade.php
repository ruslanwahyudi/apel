<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Kelahiran</title>
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
            margin: 10px 0 1px 0;
            font-size: 10pt;    
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
            margin: 5px 0;
            line-height: 1.4;
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
        @include('partials.kop-surat')
        
        {{-- Judul Surat --}}
        <div class="judul-surat">
            SURAT KETERANGAN KELAHIRAN
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
            
            <p>Yang bertanda tangan dibawah ini, Kepala Desa {{ $kopConfig->desa ?? 'Banyupelle' }}, Menerangkan bahwa;</p>
            <br>
            <p>Pada Hari ini, {{ $data['hari_lahir'] ?? '_______________' }}, tanggal {{ $data['tanggal_kelahiran'] ?? '_______________' }}, Pukul {{ $data['jam'] ?? '_______________' }} WIB, telah lahir seorang Bayi :</p>
            <div class="data-pemohon">
                <table>
                    <tr>
                        <td style="width: 280px;">Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $data['jenis_kelamin'] ?? '_______________' }}</td>
                    </tr>
                    <tr>
                        <td style="width: 280px;">Jenis Kelahiran</td>
                        <td>:</td>
                        <td>{{ $data['jenis_kelahiran'] ?? '_______________' }}</td>
                    </tr>
                    <tr>
                        <td style="width: 280px;">Kelahiran Ke</td>
                        <td>:</td>
                        <td>{{ $data['kelahiran_ke'] ?? '_______________' }}</td>
                    </tr>
                    <tr>
                        <td style="width: 280px;">Berat Lahir</td>
                        <td>:</td>
                        <td>{{ $data['berat'] ?? '_______________' }} Gram</td>
                    </tr>
                    <tr>
                        <td style="width: 280px;">Panjang Lahir</td>
                        <td>:</td>
                        <td>{{ $data['panjang'] ?? '_______________' }} Cm</td>
                    </tr>
                    <tr>
                        <td style="width: 280px;">Tempat Kelahiran</td>
                        <td>:</td>
                        <td>{{ $data['tempat_kelahiran'] ?? '_______________' }}</td>
                    </tr>

                    <tr>
                        <td style="width: 280px;">Diberi Nama</td>
                        <td>:</td>
                        <td>{{ $data['nama'] ?? '_______________' }}</td>
                    </tr>
                    <tr>
                        <td style="width: 280px;">Diberi Nama</td>
                        <td>:</td>
                        <td>{{ $data['nama_anak'] ?? '_______________' }}</td>
                    </tr>
                    
                </table>
            </div>

            <div class="menerangkan-section">
                <div class="menerangkan-title">Dari Orang Tua :</div>
                <div class="menerangkan-content">
                    <table>
                        <tr>
                            <td style="width: 280px;">Nama Ayah</td>
                            <td style="width: 20px; text-align: center;">:</td>
                            <td>{{ $data['nama_ayah'] ?? '_______________' }}</td>
                        </tr>
                        <tr>
                            <td>NIK Ayah</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $data['nik_ayah'] ?? $data['nik'] ?? '_______________' }}</td>
                        </tr>
                        <tr>
                            <td>Umur Ayah</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $data['umur_ayah'] ?? '_______________' }} Tahun
                            </td>
                        </tr>
                        <tr>
                            <td>Pekerjaan Ayah</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $data['pekerjaan_ayah'] ?? '_______________' }}</td>
                        </tr>

                        <tr>
                            <td style="width: 280px;">Nama Ibu</td>
                            <td style="width: 20px; text-align: center;">:</td>
                            <td>{{ $data['nama_ibu'] ?? '_______________' }}</td>
                        </tr>
                        <tr>
                            <td>NIK Ibu</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $data['nik_ibu'] ?? $data['nik'] ?? '_______________' }}</td>
                        </tr>
                        <tr>
                            <td>Umur Ibu</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $data['umur_ibu'] ?? '_______________' }} Tahun
                            </td>
                        </tr>
                        <tr>
                            <td>Pekerjaan Ibu</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $data['pekerjaan_ibu'] ?? '_______________' }}</td>
                        </tr>

                        <tr>
                            <td>Alamat</td>
                            <td style="text-align: center;">:</td>
                            <td>{{ $data['alamat'] ?? '_______________' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
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
            'spacingTtd' => '180px',
            'showTempat' => false,
            'showTte' => false,
            'customJabatan' => 'Camat ' . $kecamatan,
            'customNama' => $data['nama_camat'] ?? 'Muzanni, S.H, M.Si',
            'customNip' => '197006151994031008',
            'ttd_pengirim' => '${ttd_pengirim}' ?? null
        ])
    </div>

    {{-- Fallback Footer --}}
    <div class="footer-fallback">
        <strong>Dokumen ini telah ditandatangani secara elektronik</strong> | menggunakan sertifikat elektronik BSrE, Badan Siber dan Sandi Negara
    </div>
</body>
</html> 