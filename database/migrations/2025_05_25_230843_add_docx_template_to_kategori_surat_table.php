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
            $table->string('docx_template_path')->nullable()->after('pdf_template_path');
            $table->json('docx_form_fields')->nullable()->after('docx_template_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori_surat', function (Blueprint $table) {
            $table->dropColumn(['docx_template_path', 'docx_form_fields']);
        });
    }
};
