<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('informasi_perdes', function (Blueprint $table) {
            $table->id();
            $table->string('nomor');
            $table->year('tahun');
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('file')->nullable(); // untuk menyimpan file PDF perdes
            $table->date('tanggal_penetapan');
            $table->string('status')->default('berlaku'); // berlaku/tidak berlaku
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('informasi_perdes');
    }
}; 