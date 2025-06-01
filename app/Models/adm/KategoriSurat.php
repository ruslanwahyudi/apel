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

        // Ambil data bukti kepemilikan dari duk_dokumen_pengajuan
        $buktiKepemilikan = $this->getBuktiKepemilikan($pemohonId);
        if (!empty($buktiKepemilikan)) {
            $variables['bukti_kepemilikan'] = $buktiKepemilikan;
        }

        \Log::info('Final DUK variables', [
            'pemohon_id' => $pemohonId,
            'variables' => $variables
        ]);

        return $variables;
    }

    // Method untuk mendapatkan bukti kepemilikan dari dokumen pengajuan
    private function getBuktiKepemilikan($pemohonId)
    {
        \Log::info('Getting bukti kepemilikan for pemohon', [
            'pemohon_id' => $pemohonId
        ]);

        // Ambil semua dokumen yang diupload untuk pelayanan ini
        $dokumenPengajuan = \DB::table('duk_dokumen_pengajuan as ddp')
            ->join('duk_syarat_dokumen as dsd', 'ddp.syarat_dokumen_id', '=', 'dsd.id')
            ->where('ddp.pelayanan_id', $pemohonId)
            ->whereNotNull('ddp.path_dokumen')
            ->select('dsd.nama_dokumen')
            ->get();

        \Log::info('Dokumen pengajuan query result', [
            'pemohon_id' => $pemohonId,
            'dokumen_count' => $dokumenPengajuan->count(),
            'dokumen_list' => $dokumenPengajuan->pluck('nama_dokumen')->toArray()
        ]);

        // Format nama dokumen menjadi string
        if ($dokumenPengajuan->count() > 0) {
            $namaList = $dokumenPengajuan->pluck('nama_dokumen')->toArray();
            $buktiKepemilikan = implode(', ', $namaList);
            
            \Log::info('Bukti kepemilikan generated', [
                'pemohon_id' => $pemohonId,
                'bukti_kepemilikan' => $buktiKepemilikan
            ]);
            
            return $buktiKepemilikan;
        }

        \Log::info('No bukti kepemilikan found', [
            'pemohon_id' => $pemohonId
        ]);

        return '';
    }

    // Method untuk mendapatkan variables default (untuk surat non-layanan)
    private function getNonLayananVariables()
    {
        // Return empty array atau default values
        return [];
    }

    // Method untuk mendapatkan semua kategori surat berdasarkan jenis layanan
    public static function getByJenisLayanan($jenisPelayananId)
    {
        return self::where('jenis_pelayanan_id', $jenisPelayananId)
            ->where('tipe_surat', 'layanan')
            ->orderBy('nama')
            ->get();
    }

    // Method untuk mengecek apakah jenis layanan memiliki multiple kategori surat
    public static function hasMultipleKategori($jenisPelayananId)
    {
        return self::where('jenis_pelayanan_id', $jenisPelayananId)
            ->where('tipe_surat', 'layanan')
            ->count() > 1;
    }

    // Method untuk mendapatkan template variables yang akan digunakan dalam form
    public function getFormVariables()
    {
        $variables = [];
        
        if ($this->template_type === 'blade' && $this->blade_template_variables) {
            $variables = $this->blade_template_variables;
        } elseif ($this->template_type === 'pdf' && $this->pdf_form_fields) {
            foreach ($this->pdf_form_fields as $field) {
                $variables[] = [
                    'name' => $field['field_name'],
                    'label' => $field['label'],
                    'type' => $field['type'] ?? 'text',
                    'required' => $field['required'] ?? false
                ];
            }
        } elseif ($this->template_type === 'docx' && $this->docx_form_fields) {
            foreach ($this->docx_form_fields as $field) {
                $variables[] = [
                    'name' => $field['field_name'],
                    'label' => $field['label'],
                    'type' => $field['type'] ?? 'text',
                    'required' => $field['required'] ?? false
                ];
            }
        } elseif ($this->template_variables) {
            $variables = $this->template_variables;
        }
        
        return $variables;
    }

    // Method untuk mendapatkan merged variables dari semua kategori dalam jenis layanan
    public static function getMergedVariables($jenisPelayananId)
    {
        $kategoriList = self::getByJenisLayanan($jenisPelayananId);
        $mergedVariables = [];
        $variableNames = [];

        foreach ($kategoriList as $kategori) {
            $formVariables = $kategori->getFormVariables();
            
            foreach ($formVariables as $variable) {
                $varName = $variable['name'];
                
                // Jika variabel belum ada, tambahkan
                if (!in_array($varName, $variableNames)) {
                    $variableNames[] = $varName;
                    $mergedVariables[] = $variable;
                }
            }
        }

        return $mergedVariables;
    }

    // Method untuk mengecek apakah kategori dapat di-generate
    public function canGenerate()
    {
        if ($this->template_type === 'blade') {
            return $this->hasBladeTemplate();
        } elseif ($this->template_type === 'pdf') {
            return !empty($this->pdf_template_path) && !empty($this->pdf_form_fields);
        } elseif ($this->template_type === 'docx') {
            return !empty($this->docx_template_path) && !empty($this->docx_form_fields);
        } elseif ($this->template_type === 'text') {
            return !empty($this->template_surat);
        }
        
        return false;
    }
} 