<?php

namespace App\Models\adm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSurat extends Model
{
    use HasFactory;

    protected $table = 'kategori_surat';
    protected $fillable = ['nama', 'template_surat', 'template_variables', 'header_template', 'header_variables', 'header_type', 'pdf_template_path', 'pdf_form_fields', 'template_type', 'docx_template_path', 'docx_form_fields'];

    protected $casts = [
        'template_variables' => 'array',
        'header_variables' => 'array',
        'pdf_form_fields' => 'array',
        'docx_form_fields' => 'array'
    ];

    public function surat()
    {
        return $this->hasMany(RegisterSurat::class, 'kategori_id');
    }
    
} 