# Dokumentasi Update Timezone ke Waktu Lokal Indonesia

## Overview
Perubahan konfigurasi timezone dari UTC ke Asia/Jakarta (WIB) dan penambahan helper functions untuk memastikan waktu yang disimpan dan ditampilkan sesuai dengan waktu lokal Indonesia.

## Masalah Sebelumnya
- Laravel menggunakan timezone UTC secara default
- Waktu yang disimpan menggunakan `now()` akan dalam format UTC
- Waktu lokal Indonesia adalah UTC+7 (WIB)
- User melihat waktu yang tidak sesuai dengan waktu lokal

## Solusi yang Diterapkan

### 1. **Mengubah Timezone di config/app.php**
```php
// Sebelumnya
'timezone' => 'UTC',

// Sekarang
'timezone' => 'Asia/Jakarta',
```

### 2. **Menambahkan Helper Functions di app/Helpers.php**

#### `now_local()`
```php
function now_local()
{
    return now()->setTimezone('Asia/Jakarta');
}
```

#### `format_datetime_local()`
```php
function format_datetime_local($datetime, $format = 'd F Y H:i:s')
{
    if (!$datetime) {
        return null;
    }
    
    return $datetime->setTimezone('Asia/Jakarta')->format($format);
}
```

#### `parse_datetime_local()`
```php
function parse_datetime_local($datetimeString)
{
    return \Carbon\Carbon::parse($datetimeString)->setTimezone('Asia/Jakarta');
}
```

### 3. **Mengupdate Controllers**

#### LayananController
```php
// Sebelumnya
$surat->update([
    'signed_pdf_path' => $pdfPath,
    'time_signed_pdf_path' => now()
]);

// Sekarang
$surat->update([
    'signed_pdf_path' => $pdfPath,
    'time_signed_pdf_path' => now_local()
]);
```

#### RegisterSuratController
```php
// Sebelumnya
$surat->update([
    'signed_pdf_path' => $pdfPath,
    'time_signed_pdf_path' => now()
]);

// Sekarang
$surat->update([
    'signed_pdf_path' => $pdfPath,
    'time_signed_pdf_path' => now_local()
]);
```

### 4. **Mengupdate Model RegisterSurat**
```php
public function getTimeSignedPdfPathFormattedAttribute()
{
    if ($this->time_signed_pdf_path) {
        return format_datetime_local($this->time_signed_pdf_path, 'd F Y H:i:s');
    }
    return null;
}
```

### 5. **Mengupdate Response API**
Response sekarang menyertakan `time_signed_pdf_path_formatted`:
```json
{
  "surat": [
    {
      "id": 1,
      "nomor_surat": "001/2025",
      "signed_pdf_url": "http://localhost:8000/storage/surat/signed-pdfs/signed_surat_001_2025_2025-01-03_10-00-00.pdf",
      "signed_pdf_path": "surat/signed-pdfs/signed_surat_001_2025_2025-01-03_10-00-00.pdf",
      "time_signed_pdf_path": "2025-01-03T14:30:22.000000Z",
      "time_signed_pdf_path_formatted": "03 Januari 2025 21:30:22"
    }
  ]
}
```

## Perbedaan Waktu

### Sebelumnya (UTC):
- Server time: 14:30:22 UTC
- Disimpan: 14:30:22 UTC
- Ditampilkan: 14:30:22 (tidak sesuai WIB)

### Sekarang (Asia/Jakarta):
- Server time: 14:30:22 UTC
- Disimpan: 21:30:22 WIB (UTC+7)
- Ditampilkan: 03 Januari 2025 21:30:22 WIB

## Cara Penggunaan Helper Functions

### 1. **Mendapatkan Waktu Sekarang (Lokal)**
```php
$waktuSekarang = now_local();
echo $waktuSekarang; // 2025-01-03 21:30:22
```

