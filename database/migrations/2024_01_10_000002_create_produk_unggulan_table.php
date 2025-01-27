<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('informasi_produk_unggulan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->string('kategori');
            $table->decimal('harga', 15, 2);
            $table->integer('stok');
            $table->text('deskripsi');
            $table->text('spesifikasi')->nullable();
            $table->string('gambar')->nullable();
            $table->boolean('status')->default(true);
            $table->string('kontak')->nullable();
            $table->string('lokasi_penjualan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('informasi_produk_unggulan');
    }
}; 