<?php

namespace App\Models\Layanan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SyaratDokumen extends Model
{
    protected $table = 'duk_syarat_dokumen';

    protected $fillable = [
        'jenis_pelayanan_id',
        'nama_dokumen',
        'deskripsi',
        'required'
    ];

    protected $casts = [
        'required' => 'boolean'
    ];

    /**
     * Get the jenis pelayanan that owns the syarat dokumen.
     */
    public function jenisPelayanan(): BelongsTo
    {
        return $this->belongsTo(JenisPelayanan::class, 'jenis_pelayanan_id');
    }

    /**
     * Get the dokumen pengajuan for this syarat.
     */
    public function dokumenPengajuan(): HasMany
    {
        return $this->hasMany(DokumenPengajuan::class, 'syarat_dokumen_id');
    }
} 