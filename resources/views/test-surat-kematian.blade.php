<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Surat Keterangan Kematian</title>
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
            <h3>🎯 Template Surat Keterangan Kematian</h3>
            <p><strong>Status:</strong> ✅ Template berhasil dibuat dengan data lengkap</p>
            <p><strong>Template:</strong> <code>surat-keterangan-kematian.blade.php</code></p>
            <p><strong>Jenis Pelayanan:</strong> Surat Keterangan Kematian (ID: 15)</p>
            <p><strong>Field Tersedia:</strong> nama, nik, tempat_lahir, tanggal_lahir, alamat, tanggal (kematian), jam, tempat_kematian</p>
        </div>

        <hr>

        {{-- Include template surat --}}
        @php
            $data = [
                // Data Almarhum/Yang Meninggal
                'nama' => 'ABDUL RAHMAN',
                'nik' => '1234567890123456',
                'tempat_lahir' => 'Pamekasan',
                'tanggal_lahir' => '1950-03-10',
                'alamat' => 'Jl. Merdeka No. 45, RT/RW 002/003, Desa Banyupelle',
                
                // Data Kematian
                'tanggal' => '2025-01-15',
                'jam' => '14:30',
                'tempat_kematian' => 'Rumah Duka Pamekasan',
                
                // Data Surat
                'nomor' => '001/SK-K/I/2025'
            ];
            $nomor_surat = $data['nomor'];
        @endphp

        @include('templates.surat.surat-keterangan-kematian', compact('data', 'nomor_surat'))
    </div>
</body>
</html> 