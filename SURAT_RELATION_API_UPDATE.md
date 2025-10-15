# Dokumentasi Update Relasi Surat API

## Overview
Perubahan pada function `show()` di `LayananController` untuk menyesuaikan dengan perubahan struktur database dimana relasi surat sekarang menggunakan column `temp_surat_id` (Array) dan menambahkan logika untuk URL file surat berdasarkan status.

## Perubahan Struktur Database

### Sebelumnya:
- Menggunakan relasi `surat` yang berelasi langsung dengan satu surat
- Tidak ada logika untuk URL file surat

### Sekarang:
- Menggunakan column `temp_surat_id` (Array) yang berisi multiple ID surat
- Menambahkan logika untuk URL file surat berdasarkan status surat

## Perubahan pada Function `show()`

### 1. Menghapus Relasi `surat` dari Query
```php
// Sebelumnya
$layanan = Pelayanan::with([
    // ... relasi lainnya
    'surat'
])->findOrFail($id);

// Sekarang
$layanan = Pelayanan::with([
    // ... relasi lainnya
    'statusLayanan'
])->findOrFail($id);
```

### 2. Menambahkan Logika untuk Ambil Data Surat
```php
// Ambil data surat berdasarkan temp_surat_id (Array)
$suratData = [];
if (!empty($layanan->temp_surat_id) && is_array($layanan->temp_surat_id)) {
    $registerSuratList = \App\Models\adm\RegisterSurat::whereIn('id', $layanan->temp_surat_id)
        ->with(['kategori_surat', 'signer'])
        ->get();
    
    foreach ($registerSuratList as $surat) {
        $suratData[] = [
            'id' => $surat->id,
            'nomor_surat' => $surat->nomor_surat,
            'jenis_surat' => $surat->jenis_surat,
            'perihal' => $surat->perihal,
            'tanggal_surat' => $surat->tanggal_surat,
            'status' => $surat->status,
            'kategori_surat' => $surat->kategori_surat ? [
                'id' => $surat->kategori_surat->id,
                'nama' => $surat->kategori_surat->nama
            ] : null,
            'signer' => $surat->signer ? [
                'id' => $surat->signer->id,
                'name' => $surat->signer->name
            ] : null,
            // URL file surat: jika status = 3 (ditandatangani) dan ada signed_pdf_path, berikan URL
            'signed_pdf_url' => ($surat->status == 3 && $surat->signed_pdf_path) 
                ? Storage::url($surat->signed_pdf_path) 
                : null,
            'signed_pdf_path' => $surat->signed_pdf_path
        ];
    }
}

// Tambahkan data surat ke response
$layanan->surat_data = $suratData;
```

## Struktur Response Baru

### Response dari `GET /api/layanan/{id}`:
```json
{
  "success": true,
  "message": "Detail Layanan",
  "data": {
    "id": 1,
    "jenis_pelayanan_id": 1,
    "status_layanan": 1,
    "temp_surat_id": [1, 2, 3],
    "jenisPelayanan": {
      "id": 1,
      "nama_pelayanan": "Pengurusan KTP",
      "deskripsi": "Layanan pengurusan KTP"
    },
    "surat_data": [
      {
        "id": 1,
        "nomor_surat": "001/2025",
        "jenis_surat": "Surat Pengantar KTP",
        "perihal": "Pengurusan KTP",
        "tanggal_surat": "2025-01-03T10:00:00.000000Z",
        "status": 3,
        "kategori_surat": {
          "id": 1,
          "nama": "Surat Pengantar"
        },
        "signer": {
          "id": 1,
          "name": "Kepala Desa"
        },
        "signed_pdf_url": "http://localhost:8000/storage/surat/signed-pdfs/signed_surat_001_2025_2025-01-03_10-00-00.pdf",
        "signed_pdf_path": "surat/signed-pdfs/signed_surat_001_2025_2025-01-03_10-00-00.pdf"
      },
      {
        "id": 2,
        "nomor_surat": "002/2025",
        "jenis_surat": "Surat Keterangan",
        "perihal": "Keterangan Domisili",
        "tanggal_surat": "2025-01-03T10:00:00.000000Z",
        "status": 1,
        "kategori_surat": {
          "id": 2,
          "nama": "Surat Keterangan"
        },
        "signer": {
          "id": 1,
          "name": "Kepala Desa"
        },
        "signed_pdf_url": null,
        "signed_pdf_path": null
      }
    ],
    "identitas_pemohon_grouped": [
      // ... data identitas pemohon yang dikelompokkan
    ]
  }
}
```

## Logika URL File Surat

### Kondisi untuk `signed_pdf_url`:
1. **Status surat = 3** (ditandatangani)
2. **Ada `signed_pdf_path`** (file PDF sudah di-generate)

### Contoh:
- **Status = 3 dan ada signed_pdf_path**: `signed_pdf_url` berisi URL lengkap ke file PDF
- **Status ≠ 3 atau tidak ada signed_pdf_path**: `signed_pdf_url` = `null`

## Manfaat Perubahan

### 1. **Support Multiple Surat**
- Satu layanan dapat memiliki multiple surat
- Data surat dikembalikan dalam array `surat_data`

### 2. **Informasi Lengkap Surat**
- Data kategori surat
- Data penandatangan (signer)
- Status surat
- URL file PDF (jika sudah ditandatangani)

### 3. **Logika URL yang Jelas**
- URL file hanya tersedia jika surat sudah ditandatangani (status = 3)
- Mencegah akses ke file yang belum siap

### 4. **Backward Compatibility**
- Tetap menyediakan `signed_pdf_path` untuk kompatibilitas
- Menambahkan `signed_pdf_url` untuk kemudahan akses

## Cara Penggunaan di Frontend

```javascript
// Ambil data layanan
const response = await fetch(`/api/layanan/${layananId}`);
const data = await response.json();

// Tampilkan daftar surat
data.data.surat_data.forEach(surat => {
    console.log(`Surat: ${surat.nomor_surat}`);
    console.log(`Status: ${surat.status}`);
    console.log(`Kategori: ${surat.kategori_surat?.nama}`);
    
    // Tampilkan tombol download jika ada URL
    if (surat.signed_pdf_url) {
        console.log(`Download URL: ${surat.signed_pdf_url}`);
        // Render tombol download
    } else {
        console.log('Surat belum ditandatangani');
        // Render pesan "belum ditandatangani"
    }
});
```

## Migration yang Diperlukan

Pastikan migration berikut sudah dijalankan:
- `2025_01_03_000001_add_signed_pdf_path_to_register_surat_table.php`

## Testing

Untuk testing, pastikan:
1. Ada data layanan dengan `temp_surat_id` yang berisi array ID surat
2. Ada data `RegisterSurat` dengan status yang berbeda-beda
3. Ada file PDF di storage untuk surat dengan status = 3 