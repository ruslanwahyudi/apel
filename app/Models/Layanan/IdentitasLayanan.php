<?php

namespace App\Models\Layanan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Layanan\KlasifikasiIdentitasPemohon;

class IdentitasLayanan extends Model
{
    use HasFactory;

    protected $table = 'duk_identitas_pemohon';

    protected $fillable = [
        'jenis_pelayanan_id',
        'klasifikasi_id',
        'nama_field',
        'tipe_field',
        'required',
        'readonly',
    ];

    protected $casts = [
        'required' => 'boolean',
        'readonly' => 'boolean'
    ];

    public function jenis_pelayanan()
    {
        return $this->belongsTo(JenisLayanan::class);
    }

    public function layanan()
    {
        return $this->hasMany(Pelayanan::class);
    }

    public function klasifikasi()
    {
        return $this->belongsTo(KlasifikasiIdentitasPemohon::class, 'klasifikasi_id');
    }
} 