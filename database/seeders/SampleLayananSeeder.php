<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Layanan\JenisPelayanan;
use App\Models\Layanan\IdentitasPemohon;
use App\Models\Layanan\SyaratDokumen;
use App\Models\Layanan\KlasifikasiIdentitasPemohon;

class SampleLayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tambah sample jenis pelayanan
        $jenisKTP = JenisPelayanan::create([
            'nama_pelayanan' => 'Pengurusan KTP',
            'deskripsi' => 'Layanan pengurusan KTP baru atau pembaruan'
        ]);

        $jenisKK = JenisPelayanan::create([
            'nama_pelayanan' => 'Pengurusan KK',
            'deskripsi' => 'Layanan pengurusan Kartu Keluarga baru atau pembaruan'
        ]);

        $jenisSuratKeterangan = JenisPelayanan::create([
            'nama_pelayanan' => 'Surat Keterangan Domisili',
            'deskripsi' => 'Layanan penerbitan surat keterangan domisili'
        ]);

        // Ambil klasifikasi yang sudah ada
        $klasifikasiPribadi = KlasifikasiIdentitasPemohon::where('nama_klasifikasi', 'Data Pribadi')->first();
        $klasifikasiKontak = KlasifikasiIdentitasPemohon::where('nama_klasifikasi', 'Data Kontak')->first();
        $klasifikasiKeluarga = KlasifikasiIdentitasPemohon::where('nama_klasifikasi', 'Data Keluarga')->first();

        // Identitas untuk KTP
        IdentitasPemohon::create([
            'jenis_pelayanan_id' => $jenisKTP->id,
            'klasifikasi_id' => $klasifikasiPribadi->id,
            'nama_field' => 'NIK',
            'tipe_field' => 'number',
            'required' => true
        ]);

        IdentitasPemohon::create([
            'jenis_pelayanan_id' => $jenisKTP->id,
            'klasifikasi_id' => $klasifikasiPribadi->id,
            'nama_field' => 'Nama Lengkap',
            'tipe_field' => 'text',
            'required' => true
        ]);

        IdentitasPemohon::create([
            'jenis_pelayanan_id' => $jenisKTP->id,
            'klasifikasi_id' => $klasifikasiPribadi->id,
            'nama_field' => 'Tempat Lahir',
            'tipe_field' => 'text',
            'required' => true
        ]);

        IdentitasPemohon::create([
            'jenis_pelayanan_id' => $jenisKTP->id,
            'klasifikasi_id' => $klasifikasiPribadi->id,
            'nama_field' => 'Tanggal Lahir',
            'tipe_field' => 'date',
            'required' => true
        ]);

        IdentitasPemohon::create([
            'jenis_pelayanan_id' => $jenisKTP->id,
            'klasifikasi_id' => $klasifikasiKontak->id,
            'nama_field' => 'Alamat Lengkap',
            'tipe_field' => 'textarea',
            'required' => true
        ]);

        IdentitasPemohon::create([
            'jenis_pelayanan_id' => $jenisKTP->id,
            'klasifikasi_id' => $klasifikasiKontak->id,
            'nama_field' => 'Nomor Telepon',
            'tipe_field' => 'number',
            'required' => false
        ]);

        // Syarat dokumen untuk KTP
        SyaratDokumen::create([
            'jenis_pelayanan_id' => $jenisKTP->id,
            'nama_dokumen' => 'Fotocopy KK',
            'deskripsi' => 'Fotocopy Kartu Keluarga yang masih berlaku',
            'required' => true
        ]);

        SyaratDokumen::create([
            'jenis_pelayanan_id' => $jenisKTP->id,
            'nama_dokumen' => 'Pas Foto 3x4',
            'deskripsi' => 'Pas foto terbaru dengan latar belakang merah',
            'required' => true
        ]);

        // Identitas untuk KK
        IdentitasPemohon::create([
            'jenis_pelayanan_id' => $jenisKK->id,
            'klasifikasi_id' => $klasifikasiPribadi->id,
            'nama_field' => 'Nama Kepala Keluarga',
            'tipe_field' => 'text',
            'required' => true
        ]);

        IdentitasPemohon::create([
            'jenis_pelayanan_id' => $jenisKK->id,
            'klasifikasi_id' => $klasifikasiKontak->id,
            'nama_field' => 'Alamat Lengkap',
            'tipe_field' => 'textarea',
            'required' => true
        ]);

        IdentitasPemohon::create([
            'jenis_pelayanan_id' => $jenisKK->id,
            'klasifikasi_id' => $klasifikasiKeluarga->id,
            'nama_field' => 'Jumlah Anggota Keluarga',
            'tipe_field' => 'number',
            'required' => true
        ]);

        // Syarat dokumen untuk KK
        SyaratDokumen::create([
            'jenis_pelayanan_id' => $jenisKK->id,
            'nama_dokumen' => 'Surat Nikah/Cerai',
            'deskripsi' => 'Surat nikah atau cerai (jika ada)',
            'required' => false
        ]);

        SyaratDokumen::create([
            'jenis_pelayanan_id' => $jenisKK->id,
            'nama_dokumen' => 'KTP Anggota Keluarga',
            'deskripsi' => 'Fotocopy KTP semua anggota keluarga',
            'required' => true
        ]);

        // Identitas untuk Surat Keterangan
        IdentitasPemohon::create([
            'jenis_pelayanan_id' => $jenisSuratKeterangan->id,
            'klasifikasi_id' => $klasifikasiPribadi->id,
            'nama_field' => 'Nama Lengkap',
            'tipe_field' => 'text',
            'required' => true
        ]);

        IdentitasPemohon::create([
            'jenis_pelayanan_id' => $jenisSuratKeterangan->id,
            'klasifikasi_id' => $klasifikasiPribadi->id,
            'nama_field' => 'NIK',
            'tipe_field' => 'number',
            'required' => true
        ]);

        IdentitasPemohon::create([
            'jenis_pelayanan_id' => $jenisSuratKeterangan->id,
            'klasifikasi_id' => $klasifikasiKontak->id,
            'nama_field' => 'Alamat Domisili',
            'tipe_field' => 'textarea',
            'required' => true
        ]);

        // Syarat dokumen untuk Surat Keterangan
        SyaratDokumen::create([
            'jenis_pelayanan_id' => $jenisSuratKeterangan->id,
            'nama_dokumen' => 'Fotocopy KTP',
            'deskripsi' => 'Fotocopy KTP yang masih berlaku',
            'required' => true
        ]);

        // Tambahkan kategori surat untuk preview
        \App\Models\adm\KategoriSurat::create([
            'nama' => 'Surat Keterangan Pengurusan KTP',
            'tipe_surat' => 'layanan',
            'jenis_pelayanan_id' => $jenisKTP->id,
            'blade_template_name' => 'sample-ktp'
        ]);
    }
} 