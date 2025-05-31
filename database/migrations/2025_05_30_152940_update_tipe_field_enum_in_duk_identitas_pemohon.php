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
        // Update tipe_field enum to support more field types
        DB::statement("ALTER TABLE duk_identitas_pemohon MODIFY COLUMN tipe_field ENUM('text', 'number', 'date', 'email', 'textarea', 'select', 'checkbox', 'radio') DEFAULT 'text'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE duk_identitas_pemohon MODIFY COLUMN tipe_field ENUM('text', 'number', 'date', 'email', 'textarea') DEFAULT 'text'");
    }
}; 