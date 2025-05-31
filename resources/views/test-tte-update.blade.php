<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test TTE dan Footer Elektronik</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .preview-container {
            max-width: 900px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .info {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        .update-list {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .update-list ul {
            margin: 0;
            padding-left: 20px;
        }
        .template-preview {
            border: 2px solid #007bff;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="info">
            <h3>✅ Update TTE dan Footer Elektronik Selesai</h3>
            <p><strong>Status:</strong> Semua template surat telah diupdate dengan fitur TTE dan footer elektronik</p>
        </div>

        <div class="update-list">
            <h4>🔧 Perubahan yang Telah Dilakukan:</h4>
            <ul>
                <li><strong>Tanda Tangan Elektronik (TTE):</strong>
                    <ul>
                        <li>Gambar TTE akan muncul di area tanda tangan</li>
                        <li>File gambar: <code>public/assets/images/tte_kades.png</code></li>
                        <li>Ukuran maksimal: 120px x 80px dengan opacity 0.8</li>
                        <li>Posisi: Di atas nama kepala desa</li>
                    </ul>
                </li>
                <li><strong>Footer Elektronik:</strong>
                    <ul>
                        <li>Muncul di bagian bawah setiap halaman PDF</li>
                        <li>Berisi informasi sertifikat elektronik BSrE</li>
                        <li>Font size: 8pt dengan border atas</li>
                    </ul>
                </li>
                <li><strong>Template yang Diupdate:</strong>
                    <ul>
                        <li>surat-keterangan-kehilangan.blade.php</li>
                        <li>surat-keterangan-kelakuan-baik.blade.php</li>
                        <li>surat-keterangan-kematian.blade.php</li>
                        <li>surat-keterangan-ahli-waris.blade.php</li>
                        <li>surat-keterangan-domisili.blade.php</li>
                    </ul>
                </li>
                <li><strong>CSS Adjustment:</strong>
                    <ul>
                        <li>Margin bottom page: 3cm (untuk ruang footer)</li>
                        <li>Padding bottom body: 60px</li>
                        <li>Footer dengan position: fixed</li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="template-preview">
            <h4>📄 Preview Template dengan Update TTE</h4>
            <p><strong>Template:</strong> Surat Keterangan Kehilangan</p>
            
            {{-- Include template surat untuk preview --}}
            @php
                $data = [
                    'nama' => 'CONTOH PEMOHON',
                    'tempat_lahir' => 'Pamekasan',
                    'tanggal_lahir' => '1985-03-12',
                    'alamat' => 'Jl. Contoh No. 123, RT/RW 001/002, Desa Banyupelle',
                    'nama_kehilangan' => 'KTP dan SIM A',
                    'rute_dari' => 'Pamekasan',
                    'rute_sampai' => 'Surabaya',
                    'nomor' => '001/SK-KHL/I/2025'
                ];
                $nomor_surat = $data['nomor'];
            @endphp

            <div style="border: 1px solid #ccc; padding: 20px; background: white; margin-top: 15px;">
                @include('templates.surat.surat-keterangan-kehilangan', compact('data', 'nomor_surat'))
            </div>
        </div>

        <div class="info" style="margin-top: 30px;">
            <h4>📝 Catatan Penting:</h4>
            <ul>
                <li><strong>File TTE:</strong> Letakkan file gambar TTE dengan nama <code>tte_kades.png</code> di direktori <code>public/assets/images/</code></li>
                <li><strong>Format Rekomendasi:</strong> PNG dengan background transparan, resolusi 300 DPI</li>
                <li><strong>Fallback:</strong> Jika file TTE tidak ada, tanda tangan tetap muncul tanpa gambar</li>
                <li><strong>PDF Generation:</strong> Footer elektronik akan muncul otomatis di semua halaman PDF</li>
                <li><strong>Consistent Branding:</strong> Semua template menggunakan komponen tanda tangan yang sama</li>
            </ul>
        </div>
    </div>
</body>
</html> 