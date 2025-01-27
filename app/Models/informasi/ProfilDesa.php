<?php

namespace App\Models\informasi;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilDesa extends Model
{
    use HasFactory;

    protected $table = 'informasi_profil_desa';
    
    protected $fillable = [
        'nama_desa',
        'kode_desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'alamat',
        'kode_pos',
        'telepon',
        'email',
        'sejarah',
        'visi',
        'misi',
        'logo',
        'foto_kantor',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 