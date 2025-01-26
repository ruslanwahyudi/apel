<?php

namespace App\Models\Layanan;

use Illuminate\Database\Eloquent\Model;

class DokumenPengajuan extends Model
{
    protected $table = 'duk_dokumen_pengajuan';
    
    protected $fillable = [
        'pelayanan_id',
        'syarat_dokumen_id',
        'path_dokumen',
        'uploaded_at'
    ];

    protected $dates = [
        'uploaded_at'
    ];

    public function pelayanan()
    {
        return $this->belongsTo(Pelayanan::class, 'pelayanan_id');
    }

    public function syaratDokumen()
    {
        return $this->belongsTo(SyaratDokumen::class, 'syarat_dokumen_id');
    }
} 