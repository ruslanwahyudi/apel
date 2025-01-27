<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $surat->nomor_surat }} - {{ $surat->perihal }}</title>
    <style>
        @page {
            margin: 2.5cm;
        }
        
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 2cm;
        }
        
        .header img {
            max-width: 100px;
            height: auto;
            margin-bottom: 1cm;
        }
        
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        
        .header p {
            margin: 0;
        }
        
        .letter-info {
            margin-bottom: 1cm;
        }
        
        .letter-info p {
            margin: 0;
        }
        
        .letter-content {
            text-align: justify;
            margin-bottom: 2cm;
        }
        
        .signature {
            float: right;
            width: 6cm;
            text-align: center;
        }
        
        .signature p {
            margin: 0;
        }
        
        .signature .name {
            margin-top: 2cm;
            font-weight: bold;
            text-decoration: underline;
        }
        
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo">
        <h1>Pemerintah Desa Banyupelle</h1>
        <p>Kecamatan Palengaan, Kabupaten Pamekasan</p>
        <p>Jl. Raya Palengaan No. 123, Banyupelle, Pamekasan</p>
        <p>Telepon: (0324) 123456, Email: desa@banyupelle.desa.id</p>
        <hr style="border-top: 3px double #000; margin: 0.5cm 0;">
    </div>

    <div class="letter-info">
        <table>
            <tr>
                <td style="width: 120px;">Nomor</td>
                <td>: {{ $surat->nomor_surat }}</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>: {{ $surat->perihal }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ $surat->tanggal_surat->isoFormat('D MMMM Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="letter-content">
        {!! $surat->isi_surat !!}
    </div>

    <div class="signature">
        <p>Banyupelle, {{ $surat->tanggal_surat->isoFormat('D MMMM Y') }}</p>
        <p>Kepala Desa Banyupelle</p>
        <p class="name">NAMA KEPALA DESA</p>
    </div>

    <div class="no-print" style="position: fixed; bottom: 20px; right: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Cetak
        </button>
    </div>
</body>
</html> 