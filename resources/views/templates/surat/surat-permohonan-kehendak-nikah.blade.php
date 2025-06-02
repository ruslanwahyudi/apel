<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $kategori->nama ?? 'Permohonan Kehendak Nikah' }}</title>
    <style>
        @page {
            margin: 0.5cm 0.5cm 0.5cm 0.5cm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.2;
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
        
        .lampiran-section {
            text-align: right;
            margin: 5px 0;
            font-size: 11pt;
            line-height: 1.2;
        }
        
        .model-section {
            text-align: right;
            margin: 0 0 10px 0;
            font-size: 11pt;
            font-weight: bold;
        }
        
        .perihal-section {
            width: 100%;
            margin: 20px 0;
            font-size: 11pt;
        }
        
        .perihal-section table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .perihal-section td {
            vertical-align: top;
            padding: 0;
        }
        
        .perihal-section .perihal-left {
            width: 60%;
            text-align: left;
        }
        
        .perihal-section .perihal-right {
            width: 40%;
            text-align: right;
        }
        
        .alamat-section {
            margin: 20px 0;
            font-size: 11pt;
        }
        
        .isi-surat {
            text-align: justify;
            margin: 20px 0;
            line-height: 1.4;
            font-size: 11pt;
        }
        
        .isi-surat p {
            margin: 8px 0;
            text-indent: 30px;
        }
        
        .data-section {
            margin: 15px 0;
            font-size: 11pt;
        }
        
        .data-section table {
            width: 100%;
            border-collapse: collapse;
            margin-left: 30px;
        }
        
        .data-section td {
            padding: 2px 0;
            vertical-align: top;
        }
        
        .data-section td:first-child {
            width: 150px;
        }
        
        .data-section td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .data-section td:nth-child(3) {
            width: auto;
        }
        
        .dokumen-list {
            margin: 15px 0 15px 30px;
        }
        
        .dokumen-list ol {
            margin: 0;
            padding-left: 20px;
        }
        
        .dokumen-list li {
            margin: 3px 0;
            line-height: 1.3;
        }
        
        .penutup-surat {
            margin: 20px 0;
            text-align: justify;
            font-size: 11pt;
        }
        
        .penutup-surat p {
            margin: 8px 0;
            text-indent: 30px;
        }
        
        .catatan-section {
            margin: 20px 0;
            font-size: 10pt;
            font-style: italic;
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
        }
    </style>
</head>
<body>
    <div class="surat-content">
        
        {{-- Lampiran dan Model --}}
        <div class="lampiran-section">
            <div>Lampiran VI</div>
            <div>Kepdirjen Bimas Islam Nomor 473 Tahun 2020</div>
            <div>Tanggal: 24 Agustus 2020</div>
            <div>Tentang: Petunjuk Teknis Pelaksanaan Pencatatan Nikah</div>
        </div>
        <div class="model-section">
            <div>Model N2</div>
        </div>
        
        {{-- Perihal dan Nomor/Tanggal --}}
        <div class="perihal-section">
            <table>
                <tr>
                    <td class="perihal-left">
                        <strong>Perihal :</strong> Permohonan Kehendak Nikah
                    </td>
                    <td class="perihal-right">
                        {{ $kopConfig->desa ?? 'Banyupelle' }}, 
                        @php
                            $tanggalSurat = $data['tanggal'] ?? null;
                            if (!empty($tanggalSurat)) {
                                try {
                                    $formattedTanggal = \Carbon\Carbon::parse($tanggalSurat)->locale('id')->translatedFormat('j F Y');
                                } catch (\Exception $e) {
                                    $formattedTanggal = \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y');
                                }
                            } else {
                                $formattedTanggal = \Carbon\Carbon::now()->locale('id')->translatedFormat('j F Y');
                            }
                        @endphp
                        {{ $formattedTanggal }}
                    </td>
                </tr>
            </table>
        </div>
        
        {{-- Alamat Tujuan --}}
        <div class="alamat-section">
            <div>Kepada Yth.</div>
            <div>Kepala KUA Kecamatan/PPN LN</div>
            <div>di {{ $kopConfig->kecamatan ?? 'Palengaan' }}</div>
        </div>
        
        {{-- Isi Surat --}}
        <div class="isi-surat">
            @php
                use App\Models\KopSuratConfig;
                $kopConfig = KopSuratConfig::getActiveConfig();
            @endphp
            
            <p>Dengan hormat, kami mengajukan permohonan kehendak nikah untuk atas nama:</p>
        </div>
        
        {{-- Data Pemohon --}}
        <div class="data-section">
            <table>
                <tr>
                    <td style="width: 200px;">Calon Suami</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['nama'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>Calon Istri</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['nama_calon'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>Hari/tanggal/jam</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['hari_nikah'] ?? '') }} / {{ strtoupper($data['tanggal_pernikahan'] ?? '') }} / {{ strtoupper($data['jam_nikah'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>Tempat akad nikah</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['tempat'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>Mahar/mas kawin</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['mahar'] ?? '') }}</td>
                </tr>
            </table>
        </div>
        
        <div class="isi-surat">
            <p>Bersama ini kami sampaikan surat-surat yang diperlukan untuk dipaklah sebagai berikut:</p>
        </div>
        
        {{-- Daftar Dokumen --}}
        <div class="dokumen-list">
            <ol>
                <li>Surat Pengantar Nikah dari Desa / Kelurahan</li>
                <li>Persetujuan Calon Mempelai</li>
                <li>Fotokopi KTP Elektronik / Suket</li>
                <li>Fotokopi Akta Kelahiran</li>
                <li>Fotokopi Kartu Keluarga (KK)</li>
                <li>Fotokopi Ijazah Terakhir</li>
                <li>Persetujuan orang tua (bagi yang belum berusia 21 tahun atau belum pernah kawin)</li>
                <li>Fotokopi KTP Elektronik Ayah dan Ibu calon pengantin</li>
                <li>Surat ijin atasan (HD bagi anggota TNI atau anggota bagi anggota Polri Tahun "*)</li>
                <li>Surat keterangan Kematian Suami/Istri (KR) bila Duda/Janda Mati "*)</li>
                <li>Akta Cerai bila Duda/Janda Hidup "*)</li>
                <li>...........................................</li>
                <li>...........................................</li>
                <li>...........................................</li>
            </ol>
        </div>
        
        <div class="penutup-surat">
            <p>Demikian permohonan ini kami sampaikan, kiranya dapat diproses, ditindak dan dicartat sesuai dengan ketentuan peraturan perundang-undangan.</p>
        </div>
        
        {{-- Catatan --}}
        <div class="catatan-section">
            <p>*) Coret bila tidak diperlukan</p>
        </div>
        
        {{-- Tanda Tangan Khusus Format Permohonan --}}
        <div style="margin-top: 40px; margin-bottom: 30px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; text-align: left; vertical-align: top; padding: 0;">
                        <div style="text-align: center; font-size: 11pt;">
                            <div style="margin-bottom: 5px;">Yang menerima,</div>
                            <div style="margin-bottom: 5px;">Kepala KUA/PPN LN,</div>
                            <div style="margin: 80px 0 10px 0;"></div>
                            <div style="margin-bottom: 5px; text-decoration: underline;">...........................................</div>
                            <div>......................</div>
                        </div>
                    </td>
                    <td style="width: 50%; text-align: right; vertical-align: top; padding: 0;">
                        <div style="text-align: center; font-size: 11pt;">
                            <div style="margin-bottom: 5px;">Waalaikum,</div>
                            <div style="margin-bottom: 5px;">Pemohon</div>
                            <div style="margin: 80px 0 10px 0;"></div>
                            <div style="margin-bottom: 5px; text-decoration: underline;">...........................................</div>
                            <div>{{ strtoupper($data['nama'] ?? 'NURUL HUDA') }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html> 