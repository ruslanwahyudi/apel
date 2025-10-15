# Dokumentasi API Klasifikasi Identitas Pemohon

## Overview
Fitur klasifikasi identitas pemohon memungkinkan pengelompokan field-field identitas pemohon berdasarkan kategori tertentu untuk memudahkan tampilan di frontend dalam bentuk tabs.

## Struktur Database

### Tabel `duk_klasifikasi_identitas_pemohon`
- `id` - Primary key
- `nama_klasifikasi` - Nama klasifikasi (contoh: "Data Pribadi", "Data Kontak")
- `deskripsi` - Deskripsi klasifikasi
- `urutan` - Urutan tampilan
- `status` - Status aktif/nonaktif
- `created_at`, `updated_at` - Timestamps

### Tabel `duk_identitas_pemohon` (sudah ada)
- `id` - Primary key
- `jenis_pelayanan_id` - Foreign key ke jenis pelayanan
- `klasifikasi_id` - Foreign key ke klasifikasi (BARU)
- `nama_field` - Nama field untuk database
- `label` - Label untuk tampilan
- `tipe_field` - Tipe field (text, number, date, email, textarea)
- `required` - Apakah field wajib diisi
- `readonly` - Apakah field readonly

## API Endpoints

### 1. Get Detail Layanan dengan Klasifikasi
**GET** `/api/layanan/{id}`

Response akan menambahkan field `identitas_pemohon_grouped` yang berisi data yang sudah dikelompokkan berdasarkan klasifikasi:

```json
{
  "success": true,
  "message": "Detail Layanan",
  "data": {
    "id": 1,
    "jenis_pelayanan_id": 1,
    "status_layanan": 1,
    "jenisPelayanan": {
      "id": 1,
      "nama_pelayanan": "Pengurusan KTP",
      "deskripsi": "Layanan pengurusan KTP"
    },
    "identitas_pemohon_grouped": [
      {
        "klasifikasi_id": 1,
        "klasifikasi_nama": "Data Pribadi",
        "klasifikasi_deskripsi": "Informasi dasar tentang identitas pemohon",
        "klasifikasi_urutan": 1,
        "fields": [
          {
            "id": 1,
            "nama_field": "nama_lengkap",
            "label": "Nama Lengkap",
            "tipe_field": "text",
            "required": true,
            "readonly": false,
            "nilai": "John Doe",
            "data_identitas_id": 1
          },
          {
            "id": 2,
            "nama_field": "tempat_lahir",
            "label": "Tempat Lahir",
            "tipe_field": "text",
            "required": true,
            "readonly": false,
            "nilai": "Jakarta",
            "data_identitas_id": 2
          }
        ]
      },
      {
        "klasifikasi_id": 2,
        "klasifikasi_nama": "Data Kontak",
        "klasifikasi_deskripsi": "Informasi kontak dan alamat pemohon",
        "klasifikasi_urutan": 2,
        "fields": [
          {
            "id": 3,
            "nama_field": "alamat",
            "label": "Alamat Lengkap",
            "tipe_field": "textarea",
            "required": true,
            "readonly": false,
            "nilai": "Jl. Contoh No. 123",
            "data_identitas_id": 3
          }
        ]
      }
    ]
  }
}
```

### 2. Get Klasifikasi Identitas untuk Jenis Layanan
**GET** `/api/layanan/klasifikasi-identitas/{jenisPelayananId}`

Response:
```json
{
  "success": true,
  "message": "Daftar klasifikasi identitas pemohon",
  "data": [
    {
      "id": 1,
      "nama_klasifikasi": "Data Pribadi",
      "deskripsi": "Informasi dasar tentang identitas pemohon",
      "urutan": 1,
      "status": true,
      "created_at": "2025-01-03T10:00:00.000000Z",
      "updated_at": "2025-01-03T10:00:00.000000Z"
    },
    {
      "id": 2,
      "nama_klasifikasi": "Data Kontak",
      "deskripsi": "Informasi kontak dan alamat pemohon",
      "urutan": 2,
      "status": true,
      "created_at": "2025-01-03T10:00:00.000000Z",
      "updated_at": "2025-01-03T10:00:00.000000Z"
    }
  ]
}
```

## Implementasi Frontend

### Contoh Penggunaan untuk Tabs

```javascript
// Ambil data layanan
const response = await fetch(`/api/layanan/${layananId}`);
const data = await response.json();

// Buat tabs berdasarkan klasifikasi
const tabs = data.data.identitas_pemohon_grouped.map(klasifikasi => ({
  id: klasifikasi.klasifikasi_id,
  title: klasifikasi.klasifikasi_nama,
  description: klasifikasi.klasifikasi_deskripsi,
  fields: klasifikasi.fields
}));

// Render tabs
tabs.forEach(tab => {
  // Buat tab header
  const tabHeader = document.createElement('div');
  tabHeader.className = 'tab-header';
  tabHeader.textContent = tab.title;
  
  // Buat tab content
  const tabContent = document.createElement('div');
  tabContent.className = 'tab-content';
  
  // Render fields dalam tab
  tab.fields.forEach(field => {
    const fieldElement = createFieldElement(field);
    tabContent.appendChild(fieldElement);
  });
  
  // Tambahkan ke container
  tabsContainer.appendChild(tabHeader);
  tabsContainer.appendChild(tabContent);
});
```

## Manfaat

1. **Organisasi Data**: Field-field identitas pemohon dapat dikelompokkan secara logis
2. **UX yang Lebih Baik**: Frontend dapat menampilkan data dalam tabs yang terorganisir
3. **Fleksibilitas**: Klasifikasi dapat diatur sesuai kebutuhan
4. **Urutan Tampilan**: Field dapat diurutkan berdasarkan urutan klasifikasi
5. **Deskripsi**: Setiap klasifikasi memiliki deskripsi untuk pemahaman yang lebih baik

## Migration

Pastikan migration berikut sudah dijalankan:

```bash
php artisan migrate
```

Migration yang diperlukan:
- `2025_05_28_214529_create_duk_klasifikasi_identitas_pemohon_table.php`
- `2025_05_28_214541_add_klasifikasi_id_to_duk_identitas_pemohon_table.php`
- `2025_12_30_000001_add_label_to_duk_identitas_pemohon_table.php`

## Seeder

Jalankan seeder untuk data awal:

```bash
php artisan db:seed --class=KlasifikasiIdentitasPemohonSeeder
```

Seeder akan membuat klasifikasi default:
- Data Pribadi
- Data Kontak
- Riwayat Pendidikan
- Data Pekerjaan
- Data Keluarga
- Lainnya 