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
            $table->longText('template_surat')->nullable()->after('nama');
            $table->json('template_variables')->nullable()->after('template_surat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori_surat', function (Blueprint $table) {
            $table->dropColumn(['template_surat', 'template_variables']);
        });
    }
}; 