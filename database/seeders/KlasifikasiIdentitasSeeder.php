<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Layanan\KlasifikasiIdentitasPemohon;

class KlasifikasiIdentitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $klasifikasi = [
            [
                'nama_klasifikasi' => 'DATA PRIBADI',
                'deskripsi' => 'Data identitas pribadi pemohon',
                'urutan' => 1,
                'status' => true
            ],
            [
                'nama_klasifikasi' => 'DATA KELUARGA',
                'deskripsi' => 'Data keluarga dan hubungan kekerabatan',
                'urutan' => 2,
                'status' => true
            ],
            [
                'nama_klasifikasi' => 'DATA ALAMAT',
                'deskripsi' => 'Data alamat dan domisili',
                'urutan' => 3,
                'status' => true
            ],
            [
                'nama_klasifikasi' => 'DATA PEKERJAAN',
                'deskripsi' => 'Data pekerjaan dan penghasilan',
                'urutan' => 4,
                'status' => true
            ],
            [
                'nama_klasifikasi' => 'DATA PENDIDIKAN',
                'deskripsi' => 'Data pendidikan terakhir',
                'urutan' => 5,
                'status' => true
            ]
        ];

        foreach ($klasifikasi as $item) {
            KlasifikasiIdentitasPemohon::updateOrCreate(
                ['nama_klasifikasi' => $item['nama_klasifikasi']],
                $item
            );
        }

        $this->command->info('Data klasifikasi identitas berhasil ditambahkan!');
    }
}
