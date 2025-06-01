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

    /**
     * Get the kategori surat for this jenis pelayanan.
     */
    public function kategoriSurat(): HasMany
    {
        return $this->hasMany(\App\Models\adm\KategoriSurat::class, 'jenis_pelayanan_id');
    }

    /**
     * Get unique classifications for this service type's identity fields
     */
    public function getUniqueKlasifikasi()
    {
        return KlasifikasiIdentitasPemohon::whereHas('identitasPemohon', function($query) {
            $query->where('jenis_pelayanan_id', $this->id);
        })->orderBy('urutan')->get();
    }
} 