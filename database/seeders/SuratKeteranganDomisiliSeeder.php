<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuratKeteranganDomisiliSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat atau update jenis pelayanan untuk Surat Keterangan Domisili
        DB::table('duk_jenis_pelayanan')->updateOrInsert(
            ['nama_pelayanan' => 'Surat Keterangan Domisili'],
            [
                'deskripsi' => 'Layanan pembuatan surat keterangan domisili untuk warga desa',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $jenis = DB::table('duk_jenis_pelayanan')
            ->where('nama_pelayanan', 'Surat Keterangan Domisili')
            ->first();

        $jenisPelayananId = $jenis->id;

        // Hapus field identitas yang sudah ada untuk jenis pelayanan ini
        DB::table('duk_identitas_pemohon')
            ->where('jenis_pelayanan_id', $jenisPelayananId)
            ->delete();

        // Buat field-field identitas pemohon untuk Surat Keterangan Domisili
        $identitasFields = [
            [
                'jenis_pelayanan_id' => $jenisPelayananId,
                'klasifikasi_id' => null,
                'nama_field' => 'nama',
                'label' => 'Nama Lengkap',
                'tipe_field' => 'text',
                'required' => true,
                'readonly' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pelayanan_id' => $jenisPelayananId,
                'klasifikasi_id' => null,
                'nama_field' => 'tempat_lahir',
                'label' => 'Tempat Lahir',
                'tipe_field' => 'text',
                'required' => true,
                'readonly' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pelayanan_id' => $jenisPelayananId,
                'klasifikasi_id' => null,
                'nama_field' => 'tanggal_lahir',
                'label' => 'Tanggal Lahir',
                'tipe_field' => 'date',
                'required' => true,
                'readonly' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pelayanan_id' => $jenisPelayananId,
                'klasifikasi_id' => null,
                'nama_field' => 'jenis_kelamin',
                'label' => 'Jenis Kelamin',
                'tipe_field' => 'select',
                'required' => true,
                'readonly' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pelayanan_id' => $jenisPelayananId,
                'klasifikasi_id' => null,
                'nama_field' => 'agama',
                'label' => 'Agama',
                'tipe_field' => 'select',
                'required' => true,
                'readonly' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pelayanan_id' => $jenisPelayananId,
                'klasifikasi_id' => null,
                'nama_field' => 'pekerjaan',
                'label' => 'Pekerjaan',
                'tipe_field' => 'text',
                'required' => true,
                'readonly' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pelayanan_id' => $jenisPelayananId,
                'klasifikasi_id' => null,
                'nama_field' => 'alamat',
                'label' => 'Alamat',
                'tipe_field' => 'textarea',
                'required' => true,
                'readonly' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pelayanan_id' => $jenisPelayananId,
                'klasifikasi_id' => null,
                'nama_field' => 'keperluan',
                'label' => 'Keperluan',
                'tipe_field' => 'textarea',
                'required' => false,
                'readonly' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('duk_identitas_pemohon')->insert($identitasFields);

        // Hapus syarat dokumen yang sudah ada untuk jenis pelayanan ini
        DB::table('duk_syarat_dokumen')
            ->where('jenis_pelayanan_id', $jenisPelayananId)
            ->delete();

        // Buat syarat dokumen untuk Surat Keterangan Domisili
        $syaratDokumen = [
            [
                'jenis_pelayanan_id' => $jenisPelayananId,
                'nama_dokumen' => 'Fotocopy KTP',
                'deskripsi' => 'Fotocopy Kartu Tanda Penduduk yang masih berlaku',
                'required' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pelayanan_id' => $jenisPelayananId,
                'nama_dokumen' => 'Fotocopy Kartu Keluarga',
                'deskripsi' => 'Fotocopy Kartu Keluarga yang masih berlaku',
                'required' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'jenis_pelayanan_id' => $jenisPelayananId,
                'nama_dokumen' => 'Surat Keterangan RT/RW',
                'deskripsi' => 'Surat keterangan domisili dari RT/RW setempat',
                'required' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('duk_syarat_dokumen')->insert($syaratDokumen);

        // Hapus atau update kategori surat yang sudah ada
        DB::table('kategori_surat')
            ->where('nama', 'Surat Keterangan Domisili')
            ->delete();

        // Buat kategori surat untuk Surat Keterangan Domisili
        DB::table('kategori_surat')->insert([
            'nama' => 'Surat Keterangan Domisili',
            'blade_template_name' => 'surat-keterangan-domisili',
            'blade_template_variables' => json_encode([
                [
                    'name' => 'nama',
                    'label' => 'Nama Lengkap',
                    'type' => 'text',
                    'required' => true,
                    'default_value' => ''
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
                    'name' => 'jenis_kelamin',
                    'label' => 'Jenis Kelamin',
                    'type' => 'select',
                    'required' => true,
                    'default_value' => '',
                    'options' => ['Laki-laki', 'Perempuan']
                ],
                [
                    'name' => 'agama',
                    'label' => 'Agama',
                    'type' => 'select',
                    'required' => true,
                    'default_value' => '',
                    'options' => ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu']
                ],
                [
                    'name' => 'pekerjaan',
                    'label' => 'Pekerjaan',
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
                    'name' => 'keperluan',
                    'label' => 'Keperluan',
                    'type' => 'textarea',
                    'required' => false,
                    'default_value' => ''
                ],
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

        echo "✅ Seeder Surat Keterangan Domisili berhasil dijalankan!\n";
        echo "📋 Jenis Pelayanan ID: {$jenisPelayananId}\n";
        echo "📝 Field identitas: " . count($identitasFields) . " field\n";
        echo "📄 Syarat dokumen: " . count($syaratDokumen) . " dokumen\n";
        echo "📋 Kategori surat: 1 kategori\n";
    }
}
