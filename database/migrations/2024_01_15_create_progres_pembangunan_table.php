<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('progres_pembangunan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan_pembangunan')->onDelete('cascade');
            $table->date('tanggal');
            $table->decimal('persentase', 5, 2); // Menyimpan persentase progres (misal: 45.50)
            $table->text('keterangan');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('progres_pembangunan');
    }
}; 