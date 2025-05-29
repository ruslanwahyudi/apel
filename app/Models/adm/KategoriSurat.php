<?php

namespace App\Models\adm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Layanan\JenisPelayanan;

class KategoriSurat extends Model
{
    use HasFactory;

    protected $table = 'kategori_surat';
    protected $fillable = [
        'nama', 
        'blade_template_name', 
        'blade_template_variables', 
        'template_surat', 
        'template_variables', 
        'header_template', 
        'header_variables', 
        'header_type', 
        'pdf_template_path', 
        'pdf_form_fields', 
        'template_type', 
        'docx_template_path', 
        'docx_form_fields',
        'tipe_surat',
        'jenis_pelayanan_id'
    ];

    protected $casts = [
        'blade_template_variables' => 'array',
        'template_variables' => 'array',
        'header_variables' => 'array',
        'pdf_form_fields' => 'array',
        'docx_form_fields' => 'array'
    ];

    public function surat()
    {
        return $this->hasMany(RegisterSurat::class, 'kategori_id');
    }

    // Relasi ke JenisPelayanan
    public function jenisPelayanan()
    {
        return $this->belongsTo(JenisPelayanan::class, 'jenis_pelayanan_id');
    }

    // Method untuk mengecek apakah surat ini adalah surat layanan
    public function isLayanan()
    {
        return $this->tipe_surat === 'layanan';
    }

    // Method untuk mengecek apakah surat ini adalah surat non-layanan
    public function isNonLayanan()
    {
        return $this->tipe_surat === 'non_layanan';
    }

    // Method untuk mendapatkan path template blade
    public function getBladeTemplatePath()
    {
        if (!$this->blade_template_name) {
            return null;
        }
        
        return "templates.surat.{$this->blade_template_name}";
    }

    // Method untuk check apakah template blade exists
    public function hasBladeTemplate()
    {
        if (!$this->blade_template_name) {
            return false;
        }
        
        $templatePath = resource_path("views/templates/surat/{$this->blade_template_name}.blade.php");
        return file_exists($templatePath);
    }

    // Method untuk mendapatkan variables berdasarkan tipe surat
    public function getVariables($pemohonId = null)
    {
        if ($this->isLayanan() && $pemohonId) {
            return $this->getLayananVariables($pemohonId);
        }
        
        return $this->getNonLayananVariables();
    }

    // Method untuk mendapatkan variables dari DUK (untuk surat layanan)
    private function getLayananVariables($pemohonId)
    {
        \Log::info('Getting DUK variables for pemohon', [
            'pemohon_id' => $pemohonId
        ]);
        
        // Ambil data dari duk_data_identitas_pemohon berdasarkan pelayanan_id (pemohonId)
        $dataIdentitas = \DB::table('duk_data_identitas_pemohon as ddip')
            ->join('duk_identitas_pemohon as dip', 'ddip.identitas_pemohon_id', '=', 'dip.id')
            ->where('ddip.pelayanan_id', $pemohonId)
            ->select('dip.nama_field', 'ddip.nilai')
            ->get();

        \Log::info('DUK query result', [
            'pemohon_id' => $pemohonId,
            'raw_data' => $dataIdentitas->toArray(),
            'count' => $dataIdentitas->count()
        ]);

        $variables = [];
        
        // Konversi data ke format yang diharapkan
        foreach ($dataIdentitas as $data) {
            $variables[$data->nama_field] = $data->nilai ?? '';
            \Log::info('Processing DUK field', [
                'field_name' => $data->nama_field,
                'field_value' => $data->nilai
            ]);
        }

        \Log::info('Final DUK variables', [
            'pemohon_id' => $pemohonId,
            'variables' => $variables
        ]);

        return $variables;
    }

    // Method untuk mendapatkan variables default (untuk surat non-layanan)
    private function getNonLayananVariables()
    {
        // Return empty array atau default values
        return [];
    }
} 