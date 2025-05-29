<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Layanan\Pelayanan;
use App\Models\Layanan\DataIdentitasPemohon;
use App\Models\User;
use App\Models\MasterOption;

class TestEditLayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada user dan status
        $user = User::first();
        $statusLayanan = MasterOption::where(['value' => 'Draft', 'type' => 'status_layanan'])->first();
        
        if (!$user || !$statusLayanan) {
            $this->command->info('User atau status layanan tidak ditemukan. Pastikan seeder sudah dijalankan.');
            return;
        }

        // Cek apakah sudah ada data test
        $existingPelayanan = Pelayanan::where('catatan', 'LIKE', '%Test edit%')->first();
        if ($existingPelayanan) {
            $this->command->info('Data test edit sudah ada.');
        } else {
            // Ambil jenis layanan yang ada
            $jenisLayanan = \App\Models\Layanan\JenisPelayanan::first();
            if (!$jenisLayanan) {
                $this->command->info('Jenis layanan tidak ditemukan. Jalankan SampleLayananSeeder terlebih dahulu.');
                return;
            }

            // Buat pelayanan test untuk edit
            $pelayanan = Pelayanan::create([
                'user_id' => $user->id,
                'jenis_pelayanan_id' => $jenisLayanan->id,
                'catatan' => 'Test edit pelayanan - data untuk testing form edit',
                'status_layanan' => $statusLayanan->id
            ]);

            // Ambil identitas pemohon dan buat data test
            $identitasPemohon = \App\Models\Layanan\IdentitasPemohon::where('jenis_pelayanan_id', $jenisLayanan->id)
                ->limit(5)
                ->get();

            foreach ($identitasPemohon as $identitas) {
                \App\Models\Layanan\DataIdentitasPemohon::create([
                    'pelayanan_id' => $pelayanan->id,
                    'identitas_pemohon_id' => $identitas->id,
                    'nilai' => 'Test Data ' . $identitas->nama_field
                ]);
            }

            $this->command->info('Data test edit pelayanan berhasil dibuat dengan ID: ' . $pelayanan->id);
        }

        // Cek apakah sudah ada data test untuk delete
        $existingDeleteTest = Pelayanan::where('catatan', 'LIKE', '%Test delete%')->first();
        if ($existingDeleteTest) {
            $this->command->info('Data test delete sudah ada.');
        } else {
            // Ambil jenis layanan yang ada
            $jenisLayanan = \App\Models\Layanan\JenisPelayanan::first();
            if (!$jenisLayanan) {
                $this->command->info('Jenis layanan tidak ditemukan. Jalankan SampleLayananSeeder terlebih dahulu.');
                return;
            }

            // Buat pelayanan test untuk delete
            $pelayananDelete = Pelayanan::create([
                'user_id' => $user->id,
                'jenis_pelayanan_id' => $jenisLayanan->id,
                'catatan' => 'Test delete pelayanan - data untuk testing delete functionality',
                'status_layanan' => $statusLayanan->id
            ]);

            // Ambil identitas pemohon dan buat data test
            $identitasPemohon = \App\Models\Layanan\IdentitasPemohon::where('jenis_pelayanan_id', $jenisLayanan->id)
                ->limit(3)
                ->get();

            foreach ($identitasPemohon as $identitas) {
                \App\Models\Layanan\DataIdentitasPemohon::create([
                    'pelayanan_id' => $pelayananDelete->id,
                    'identitas_pemohon_id' => $identitas->id,
                    'nilai' => 'Test Delete Data ' . $identitas->nama_field
                ]);
            }

            $this->command->info('Data test delete pelayanan berhasil dibuat dengan ID: ' . $pelayananDelete->id);
        }
    }
}
