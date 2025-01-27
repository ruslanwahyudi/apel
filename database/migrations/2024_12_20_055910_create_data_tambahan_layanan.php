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
        Schema::create('duk_identitas_pemohon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_pelayanan_id')->constrained('duk_jenis_pelayanan')->onDelete('cascade');
            $table->string('nama_field');
            $table->enum('tipe_field', ['text', 'number', 'date', 'email', 'textarea']);
            $table->boolean('required')->default(true);
            $table->timestamps();
        });

        Schema::create('duk_data_identitas_pemohon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelayanan_id')->constrained('duk_pelayanan')->onDelete('cascade');
            $table->foreignId('identitas_pemohon_id')->constrained('duk_identitas_pemohon')->onDelete('cascade');
            $table->text('nilai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duk_data_identitas_pemohon');
        Schema::dropIfExists('duk_identitas_pemohon');
    }
};
