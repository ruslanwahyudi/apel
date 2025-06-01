<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $kategori->nama ?? 'Surat Keterangan Test Multiple' }}</title>
    <style>
        @page {
            margin: 2cm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .header-surat {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .judul-surat {
            text-align: center;
            font-weight: bold;
            font-size: 16pt;
            margin: 30px 0;
            text-decoration: underline;
            text-transform: uppercase;
        }
        
        .nomor-tanggal {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .isi-surat {
            text-align: justify;
            margin: 20px 0;
            line-height: 1.8;
        }
        
        .data-pemohon {
            margin: 20px 0 20px 50px;
        }
        
        .data-pemohon table {
            border-collapse: collapse;
            width: 100%;
        }
        
        .data-pemohon td {
            padding: 5px 0;
            vertical-align: top;
        }
        
        .data-pemohon td:first-child {
            width: 150px;
        }
        
        .data-pemohon td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        
        .ttd-section {
            margin-top: 50px;
            text-align: right;
        }
        
        .nama-bold {
            font-weight: bold;
        }
        
        .footer-info {
            margin-top: 30px;
            text-align: center;
            font-size: 10pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header-surat">
        <h2>PEMERINTAH DESA BANYUPELLE</h2>
        <h3>KECAMATAN XXX KABUPATEN XXX</h3>
        <p>Alamat: Jl. XXX, Telp: XXX</p>
    </div>
    
    <div class="judul-surat">
        {{ $kategori->nama ?? 'SURAT KETERANGAN TEST MULTIPLE' }}
    </div>
    
    <div class="nomor-tanggal">
        <div>Nomor: {{ $data['nomor'] ?? $nomor_surat ?? 'XXX/XXX/XXX/2025' }}</div>
        <div>Tanggal: {{ $data['tanggal'] ?? $generated_at?->format('d F Y') ?? date('d F Y') }}</div>
    </div>
    
    <div class="isi-surat">
        <p>Yang bertanda tangan di bawah ini, Kepala Desa Banyupelle, Kecamatan XXX, Kabupaten XXX, menerangkan dengan sebenarnya bahwa:</p>
        
        <div class="data-pemohon">
            <table>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td class="nama-bold">{{ $data['nama'] ?? $data['nama_pemohon'] ?? '[Nama Pemohon]' }}</td>
                </tr>
                @if(isset($data['nik']))
                <tr>
                    <td>NIK</td>
                    <td>:</td>
                    <td>{{ $data['nik'] }}</td>
                </tr>
                @endif
                @if(isset($data['tempat_lahir']) || isset($data['tanggal_lahir']))
                <tr>
                    <td>Tempat, Tanggal Lahir</td>
                    <td>:</td>
                    <td>{{ $data['tempat_lahir'] ?? '' }}{{ isset($data['tanggal_lahir']) ? ', ' . date('d F Y', strtotime($data['tanggal_lahir'])) : '' }}</td>
                </tr>
                @endif
                @if(isset($data['alamat']) || isset($data['alamat_pemohon']))
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $data['alamat'] ?? $data['alamat_pemohon'] ?? '' }}</td>
                </tr>
                @endif
                @if(isset($data['pekerjaan']))
                <tr>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td>{{ $data['pekerjaan'] }}</td>
                </tr>
                @endif
                @if(isset($data['agama']))
                <tr>
                    <td>Agama</td>
                    <td>:</td>
                    <td>{{ $data['agama'] }}</td>
                </tr>
                @endif
                @if(isset($data['status_perkawinan']))
                <tr>
                    <td>Status Perkawinan</td>
                    <td>:</td>
                    <td>{{ $data['status_perkawinan'] }}</td>
                </tr>
                @endif
                @if(isset($data['kewarganegaraan']))
                <tr>
                    <td>Kewarganegaraan</td>
                    <td>:</td>
                    <td>{{ $data['kewarganegaraan'] }}</td>
                </tr>
                @endif
            </table>
        </div>
        
        <p>
            @if(isset($data['keperluan']))
                Adalah benar warga desa kami dan surat keterangan ini dibuat untuk keperluan <strong>{{ $data['keperluan'] }}</strong>.
            @else
                Adalah benar warga desa kami yang berdomisili di {{ $data['alamat'] ?? $data['alamat_pemohon'] ?? 'alamat tersebut di atas' }}.
            @endif
        </p>
        
        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>
    
    <div class="ttd-section">
        <div style="margin-bottom: 80px;">
            {{ $data['tempat'] ?? 'Banyupelle' }}, {{ $data['tanggal'] ?? $generated_at?->format('d F Y') ?? date('d F Y') }}
        </div>
        <div>
            <strong>Kepala Desa Banyupelle</strong>
        </div>
        <div style="margin-top: 80px;">
            <strong><u>{{ $data['nama_kepala_desa'] ?? 'SYAMSUL SE' }}</u></strong><br>
            NIP. {{ $data['nip_kepala_desa'] ?? '196020520101016' }}
        </div>
    </div>
    
    <div class="footer-info">
        <em>Dokumen ini di-generate pada {{ $generated_at?->format('d/m/Y H:i:s') ?? date('d/m/Y H:i:s') }}</em>
        @if(isset($kategori))
            <br><small>Template: {{ $kategori->nama }} (Multiple Print)</small>
        @endif
    </div>
</body>
</html> 