# Multiple Register Surat Enhancement

## Overview
Modifikasi pada sistem layanan untuk mendukung pembuatan multiple register surat berdasarkan semua kategori surat yang tersedia untuk satu jenis pelayanan.

## Problem yang Diselesaikan
Sebelumnya, ketika ada lebih dari 1 template surat untuk satu jenis pelayanan (misalnya: "Surat Pengantar Pernikahan" dan "Surat Pengantar Numpang Nikah" keduanya untuk jenis_pelayanan_id = 7), sistem hanya membuat 1 register surat.

Sekarang sistem akan membuat register surat untuk setiap template yang tersedia.

## File yang Dimodifikasi

### 1. DaftarLayananController.php
**File:** `app/Http/Controllers/layanan/DaftarLayananController.php`

#### Method yang diubah:
1. `approve($id)` - Lines 335-393
2. `simpleApprove($id)` - Lines 726-762

## Perubahan Detail

### Sebelum (Old Code):
```php
// Hanya mengambil 1 kategori surat
$kategori_surat = KategoriSurat::where('jenis_pelayanan_id', $layanan->jenis_pelayanan_id)
    ->where('tipe_surat', 'layanan')
    ->first()->id;

// Membuat 1 register surat
$ins_reg_surat = RegisterSurat::create([
    'nomor_surat' => $no_surat,
    'kategori_surat_id' => $kategori_surat,
    // ...
]);
```

### Sesudah (New Code):
```php
// Mengambil SEMUA kategori surat untuk jenis pelayanan ini
$kategori_surat_list = KategoriSurat::where('jenis_pelayanan_id', $layanan->jenis_pelayanan_id)
    ->where('tipe_surat', 'layanan')
    ->get();

// Loop untuk setiap kategori surat
foreach ($kategori_surat_list as $kategori_surat) {
    $no_surat = generateNoSurat();
    $jenis_surat = $layanan->jenisPelayanan->nama_pelayanan . ' - ' . $kategori_surat->nama;
    $perihal = $kategori_surat->nama;
    
    $ins_reg_surat = RegisterSurat::create([
        'nomor_surat' => $no_surat,
        'kategori_surat_id' => $kategori_surat->id,
        'jenis_surat' => $jenis_surat,
        'perihal' => $perihal,
        // ...
    ]);
}
```

## Fitur Enhancement

### 1. Multiple Register Creation
- Sistem sekarang membuat register surat untuk setiap template yang tersedia
- Setiap register mendapat nomor surat unik
- Log detail untuk tracking setiap register yang dibuat

### 2. Backward Compatibility
- Field `layanan.surat_id` tetap diisi dengan register surat pertama
- Existing functionality tidak berubah
- API response tetap konsisten

### 3. Improved Logging
- Log detail untuk setiap register surat yang dibuat
- Informasi kategori_surat_id, nama, dan nomor surat
- Memudahkan debugging dan monitoring

### 4. Fallback Mechanism (approve method only)
- Jika tidak ada kategori surat khusus, gunakan kategori default "Layanan"
- Memastikan sistem tetap berfungsi untuk jenis layanan tanpa template khusus

## Contoh Skenario

### Jenis Pelayanan: Formulir Pernikahan (ID: 7)
**Kategori Surat yang Tersedia:**
1. Surat Pengantar Pernikahan (ID: 25)
2. Surat Pengantar Numpang Nikah (ID: 26)

**Hasil Setelah Approve:**
```
Register Surat #1:
- nomor_surat: "001/DESA/01/2025"
- kategori_surat_id: 25
- jenis_surat: "Formulir Pernikahan - Surat Pengantar Pernikahan"
- perihal: "Surat Pengantar Pernikahan"

Register Surat #2:
- nomor_surat: "002/DESA/01/2025"
- kategori_surat_id: 26
- jenis_surat: "Formulir Pernikahan - Surat Pengantar Numpang Nikah"
- perihal: "Surat Pengantar Numpang Nikah"
```

## Database Impact

### Table: register_surat
- **Sebelum**: 1 record per layanan approval
- **Sesudah**: N records per layanan approval (N = jumlah kategori surat)

### Table: pelayanan
- Field `surat_id` tetap menunjuk ke register surat pertama
- Tidak ada perubahan struktur

## Benefits

### 1. Complete Document Generation
- Semua template surat untuk satu jenis layanan akan ter-generate
- User mendapat akses ke semua dokumen yang diperlukan

### 2. Administrative Efficiency
- Petugas tidak perlu manually membuat surat satu per satu
- Workflow otomatis untuk multiple documents

### 3. Audit Trail
- Complete logging untuk setiap register surat
- Better tracking dan reporting

### 4. Scalability
- Mudah menambah template baru tanpa perubahan code
- Sistem otomatis adapt dengan template yang tersedia

## Testing Scenarios

### 1. Jenis Layanan dengan Multiple Templates
- Test dengan jenis pelayanan yang punya 2+ kategori surat
- Verifikasi semua register surat terbuat
- Check nomor surat unik untuk setiap register

### 2. Jenis Layanan dengan Single Template
- Test dengan jenis layanan yang punya 1 kategori surat
- Verifikasi backward compatibility

### 3. Jenis Layanan tanpa Template Khusus
- Test dengan jenis layanan tanpa kategori surat
- Verifikasi fallback ke kategori "Layanan" (approve method)

### 4. Log Verification
- Check log output untuk setiap register creation
- Verify log data completeness

## Migration Notes
- **No database migration required**
- **No API breaking changes**
- **Existing data remains intact**
- **Immediate effect after deployment**

## Future Enhancements
1. **Bulk PDF Generation**: Generate PDF untuk semua register sekaligus
2. **Template Priority**: Allow prioritizing certain templates
3. **Conditional Templates**: Template berdasarkan kondisi tertentu
4. **User Template Selection**: Allow user memilih template yang diinginkan 