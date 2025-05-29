<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kategori_surat', function (Blueprint $table) {
            $table->string('blade_template_name')->nullable()->after('nama');
            $table->json('template_variables')->nullable()->after('blade_template_name');
            $table->enum('template_type', ['blade', 'text', 'pdf', 'docx'])->default('blade')->after('template_variables');
        });
    }

    public function down()
    {
        Schema::table('kategori_surat', function (Blueprint $table) {
            $table->dropColumn(['blade_template_name', 'template_variables', 'template_type']);
        });
    }
}; 