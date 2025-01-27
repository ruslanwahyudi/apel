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
        Schema::table('register_surat', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('kategori_surat_id')->nullable();
            $table->foreign('kategori_surat_id')->references('id')->on('kategori_surat')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('register_surat', function (Blueprint $table) {
            $table->dropForeign(['kategori_surat_id']);
            $table->dropColumn('kategori_surat_id');
        });
    }
};
