<?php

namespace App\Models\adm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSurat extends Model
{
    use HasFactory;

    protected $table = 'kategori_surat';
    protected $fillable = ['nama'];

    public function surat()
    {
        return $this->hasMany(RegisterSurat::class, 'kategori_id');
    }
    
} 