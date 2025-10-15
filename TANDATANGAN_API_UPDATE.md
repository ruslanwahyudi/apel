# Dokumentasi Update Function Tandatangan API

## Overview
Perubahan pada function `tandatangan()` di `LayananController` untuk menambahkan fitur generate dan simpan PDF setelah layanan ditandatangani, agar konsisten dengan `RegisterSuratController.php`.

## Perubahan yang Dilakukan

### 1. **Menambahkan Import DomPDF**
```php
use Barryvdh\DomPDF\Facade\Pdf;
```

### 2. **Modifikasi Function `tandatangan()`**
Menambahkan pemanggilan method untuk generate PDF setelah update status layanan:

```php
if($upd){
    // Generate dan simpan PDF untuk semua surat yang terkait dengan layanan ini
    try {
        $this->generateAndSavePdfsForLayanan($layanan);
    } catch (\Exception $e) {
        \Log::error('Error generating PDFs for layanan tandatangan:', [
            'layanan_id' => $layanan->id,
            'error' => $e->getMessage()
        ]);
        // Lanjutkan eksekusi meski generate PDF gagal
    }
    
    // ... existing notification logic
}
```

### 3. **Menambahkan Method `generateAndSavePdfsForLayanan()`**
Method ini akan:
- Mengambil semua surat yang terkait dengan layanan dari `temp_surat_id`
- Update status setiap surat menjadi ditandatangani (status = 3)
- Generate dan simpan PDF untuk setiap surat
- Update `signed_pdf_path` pada setiap surat

### 4. **Menambahkan Method `generateAndSaveSignedPdf()`**
Copy dari `RegisterSuratController` dengan penyesuaian:
- Menggunakan template blade dari kategori surat jika tersedia
- Menggunakan data DUK untuk surat layanan
- Fallback ke template default jika tidak ada template blade
- Menyimpan PDF ke storage dengan nama file yang konsisten

### 5. **Menambahkan Method `sanitizeFileName()`**
Helper method untuk membersihkan nama file dari karakter yang tidak valid.

## Alur Kerja Baru

### Ketika Layanan Ditandatangani dari Android:

1. **Update Status Layanan**
   - Status layanan diubah menjadi "Selesai"

2. **Generate PDF untuk Semua Surat**
   - Ambil semua surat dari `temp_surat_id`
   - Update status setiap surat menjadi ditandatangani (status = 3)
   - Generate PDF menggunakan template blade atau template default
   - Simpan PDF ke storage
   - Update `signed_pdf_path` pada setiap surat

3. **Kirim Notifikasi**
   - Kirim notifikasi ke user bahwa layanan telah selesai
   - Kirim push notification via FCM

4. **Return Response**
   - Return response dengan data layanan yang sudah di-update

## Konsistensi dengan RegisterSuratController

### Perbedaan Sebelumnya:
- **Web (RegisterSuratController)**: Generate PDF saat surat ditandatangani
- **Android (LayananController)**: Tidak generate PDF, hanya update status

### Setelah Update:
- **Web (RegisterSuratController)**: Generate PDF saat surat ditandatangani
- **Android (LayananController)**: Generate PDF untuk semua surat saat layanan ditandatangani

## Struktur File PDF yang Dihasilkan

### Nama File:
```
signed_surat_{nomor_surat}_{timestamp}.pdf
```

### Contoh:
```
signed_surat_001_2025_20250103143022.pdf
```

### Lokasi Storage:
```
storage/app/public/surat/signed-pdfs/
```

## Logging

### Log yang Ditambahkan:
- `Generating PDFs for layanan`: Saat mulai generate PDF
- `PDF generated and saved successfully for surat`: Saat PDF berhasil disimpan
- `Error generating PDF for individual surat`: Saat ada error pada surat tertentu
- `Error generating PDFs for layanan tandatangan`: Saat ada error umum

## Error Handling

### Error pada Individual Surat:
- Jika ada error pada satu surat, proses akan dilanjutkan ke surat berikutnya
- Error akan di-log tapi tidak menghentikan proses

### Error pada Generate PDF:
- Jika ada error pada generate PDF, proses akan dilanjutkan ke notifikasi
- Error akan di-log tapi tidak menghentikan proses

## Manfaat Perubahan

### 1. **Konsistensi**
- Web dan Android sekarang memiliki behavior yang sama
- PDF di-generate otomatis saat ditandatangani

### 2. **User Experience**
- User Android dapat langsung mengakses PDF yang sudah ditandatangani
- Tidak perlu menunggu admin web untuk generate PDF

### 3. **Efisiensi**
- PDF di-generate sekali saat ditandatangani
- Tidak perlu generate ulang setiap kali diakses

### 4. **Reliability**
- Error handling yang robust
- Logging yang detail untuk debugging

## Testing

### Test Case yang Perlu Diuji:
1. **Layanan dengan satu surat**
   - Pastikan PDF di-generate dan disimpan
   - Pastikan `signed_pdf_path` ter-update

2. **Layanan dengan multiple surat**
   - Pastikan semua surat di-generate
   - Pastikan semua `signed_pdf_path` ter-update

3. **Error handling**
   - Test dengan kategori surat tanpa template blade
   - Test dengan data layanan yang tidak lengkap

4. **File storage**
   - Pastikan file tersimpan di lokasi yang benar
   - Pastikan nama file sesuai format

## Migration yang Diperlukan

Pastikan migration berikut sudah dijalankan:
- `2025_01_03_000001_add_signed_pdf_path_to_register_surat_table.php`

## Dependencies

### Package yang Diperlukan:
- `barryvdh/laravel-dompdf`: Untuk generate PDF
- `google/apiclient`: Untuk FCM notification

### Storage:
- Pastikan storage `public` sudah di-link ke `public/storage`
- Pastikan folder `surat/signed-pdfs/` sudah ada dan writable 