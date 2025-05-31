<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Rekomendasi Pembelian BBM</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 2cm 1.5cm;
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
            margin: 10px 0 10px 0;
            text-decoration: underline;
            /* text-transform: uppercase; */
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
        
        .isi-surat ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        
        .isi-surat ol li {
            margin: 8px 0;
            line-height: 1.6;
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
            width: 200px;
        }
        
        .data-pemohon td:nth-child(2) {
            width: 200px;
            text-align: center;
        }
        
        .menerangkan-section {
            margin: 40px 0;
        }
        
        .menerangkan-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .menerangkan-content {
            margin-left: 30px;
        }
        
        .keperluan-section {
            margin: 20px 0;
        }
        
        .table-konsumen {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .table-konsumen th, 
        .table-konsumen td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }
        
        .table-konsumen th {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        
        .table-konsumen .text-left {
            text-align: left;
        }
        
        .table-konsumen .number-column {
            width: 40px;
        }
        
        .table-konsumen .jenis-column {
            width: 200px;
        }
        
        .table-konsumen .alat-column {
            width: 120px;
        }
        
        .table-konsumen .fungsi-column {
            width: 150px;
        }
        
        .table-konsumen .kebutuhan-column {
            width: 100px;
        }
        
        .table-konsumen .jam-column {
            width: 100px;
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
        }
    </style>
</head>
<body>
    <div class="surat-content">
        {{-- Include Kop Surat --}}
        @include('partials.kop-surat')
        
        {{-- Judul Surat --}}
        <div class="judul-surat">
            SURAT REKOMENDASI PEMBELIAN BBM JENIS KHUSUS/PENUGASAN PERTAMINA
        </div>
        
        {{-- Nomor Surat --}}
        <div class="nomor-tanggal">
            <div>Nomor: {{ $data['nomor'] ?? $nomor_surat ?? '001/BBM/XII/2024' }}</div>
        </div>
        
        {{-- Isi Surat --}}
        <div class="isi-surat">
            @php
                use App\Models\KopSuratConfig;
                $kopConfig = KopSuratConfig::getActiveConfig();
            @endphp
            
            <p>Dasar Hukum:</p>
            <ol>
                <li>Undang-undang Nomor 22 Tahun 2001 tentang Minyak dan Gas Bumi sebagaimana telah diubah dengan Undang-undang Nomor 6 Tahun 2023 tentang Penetapan Peraturan Pemerintah Pengganti Undang-undang Nomor 2 Tahun 2022 tentang Cipta Kerja menjadi Undang-undang.</li>
                <li>Undang-undang Nomor 23 Tahun 2014 tentang Pemerintah Daerah sebagaimana telah beberapa kali diubah terakhir dengan Undang-undang Nomor 1 Tahun 2022 tentang Hubungan Keuangan antara Pemerintah Pusat dan Pemerintahan Daerah.</li>
                <li>Peraturan Menteri Energi dan Sumber Daya Mineral Nomor 191 Tahun 2014 tentang Penyediaan, Pendistribusian dan Harga Jual Eceran Bahan Bakar Minyak sebagaimana telah beberapa kali diubah terakhir dengan Peraturan Menteri Energi dan Sumber Daya Mineral Nomor 51 Tahun 2020 tentang Perubahan Kelima atas Peraturan Menteri Energi dan Sumber Daya Mineral Nomor 191 Tahun 2014 tentang Penyediaan, Pendistribusian dan Harga Jual Eceran Bahan Bakar Minyak.</li>
                <li>Peraturan Menteri Energi dan Sumber Daya Mineral Nomor 31 Tahun 2014 tentang Penugasan PT. Pertamina (Persero) untuk Menyediakan dan Mendistribusikan Bahan Bakar Minyak.</li>
                <li>Aplikasi penggunaan surat rekomendasi ini tidak ada dalam verdalam kepentingan penerbitan (institusi), maka observasi Alokasi Volume Bahan Bakar Minyak dengan satu sebentar.</li>
            </ol>
            
            <div class="menerangkan-section">
                <div class="menerangkan-title">MEREKOMENDASIKAN:</div>
                <div class="menerangkan-content">
                    <table>
                        <tr>
                            <td style="width: 280px;">Nama</td>
                            <td>:</td>
                            <td><strong>{{ $data['nama'] ?? '_______________' }}</strong></td>
                        </tr>
                        <tr>
                            <td>NIK</td>
                            <td>:</td>
                            <td>{{ $data['nik'] ?? '_______________' }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal Lahir</td>
                            <td>:</td>
                            <td>
                                @php
                                    $tanggalLahir = $data['tanggal_lahir'] ?? null;
                                    
                                    if (!empty($tanggalLahir)) {
                                        try {
                                            $formattedDate = \Carbon\Carbon::parse($tanggalLahir)->format('d F Y');
                                        } catch (\Exception $e) {
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
                            <td>{{ $data['Jenis Kelamin'] ?? '_______________' }}</td>
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
                    </table>
                </div>
            </div>
            
            <div class="keperluan-section">
                <p><strong>Adalah Konsumen/Pengguna:</strong> {{ $data['konsumen_pengguna'] ?? '_______________' }}</p>
                
                <p>Berdasarkan data diatas maka dapat direkomendasikan pembelian BBM sesuai konsumen pengguna:</p>
                
                <table class="table-konsumen">
                    <thead>
                        <tr>
                            <th class="number-column">No</th>
                            <th class="jenis-column">Jenis</th>
                            <th class="alat-column">Alat/Jumlah</th>
                            <th class="fungsi-column">Fungsi</th>
                            <th class="kebutuhan-column">Kebutuhan Perhari</th>
                            <th class="kebutuhan-column">Kebutuhan Perminggu</th>
                            <th class="jam-column">Jam Operasi Perhari</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td class="text-left">{{ $data['Jenis'] ?? '_______________' }}</td>
                            <td>{{ $data['alat_jumlah'] ?? '_______________' }}</td>
                            <td class="text-left">{{ $data['fungsi'] ?? '_______________' }}</td>
                            <td>{{ $data['kebutuhan_perhari'] ?? '_______________' }}</td>
                            <td>{{ $data['kebutuhan_perminggu'] ?? '_______________' }}</td>
                            <td>{{ $data['jam_operasi'] ?? '_______________' }}</td>
                        </tr>
                    </tbody>
                </table>
                
                <p style="margin-top: 15px;"><strong>Jumlah: {{ $data['kebutuhan_perhari'] ?? '_______________' }} Liter</strong></p>
            </div>
            
            <p>Demikian surat rekomendasi ini dibuat dengan sebenarnya dan dapat dipergunakan sebagaimana mestinya.</p>
        </div>
        
        {{-- Tanda Tangan Kepala Desa --}}
        @include('partials.tanda-tangan', [
            'position' => 'right',
            'width' => '500px',
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