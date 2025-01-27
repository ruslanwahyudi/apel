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
        Schema::table('duk_pelayanan', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('surat_id')->nullable(); 
            $table->foreign('surat_id')->references('id')->on('register_surat')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('duk_pelayanan', function (Blueprint $table) {
            $table->dropForeign(['surat_id']);
            $table->dropColumn('surat_id');
        });
    }
};
