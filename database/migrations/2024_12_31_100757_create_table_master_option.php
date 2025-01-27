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
        Schema::create('master_option', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('key');
            $table->string('value');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        DB::table('master_option')->insert([
            'type' => 'status_surat',
            'key' => '1',
            'value' => 'Draft',
            'description' => 'Draft',
        ]);
        DB::table('master_option')->insert([
            'type' => 'status_surat',
            'key' => '2',
            'value' => 'Proses',
            'description' => 'Proses',
        ]);
        DB::table('master_option')->insert([
            'type' => 'status_surat',
            'key' => '3',
            'value' => 'Selesai',
            'description' => 'Selesai',
        ]);
        DB::table('master_option')->insert([
            'type' => 'status_surat',
            'key' => '4',
            'value' => 'Ditolak',
            'description' => 'Ditolak',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_option');
    }
};
