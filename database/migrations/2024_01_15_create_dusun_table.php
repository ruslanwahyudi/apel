<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dusun', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dusun');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('jumlah_kk');
            $table->integer('jumlah_pr');
            $table->integer('jumlah_lk');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dusun');
    }
}; 