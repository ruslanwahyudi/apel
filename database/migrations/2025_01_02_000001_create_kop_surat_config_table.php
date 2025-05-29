<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kop_surat_config', function (Blueprint $table) {
            $table->id();
            $table->string('logo_path')->default('assets/images/logo-pamekasan.png');
            $table->string('kabupaten')->default('PAMEKASAN');
            $table->string('kecamatan')->default('PALENGAAN');
            $table->string('desa')->default('BANYUPELLE');
            $table->text('alamat')->default('Jl. Raya Palengaan Proppo Cemkepak Desa Banyupelle 69362');
            $table->string('website1')->default('http://banyupelle.desa.id/');
            $table->string('website2')->default('www.banyupelle.desa.id/');
            $table->string('kepala_desa')->nullable();
            $table->string('nip_kepala_desa')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default configuration
        DB::table('kop_surat_config')->insert([
            'logo_path' => 'assets/images/logo-pamekasan.png',
            'kabupaten' => 'PAMEKASAN',
            'kecamatan' => 'PALENGAAN',
            'desa' => 'BANYUPELLE',
            'alamat' => 'Jl. Raya Palengaan Proppo Cemkepak Desa Banyupelle 69362',
            'website1' => 'http://banyupelle.desa.id/',
            'website2' => 'www.banyupelle.desa.id/',
            'kepala_desa' => 'NAMA KEPALA DESA',
            'nip_kepala_desa' => '123456789012345678',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kop_surat_config');
    }
}; 