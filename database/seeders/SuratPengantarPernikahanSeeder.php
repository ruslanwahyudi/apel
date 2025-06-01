<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuratPengantarPernikahanSeeder extends Seeder
{
    public function run()
    {
        // Check if kategori already exists
        $existing = DB::table('kategori_surat')
            ->where('nama', 'Surat Pengantar Pernikahan')
            ->where('jenis_pelayanan_id', 7)
            ->first();

        if (!$existing) {
            // Insert kategori surat for Surat Pengantar Pernikahan
            DB::table('kategori_surat')->insert([
                'nama' => 'Surat Pengantar Pernikahan',
                'blade_template_name' => 'surat-pengantar-pernikahan',
                'template_type' => 'blade',
                'tipe_surat' => 'layanan',
                'jenis_pelayanan_id' => 7, // Formulir Pernikahan
                'blade_template_variables' => json_encode([
                    // Data Pemohon
                    [
                        'name' => 'nama',
                        'label' => 'Nama',
                        'type' => 'text',
                        'required' => true,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'nik',
                        'label' => 'NIK',
                        'type' => 'text',
                        'required' => true,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'jenis_kelamin',
                        'label' => 'Jenis Kelamin',
                        'type' => 'select',
                        'required' => true,
                        'default_value' => '',
                        'options' => ['Laki-laki', 'Perempuan']
                    ],
                    [
                        'name' => 'tempat_lahir',
                        'label' => 'Tempat Lahir',
                        'type' => 'text',
                        'required' => true,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'tanggal_lahir',
                        'label' => 'Tanggal Lahir',
                        'type' => 'date',
                        'required' => true,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'kewarganegaraan',
                        'label' => 'Kewarganegaraan',
                        'type' => 'text',
                        'required' => true,
                        'default_value' => 'WNI'
                    ],
                    [
                        'name' => 'agama',
                        'label' => 'Agama',
                        'type' => 'text',
                        'required' => true,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'pekerjaan',
                        'label' => 'Pekerjaan',
                        'type' => 'text',
                        'required' => true,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'pendidikan_terakhir',
                        'label' => 'Pendidikan Terakhir',
                        'type' => 'text',
                        'required' => true,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'bin',
                        'label' => 'Bin/Binti',
                        'type' => 'text',
                        'required' => true,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'alamat',
                        'label' => 'Alamat',
                        'type' => 'textarea',
                        'required' => true,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'status_kawin',
                        'label' => 'Status Perkawinan',
                        'type' => 'select',
                        'required' => true,
                        'default_value' => '',
                        'options' => ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']
                    ],
                    
                    // Data Ayah
                    [
                        'name' => 'nama_ayah',
                        'label' => 'Nama Ayah',
                        'type' => 'text',
                        'required' => false,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'nik_ayah',
                        'label' => 'NIK Ayah',
                        'type' => 'text',
                        'required' => false,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'tempat_lahir_ayah',
                        'label' => 'Tempat Lahir Ayah',
                        'type' => 'text',
                        'required' => false,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'tanggal_lahir_ayah',
                        'label' => 'Tanggal Lahir Ayah',
                        'type' => 'date',
                        'required' => false,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'kewarganegaraan_ayah',
                        'label' => 'Kewarganegaraan Ayah',
                        'type' => 'text',
                        'required' => false,
                        'default_value' => 'WNI'
                    ],
                    [
                        'name' => 'agama_ayah',
                        'label' => 'Agama Ayah',
                        'type' => 'text',
                        'required' => false,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'pekerjaan_ayah',
                        'label' => 'Pekerjaan Ayah',
                        'type' => 'text',
                        'required' => false,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'alamat_ayah',
                        'label' => 'Alamat Ayah',
                        'type' => 'textarea',
                        'required' => false,
                        'default_value' => ''
                    ],
                    
                    // Data Ibu
                    [
                        'name' => 'nama_ibu',
                        'label' => 'Nama Ibu',
                        'type' => 'text',
                        'required' => false,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'bin_ibu',
                        'label' => 'Bin/Binti Ibu',
                        'type' => 'text',
                        'required' => false,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'nik_ibu',
                        'label' => 'NIK Ibu',
                        'type' => 'text',
                        'required' => false,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'tempat_lahir_ibu',
                        'label' => 'Tempat Lahir Ibu',
                        'type' => 'text',
                        'required' => false,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'tanggal_lahir_ibu',
                        'label' => 'Tanggal Lahir Ibu',
                        'type' => 'date',
                        'required' => false,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'kewarganegaraan_ibu',
                        'label' => 'Kewarganegaraan Ibu',
                        'type' => 'text',
                        'required' => false,
                        'default_value' => 'WNI'
                    ],
                    [
                        'name' => 'agama_ibu',
                        'label' => 'Agama Ibu',
                        'type' => 'text',
                        'required' => false,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'pekerjaan_ibu',
                        'label' => 'Pekerjaan Ibu',
                        'type' => 'text',
                        'required' => false,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'alamat_ibu',
                        'label' => 'Alamat Ibu',
                        'type' => 'textarea',
                        'required' => false,
                        'default_value' => ''
                    ],
                    
                    // Meta fields
                    [
                        'name' => 'nomor',
                        'label' => 'Nomor Surat',
                        'type' => 'text',
                        'required' => false,
                        'default_value' => ''
                    ],
                    [
                        'name' => 'tanggal',
                        'label' => 'Tanggal Surat',
                        'type' => 'date',
                        'required' => false,
                        'default_value' => ''
                    ]
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "Kategori Surat 'Surat Pengantar Pernikahan' berhasil ditambahkan.\n";
        } else {
            echo "Kategori Surat 'Surat Pengantar Pernikahan' sudah ada.\n";
        }
    }
} 