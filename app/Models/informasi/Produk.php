<?php

namespace App\Models\informasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'informasi_produk_unggulan';

    protected $fillable = [

        'nama_produk',
        'kategori',
        'harga',
        'stok',
        'deskripsi',
        'spesifikasi',
        'gambar',
        'status' ,
        'kontak',
        'lokasi_penjualan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 