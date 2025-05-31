<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuratKeteranganAhliWarisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari jenis pelayanan yang sudah ada
        $jenis = DB::table('duk_jenis_pelayanan')
            ->where('nama_pelayanan', 'Surat Keterangan Ahli Waris')
            ->first();

        if (!$jenis) {
            echo "❌ Jenis pelayanan 'Surat Keterangan Ahli Waris' tidak ditemukan di database!\n";
            return;
        }

        $jenisPelayananId = $jenis->id;

        // Hapus kategori surat yang sudah ada (jika ada)
        DB::table('kategori_surat')
            ->where('nama', 'Surat Keterangan Ahli Waris')
            ->delete();

        // Buat kategori surat untuk Surat Keterangan Ahli Waris
        DB::table('kategori_surat')->insert([
            'nama' => 'Surat Keterangan Ahli Waris',
            'blade_template_name' => 'surat-keterangan-ahli-waris',
            'blade_template_variables' => json_encode([
                [
                    'name' => 'nama',
                    'label' => 'Nama (Almarhum)',
                    'type' => 'text',
                    'required' => true,
                    'default_value' => ''
                ],
                [
                    'name' => 'nik',
                    'label' => 'NIK (Almarhum)',
                    'type' => 'text',
                    'required' => true,
                    'default_value' => ''
                ],
                [
                    'name' => 'tempat_lahir',
                    'label' => 'Tempat Lahir (Almarhum)',
                    'type' => 'text',
                    'required' => true,
                    'default_value' => ''
                ],
                [
                    'name' => 'tanggal_lahir',
                    'label' => 'Tanggal Lahir (Almarhum)',
                    'type' => 'date',
                    'required' => true,
                    'default_value' => ''
                ],
                [
                    'name' => 'pekerjaan',
                    'label' => 'Pekerjaan (Almarhum)',
                    'type' => 'text',
                    'required' => true,
                    'default_value' => ''
                ],
                [
                    'name' => 'alamat',
                    'label' => 'Alamat (Almarhum)',
                    'type' => 'textarea',
                    'required' => true,
                    'default_value' => ''
                ],
                [
                    'name' => 'nama_ahliwaris',
                    'label' => 'Nama Ahli Waris',
                    'type' => 'text',
                    'required' => true,
                    'default_value' => ''
                ],
                [
                    'name' => 'nik_ahliwaris',
                    'label' => 'NIK Ahli Waris',
                    'type' => 'text',
                    'required' => true,
                    'default_value' => ''
                ],
                [
                    'name' => 'tempat_lahir_ahliwaris',
                    'label' => 'Tempat Lahir Ahli Waris',
                    'type' => 'text',
                    'required' => false,
                    'default_value' => ''
                ],
                [
                    'name' => 'tanggal_lahir_ahliwaris',
                    'label' => 'Tanggal Lahir Ahli Waris',
                    'type' => 'date',
                    'required' => false,
                    'default_value' => ''
                ],
                [
                    'name' => 'alamat_ahliwaris',
                    'label' => 'Alamat Ahli Waris',
                    'type' => 'textarea',
                    'required' => true,
                    'default_value' => ''
                ],
                [
                    'name' => 'nomor',
                    'label' => 'Nomor Surat',
                    'type' => 'text',
                    'required' => false,
                    'default_value' => ''
                ]
            ]),
            'template_surat' => null,
            'template_variables' => null,
            'header_template' => null,
            'header_variables' => null,
            'header_type' => 'simple',
            'pdf_template_path' => null,
            'pdf_form_fields' => null,
            'template_type' => 'blade',
            'docx_template_path' => null,
            'docx_form_fields' => null,
            'tipe_surat' => 'layanan',
            'jenis_pelayanan_id' => $jenisPelayananId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "✅ Seeder Surat Keterangan Ahli Waris berhasil dijalankan!\n";
        echo "📋 Jenis Pelayanan ID: {$jenisPelayananId}\n";
        echo "📄 Template: surat-keterangan-ahli-waris.blade.php\n";
        echo "📋 Kategori surat: 1 kategori dengan 12 field variables\n";
    }
}
