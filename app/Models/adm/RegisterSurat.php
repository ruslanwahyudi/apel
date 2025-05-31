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
        return $this->hasOne(Pelayanan::class, 'surat_id', 'id');
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
} 