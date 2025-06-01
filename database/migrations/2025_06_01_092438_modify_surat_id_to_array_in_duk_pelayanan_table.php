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
        // Add new column for array of surat IDs
        Schema::table('duk_pelayanan', function (Blueprint $table) {
            $table->text('surat_ids')->nullable()->after('surat_id');
        });

        // Convert existing single surat_id to JSON array format in new column
        $pelayananRecords = DB::table('duk_pelayanan')
            ->whereNotNull('surat_id')
            ->get(['id', 'surat_id']);

        foreach ($pelayananRecords as $record) {
            if ($record->surat_id) {
                DB::table('duk_pelayanan')
                    ->where('id', $record->id)
                    ->update(['surat_ids' => json_encode([$record->surat_id])]);
            }
        }

        // Copy remaining null values
        DB::table('duk_pelayanan')
            ->whereNull('surat_id')
            ->update(['surat_ids' => null]);

        // Drop the old column's foreign key constraint first
        try {
            Schema::table('duk_pelayanan', function (Blueprint $table) {
                $table->dropForeign(['surat_id']);
            });
        } catch (\Exception $e) {
            // Foreign key might not exist, continue
        }

        // Drop the old column
        Schema::table('duk_pelayanan', function (Blueprint $table) {
            $table->dropColumn('surat_id');
        });

        // Rename the new column to replace the old one
        Schema::table('duk_pelayanan', function (Blueprint $table) {
            $table->renameColumn('surat_ids', 'surat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add new column for single surat ID
        Schema::table('duk_pelayanan', function (Blueprint $table) {
            $table->unsignedBigInteger('surat_id_temp')->nullable()->after('surat_id');
        });

        // Convert JSON array back to single integer (take first value)
        $pelayananRecords = DB::table('duk_pelayanan')
            ->whereNotNull('surat_id')
            ->get(['id', 'surat_id']);

        foreach ($pelayananRecords as $record) {
            if ($record->surat_id) {
                $suratIds = json_decode($record->surat_id, true);
                if (is_array($suratIds) && !empty($suratIds)) {
                    DB::table('duk_pelayanan')
                        ->where('id', $record->id)
                        ->update(['surat_id_temp' => $suratIds[0]]);
                }
            }
        }

        // Drop the JSON column
        Schema::table('duk_pelayanan', function (Blueprint $table) {
            $table->dropColumn('surat_id');
        });

        // Rename temp column back to surat_id
        Schema::table('duk_pelayanan', function (Blueprint $table) {
            $table->renameColumn('surat_id_temp', 'surat_id');
        });

        // Add foreign key constraint back
        try {
            Schema::table('duk_pelayanan', function (Blueprint $table) {
                $table->foreign('surat_id')->references('id')->on('register_surat')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Might fail if register_surat table doesn't exist
        }
    }
};
