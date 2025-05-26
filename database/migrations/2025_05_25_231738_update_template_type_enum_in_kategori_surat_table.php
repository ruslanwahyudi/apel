<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update template_type column to support 'docx' value
        DB::statement("ALTER TABLE kategori_surat MODIFY COLUMN template_type ENUM('text', 'pdf', 'docx') DEFAULT 'text'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE kategori_surat MODIFY COLUMN template_type ENUM('text', 'pdf') DEFAULT 'text'");
    }
};
