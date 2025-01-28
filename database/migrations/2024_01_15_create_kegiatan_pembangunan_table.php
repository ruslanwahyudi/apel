<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kegiatan_pembangunan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan');
            $table->text('deskripsi');
            $table->string('lokasi');
            $table->decimal('anggaran', 15, 2);
            $table->string('sumber_dana');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('pelaksana');
            $table->enum('status', ['Belum Dimulai', 'Dalam Pengerjaan', 'Selesai', 'Terhenti'])->default('Belum Dimulai');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kegiatan_pembangunan');
    }
}; 