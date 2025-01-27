<?php

namespace App\Models\Layanan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PersyaratanDokumen extends Model
{
    use HasFactory;

    protected $table = 'duk_syarat_dokumen';

    protected $fillable = [
        'nama_dokumen',
        'jenis_pelayanan_id',
        'deskripsi',
        'required',
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function layanan()
    {
        return $this->belongsToMany(Layanan::class, 'layanan_persyaratan');
    }

    public function jenisLayanan()
    {
        return $this->belongsTo(JenisLayanan::class, 'jenis_pelayanan_id');
    }
} 