### 2. **Format Waktu ke Lokal**
```php
$surat = RegisterSurat::find(1);
$waktuLokal = format_datetime_local($surat->time_signed_pdf_path);
echo $waktuLokal; // 03 Januari 2025 21:30:22
```

### 3. **Parse String Waktu ke Lokal**
```php
$waktuString = "2025-01-03 14:30:22";
$waktuLokal = parse_datetime_local($waktuString);
echo $waktuLokal->format('d F Y H:i:s'); // 03 Januari 2025 21:30:22
```

### 4. **Query berdasarkan Waktu Lokal**
```php
// Surat yang ditandatangani hari ini (WIB)
$todaySigned = RegisterSurat::whereDate('time_signed_pdf_path', now_local()->toDateString())->get();

// Surat yang ditandatangani dalam 7 hari terakhir (WIB)
$recentSigned = RegisterSurat::where('time_signed_pdf_path', '>=', now_local()->subDays(7))->get();
```

## Timezone yang Didukung

### Indonesia:
- `Asia/Jakarta` - WIB (UTC+7)
- `Asia/Makassar` - WITA (UTC+8)
- `Asia/Jayapura` - WIT (UTC+9)

### Untuk Aplikasi Multi-timezone:
```php
// Bisa disesuaikan berdasarkan user preference
function now_user_timezone($timezone = 'Asia/Jakarta')
{
    return now()->setTimezone($timezone);
}
```

## Testing

### Test Cases:
1. **Generate PDF dari Web**
   - Pastikan `time_signed_pdf_path` menggunakan waktu WIB
   - Pastikan format tampilan sesuai dengan waktu lokal

2. **Generate PDF dari Android**
   - Pastikan timestamp konsisten dengan web
   - Pastikan waktu sesuai dengan zona waktu Indonesia

3. **Display di Frontend**
   - Pastikan waktu ditampilkan dalam format WIB
   - Pastikan tidak ada perbedaan 7 jam dengan waktu server

4. **Database Storage**
   - Pastikan waktu tersimpan dalam format yang benar
   - Pastikan query berdasarkan tanggal berfungsi dengan baik

## Migration yang Diperlukan

### Jika ada data lama yang perlu dikonversi:
```php
// Migration untuk mengkonversi data lama
public function up()
{
    // Update semua timestamp yang ada ke timezone lokal
    DB::statement("UPDATE register_surat SET time_signed_pdf_path = CONVERT_TZ(time_signed_pdf_path, 'UTC', 'Asia/Jakarta') WHERE time_signed_pdf_path IS NOT NULL");
}
```

## Best Practices

### 1. **Selalu Gunakan Helper Functions**
```php
// ✅ Benar
$waktu = now_local();

// ❌ Salah
$waktu = now();
```

### 2. **Format untuk Display**
```php
// ✅ Benar
$formatted = format_datetime_local($datetime);

// ❌ Salah
$formatted = $datetime->format('d F Y H:i:s');
```

### 3. **Query dengan Timezone**
```php
// ✅ Benar
$today = RegisterSurat::whereDate('time_signed_pdf_path', now_local()->toDateString())->get();

// ❌ Salah
$today = RegisterSurat::whereDate('time_signed_pdf_path', today())->get();
```

## Troubleshooting

### Jika waktu masih tidak sesuai:
1. **Clear Cache**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Restart Server**
   ```bash
   php artisan serve
   ```

3. **Check Server Timezone**
   ```bash
   date
   # Pastikan server menggunakan timezone yang benar
   ```

4. **Check Database Timezone**
   ```sql
   SELECT @@global.time_zone, @@session.time_zone;
   ```

## Kesimpulan

Dengan perubahan ini:
- Waktu yang disimpan akan sesuai dengan waktu lokal Indonesia (WIB)
- User akan melihat waktu yang akurat dan sesuai dengan zona waktu mereka
- Sistem tetap konsisten antara web dan mobile
- Audit trail akan menampilkan waktu yang mudah dipahami 