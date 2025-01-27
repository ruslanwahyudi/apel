<?php

namespace App\Models\informasi;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perdes extends Model
{
    use HasFactory;

    protected $table = 'informasi_perdes';
    
    protected $fillable = [
        'nomor',
        'tahun',
        'judul',
        'deskripsi',
        'file',
        'tanggal_penetapan',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 