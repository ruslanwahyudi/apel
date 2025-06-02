<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $kategori->nama ?? 'Surat Pengantar Pernikahan' }}</title>
    <style>
        @page {
            margin: 0.5cm 1m 0.5cm 1cm;
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
        
        .header-section {
            text-align: right;
            margin-bottom: 10px;
            font-size: 10pt;
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
            line-height: 0.8;
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
        .nomor-tanggal {
            text-align: center;
            margin: 10px 0 1px 0;
            font-size: 10pt;    
        }
    </style>
</head>
<body>
    <div class="surat-content">
        {{-- Include Kop Surat --}}
        @include('partials.kop-surat')

        <div class="header-section">
            <div>Lampiran IX</div>
            <div>Kepdirjen Bimas Islam Nomor 473 Tahun 2020</div>
            <div>Tentang</div>
            <div>Petunjuk Teknis Pelaksanaan Pencatatan Nikah</div>
        </div>
            
        {{-- Judul Surat --}}
        <div class="judul-surat">
            PENGANTAR NIKAH
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
                        <td>Nomor Induk Kependudukan (NIK)</td>
                        <td>:</td>
                        <td>{{ $data['nik'] ?? '............................' }}</td>
                    </tr>
                    <tr>
                        <td>3.</td>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $data['jenis_kelamin'] ?? '............................' }}</td>
                    </tr>
                    <tr>
                        <td>4.</td>
                        <td>Tempat dan tanggal lahir</td>
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
                        <td>5.</td>
                        <td>Kewarganegaraan</td>
                        <td>:</td>
                        <td>{{ $data['kewarganegaraan'] ?? '............................' }}</td>
                    </tr>
                    <tr>
                        <td>6.</td>
                        <td>Agama</td>
                        <td>:</td>
                        <td>{{ $data['agama'] ?? '............................' }}</td>
                    </tr>
                    <tr>
                        <td>7.</td>
                        <td>Pekerjaan</td>
                        <td>:</td>
                        <td>{{ $data['pekerjaan'] ?? '............................' }}</td>
                    </tr>
                    <tr>
                        <td>8.</td>
                        <td>Pendidikan Terakhir</td>
                        <td>:</td>
                        <td>{{ $data['pendidikan_terakhir'] ?? '............................' }}</td>
                    </tr>
                    <tr>
                        <td>9.</td>
                        <td>Bin/Binti</td>
                        <td>:</td>
                        <td>{{ $data['bin'] ?? '............................' }}</td>
                    </tr>
                    <tr>
                        <td>10.</td>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $data['alamat'] ?? '............................' }}</td>
                    </tr>
                    <tr>
                        <td>11.</td>
                        <td>Status Perkawinan</td>
                        <td>:</td>
                        <td>{{ $data['status_kawin'] ?? '............................' }}</td>
                    </tr>
                </table>
            </div>
            
            {{-- Data Ayah --}}
            <div class="form-section">
                <div class="form-section-title">Nama lengkap Ayah :</div>
                <div class="data-pemohon">
                    <table>
                        <tr>
                            <td>12.</td>
                            <td style="width: 380px;">Nama lengkap dan alias</td>
                            <td>:</td>
                            <td>{{ $data['nama_ayah'] ?? '............................' }}</td>
                        </tr>
                        <tr>
                            <td>13.</td>
                            <td>Bin</td>
                            <td>:</td>
                            <td>{{ $data['bin_ayah'] ?? '............................' }}</td>
                        </tr>
                        <tr>
                            <td>14.</td>
                            <td>Nomor Induk Kependudukan (NIK)</td>
                            <td>:</td>
                            <td>{{ $data['nik_ayah'] ?? '............................' }}</td>
                        </tr>
                        <tr>
                            <td>15.</td>
                            <td>Tempat dan tanggal lahir</td>
                            <td>:</td>
                            <td>{{ $data['tempat_lahir_ayah'] ?? '............................' }}, 
                                @php
                                    $tanggalLahirAyah = $data['tanggal_lahir_ayah'] ?? null;
                                    
                                    if (!empty($tanggalLahirAyah)) {
                                        try {
                                            $formattedDate = \Carbon\Carbon::parse($tanggalLahirAyah)->locale('id')->translatedFormat('j F Y');
                                        } catch (\Exception $e) {
                                            $formattedDate = $tanggalLahirAyah;
                                        }
                                    } else {
                                        $formattedDate = '.............................';
                                    }
                                @endphp
                                {{ $formattedDate }}
                            </td>
                        </tr>
                        <tr>
                            <td>16.</td>
                            <td>Kewarganegaraan</td>
                            <td>:</td>
                            <td>{{ $data['kewarganegaraan_ayah'] ?? '............................' }}</td>
                        </tr>
                        <tr>
                            <td>17.</td>
                            <td>Agama</td>
                            <td>:</td>
                            <td>{{ $data['agama_ayah'] ?? '............................' }}</td>
                        </tr>
                        <tr>
                            <td>18.</td>
                            <td>Pekerjaan</td>
                            <td>:</td>
                            <td>{{ $data['pekerjaan_ayah'] ?? '............................' }}</td>
                        </tr>
                        <tr>
                            <td>19.</td>
                            <td>Alamat</td>
                            <td>:</td>
                            <td>{{ $data['alamat_ayah'] ?? $data['alamat'] ?? '............................' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            {{-- Data Ibu --}}
            <div class="form-section">
                <div class="form-section-title">Nama lengkap Ibu :</div>
                <div class="data-pemohon">
                    <table>
                        <tr>
                            <td>20.</td>
                            <td style="width: 380px;">Nama lengkap dan alias</td>
                            <td>:</td>
                            <td>{{ $data['nama_ibu'] ?? '............................' }}</td>
                        </tr>
                        <tr>
                            <td>21.</td>
                            <td>Binti</td>
                            <td>:</td>
                            <td>{{ $data['bin_ibu'] ?? '............................' }}</td>
                        </tr>
                        <tr>
                            <td>22.</td>
                            <td>Nomor Induk Kependudukan (NIK)</td>
                            <td>:</td>
                            <td>{{ $data['nik_ibu'] ?? '............................' }}</td>
                        </tr>
                        <tr>
                            <td>23.</td>
                            <td>Tempat dan tanggal lahir</td>
                            <td>:</td>
                            <td>{{ $data['tempat_lahir_ibu'] ?? '............................' }}, 
                                @php
                                    $tanggalLahirIbu = $data['tanggal_lahir_ibu'] ?? null;
                                    
                                    if (!empty($tanggalLahirIbu)) {
                                        try {
                                            $formattedDate = \Carbon\Carbon::parse($tanggalLahirIbu)->locale('id')->translatedFormat('j F Y');
                                        } catch (\Exception $e) {
                                            $formattedDate = $tanggalLahirIbu;
                                        }
                                    } else {
                                        $formattedDate = '.............................';
                                    }
                                @endphp
                                {{ $formattedDate }}
                            </td>
                        </tr>
                        <tr>
                            <td>24.</td>
                            <td>Kewarganegaraan</td>
                            <td>:</td>
                            <td>{{ $data['kewarganegaraan_ibu'] ?? '............................' }}</td>
                        </tr>
                        <tr>
                            <td>25.</td>
                            <td>Agama</td>
                            <td>:</td>
                            <td>{{ $data['agama_ibu'] ?? '............................' }}</td>
                        </tr>
                        <tr>
                            <td>26.</td>
                            <td>Pekerjaan</td>
                            <td>:</td>
                            <td>{{ $data['pekerjaan_ibu'] ?? '............................' }}</td>
                        </tr>
                        <tr>
                            <td>27.</td>
                            <td>Alamat</td>
                            <td>:</td>
                            <td>{{ $data['alamat_ibu'] ?? $data['alamat'] ?? '............................' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <p>Demikian surat pengantar ini dibuat dengan sesungguhnya untuk dipergunakan sebagaimana mestinya.</p>
        </div>
        
        {{-- Include Tanda Tangan --}}
        @include('partials.tanda-tangan')
    </div>
</body>
</html> 