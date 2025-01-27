<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('informasi_wisata', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('lokasi');
            $table->text('deskripsi');
            $table->text('fasilitas')->nullable();
            $table->string('jam_buka')->nullable();
            $table->string('jam_tutup')->nullable();
            $table->decimal('harga_tiket', 15, 2)->nullable();
            $table->string('kontak')->nullable();
            $table->string('gambar')->nullable();
            $table->text('petunjuk_arah')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('informasi_wisata');
    }
}; 