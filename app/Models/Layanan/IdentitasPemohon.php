<?php

namespace App\Models\Layanan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IdentitasPemohon extends Model
{
    protected $table = 'duk_identitas_pemohon';

    protected $fillable = [
        'jenis_pelayanan_id',
        'nama_field',
        'tipe_field',
        'required'
    ];

    protected $casts = [
        'required' => 'boolean'
    ];

    /**
     * Get the jenis pelayanan that owns the identitas pemohon.
     */
    public function jenisPelayanan(): BelongsTo
    {
        return $this->belongsTo(JenisPelayanan::class, 'jenis_pelayanan_id');
    }

    /**
     * Get the data identitas pemohon for this identitas.
     */
    public function dataIdentitas(): HasMany
    {
        return $this->hasMany(DataIdentitasPemohon::class, 'identitas_pemohon_id');
    }
} 