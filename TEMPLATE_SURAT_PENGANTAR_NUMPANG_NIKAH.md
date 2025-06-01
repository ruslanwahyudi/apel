# Template Surat Pengantar Numpang Nikah

## Overview
Template surat pengantar numpang nikah telah berhasil dibuat berdasarkan format dokumen resmi. Template ini terintegrasi dengan sistem DUK (Data Umum Kependudukan) dan menggunakan field yang sama dengan formulir pernikahan (jenis_pelayanan_id = 7).

## File yang Dibuat

### 1. Template Blade
**File:** `resources/views/templates/surat/surat-pengantar-numpang-nikah.blade.php`

Template ini menggunakan format surat resmi dengan struktur:
- Kop surat (include dari `partials.kop-surat`)
- Judul: "SURAT PENGANTAR NUMPANG NIKAH"
- Nomor surat (di bawah judul)
- Pembukaan surat dari Kepala Desa
- Data pemohon (9 field utama)
- Keterangan data orang tua (4 field)
- Data calon suami/pasangan (7 field)
- Penutup surat
- Tanda tangan (include dari `partials.tanda-tangan`)

### 2. Seeder Database
**File:** `database/seeders/SuratPengantarNumpangNikahSeeder.php`

Seeder untuk mendaftarkan kategori surat ke database dengan:
- Nama: "Surat Pengantar Numpang Nikah"
- Template type: blade
- Jenis pelayanan ID: 7 (sama dengan formulir pernikahan)
- 22 field form variables untuk input data

## Field yang Tersedia

### Data Pemohon (9 field):
1. `nama` - Nama Lengkap (required)
2. `tempat_lahir` - Tempat Lahir (required)
3. `tanggal_lahir` - Tanggal Lahir (required)
4. `jenis_kelamin` - Jenis Kelamin (required)
5. `status_kawin` - Status Perkawinan (required)
6. `kewarganegaraan` - Kewarganegaraan (optional, default: Indonesia)
7. `agama` - Agama (required)
8. `pekerjaan` - Pekerjaan (required)
9. `alamat` - Alamat (required)

### Data Orang Tua (4 field):
10. `nama_ayah` - Nama Ayah (required)
11. `alamat_ayah` - Alamat Ayah (optional)
12. `nama_ibu` - Nama Ibu (required)
13. `alamat_ibu` - Alamat Ibu (optional)

### Data Calon Suami/Pasangan (7 field):
14. `nama_pasangan` - Nama Calon Suami (required)
15. `bin_pasangan` - Bin (Anak dari) (required)
16. `tempat_lahir_pasangan` - Tempat Lahir Calon Suami (required)
17. `tanggal_lahir_pasangan` - Tanggal Lahir Calon Suami (required)
18. `kewarganegaraan_pasangan` - Kewarganegaraan Calon Suami (optional, default: WNI)
19. `agama_pasangan` - Agama Calon Suami (required)
20. `pekerjaan_pasangan` - Pekerjaan Calon Suami (required)
21. `alamat_pasangan` - Alamat Calon Suami (required)

### Field Tambahan:
22. `nomor` - Nomor Surat (optional, auto-generated)

## Fitur Template

### 1. Integrasi dengan Sistem DUK
- Otomatis mengambil data dari database `duk_pelayanan` berdasarkan `pemohon_id`
- Menggunakan mapping field dari `duk_identitas_pemohon` sesuai jenis pelayanan pernikahan
- Field yang kosong akan ditampilkan sebagai titik-titik untuk diisi manual

### 2. Format Tanggal Otomatis
- Tanggal lahir diformat ke bahasa Indonesia (contoh: "15 Januari 1990")
- Nomor surat auto-generated dengan format: "001/DESA/01/2025"

### 3. Styling Responsif
- Font Times New Roman 12pt untuk konsistensi dokumen resmi
- Margin minimal untuk optimasi kertas A4
- Style khusus untuk print dengan spacing yang tepat

### 4. Include Components
- **Kop Surat**: Menggunakan `partials.kop-surat` untuk header dokumen
- **Tanda Tangan**: Menggunakan `partials.tanda-tangan` untuk footer dokumen

## Cara Penggunaan

### 1. Melalui Form Individual
- Akses halaman kategori surat
- Pilih "Surat Pengantar Numpang Nikah"
- Isi semua field yang diperlukan
- Generate PDF

### 2. Melalui Multiple Print
- Pilih jenis pelayanan "Formulir Pernikahan"
- Masukkan pelayanan_id dari database
- Template ini akan otomatis tersedia dalam daftar kategori

### 3. Integrasi DUK
- Data pemohon akan otomatis diambil dari database DUK
- Field formulir akan ter-populate otomatis
- User dapat mengubah data sebelum generate PDF

## Technical Notes

### Database Integration
- Template menggunakan jenis_pelayanan_id = 7 (Formulir Pernikahan)
- Field mapping sesuai dengan `duk_identitas_pemohon.nama_field`
- Tidak ada perubahan pada struktur database existing

### Template Type
- Type: `blade` (menggunakan Laravel Blade templating)
- Auto-generate PDF menggunakan DomPDF
- Support untuk preview dan download

### Compatibility
- Compatible dengan sistem multiple print
- Dapat digunakan dalam ZIP generation atau combined PDF
- Mengikuti format konsisten dengan template lainnya

## Status Template
✅ **Template Aktif dan Siap Digunakan**

Template telah terdaftar di database dan dapat digunakan untuk:
- Generate PDF individual
- Multiple print dengan kategori lain
- Preview template sebelum generate
- Integration dengan data DUK existing 