# Fitur Upload/Download Dokumen yang Sudah Ditandatangani

## Deskripsi
Fitur ini memungkinkan admin untuk mengupload dokumen layanan yang sudah ditandatangani oleh Kepala Desa, sehingga user dapat mendownload dokumen tersebut melalui aplikasi Android.

## Status Implementasi: ✅ SELESAI

## Komponen yang Diimplementasi

### 1. Database Migration
- **File**: `database/migrations/2025_05_29_160216_add_signed_document_to_pelayanan_table.php`
- **Kolom baru**: `signed_document_path` (nullable string) di tabel `duk_pelayanan`

### 2. Model Update
- **File**: `app/Models/Layanan/Pelayanan.php`
- **Perubahan**: Menambahkan `signed_document_path` ke dalam `$fillable` array

### 3. Controller Methods
- **File**: `app/Http/Controllers/layanan/DaftarLayananController.php`
- **Methods baru**:
  - `uploadSignedDocument($request, $id)` - Upload dokumen yang sudah ditandatangani
  - `downloadSignedDocument($id)` - Download dokumen yang sudah ditandatangani

### 4. API Controller Methods
- **File**: `app/Http/Controllers/Api/LayananController.php`
- **Method baru**: `downloadSignedDocument($id)` - API untuk Android download dokumen

### 5. Routes
- **Web Routes** (`routes/web.php`):
  - `POST layanan/daftar-layanan/upload-signed-document/{id}`
  - `GET layanan/daftar-layanan/download-signed-document/{id}`
- **API Routes** (`routes/api.php`):
  - `GET layanan/download-signed-document/{id}`

### 6. Frontend Interface
- **File**: `resources/views/layanan/daftar/index.blade.php`
- **Komponen baru**:
  - Modal upload dokumen (`uploadSignedDocModal`) dengan mode dinamis
  - Tombol upload (hijau) untuk layanan status = 8 tanpa dokumen
  - Tombol download (biru) untuk layanan status = 8 dengan dokumen
  - Tombol ganti dokumen (kuning) untuk layanan status = 8 dengan dokumen
  - Konfirmasi sweet alert sebelum mengganti dokumen
  - Modal dinamis dengan warna berbeda (hijau untuk upload, kuning untuk ganti)
  - Pesan sukses yang berbeda untuk upload vs ganti dokumen
  - JavaScript handlers untuk upload/download/ganti dokumen

### 7. Storage Folder
- **Path**: `storage/app/public/layanan/signed-documents/`
- **Fungsi**: Menyimpan file dokumen yang sudah ditandatangani

## Cara Menggunakan

### Untuk Admin (Web Interface)
1. Login sebagai admin di halaman web
2. Buka menu "Layanan" → "Daftar Layanan"
3. Cari layanan dengan status "Selesai" (status_layanan = 8)
4. **Upload Dokumen Baru** (untuk layanan yang belum ada dokumen):
   - Klik tombol "Upload Dokumen" (hijau)
   - Pilih file dokumen (PDF, DOC, DOCX, JPG, JPEG, PNG, max 10MB)
   - Klik "Upload Dokumen"
5. **Ganti Dokumen yang Sudah Ada** (untuk layanan yang sudah ada dokumen):
   - Akan muncul tombol "Download" (biru) dan "Ganti Dokumen" (kuning)
   - Klik tombol "Ganti Dokumen"
   - Konfirmasi penggantian dokumen
   - Pilih file dokumen baru
   - Klik "Ganti Dokumen"
   - File lama akan otomatis dihapus dan diganti dengan file baru
6. Admin dapat download dokumen kapan saja dengan tombol "Download"

### Untuk User Android (API)
1. User dapat download dokumen melalui API endpoint:
   ```
   GET /api/layanan/download-signed-document/{id}
   ```
2. Response berupa JSON dengan download URL:
   ```json
   {
     "success": true,
     "message": "Link download dokumen yang sudah ditandatangani",
     "data": {
       "download_url": "https://domain.com/storage/layanan/signed-documents/file.pdf",
       "file_name": "Dokumen_Ditandatangani_NamaLayanan_ID.pdf",
       "layanan_id": 1,
       "jenis_layanan": "Surat Keterangan Domisili"
     }
   }
   ```

## Validasi dan Security

### Upload/Ganti Dokumen
- Hanya layanan dengan `status_layanan = 8` (Selesai) yang bisa diupload/diganti dokumen
- **Konfirmasi sweet alert sebelum mengganti dokumen untuk mencegah kesalahan**
- File types yang diizinkan: PDF, DOC, DOCX, JPG, JPEG, PNG
- Maximum file size: 10MB
- **File lama akan otomatis dihapus saat upload file baru atau ganti dokumen**
- **Modal visual feedback dengan warna berbeda (hijau = upload, kuning = ganti)**

### Download Dokumen
- Web: Hanya admin yang bisa download
- API: Hanya user yang mengajukan layanan atau admin yang bisa download
- File validation: Cek apakah file benar-benar ada di storage

## Database Schema
```sql
ALTER TABLE `duk_pelayanan` 
ADD COLUMN `signed_document_path` VARCHAR(255) NULL AFTER `status_layanan`;
```

## File Structure
```
storage/app/public/layanan/signed-documents/
├── signed-doc-1-1640995200.pdf
├── signed-doc-2-1640995300.pdf
└── ...
```

## Status Layanan
- `5` = Draft
- `6` = Belum Diproses
- `7` = Sedang Diproses
- `8` = Selesai ← **Fitur ini aktif untuk status ini**

## Testing
Sudah ada data pelayanan dengan ID 1 yang memiliki status_layanan = 8 dan belum ada signed_document_path, sehingga siap untuk testing upload dokumen.

## Catatan
- Fitur ini terintegrasi penuh dengan sistem existing
- Tidak mengubah alur kerja yang sudah ada
- Kompatibel dengan aplikasi Android yang sudah ada
- Dokumen disimpan secara aman dengan nama file yang unique

## Update Terbaru (v2.0)
### ✅ Fitur Ganti Dokumen
- **Tombol "Ganti Dokumen"** untuk file yang sudah ada
- **Konfirmasi sebelum mengganti** untuk mencegah kesalahan
- **Modal dinamis** dengan warna berbeda:
  - 🟢 Hijau untuk upload dokumen baru
  - 🟡 Kuning untuk ganti dokumen yang sudah ada
- **Pesan sukses yang berbeda** untuk upload vs ganti
- **Auto-reset modal** ke keadaan semula saat ditutup
- **File lama otomatis dihapus** saat diganti

### UI/UX Improvements
- Visual feedback yang jelas antara upload dan ganti
- Konfirmasi pencegahan kesalahan
- Pesan yang lebih informatif
- Better user experience untuk admin

**Status Update: SELESAI ✅** - Fitur upload/download dan ganti dokumen lengkap dan siap digunakan! 