<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KopSuratConfig extends Model
{
    use HasFactory;

    protected $table = 'kop_surat_config';

    protected $fillable = [
        'logo_path',
        'kabupaten',
        'kecamatan',
        'desa',
        'alamat',
        'website1',
        'website2',
        'kepala_desa',
        'nip_kepala_desa',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the active kop surat configuration
     */
    public static function getActiveConfig()
    {
        return static::where('is_active', true)->first() ?? static::getDefaultConfig();
    }

    /**
     * Get default configuration if no active config exists
     */
    public static function getDefaultConfig()
    {
        return (object) [
            'logo_path' => 'assets/images/logo-pamekasan.png',
            'kabupaten' => 'PAMEKASAN',
            'kecamatan' => 'PALENGAAN',
            'desa' => 'BANYUPELLE',
            'alamat' => 'Jl. Raya Palengaan Proppo Cemkepak Desa Banyupelle 69362',
            'website1' => 'http://banyupelle.desa.id/',
            'website2' => 'www.banyupelle.desa.id/',
            'kepala_desa' => 'NAMA KEPALA DESA',
            'nip_kepala_desa' => '123456789012345678',
        ];
    }

    /**
     * Set this config as active and deactivate others
     */
    public function setAsActive()
    {
        // Deactivate all other configs
        static::where('id', '!=', $this->id)->update(['is_active' => false]);
        
        // Activate this config
        $this->update(['is_active' => true]);
    }
} 