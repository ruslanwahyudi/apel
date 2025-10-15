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
            $table->string('signed_pdf_path')->nullable()->after('lampiran')->comment('Path ke file PDF yang sudah ditandatangani');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('register_surat', function (Blueprint $table) {
            $table->dropColumn('signed_pdf_path');
        });
    }
}; 