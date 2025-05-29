<?php

namespace Database\Seeders;

use App\Models\Layanan\KlasifikasiIdentitasPemohon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KlasifikasiIdentitasPemohonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $klasifikasi = [
            [
                'nama_klasifikasi' => 'Data Pribadi',
                'deskripsi' => 'Informasi dasar tentang identitas pemohon',
                'urutan' => 1,
                'status' => true,
            ],
            [
                'nama_klasifikasi' => 'Data Kontak',
                'deskripsi' => 'Informasi kontak dan alamat pemohon',
                'urutan' => 2,
                'status' => true,
            ],
            [
                'nama_klasifikasi' => 'Riwayat Pendidikan',
                'deskripsi' => 'Informasi mengenai pendidikan pemohon',
                'urutan' => 3,
                'status' => true,
            ],
            [
                'nama_klasifikasi' => 'Data Pekerjaan',
                'deskripsi' => 'Informasi mengenai pekerjaan pemohon',
                'urutan' => 4,
                'status' => true,
            ],
            [
                'nama_klasifikasi' => 'Data Keluarga',
                'deskripsi' => 'Informasi mengenai keluarga pemohon',
                'urutan' => 5,
                'status' => true,
            ],
            [
                'nama_klasifikasi' => 'Lainnya',
                'deskripsi' => 'Informasi tambahan lainnya',
                'urutan' => 99,
                'status' => true,
            ],
        ];

        foreach ($klasifikasi as $item) {
            KlasifikasiIdentitasPemohon::updateOrCreate(
                ['nama_klasifikasi' => $item['nama_klasifikasi']],
                $item
            );
        }
    }
}
