<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KegiatanPembangunan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan_pembangunan';
    
    protected $fillable = [
        'nama_kegiatan',
        'deskripsi',
        'lokasi',
        'anggaran',
        'sumber_dana',
        'tanggal_mulai',
        'tanggal_selesai',
        'pelaksana',
        'status',
        'user_id'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'anggaran' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function progres()
    {
        return $this->hasMany(ProgresPembangunan::class, 'kegiatan_id');
    }

    public function getLatestProgresAttribute()
    {
        return $this->progres()->latest()->first();
    }
} 