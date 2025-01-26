<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['unverified', 'pending_verification', 'verified', 'rejected'])
                  ->default('unverified')
                  ->after('remember_token');
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->enum('status_verifikasi', ['pending', 'approved', 'rejected'])
                  ->nullable()
                  ->after('agama');
            $table->text('keterangan_verifikasi')->nullable()->after('status_verifikasi');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['status_verifikasi', 'keterangan_verifikasi']);
        });
    }
}; 