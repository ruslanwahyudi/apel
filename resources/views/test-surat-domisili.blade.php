<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Surat Keterangan Domisili</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .preview-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="info">
            <h3>🎯 Template Surat Keterangan Domisili</h3>
            <p><strong>Status:</strong> ✅ Template berhasil dibuat dengan data lengkap</p>
            <p><strong>Template:</strong> <code>surat-keterangan-domisili.blade.php</code></p>
            <p><strong>Jenis Pelayanan:</strong> Surat Keterangan Domisili (ID: 3)</p>
            <p><strong>Field Tersedia:</strong> nama, tempat_lahir, tanggal_lahir, jenis_kelamin (select), agama (select), pekerjaan, alamat, keperluan</p>
        </div>

        <hr>

        {{-- Include template surat --}}
        @php
            $data = [
                'nama' => 'JOHN DOE EXAMPLE',
                'tempat_lahir' => 'Pamekasan',
                'tanggal_lahir' => '1990-05-15',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Islam',
                'pekerjaan' => 'Wiraswasta',
                'alamat' => 'Jl. Contoh No. 123, RT/RW 001/002, Desa Banyupelle',
                'keperluan' => 'Syarat Administrasi Pekerjaan',
                'nomor' => '001/SK-DOM/V/2025',
                'tanggal' => '2025-05-30'
            ];
            $nomor_surat = $data['nomor'];
        @endphp

        @include('templates.surat.surat-keterangan-domisili', compact('data', 'nomor_surat'))
    </div>
</body>
</html> 