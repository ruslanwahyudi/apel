<?php

namespace App\Models\Layanan;

use App\Models\adm\RegisterSurat;
use App\Models\MasterOption;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelayanan extends Model
{
    protected $table = 'duk_pelayanan';

    protected $fillable = [
        'user_id',
        'jenis_pelayanan_id',
        'catatan',
        'status_layanan',
        'signed_document_path'
    ];

    /**
     * Get the user that owns the pelayanan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the jenis pelayanan that owns the pelayanan.
     */
    public function jenisPelayanan(): BelongsTo
    {
        return $this->belongsTo(JenisPelayanan::class, 'jenis_pelayanan_id');
    }

    public function status()
    {
        return $this->belongsTo(MasterOption::class, 'status_layanan', 'id');
    }

    /**
     * Get the dokumen pengajuan for this pelayanan.
     */
    public function dokumenPengajuan(): HasMany
    {
        return $this->hasMany(DokumenPengajuan::class, 'pelayanan_id');
    }

    /**
     * Get the data identitas pemohon for this pelayanan.
     */
    public function dataIdentitas(): HasMany
    {
        return $this->hasMany(DataIdentitasPemohon::class, 'pelayanan_id');
    }

    public function statusLayanan()
    {
        return $this->belongsTo(MasterOption::class, 'status_layanan', 'id');
    }

    public function surat()
    {
        return $this->hasOne(RegisterSurat::class, 'id', 'surat_id');
    }   
} 