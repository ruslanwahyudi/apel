<?php

namespace App\Models\Layanan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Layanan\IdentitasPemohon;
use App\Models\Layanan\Pelayanan;

class DataIdentitasPemohon extends Model
{
    protected $table = 'duk_data_identitas_pemohon';

    protected $fillable = [
        'pelayanan_id',
        'identitas_pemohon_id',
        'nilai'
    ];

    /**
     * Get the pelayanan that owns the data identitas.
     */
    public function pelayanan(): BelongsTo
    {
        return $this->belongsTo(Pelayanan::class, 'pelayanan_id');
    }

    /**
     * Get the identitas pemohon that owns the data.
     */
    public function identitasPemohon()
    {
        return $this->belongsTo(IdentitasPemohon::class, 'identitas_pemohon_id');
    }
} 