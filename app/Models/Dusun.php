<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dusun extends Model
{
    use HasFactory;

    protected $table = 'dusun';
    
    protected $fillable = [
        'nama_dusun',
        'user_id',
        'jumlah_kk',
        'jumlah_pr', 
        'jumlah_lk'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 