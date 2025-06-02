<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $kategori->nama ?? 'Surat Ijin Orangtua Pernikahan' }}</title>
    <style>
        @page {
            margin: 0.5cm 1.5cm 0.5cm 1.5cm;
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
        
        .header-section {
            text-align: right;
            margin-bottom: 10px;
            font-size: 10pt;
        }
        
        .model-section {
            text-align: right;
            margin: 0 0 10px 0;
            font-size: 11pt;
            font-weight: bold;
        }
        
        .title-section {
            text-align: center;
            margin: 20px 0;
            line-height: 1;
        }
        
        .title-section h1 {
            font-size: 12pt;
            font-weight: bold;
            margin: 5px 0;
            text-decoration: underline;
        }
        
        .intro-section {
            margin: 15px 0;
            line-height: 1;
        }
        
        .intro-section p {
            margin: 5px 0;
            text-indent: 0;
        }
        
        .data-section {
            margin: 1px 0;
        }
        
        .data-section h2 {
            font-size: 10pt;
            font-weight: bold;
            margin: 1px 0 1px 0;
        }
        
        .data-section table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-section td {
            padding: 2px 0;
            vertical-align: top;
        }
        
        .data-section td:first-child {
            width: 30px;
            text-align: right;
            padding-right: 5px;
        }
        
        .data-section td:nth-child(2) {
            width: 250px;
        }
        
        .data-section td:nth-child(3) {
            width: 20px;    
            text-align: center;
        }
        
        .statement-section {
            margin: 20px 0;
            text-align: justify;
            line-height: 1;
        }
        
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .signature-section table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .signature-section td {
            vertical-align: top;
            width: 50%;
            padding: 0;
        }
        
        .signature-box {
            text-align: center;
            margin-top: 15px;
        }
        
        .signature-name {
            margin-top: 70px;
            font-weight: bold;
            text-decoration: underline;
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
        {{-- Header and Model --}}
        <div class="header-section">
            <div>Lampiran IX</div>
            <div>Kepdirjen Bimas Islam Nomor 473 Tahun 2020</div>
            <div>Tentang</div>
            <div>Petunjuk Teknis Pelaksanaan Pencatatan Nikah</div>
        </div>
        
        <div class="model-section">
            <div>Model N5</div>
        </div>
        
        {{-- Title --}}
        <div class="title-section">
            <h1>SURAT IZIN ORANG TUA</h1>
        </div>
        
        {{-- Main Content --}}
        <div class="intro-section">
            <p>Yang bertanda tangan di bawah ini :</p>
        </div>
        
        {{-- Data Ayah --}}
        <div class="data-section">
            <h2>A. Ayah :</h2>
            <table>
                <tr>
                    <td>1.</td>
                    <td style="width: 380px;">Nama lengkap dan alias</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['nama_ayah'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>2.</td>
                    <td>Bin</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['bin_ayah'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>3.</td>
                    <td>Nomor Induk Kependudukan</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['nik_ayah'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>4.</td>
                    <td>Tempat dan tanggal lahir</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['tempat_lahir_ayah'] ?? '') }}, {{ $data['tanggal_lahir_ayah'] ?? '' }}</td>
                </tr>
                <tr>
                    <td>5.</td>
                    <td>Kewarganegaraan</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['kewarganegaraan_ayah'] ?? 'Indonesia') }}</td>
                </tr>
                <tr>
                    <td>6.</td>
                    <td>Agama</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['agama_ayah'] ?? 'Islam') }}</td>
                </tr>
                <tr>
                    <td>7.</td>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['pekerjaan_ayah'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>8.</td>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['alamat'] ?? '') }}</td>
                </tr>
            </table>
        </div>
        
        {{-- Data Ibu --}}
        <div class="data-section">
            <h2>B. Ibu :</h2>
            <table>
                <tr>
                    <td>1.</td>
                    <td style="width: 380px;">Nama lengkap dan alias</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['nama_ibu'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>2.</td>
                    <td>Binti</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['bin_ibu'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>3.</td>
                    <td>Nomor Induk Kependudukan</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['nik_ibu'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>4.</td>
                    <td>Tempat dan tanggal lahir</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['tempat_lahir_ibu'] ?? '') }}, {{ $data['tanggal_lahir_ibu'] ?? '' }}</td>
                </tr>
                <tr>
                    <td>5.</td>
                    <td>Kewarganegaraan</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['kewarganegaraan_ibu'] ?? 'Indonesia') }}</td>
                </tr>
                <tr>
                    <td>6.</td>
                    <td>Agama</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['agama_ibu'] ?? 'Islam') }}</td>
                </tr>
                <tr>
                    <td>7.</td>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['pekerjaan_ibu'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>8.</td>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['alamat_ibu'] ?? '') }}</td>
                </tr>
            </table>
        </div>
        
        {{-- Data Anak --}}
        <div class="data-section">
            <p>Adalah ayah dan ibu kandung dari :</p>
            <table>
                <tr>
                    <td>1.</td>
                    <td style="width: 380px;">Nama lengkap dan alias</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['nama'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>2.</td>
                    <td>Bin</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['bin'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>3.</td>
                    <td>Nomor Induk Kependudukan</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['nik'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>4.</td>
                    <td>Tempat dan tanggal lahir</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['tempat_lahir'] ?? '') }}, {{ $data['tanggal_lahir'] ?? '' }}</td>
                </tr>
                <tr>
                    <td>5.</td>
                    <td>Kewarganegaraan</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['kewarganegaraan'] ?? 'WNI') }}</td>
                </tr>
                <tr>
                    <td>6.</td>
                    <td>Agama</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['agama'] ?? 'Islam') }}</td>
                </tr>
                <tr>
                    <td>7.</td>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['pekerjaan'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>8.</td>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['alamat'] ?? '') }}</td>
                </tr>
            </table>
        </div>
        
        {{-- Data Calon --}}
        <div class="data-section">
            <p>Memberikan izin kepada anak kami untuk melakukan pernikahan dengan :</p>
            <table>
                <tr>
                    <td>1.</td>
                    <td style="width: 380px;">Nama lengkap dan alias</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['nama_calon'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>2.</td>
                    <td>Bin</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['bin_calon'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>3.</td>
                    <td>Nomor Induk Kependudukan</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['nik_calon'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>4.</td>
                    <td>Tempat dan tanggal lahir</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['tempat_lahir_calon'] ?? '') }}, {{ $data['tanggal_lahir_calon'] ?? '' }}</td>
                </tr>
                <tr>
                    <td>5.</td>
                    <td>Kewarganegaraan</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['kewarganegaraan_calon'] ?? 'WNI') }}</td>
                </tr>
                <tr>
                    <td>6.</td>
                    <td>Agama</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['agama_calon'] ?? 'Islam') }}</td>
                </tr>
                <tr>
                    <td>7.</td>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['pekerjaan_calon'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>8.</td>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['alamat_calon'] ?? '') }}</td>
                </tr>
            </table>
        </div>
        
        {{-- Statement --}}
        <div class="statement-section">
            <p>Demikian surat izin ini dibuat dengan kesadaran, tanpa ada paksaan dari siapapun dan untuk digunakan seperlunya.</p>
        </div>
        
        {{-- Signature --}}
        <div class="signature-section">
            <table>
                <tr>
                    <td style="text-align: center;">
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
                <tr>
                    <td>
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 50%; text-align: center;">
                                    <div class="signature-box">
                                        <div>Ayah</div>
                                        <div class="signature-name">{{ strtoupper($data['nama_ayah'] ?? '') }}</div>
                                    </div>
                                </td>
                                <td style="width: 50%; text-align: center;">
                                    <div class="signature-box">
                                        <div>Ibu</div>
                                        <div class="signature-name">{{ strtoupper($data['nama_ibu'] ?? '') }}</div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html> 