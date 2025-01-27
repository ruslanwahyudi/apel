<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('informasi_rkpdes', function (Blueprint $table) {
            $table->id();
            $table->string('nomor');
            $table->year('tahun_anggaran');
            $table->string('program');
            $table->string('kegiatan');
            $table->text('lokasi');
            $table->decimal('anggaran', 15, 2);
            $table->string('sumber_dana');
            $table->text('target')->nullable();
            $table->text('sasaran')->nullable();
            $table->string('status')->default('direncanakan'); // direncanakan/berlangsung/selesai
            $table->text('keterangan')->nullable();
            $table->string('file')->nullable(); // untuk menyimpan file PDF RKPDes
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('informasi_rkpdes');
    }
}; 