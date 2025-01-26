<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $surat->nomor_surat }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 2cm;
            font-size: 12pt;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header hr {
            border: 1px solid #000;
            margin: 2px 0;
        }
        .tujuan {
            margin: 20px 0;
        }
        .content {
            margin-bottom: 50px;
            text-align: justify;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
            float: right;
            width: 250px;
        }
        .meta {
            margin-bottom: 30px;
        }
        .meta table {
            width: 100%;
        }
        .meta td {
            padding: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- <img src="{{ public_path('assets/images/logo.png') }}" height="80"> -->
        <h2>PEMERINTAH KABUPATEN SAMPANG</h2>
        <h3>KECAMATAN CAMPLONG</h3>
        <h1>DESA BANYUPELLE</h1>
        <hr>
    </div>

    <div class="tujuan">
        <p>Kepada Yth.<br>{{ $surat->tujuan }}</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td width="120">Nomor</td>
                <td>: {{ $surat->nomor_surat }}</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>: {{ $surat->perihal }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ $surat->tanggal_surat->format('d F Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="content">
        {!! nl2br($surat->isi_surat) !!}
    </div>

    <div class="footer">
        <p>
            Banyupelle, {{ $surat->tanggal_surat->format('d F Y') }}<br>
            {{ $surat->signer->jabatan ?? 'Kepala Desa' }}<br><br><br><br>
            <strong>{{ $surat->signer->name }}</strong>
        </p>
    </div>
</body>
</html> 