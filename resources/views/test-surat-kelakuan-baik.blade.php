<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Surat Keterangan Kelakuan Baik</title>
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
            <h3>🎯 Template Surat Keterangan Kelakuan Baik</h3>
            <p><strong>Status:</strong> ✅ Template berhasil dibuat dengan data lengkap</p>
            <p><strong>Template:</strong> <code>surat-keterangan-kelakuan-baik.blade.php</code></p>
            <p><strong>Jenis Pelayanan:</strong> Surat Keterangan Kelakuan Baik (ID: 11)</p>
            <p><strong>Field Tersedia:</strong> nama, nik, tempat_lahir, tanggal_lahir, pekerjaan, alamat</p>
        </div>

        <hr>

        {{-- Include template surat --}}
        @php
            $data = [
                // Data Pemohon
                'nama' => 'AHMAD FAUZI',
                'nik' => '1234567890123456',
                'tempat_lahir' => 'Pamekasan',
                'tanggal_lahir' => '1990-05-15',
                'pekerjaan' => 'Wiraswasta',
                'alamat' => 'Jl. Raya No. 123, RT/RW 005/002, Desa Banyupelle',
                
                // Data Surat
                'nomor' => '001/SK-KB/I/2025'
            ];
            $nomor_surat = $data['nomor'];
        @endphp

        @include('templates.surat.surat-keterangan-kelakuan-baik', compact('data', 'nomor_surat'))
    </div>
</body>
</html> 