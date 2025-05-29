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
        Schema::table('kategori_surat', function (Blueprint $table) {
            $table->enum('tipe_surat', ['layanan', 'non_layanan'])->default('non_layanan')->after('template_type');
            $table->unsignedBigInteger('jenis_pelayanan_id')->nullable()->after('tipe_surat');
            
            // Add foreign key constraint
            $table->foreign('jenis_pelayanan_id')->references('id')->on('jenis_pelayanan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori_surat', function (Blueprint $table) {
            $table->dropForeign(['jenis_pelayanan_id']);
            $table->dropColumn(['tipe_surat', 'jenis_pelayanan_id']);
        });
    }
};
