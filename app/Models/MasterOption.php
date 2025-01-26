<?php

namespace App\Models;

use App\Models\adm\RegisterSurat;
use App\Models\layanan\Layanan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterOption extends Model
{
    use HasFactory;

    protected $table = 'master_option';
    protected $fillable = ['type', 'key', 'value', 'description'];

    public function dukPelayanan()
    {
        return $this->hasMany(Layanan::class, 'status_layanan', 'key');
    }

    public function registerSurat()
    {
        return $this->hasMany(RegisterSurat::class, 'status_surat', 'key');
    }
}
