<?php

namespace App\Models\Layanan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPelayanan extends Model
{
    protected $table = 'duk_jenis_pelayanan';

    protected $fillable = [
        'nama_pelayanan',
        'deskripsi'
    ];

    /**
     * Get the pelayanan for this jenis.
     */
    public function pelayanan(): HasMany
    {
        return $this->hasMany(Pelayanan::class, 'jenis_pelayanan_id');
    }

    /**
     * Get the identitas pemohon for this jenis.
     */
    public function identitasPemohon(): HasMany
    {
        return $this->hasMany(IdentitasPemohon::class, 'jenis_pelayanan_id');
    }

    /**
     * Get the syarat dokumen for this jenis.
     */
    public function syaratDokumen(): HasMany
    {
        return $this->hasMany(SyaratDokumen::class, 'jenis_pelayanan_id');
    }
} 