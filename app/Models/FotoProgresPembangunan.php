<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoProgresPembangunan extends Model
{
    use HasFactory;

    protected $table = 'foto_progres_pembangunan';
    
    protected $fillable = [
        'progres_id',
        'foto',
        'caption'
    ];

    public function progres()
    {
        return $this->belongsTo(ProgresPembangunan::class, 'progres_id');
    }

    public function getFotoUrlAttribute()
    {
        return asset('storage/pembangunan/' . $this->foto);
    }
} 