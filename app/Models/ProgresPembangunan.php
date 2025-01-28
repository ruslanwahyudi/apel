<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgresPembangunan extends Model
{
    use HasFactory;

    protected $table = 'progres_pembangunan';
    
    protected $fillable = [
        'kegiatan_id',
        'tanggal',
        'persentase',
        'keterangan',
        'user_id'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'persentase' => 'decimal:2'
    ];

    public function kegiatan()
    {
        return $this->belongsTo(KegiatanPembangunan::class, 'kegiatan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fotos()
    {
        return $this->hasMany(FotoProgresPembangunan::class, 'progres_id');
    }
} 