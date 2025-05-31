<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Surat Keterangan Ahli Waris</title>
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
            <h3>🎯 Template Surat Keterangan Ahli Waris</h3>
            <p><strong>Status:</strong> ✅ Template berhasil dibuat dengan data lengkap</p>
            <p><strong>Template:</strong> <code>surat-keterangan-ahli-waris.blade.php</code></p>
            <p><strong>Jenis Pelayanan:</strong> Surat Keterangan Ahli Waris (ID: 13)</p>
            <p><strong>Field Tersedia:</strong> nama, nik, tempat_lahir, tanggal_lahir, pekerjaan, alamat (almarhum), nama_ahliwaris, nik_ahliwaris, tempat_lahir_ahliwaris, tanggal_lahir_ahliwaris, alamat_ahliwaris</p>
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
                'pekerjaan' => 'Petani',
                'alamat' => 'Jl. Merdeka No. 45, RT/RW 002/003, Desa Banyupelle',
                
                // Data Ahli Waris
                'nama_ahliwaris' => 'SITI AMINAH',
                'nik_ahliwaris' => '6543210987654321',
                'tempat_lahir_ahliwaris' => 'Pamekasan',
                'tanggal_lahir_ahliwaris' => '1975-08-20',
                'alamat_ahliwaris' => 'Jl. Merdeka No. 45, RT/RW 002/003, Desa Banyupelle',
                
                // Data Surat
                'nomor' => '001/SK-AW/V/2025'
            ];
            $nomor_surat = $data['nomor'];
        @endphp

        @include('templates.surat.surat-keterangan-ahli-waris', compact('data', 'nomor_surat'))
    </div>
</body>
</html> 