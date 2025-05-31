<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Surat Keterangan Kehilangan</title>
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
            <h3>🎯 Template Surat Keterangan Kehilangan</h3>
            <p><strong>Status:</strong> ✅ Template berhasil dibuat dengan data lengkap</p>
            <p><strong>Template:</strong> <code>surat-keterangan-kehilangan.blade.php</code></p>
            <p><strong>Jenis Pelayanan:</strong> Surat Keterangan Kehilangan (ID: 4)</p>
            <p><strong>Field Tersedia:</strong> nama, tempat_lahir, tanggal_lahir, alamat, nama_kehilangan, rute_dari, rute_sampai</p>
        </div>

        <hr>

        {{-- Include template surat --}}
        @php
            $data = [
                // Data Pemohon
                'nama' => 'AHMAD FARID',
                'tempat_lahir' => 'Pamekasan',
                'tanggal_lahir' => '1985-03-12',
                'alamat' => 'Jl. Merdeka No. 45, RT/RW 003/001, Desa Banyupelle',
                
                // Data Kehilangan
                'nama_kehilangan' => 'KTP dan BPJS Kesehatan',
                'rute_dari' => 'Pamekasan',
                'rute_sampai' => 'Surabaya',
                
                // Data Surat
                'nomor' => '001/SK-KHL/I/2025'
            ];
            $nomor_surat = $data['nomor'];
        @endphp

        @include('templates.surat.surat-keterangan-kehilangan', compact('data', 'nomor_surat'))
    </div>
</body>
</html> 