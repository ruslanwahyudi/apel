<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('foto_progres_pembangunan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('progres_id')->constrained('progres_pembangunan')->onDelete('cascade');
            $table->string('foto');
            $table->string('caption')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('foto_progres_pembangunan');
    }
}; 