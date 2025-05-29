<?php

namespace App\Models\Layanan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KlasifikasiIdentitasPemohon extends Model
{
    use HasFactory;

    protected $table = 'duk_klasifikasi_identitas_pemohon';

    protected $fillable = [
        'nama_klasifikasi',
        'deskripsi',
        'urutan',
        'status'
    ];

    /**
     * Get the identitas pemohon for this klasifikasi.
     */
    public function identitasPemohon(): HasMany
    {
        return $this->hasMany(IdentitasPemohon::class, 'klasifikasi_id');
    }
}
