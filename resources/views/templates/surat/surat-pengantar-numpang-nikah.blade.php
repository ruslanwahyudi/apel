<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $kategori->nama ?? 'Surat Pengantar Numpang Nikah' }}</title>
    <style>
        @page {
            margin: 1cm 1cm 1cm 1cm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1;
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
            text-align: center;
            margin: 10px 0 1px 0;
            font-size: 10pt;
        }
        
        .judul-surat {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            margin: 20px 0;
            text-decoration: underline;
            text-transform: uppercase;
        }
        
        .isi-surat {
            text-align: justify;
            margin: 15px 0;
            line-height: 1.4;
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
            width: 100%;
        }
        
        .data-pemohon td {
            padding: 3px 0;
            vertical-align: top;
        }
        
        .data-pemohon td:first-child {
            width: 25px;
        }
        
        .data-pemohon td:nth-child(2) {
            width: 200px;
        }
        
        .data-pemohon td:nth-child(3) {
            width: 20px;
            text-align: center;
        }
        
        .form-section {
            margin: 20px 0;
        }
        
        .form-section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 10px;
            margin-left: 50px;
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
        {{-- Include Kop Surat --}}
        @include('partials.kop-surat')
            
        {{-- Judul Surat --}}
        <div class="judul-surat">
            SURAT PENGANTAR NUMPANG NIKAH
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
            
            <p>Yang bertanda tangan di bawah ini Kepala {{ $kopConfig->desa }}, Kecamatan {{ $kopConfig->kecamatan }}, Kabupaten {{ $kopConfig->kabupaten }}, dengan ini menerangkan dengan sesungguhnya bahwa :</p>
            
            {{-- Data Pemohon --}}
            <div class="data-pemohon">
                <table>
                    <tr>
                        <td>1.</td>
                        <td style="width: 380px;">Nama</td>
                        <td>:</td>
                        <td><strong>{{ strtoupper($data['nama'] ?? '...........................') }}</strong></td>
                    </tr>
                    <tr>
                        <td>2.</td>
                        <td>Tempat/tanggal lahir</td>
                        <td>:</td>
                        <td>{{ $data['tempat_lahir'] ?? '............................' }}, 
                            @php
                                $tanggalLahir = $data['tanggal_lahir'] ?? null;
                                
                                if (!empty($tanggalLahir)) {
                                    try {
                                        $formattedDate = \Carbon\Carbon::parse($tanggalLahir)->locale('id')->translatedFormat('j F Y');
                                    } catch (\Exception $e) {
                                        $formattedDate = $tanggalLahir;
                                    }
                                } else {
                                    $formattedDate = '.............................';
                                }
                            @endphp
                            {{ $formattedDate }}
                        </td>
                    </tr>
                    <tr>
                        <td>3.</td>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $data['jenis_kelamin'] ?? '............................' }}</td>
                    </tr>
                    <tr>
                        <td>4.</td>
                        <td>Status hubungan dalam KTP</td>
                        <td>:</td>
                        <td>{{ $data['status_kawin'] ?? '............................' }}</td>
                    </tr>
                    <tr>
                        <td>5.</td>
                        <td>Warga negara</td>
                        <td>:</td>
                        <td>{{ $data['kewarganegaraan'] ?? 'Indonesia' }}</td>
                    </tr>
                    <tr>
                        <td>6.</td>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ $data['agama'] ?? '............................' }}</td>
                    </tr>
                    <tr>
                        <td>7.</td>
                        <td>Status Perkawinan</td>
                        <td>:</td>
                        <td>{{ $data['status_kawin'] ?? '............................' }}</td>
                    </tr>
                    <tr>
                        <td>8.</td>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>{{ $data['pekerjaan'] ?? '............................' }}</td>
                    </tr>
                    <tr>
                        <td>9.</td>
                        <td>Tempat tinggal</td>
                        <td>:</td>
                        <td>{{ $data['alamat'] ?? '............................' }}</td>
                    </tr>
                </table>
            </div>
            
            <p>Nama tersebut di atas betul bermaksud akan menumpang Nikah/Kawin di {{ $data['tempat'] ?? '............................' }} Kepada seorang Laki-laki/Perempuan yang bernama : <strong>{{ $data['nama_calon'] ?? '............................' }}</strong> Bin {{ $data['bin_calon'] ?? '............................' }} </p>
                    
            <p>Demikian Surat ini dibuat, untuk dipergunakan sebagaimana mestinya.</p>
        </div>
        
        {{-- Include Tanda Tangan --}}
        @include('partials.tanda-tangan')
    </div>
</body>
</html> 