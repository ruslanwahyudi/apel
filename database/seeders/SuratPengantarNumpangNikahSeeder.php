<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuratPengantarNumpangNikahSeeder extends Seeder
{
    public function run()
    {
        // Check if kategori already exists
        $existing = DB::table('kategori_surat')
            ->where('nama', 'Surat Pengantar Numpang Nikah')
            ->where('jenis_pelayanan_id', 7)
            ->first();

        if (!$existing) {
            // Insert kategori surat for Surat Pengantar Numpang Nikah
            DB::table('kategori_surat')->insert([
                'nama' => 'Surat Pengantar Numpang Nikah',
                'blade_template_name' => 'surat-pengantar-numpang-nikah',
                'template_type' => 'blade',
                'tipe_surat' => 'layanan',
                'jenis_pelayanan_id' => 7, // Same as marriage form
                'blade_template_variables' => json_encode([
                    ['name' => 'nomor', 'label' => 'Nomor Surat', 'type' => 'text', 'required' => false],
                    ['name' => 'nama', 'label' => 'Nama Lengkap', 'type' => 'text', 'required' => true],
                    ['name' => 'tempat_lahir', 'label' => 'Tempat Lahir', 'type' => 'text', 'required' => true],
                    ['name' => 'tanggal_lahir', 'label' => 'Tanggal Lahir', 'type' => 'date', 'required' => true],
                    ['name' => 'jenis_kelamin', 'label' => 'Jenis Kelamin', 'type' => 'select', 'required' => true],
                    ['name' => 'status_kawin', 'label' => 'Status Perkawinan', 'type' => 'text', 'required' => true],
                    ['name' => 'kewarganegaraan', 'label' => 'Kewarganegaraan', 'type' => 'text', 'required' => false],
                    ['name' => 'agama', 'label' => 'Agama', 'type' => 'text', 'required' => true],
                    ['name' => 'pekerjaan', 'label' => 'Pekerjaan', 'type' => 'text', 'required' => true],
                    ['name' => 'alamat', 'label' => 'Alamat', 'type' => 'textarea', 'required' => true],
                    ['name' => 'nama_ayah', 'label' => 'Nama Ayah', 'type' => 'text', 'required' => true],
                    ['name' => 'alamat_ayah', 'label' => 'Alamat Ayah', 'type' => 'textarea', 'required' => false],
                    ['name' => 'nama_ibu', 'label' => 'Nama Ibu', 'type' => 'text', 'required' => true],
                    ['name' => 'alamat_ibu', 'label' => 'Alamat Ibu', 'type' => 'textarea', 'required' => false],
                    ['name' => 'nama_pasangan', 'label' => 'Nama Calon Suami', 'type' => 'text', 'required' => true],
                    ['name' => 'bin_pasangan', 'label' => 'Bin (Anak dari)', 'type' => 'text', 'required' => true],
                    ['name' => 'tempat_lahir_pasangan', 'label' => 'Tempat Lahir Calon Suami', 'type' => 'text', 'required' => true],
                    ['name' => 'tanggal_lahir_pasangan', 'label' => 'Tanggal Lahir Calon Suami', 'type' => 'date', 'required' => true],
                    ['name' => 'kewarganegaraan_pasangan', 'label' => 'Kewarganegaraan Calon Suami', 'type' => 'text', 'required' => false],
                    ['name' => 'agama_pasangan', 'label' => 'Agama Calon Suami', 'type' => 'text', 'required' => true],
                    ['name' => 'pekerjaan_pasangan', 'label' => 'Pekerjaan Calon Suami', 'type' => 'text', 'required' => true],
                    ['name' => 'alamat_pasangan', 'label' => 'Alamat Calon Suami', 'type' => 'textarea', 'required' => true]
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            echo "✓ Kategori surat 'Surat Pengantar Numpang Nikah' berhasil ditambahkan\n";
        } else {
            echo "ℹ Kategori surat 'Surat Pengantar Numpang Nikah' sudah ada\n";
        }
    }
} 