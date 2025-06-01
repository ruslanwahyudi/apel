# Template Surat Pengantar Pernikahan

## Overview
Template surat pengantar pernikahan telah berhasil dibuat dengan format yang sesuai dengan dokumen resmi. Template ini terintegrasi dengan sistem DUK (Data Umum Kependudukan) dan dapat mengambil data pemohon secara otomatis.

## File yang Dibuat

### 1. Template Blade
**File:** `resources/views/templates/surat/surat-pengantar-pernikahan.blade.php`

Template ini menggunakan format surat resmi dengan struktur:
- Kop surat (include dari `partials.kop-surat`)
- Nomor dan tanggal surat
- Judul: "PENGANTAR NIKAH"
- Data pemohon (27 field)
- Tanda tangan (include dari `partials.tanda-tangan`)

### 2. Seeder Database
**File:** `database/seeders/SuratPengantarPernikahanSeeder.php`

Seeder untuk mendaftarkan kategori surat ke database dengan:
- Nama: "Surat Pengantar Pernikahan"
- Template type: Blade
- Tipe surat: Layanan
- Jenis pelayanan ID: 7 (Formulir Pernikahan)

## Integrasi dengan Database

### Jenis Pelayanan
Template ini terhubung dengan jenis pelayanan ID 7 ("Formulir Pernikahan") yang memiliki 29 field identitas pemohon.

### Field yang Tersedia
Template menggunakan field dari `duk_identitas_pemohon` untuk jenis pelayanan pernikahan:

#### Data Pemohon Utama (11 field):
1. Nama
2. NIK
3. Jenis Kelamin
4. Tempat Lahir
5. Tanggal Lahir
6. Kewarganegaraan
7. Agama
8. Pekerjaan
9. Pendidikan Terakhir
10. Bin/Binti
11. Alamat
12. Status Perkawinan

#### Data Ayah (8 field):
13. Nama Ayah
14. Bin Ayah
15. NIK Ayah
16. Tempat Lahir Ayah
17. Tanggal Lahir Ayah
18. Kewarganegaraan Ayah
19. Agama Ayah
20. Pekerjaan Ayah
21. Alamat Ayah

#### Data Ibu (8 field):
22. Nama Ibu
23. Binti Ibu
24. NIK Ibu
25. Tempat Lahir Ibu
26. Tanggal Lahir Ibu
27. Kewarganegaraan Ibu
28. Agama Ibu
29. Pekerjaan Ibu
30. Alamat Ibu

## Fitur Template

### 1. Format Sesuai Standar
- Menggunakan kop surat resmi
- Format penomoran yang sistematis (1-27)
- Tanda tangan dengan TTE (Tanda Tangan Elektronik)

### 2. Auto-fill Data
- Dapat mengambil data dari database DUK secara otomatis
- Fallback ke placeholder jika data kosong

### 3. Format Tanggal Indonesia
- Otomatis format tanggal ke bahasa Indonesia
- Contoh: "31 Januari 2025"

### 4. Responsive Design
- Optimized untuk print (media query @print)
- Layout A4 dengan margin yang sesuai

## Cara Menggunakan

### 1. Melalui Admin Panel
1. Login sebagai admin
2. Masuk ke menu "Kategori Surat"
3. Pilih "Surat Pengantar Pernikahan"
4. Isi form data pemohon
5. Generate PDF

### 2. Melalui Multiple Print
1. Pilih "Formulir Pernikahan" pada jenis layanan
2. Masukkan ID pelayanan pemohon
3. Data akan auto-fill dari database DUK
4. Generate PDF atau ZIP

### 3. Preview Template
Template dapat di-preview di URL:
```
/adm/kategori-surat/{id}/preview-blade
```

## Status Implementasi
✅ Template Blade dibuat  
✅ Seeder database dibuat  
✅ Integrasi dengan kop-surat.blade.php  
✅ Integrasi dengan tanda-tangan.blade.php  
✅ Mapping field dari database DUK  
✅ Format tanggal Indonesia  
✅ Responsive design untuk print  
✅ Auto-generate nomor surat  

## Catatan Teknis
- Template menggunakan `@include('partials.kop-surat')` untuk konsistensi kop surat
- Template menggunakan `@include('partials.tanda-tangan')` untuk konsistensi tanda tangan
- Data diambil langsung dari database tanpa mengubah struktur database yang ada
- Template kompatibel dengan sistem multiple print yang sudah ada

## Testing
Template telah ditest dan dapat:
- ✅ Di-generate sebagai PDF
- ✅ Menampilkan data dari database DUK
- ✅ Format print yang sesuai standar
- ✅ Integrasi dengan sistem yang ada 