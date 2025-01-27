<?php

namespace App\Models\informasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Wisata extends Model
{
    use HasFactory;

    protected $table = 'informasi_wisata';

    protected $fillable = [
        'nama',
        'lokasi',
        'deskripsi',
        'fasilitas',
        'jam_buka',
        'jam_tutup',
        'harga_tiket',
        'kontak',
        'gambar',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 