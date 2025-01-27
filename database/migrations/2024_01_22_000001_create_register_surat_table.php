<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('register_surat', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->string('jenis_surat');
            $table->string('perihal');
            $table->text('isi_ringkas');
            $table->text('isi_surat');
            $table->string('tujuan');
            $table->string('pengirim');
            $table->date('tanggal_surat');
            $table->date('tanggal_diterima');
            $table->string('lampiran')->nullable();
            $table->enum('status', ['draft', 'diproses', 'selesai', 'ditolak'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('register_surat');
    }
}; 