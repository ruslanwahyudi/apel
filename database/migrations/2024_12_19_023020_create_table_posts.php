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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul berita
            $table->string('slug')->unique(); // Slug unik untuk URL
            $table->text('content'); // Konten berita
            $table->string('image')->nullable(); // Menyimpan path gambar
            $table->foreignId('kategori_id')->constrained('blog_kategori')->onDelete('cascade'); // Relasi ke kategori
            $table->json('label_id')->nullable(); // Menyimpan array label
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Relasi ke pengguna (opsional)
            $table->timestamp('published_at')->nullable(); // Waktu publikasi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
