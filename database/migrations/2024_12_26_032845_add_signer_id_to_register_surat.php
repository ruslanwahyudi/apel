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
        Schema::table('register_surat', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('signer_id')->nullable();
            $table->foreign('signer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('register_surat', function (Blueprint $table) {
            //
            $table->dropForeign(['signer_id']);
            $table->dropColumn('signer_id');
        });
    }
};
