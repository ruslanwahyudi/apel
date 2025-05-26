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
            $table->longText('header_template')->nullable()->after('template_variables');
            $table->json('header_variables')->nullable()->after('header_template');
            $table->enum('header_type', ['simple', 'full'])->default('simple')->after('header_variables');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori_surat', function (Blueprint $table) {
            $table->dropColumn(['header_template', 'header_variables', 'header_type']);
        });
    }
}; 