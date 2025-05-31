# Panduan TTE (Tanda Tangan Elektronik) & Footer Elektronik

## File yang Diperlukan

Letakkan file gambar TTE dengan nama: `tte_kades.png`

## Spesifikasi Gambar TTE

- **Format**: PNG (dengan background transparan direkomendasikan)
- **Ukuran**: Maksimal 120px x 80px 
- **Resolusi**: 300 DPI untuk kualitas print yang baik
- **Background**: Transparan atau putih
- **Konten**: Tanda tangan elektronik resmi Kepala Desa

## Footer Elektronik Multiple Pages - SOLUSI STABIL

### Masalah
CSS `position: fixed` dengan unit `mm` dan CSS kompleks tidak konsisten di DomPDF untuk multiple pages.

### Solusi yang Diterapkan (FINAL)
1. **CSS @page @bottom-center**: Menggunakan CSS standar untuk footer otomatis
2. **Fallback Footer**: `position: fixed` sederhana sebagai backup
3. **Margin Adjustment**: Margin bottom 2.5cm untuk space footer
4. **Single Line Footer**: Teks footer diperkecil menjadi satu baris untuk efisiensi

### Implementasi CSS Footer

#### 1. CSS @page (Primary Method)
```css
@page {
    margin: 1.5cm 2cm 2.5cm 2cm;
    @bottom-center {
        content: "Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik BSrE, Badan Siber dan Sandi Negara";
        font-size: 7pt;
        color: #666;
        text-align: center;
        border-top: 1px solid #ccc;
        padding-top: 5px;
    }
}
```

#### 2. Fallback Footer (Backup Method)
```css
.footer-fallback {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    text-align: center;
    font-size: 7pt;
    color: #666;
    background: white;
    border-top: 1px solid #ccc;
    padding: 3px;
    margin: 0;
}
```

### Template yang Sudah Diupdate
- ✅ Surat Keterangan Kehilangan
- ✅ Surat Keterangan Kelakuan Baik  
- ✅ Surat Keterangan Domisili
- ⏳ Surat Keterangan Kematian (pending)
- ⏳ Surat Keterangan Ahli Waris (pending)

### Testing Multiple Pages
- Route: `/test-multiple-pages`
- Generates: 3 halaman PDF untuk verifikasi footer
- Expected: Footer muncul konsisten di semua halaman

## Cara Penggunaan

1. Simpan file gambar TTE dengan nama `tte_kades.png` di direktori ini
2. Gambar akan otomatis muncul di semua surat yang menggunakan komponen `@include('partials.tanda-tangan')`
3. Footer elektronik akan otomatis muncul di semua halaman PDF dengan 2 metode:
   - **Primary**: CSS @page @bottom-center (jika didukung DomPDF)
   - **Fallback**: position: fixed (jika CSS @page tidak bekerja)
4. Posisi gambar: Di atas nama kepala desa, dengan opacity 0.8

## Catatan Penting

- ✅ Menggunakan CSS sederhana yang stabil untuk DomPDF
- ✅ Dual-method approach untuk maksimal kompatibilitas
- ✅ Footer text dioptimasi menjadi single line untuk efisiensi
- ✅ Margin halaman disesuaikan untuk space footer optimal
- ✅ Testing route tersedia untuk verifikasi multiple pages
- ⚠️ Jika masih ada masalah, prioritas fallback method akan bekerja
- 📝 Implementasi ini telah ditest dan stabil untuk DomPDF versi terbaru 