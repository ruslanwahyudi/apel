<?php

namespace App\Models\adm;

use App\Models\Layanan\Pelayanan;
use App\Models\MasterOption;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class RegisterSurat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'register_surat';

    protected $fillable = [
        'nomor_surat',
        'urut_register',
        'jenis_surat',
        'perihal',
        'isi_ringkas',
        'isi_surat',
        'tujuan',
        'pengirim',
        'tanggal_surat',
        'tanggal_diterima',
        'lampiran',
        'status',
        'keterangan',
        'signer_id',
        'kategori_surat_id'
    ];

    protected $casts = [
        'tanggal_surat' => 'datetime',
        'tanggal_diterima' => 'datetime',
    ];

    protected $appends = ['layanan_data'];

    public function getLampiranUrlAttribute()
    {
        if ($this->lampiran) {
            return Storage::url('surat/lampiran/' . $this->lampiran);
        }
        return null;
    }

    public function statusSurat()
    {
        return $this->belongsTo(MasterOption::class, 'status', 'id');
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'signer_id');
    }

    public function kategori_surat()
    {
        return $this->belongsTo(KategoriSurat::class, 'kategori_surat_id');
    }

    public function layanan()
    {
        // Karena temp_surat_id sekarang adalah JSON array, kita perlu custom query
        return $this->hasOne(Pelayanan::class, 'id', 'id')
            ->whereRaw('JSON_CONTAINS(temp_surat_id, ?)', [json_encode($this->id)]);
    }

    // Helper method untuk mendapatkan pelayanan dengan cara yang lebih reliable
    public function getPelayanan()
    {
        return Pelayanan::whereRaw('JSON_CONTAINS(temp_surat_id, ?)', [json_encode($this->id)])->first();
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    // Helper method untuk mendapatkan data layanan lengkap
    public function getLayananData()
    {
        if (!$this->layanan) {
            return null;
        }

        $layanan = $this->layanan->load([
            'user',
            'jenisPelayanan', 
            'dataIdentitas.identitasPemohon',
            'dokumenPengajuan.syaratDokumen'
        ]);

        return $layanan;
    }

    // Helper method untuk check apakah ini surat layanan
    public function isLayananSurat()
    {
        return $this->layanan !== null;
    }

    // Accessor untuk layanan_data
    public function getLayananDataAttribute()
    {
        $layanan = $this->getPelayanan();
        if ($layanan) {
            $layanan->load([
                'user',
                'jenisPelayanan', 
                'dataIdentitas.identitasPemohon'
            ]);
            return $layanan;
        }
        return null;
    }
} 