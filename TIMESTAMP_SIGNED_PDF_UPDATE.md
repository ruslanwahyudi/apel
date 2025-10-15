# Dokumentasi Update Timestamp Signed PDF

## Overview
Penambahan kolom `time_signed_pdf_path` pada tabel `register_surat` untuk mencatat waktu kapan PDF yang sudah ditandatangani di-generate dan disimpan.

## Perubahan Database

### Kolom Baru:
- **`time_signed_pdf_path`**: `TIMESTAMP` - Mencatat waktu kapan PDF yang sudah ditandatangani di-generate dan disimpan

### Migration yang Diperlukan:
```php
// Migration untuk menambahkan kolom time_signed_pdf_path
Schema::table('register_surat', function (Blueprint $table) {
    $table->timestamp('time_signed_pdf_path')->nullable()->after('signed_pdf_path');
});
```

## Perubahan pada Model RegisterSurat

### 1. **Menambahkan ke $fillable**
```php
protected $fillable = [
    // ... existing fields
    'signed_pdf_path',
    'time_signed_pdf_path'
];
```

### 2. **Menambahkan Cast**
```php
protected $casts = [
    'tanggal_surat' => 'datetime',
    'tanggal_diterima' => 'datetime',
    'time_signed_pdf_path' => 'datetime',
];
```

### 3. **Menambahkan Accessor**
```php
public function getTimeSignedPdfPathFormattedAttribute()
{
    if ($this->time_signed_pdf_path) {
        return $this->time_signed_pdf_path->format('d F Y H:i:s');
    }
    return null;
}
```

## Perubahan pada Controllers

### 1. **LayananController - Function tandatangan()**

#### Sebelumnya:
```php
$surat->update(['signed_pdf_path' => $pdfPath]);
```

#### Sekarang:
```php
$surat->update([
    'signed_pdf_path' => $pdfPath,
    'time_signed_pdf_path' => now()
]);
```

### 2. **RegisterSuratController - Function sign()**

#### Sebelumnya:
```php
$surat->update(['signed_pdf_path' => $pdfPath]);
```

#### Sekarang:
```php
$surat->update([
    'signed_pdf_path' => $pdfPath,
    'time_signed_pdf_path' => now()
]);
```

## Struktur Data Baru

### Response dari API:
```json
{
  "success": true,
  "message": "Detail Layanan",
  "data": {
    "surat": [
      {
        "id": 1,
        "nomor_surat": "001/2025",
        "jenis_surat": "Surat Pengantar KTP",
        "perihal": "Pengurusan KTP",
        "tanggal_surat": "2025-01-03T10:00:00.000000Z",
        "status": 3,
        "signed_pdf_url": "http://localhost:8000/storage/surat/signed-pdfs/signed_surat_001_2025_2025-01-03_10-00-00.pdf",
        "signed_pdf_path": "surat/signed-pdfs/signed_surat_001_2025_2025-01-03_10-00-00.pdf",
        "time_signed_pdf_path": "2025-01-03T14:30:22.000000Z",
        "time_signed_pdf_path_formatted": "03 January 2025 14:30:22"
      }
    ]
  }
}
```

## Manfaat Perubahan

### 1. **Audit Trail**
- Mencatat waktu kapan PDF di-generate
- Memudahkan tracking dan debugging

### 2. **User Experience**
- User dapat melihat kapan surat ditandatangani
- Informasi waktu yang akurat untuk referensi

### 3. **Data Analytics**
- Dapat menganalisis waktu rata-rata penandatanganan
- Monitoring performa sistem

### 4. **Compliance**
- Mencatat timestamp untuk kepatuhan regulasi
- Bukti audit yang lebih lengkap

## Logging yang Diperbarui

### LayananController:
```php
\Log::info('PDF generated and saved successfully for surat', [
    'layanan_id' => $layanan->id,
    'surat_id' => $surat->id,
    'pdf_path' => $pdfPath,
    'time_signed_pdf_path' => now()
]);
```

### RegisterSuratController:
```php
\Log::info('PDF signed surat saved successfully', [
    'register_surat_id' => $surat->id,
    'pdf_path' => $pdfPath,
    'time_signed_pdf_path' => now()
]);
```

## Cara Penggunaan

### 1. **Mengakses Timestamp**
```php
$surat = RegisterSurat::find(1);

// Raw timestamp
$timestamp = $surat->time_signed_pdf_path;

// Formatted timestamp
$formatted = $surat->time_signed_pdf_path_formatted;

// Check if PDF has been signed
if ($surat->time_signed_pdf_path) {
    echo "PDF ditandatangani pada: " . $surat->time_signed_pdf_path_formatted;
}
```

### 2. **Query berdasarkan Timestamp**
```php
// Surat yang ditandatangani hari ini
$todaySigned = RegisterSurat::whereDate('time_signed_pdf_path', today())->get();

// Surat yang ditandatangani dalam 7 hari terakhir
$recentSigned = RegisterSurat::where('time_signed_pdf_path', '>=', now()->subDays(7))->get();
```

### 3. **Frontend Display**
```javascript
// Tampilkan waktu penandatanganan
if (surat.time_signed_pdf_path) {
    const signedTime = new Date(surat.time_signed_pdf_path);
    console.log(`Surat ditandatangani pada: ${signedTime.toLocaleString('id-ID')}`);
}
```

## Migration Script

### Migration File:
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('register_surat', function (Blueprint $table) {
            $table->timestamp('time_signed_pdf_path')->nullable()->after('signed_pdf_path');
        });
    }

    public function down(): void
    {
        Schema::table('register_surat', function (Blueprint $table) {
            $table->dropColumn('time_signed_pdf_path');
        });
    }
};
```

## Testing

### Test Cases:
1. **Generate PDF dari Web**
   - Pastikan `time_signed_pdf_path` terisi saat PDF di-generate
   - Pastikan timestamp sesuai dengan waktu server

2. **Generate PDF dari Android**
   - Pastikan `time_signed_pdf_path` terisi saat layanan ditandatangani
   - Pastikan timestamp konsisten dengan web

3. **Multiple Surat**
   - Pastikan setiap surat memiliki timestamp yang berbeda
   - Pastikan timestamp sesuai urutan penandatanganan

4. **Error Handling**
   - Test dengan generate PDF yang gagal
   - Pastikan timestamp tidak terisi jika PDF gagal di-generate

## Backward Compatibility

- Kolom `time_signed_pdf_path` bersifat nullable
- Surat yang sudah ada sebelum update tidak akan memiliki timestamp
- Sistem tetap berfungsi normal untuk data lama 