<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Pengurusan KTP</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 40px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 18pt;
            font-weight: bold;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16pt;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            font-size: 10pt;
        }
        .content {
            margin: 30px 0;
        }
        .content h3 {
            text-align: center;
            text-decoration: underline;
            margin-bottom: 30px;
            font-size: 14pt;
        }
        .nomor-surat {
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }
        .isi-surat {
            text-align: justify;
            margin-bottom: 30px;
        }
        .data-pemohon {
            margin: 20px 0;
            padding-left: 30px;
        }
        .data-pemohon table {
            border-collapse: collapse;
        }
        .data-pemohon td {
            padding: 3px 15px 3px 0;
            vertical-align: top;
        }
        .ttd {
            margin-top: 50px;
            text-align: right;
            margin-right: 100px;
        }
        .ttd .tempat-tanggal {
            margin-bottom: 80px;
        }
        .ttd .nama-pejabat {
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PEMERINTAH DESA BANYUPELLE</h1>
        <h2>KECAMATAN KASEMBON</h2>
        <h2>KABUPATEN MALANG</h2>
        <p>Alamat: Jl. Raya Banyupelle No. 1, Desa Banyupelle, Kasembon, Malang</p>
        <p>Kode Pos: 65325 | Telp: (0341) 123456</p>
    </div>

    <div class="content">
        <div class="nomor-surat">
            Nomor: {{ $nomor_surat ?? 'XXX/XXX/XXX' }}
        </div>

        <h3>SURAT KETERANGAN PENGURUSAN KTP</h3>

        <div class="isi-surat">
            <p>Yang bertanda tangan di bawah ini, Kepala Desa Banyupelle, Kecamatan Kasembon, Kabupaten Malang, dengan ini menerangkan bahwa:</p>

            <div class="data-pemohon">
                <table>
                    <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td>{{ $data['Nama Lengkap'] ?? $data['nama_pemohon'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ $data['NIK'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat/Tgl Lahir</td>
                        <td>:</td>
                        <td>{{ $data['Tempat Lahir'] ?? '-' }}, {{ $data['Tanggal Lahir'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $data['Alamat Lengkap'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Nomor Telepon</td>
                        <td>:</td>
                        <td>{{ $data['Nomor Telepon'] ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <p>Adalah benar-benar penduduk Desa Banyupelle dan bermaksud mengurus KTP (Kartu Tanda Penduduk). Surat keterangan ini dibuat untuk keperluan pengurusan KTP di Dinas Kependudukan dan Pencatatan Sipil Kabupaten Malang.</p>

            @if(isset($data['catatan']) && $data['catatan'])
            <p><strong>Catatan:</strong> {{ $data['catatan'] }}</p>
            @endif

            <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>
        </div>

        <div class="ttd">
            <div class="tempat-tanggal">
                Banyupelle, {{ $generated_at ? $generated_at->format('d F Y') : date('d F Y') }}
            </div>
            <div>
                <p>Kepala Desa Banyupelle</p>
                <br><br><br>
                <div class="nama-pejabat">
                    (Nama Kepala Desa)
                </div>
            </div>
        </div>
    </div>
</body>
</html> 