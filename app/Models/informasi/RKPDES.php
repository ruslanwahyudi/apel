<?php

namespace App\Models\informasi;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RKPDES extends Model
{
    use HasFactory;

    protected $table = 'informasi_rkpdes';
    
    protected $fillable = [
        'nomor',
        'tahun_anggaran',
        'program',
        'kegiatan',
        'lokasi',
        'anggaran',
        'sumber_dana',
        'target',
        'sasaran',
        'status',
        'keterangan',
        'file'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 