<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $kategori->nama ?? 'Surat Pernyataan Status' }}</title>
    <style>
        @page {
            margin: 2cm 2cm 2cm 2cm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.3;
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
        
        .title-section {
            text-align: center;
            margin: 0 0 20px 0;
            line-height: 1.2;
        }
        
        .title-section h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 5px 0;
            text-decoration: underline;
        }
        
        .intro-section {
            margin: 15px 0;
            line-height: 1.5;
        }
        
        .intro-section p {
            margin: 5px 0;
            text-indent: 0;
        }
        
        .data-section {
            margin: 15px 0;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        
        .data-table td:nth-child(1) {
            width: 200px;
        }
        
        .data-table td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .statement-section {
            margin: 20px 0;
            text-align: justify;
            line-height: 1.5;
        }
        
        .signature-section {
            margin-top: 40px;
            text-align: right;
        }
        
        .signature-box {
            display: inline-block;
            text-align: center;
            width: 250px;
        }
        
        .signature-name {
            margin-top: 80px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .saksi-section {
            margin-top: 50px;
        }
        
        .saksi-section p {
            margin: 5px 0;
        }
        
        .saksi-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .saksi-table td {
            vertical-align: top;
            padding: 5px 0;
        }
        
        .saksi-table td:nth-child(1) {
            width: 30px;
            text-align: right;
            padding-right: 5px;
        }
        
        .saksi-table td:nth-child(2) {
            width: 45%;
        }
        
        .saksi-table td:nth-child(3) {
            width: 5%;
            text-align: center;
        }
        
        .saksi-table td:nth-child(4) {
            width: 45%;
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
        {{-- Title --}}
        <div class="title-section">
            <h1>SURAT PERNYATAAN STATUS</h1>
        </div>
        
        {{-- Main Content --}}
        <div class="intro-section">
            <p>Yang bertanda tangan di bawah ini :</p>
        </div>
        
        {{-- Data Pemohon --}}
        <div class="data-section">
            <table class="data-table">
                <tr>
                    <td style="width: 380px;">Nama lengkap dan alias</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['nama'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>Nomor Induk Kependudukan</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['nik'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['jenis_kelamin'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>Tempat dan tanggal lahir</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['tempat_lahir'] ?? '') }}, {{ $data['tanggal_lahir'] ?? '' }}</td>
                </tr>
                <tr>
                    <td>Kewarganegaraan</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['kewarganegaraan'] ?? 'WNI') }}</td>
                </tr>
                <tr>
                    <td>Agama</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['agama'] ?? 'Islam') }}</td>
                </tr>
                <tr>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['pekerjaan'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>Pendidikan terakhir</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['pendidikan_terakhir'] ?? '') }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ strtoupper($data['alamat'] ?? '') }}</td>
                </tr>
            </table>
        </div>
        
        {{-- Statement --}}
        <div class="statement-section">
            <p>Dengan ini menyatakan bahwa, Saya betul-betul pada saat ini berstatus Jejaka dan surat pernyataan ini dibuat guna persyaratan Pernikahan.</p>
            <p>Demikianlah surat pernyataan ini saya buat dengan sebenarnya, dalam keadaan sehat jasmani dan rohani tanpa ada paksaan dari pihak manapun. Apabila di kemudian hari menyalahi surat pernyataan ini, saya bersedia disuntut sesuai Perundang-undangan/Hukum yang berlaku dan tidak akan melibatkan aparat setempat ( Resiko Sendiri).</p>
        </div>
        
        {{-- Signature --}}
        <div class="signature-section">
            <div class="signature-box">
                <div>Yang membuat pernyataan</div>
                <div style="margin-top: 5px;">Materai 10.000</div>
                <div class="signature-name">{{ strtoupper($data['nama'] ?? '') }}</div>
            </div>
        </div>
        
        {{-- Saksi Section --}}
        <div class="saksi-section">
            <p>Saksi-saksi :</p>
            <table class="saksi-table">
                <tr>
                    <td>1.</td>
                    <td>...............................................</td>
                    <td>(</td>
                    <td>...............................................</td>
                    <td>)</td>
                </tr>
                <tr>
                    <td>2.</td>
                    <td>...............................................</td>
                    <td>(</td>
                    <td>...............................................</td>
                    <td>)</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html> 