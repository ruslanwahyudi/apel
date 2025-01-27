<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('duk_syarat_dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_pelayanan_id')->constrained('duk_jenis_pelayanan')->onDelete('cascade');
            $table->string('nama_dokumen');
            $table->text('deskripsi')->nullable();
            $table->boolean('required')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duk_syarat_dokumen');
    }
};
