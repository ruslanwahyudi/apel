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
        'signed_document_path',
        'surat_id'
    ];

    protected $casts = [
        'surat_id' => 'array',
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

    /**
     * Get the first register surat (for backward compatibility)
     */
    public function surat()
    {
        if (empty($this->surat_id) || !is_array($this->surat_id)) {
            return null;
        }
        
        $firstSuratId = $this->surat_id[0] ?? null;
        if (!$firstSuratId) {
            return null;
        }
        
        return $this->hasOne(RegisterSurat::class, 'id', 'id')->where('id', $firstSuratId);
    }

    /**
     * Get all register surat for this pelayanan
     */
    public function allSurat()
    {
        if (empty($this->surat_id) || !is_array($this->surat_id)) {
            return collect();
        }
        
        return RegisterSurat::whereIn('id', $this->surat_id)->get();
    }

    /**
     * Add a register surat ID to the array
     */
    public function addSuratId($suratId)
    {
        $currentIds = $this->surat_id ?? [];
        if (!in_array($suratId, $currentIds)) {
            $currentIds[] = $suratId;
            $this->surat_id = $currentIds;
            $this->save();
        }
    }

    /**
     * Remove a register surat ID from the array
     */
    public function removeSuratId($suratId)
    {
        $currentIds = $this->surat_id ?? [];
        $newIds = array_filter($currentIds, function($id) use ($suratId) {
            return $id != $suratId;
        });
        $this->surat_id = array_values($newIds); // Reindex array
        $this->save();
    }

    /**
     * Check if pelayanan has any register surat
     */
    public function hasSurat()
    {
        return !empty($this->surat_id) && is_array($this->surat_id) && count($this->surat_id) > 0;
    }

    /**
     * Get count of register surat
     */
    public function getSuratCount()
    {
        return is_array($this->surat_id) ? count($this->surat_id) : 0;
    }
} 