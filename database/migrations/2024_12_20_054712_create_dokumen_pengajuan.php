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
        Schema::create('duk_dokumen_pengajuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelayanan_id')->constrained('duk_pelayanan')->onDelete('cascade');
            $table->foreignId('syarat_dokumen_id')->constrained('duk_syarat_dokumen')->onDelete('cascade');
            $table->string('path_dokumen');
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duk_dokumen_pengajuan');
    }
};
