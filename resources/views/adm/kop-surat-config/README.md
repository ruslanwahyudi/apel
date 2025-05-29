# Dokumentasi Kop Surat

## Overview
Sistem kop surat yang telah dibuat memungkinkan Anda untuk:
1. Membuat kop surat yang seragam untuk semua surat
2. Mengelola konfigurasi kop surat melalui database
3. Menggunakan kop surat secara berulang di berbagai template

## File yang Dibuat

### 1. Partial Views
- `resources/views/partials/kop-surat.blade.php` - Kop surat statis
- `resources/views/partials/kop-surat-configurable.blade.php` - Kop surat dengan parameter
- `resources/views/partials/kop-surat-dynamic.blade.php` - Kop surat dari database

### 2. Components
- `resources/views/components/kop-surat.blade.php` - Komponen kop surat standalone

### 3. Models
- `app/Models/KopSuratConfig.php` - Model untuk konfigurasi kop surat

### 4. Controllers
- `app/Http/Controllers/adm/KopSuratConfigController.php` - Controller untuk mengelola konfigurasi

### 5. Migrations
- `database/migrations/2025_01_02_000001_create_kop_surat_config_table.php` - Tabel konfigurasi

### 6. Template Examples
- `resources/views/templates/surat/contoh-surat-dengan-kop.blade.php` - Contoh penggunaan
- `resources/views/templates/surat/surat-keterangan-umum.blade.php` - Template dengan kop dinamis

## Cara Penggunaan

### 1. Menggunakan Kop Surat Statis
```blade
@include('partials.kop-surat')
```

### 2. Menggunakan Kop Surat dengan Parameter
```blade
@include('partials.kop-surat-configurable', [
    'kabupaten' => 'PAMEKASAN',
    'kecamatan' => 'PALENGAAN',
    'desa' => 'BANYUPELLE',
    'alamat' => 'Jl. Raya Palengaan Proppo Cemkepak Desa Banyupelle 69362',
    'website1' => 'http://banyupelle.desa.id/',
    'website2' => 'www.banyupelle.desa.id/',
    'logo_path' => 'assets/images/logo-pamekasan.png'
])
```

### 3. Menggunakan Kop Surat Dinamis (Dari Database)
```blade
@include('partials.kop-surat-dynamic')
```

### 4. Menggunakan sebagai Component
```blade
<x-kop-surat />
```

## Konfigurasi Database

### Menjalankan Migration
```bash
php artisan migrate
```

### Mengelola Konfigurasi
1. Akses halaman admin konfigurasi kop surat
2. Tambah/edit konfigurasi sesuai kebutuhan
3. Set konfigurasi sebagai aktif
4. Konfigurasi aktif akan digunakan di semua template

## Struktur Template Surat

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $kategori->nama ?? 'Surat' }}</title>
    <style>
        /* CSS untuk styling surat */
    </style>
</head>
<body>
    <div class="surat-content">
        {{-- Include Kop Surat --}}
        @include('partials.kop-surat-dynamic')
        
        {{-- Nomor dan Tanggal Surat --}}
        <div class="nomor-tanggal">
            <div>Nomor: {{ $data['nomor'] ?? $nomor_surat ?? '___/___/___' }}</div>
            <div>Tanggal: {{ isset($data['tanggal']) ? \Carbon\Carbon::parse($data['tanggal'])->format('d F Y') : date('d F Y') }}</div>
        </div>
        
        {{-- Judul Surat --}}
        <div class="judul-surat">
            {{ $kategori->nama ?? 'SURAT KETERANGAN' }}
        </div>
        
        {{-- Isi Surat --}}
        <div class="isi-surat">
            <!-- Konten surat -->
        </div>
        
        {{-- Tanda Tangan --}}
        <div class="ttd-section">
            <!-- Area tanda tangan -->
        </div>
    </div>
</body>
</html>
```

## Fitur Kop Surat

### 1. Responsive Design
- Tampilan optimal di layar dan print
- Ukuran font dan spacing yang sesuai

### 2. Konfigurasi Fleksibel
- Logo dapat diganti
- Teks dapat disesuaikan
- Website dan alamat dapat diubah

### 3. Database Driven
- Konfigurasi tersimpan di database
- Dapat memiliki multiple konfigurasi
- Hanya satu konfigurasi yang aktif

### 4. Easy Integration
- Mudah diintegrasikan ke template existing
- Tidak memerlukan perubahan besar pada kode

## Logo Requirements

### Format yang Didukung
- JPEG, PNG, JPG, GIF
- Maksimal 2MB
- Resolusi optimal: 300x300px

### Lokasi File
- Default: `public/assets/images/logo-pamekasan.png`
- Upload: `public/assets/images/logo-{timestamp}.{ext}`

## Styling

### CSS Classes yang Tersedia
- `.kop-surat` - Container utama
- `.kop-header` - Header section
- `.logo-container` - Container logo
- `.header-text` - Container teks header
- `.header-title` - Judul kabupaten
- `.header-subtitle` - Judul kecamatan
- `.header-village` - Nama desa
- `.header-address` - Alamat
- `.header-website` - Website
- `.kop-divider` - Garis pembatas tebal
- `.kop-divider-thin` - Garis pembatas tipis

### Print Optimization
- Font size disesuaikan untuk print
- Margin dan spacing optimal
- Logo size responsive

## Best Practices

1. **Gunakan kop surat dinamis** untuk fleksibilitas maksimal
2. **Upload logo berkualitas tinggi** untuk hasil print yang baik
3. **Test preview** sebelum menggunakan di production
4. **Backup konfigurasi** sebelum melakukan perubahan besar
5. **Gunakan naming convention** yang konsisten untuk template

## Troubleshooting

### Logo tidak muncul
- Pastikan file logo ada di lokasi yang benar
- Check permission folder `public/assets/images/`
- Pastikan path logo di database benar

### Styling tidak sesuai
- Check CSS conflicts dengan template existing
- Pastikan CSS kop surat di-load dengan benar
- Test di browser yang berbeda

### Konfigurasi tidak tersimpan
- Check validasi form
- Pastikan migration sudah dijalankan
- Check permission database

## Support

Untuk pertanyaan atau masalah, silakan hubungi tim development atau buat issue di repository project. 