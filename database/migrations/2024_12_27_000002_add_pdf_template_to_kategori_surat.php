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
            $table->string('pdf_template_path')->nullable()->after('header_type');
            $table->json('pdf_form_fields')->nullable()->after('pdf_template_path');
            $table->enum('template_type', ['text', 'pdf'])->default('text')->after('pdf_form_fields');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori_surat', function (Blueprint $table) {
            $table->dropColumn(['pdf_template_path', 'pdf_form_fields', 'template_type']);
        });
    }
}; 