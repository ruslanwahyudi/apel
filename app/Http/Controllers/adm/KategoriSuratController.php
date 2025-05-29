<?php

namespace App\Http\Controllers\adm;

use App\Http\Controllers\Controller;
use App\Models\adm\KategoriSurat;
use App\Models\Layanan\JenisPelayanan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

// Include FPDF first, then FPDI
require_once base_path('vendor/setasign/fpdf/fpdf.php');
require_once base_path('vendor/setasign/fpdi/src/autoload.php');
use setasign\Fpdi\Fpdi;

// Include PDF Parser for text extraction and position detection
use Smalot\PdfParser\Parser;
use Smalot\PdfParser\Document;

// Include PhpWord for DOCX processing
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

class KategoriSuratController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $kategori = KategoriSurat::all();

            return response()->json($kategori);
        }

        return view('adm.kategori-surat.index');
    }

    public function create()
    {
        return view('adm.kategori-surat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_surat,nama',
            'blade_template_name' => 'nullable|string|max:255',
            'blade_template_variables' => 'nullable|array',
            'template_type' => 'required|in:blade,text,pdf,docx',
            'template_surat' => 'nullable|string',
            'template_variables' => 'nullable|array',
            'header_template' => 'nullable|string',
            'header_variables' => 'nullable|array',
            'header_type' => 'nullable|in:simple,full',
            'tipe_surat' => 'required|in:layanan,non_layanan',
            'jenis_pelayanan_id' => 'nullable|exists:duk_jenis_pelayanan,id|required_if:tipe_surat,layanan'
        ]);

        $data = $request->only([
            'nama', 
            'blade_template_name', 
            'template_type', 
            'template_surat', 
            'header_template', 
            'header_type',
            'tipe_surat',
            'jenis_pelayanan_id'
        ]);
        
        // Process blade template variables
        if ($request->has('blade_template_variables') && is_array($request->blade_template_variables)) {
            $variables = [];
            foreach ($request->blade_template_variables as $var) {
                if (!empty($var['name']) && !empty($var['label'])) {
                    $variables[] = [
                        'name' => $var['name'],
                        'label' => $var['label'],
                        'type' => $var['type'] ?? 'text',
                        'required' => isset($var['required']) ? (bool)$var['required'] : false,
                        'default_value' => $var['default_value'] ?? ''
                    ];
                }
            }
            $data['blade_template_variables'] = $variables;
        }

        // Process template variables
        if ($request->has('template_variables') && is_array($request->template_variables)) {
            $variables = [];
            foreach ($request->template_variables as $var) {
                if (!empty($var['name']) && !empty($var['label'])) {
                    $variables[] = [
                        'name' => $var['name'],
                        'label' => $var['label'],
                        'type' => $var['type'] ?? 'text',
                        'required' => isset($var['required']) ? (bool)$var['required'] : false
                    ];
                }
            }
            $data['template_variables'] = $variables;
        }

        // Process header variables
        if ($request->has('header_variables') && is_array($request->header_variables)) {
            $headerVariables = [];
            foreach ($request->header_variables as $var) {
                if (!empty($var['name']) && !empty($var['label'])) {
                    $headerVariables[] = [
                        'name' => $var['name'],
                        'label' => $var['label'],
                        'type' => $var['type'] ?? 'text',
                        'required' => isset($var['required']) ? (bool)$var['required'] : false
                    ];
                }
            }
            $data['header_variables'] = $headerVariables;
        }

        KategoriSurat::create($data);

        return redirect()
            ->route('adm.kategori-surat')
            ->with('success', 'Kategori surat berhasil ditambahkan');
    }

    public function edit(KategoriSurat $kategori)
    {
        return view('adm.kategori-surat.edit', compact('kategori'));
    }

    public function editSimple(KategoriSurat $kategori)
    {
        return view('adm.kategori-surat.edit-simple', compact('kategori'));
    }

    public function update(Request $request, KategoriSurat $kategori)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_surat,nama,' . $kategori->id,
            'blade_template_name' => 'nullable|string|max:255',
            'blade_template_variables' => 'nullable|array',
            'template_surat' => 'nullable|string',
            'template_variables' => 'nullable|array',
            'header_template' => 'nullable|string',
            'header_variables' => 'nullable|array',
            'header_type' => 'nullable|in:simple,full',
            'template_type' => 'required|in:blade,text,pdf,docx',
            'pdf_template' => 'nullable|file|mimes:pdf|max:10240',
            'pdf_form_fields' => 'nullable|array',
            'docx_template' => 'nullable|file|mimes:docx|max:10240',
            'docx_form_fields' => 'nullable|array',
            'tipe_surat' => 'required|in:layanan,non_layanan',
            'jenis_pelayanan_id' => 'nullable|exists:duk_jenis_pelayanan,id|required_if:tipe_surat,layanan'
        ]);

        $data = $request->only([
            'nama', 
            'blade_template_name', 
            'template_surat', 
            'header_template', 
            'header_type', 
            'template_type',
            'tipe_surat',
            'jenis_pelayanan_id'
        ]);
        
        // Handle PDF upload
        if ($request->hasFile('pdf_template')) {
            $file = $request->file('pdf_template');
            // Sanitize filename - remove spaces and special characters
            $originalName = $file->getClientOriginalName();
            $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
            $filename = time() . '_' . $sanitizedName;
            $path = $file->storeAs('pdf_templates', $filename, 'public');
            $data['pdf_template_path'] = $path;
            
            \Log::info("PDF uploaded: Original name = {$originalName}, Sanitized = {$filename}");
        }
        
        // Handle DOCX upload
        if ($request->hasFile('docx_template')) {
            $file = $request->file('docx_template');
            // Sanitize filename - remove spaces and special characters
            $originalName = $file->getClientOriginalName();
            $sanitizedName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
            $filename = time() . '_' . $sanitizedName;
            $path = $file->storeAs('docx_templates', $filename, 'public');
            $data['docx_template_path'] = $path;
            
            \Log::info("DOCX uploaded: Original name = {$originalName}, Sanitized = {$filename}");
        }
        
        // Process blade template variables
        if ($request->has('blade_template_variables') && is_array($request->blade_template_variables)) {
            $variables = [];
            foreach ($request->blade_template_variables as $var) {
                if (!empty($var['name']) && !empty($var['label'])) {
                    $variables[] = [
                        'name' => $var['name'],
                        'label' => $var['label'],
                        'type' => $var['type'] ?? 'text',
                        'required' => isset($var['required']) ? (bool)$var['required'] : false,
                        'default_value' => $var['default_value'] ?? ''
                    ];
                }
            }
            $data['blade_template_variables'] = $variables;
        }
        
        // Process template variables
        if ($request->has('template_variables') && is_array($request->template_variables)) {
            $variables = [];
            foreach ($request->template_variables as $var) {
                if (!empty($var['name']) && !empty($var['label'])) {
                    $variables[] = [
                        'name' => $var['name'],
                        'label' => $var['label'],
                        'type' => $var['type'] ?? 'text',
                        'required' => isset($var['required']) ? (bool)$var['required'] : false
                    ];
                }
            }
            $data['template_variables'] = $variables;
        }

        // Process header variables
        if ($request->has('header_variables') && is_array($request->header_variables)) {
            $headerVariables = [];
            foreach ($request->header_variables as $var) {
                if (!empty($var['name']) && !empty($var['label'])) {
                    $headerVariables[] = [
                        'name' => $var['name'],
                        'label' => $var['label'],
                        'type' => $var['type'] ?? 'text',
                        'required' => isset($var['required']) ? (bool)$var['required'] : false
                    ];
                }
            }
            $data['header_variables'] = $headerVariables;
        }

        // Process PDF form fields
        if ($request->has('pdf_form_fields') && is_array($request->pdf_form_fields)) {
            $pdfFields = [];
            foreach ($request->pdf_form_fields as $field) {
                if (!empty($field['field_name']) && !empty($field['label'])) {
                    $pdfFields[] = [
                        'field_name' => $field['field_name'],
                        'label' => $field['label'],
                        'type' => $field['type'] ?? 'text',
                        'position_x' => (int)($field['position_x'] ?? 100),
                        'position_y' => (int)($field['position_y'] ?? 100)
                    ];
                }
            }
            $data['pdf_form_fields'] = $pdfFields;
        }
        
        // Process DOCX form fields
        if ($request->has('docx_form_fields') && is_array($request->docx_form_fields)) {
            $docxFields = [];
            foreach ($request->docx_form_fields as $field) {
                if (!empty($field['field_name']) && !empty($field['label'])) {
                    $docxFields[] = [
                        'field_name' => $field['field_name'],
                        'label' => $field['label'],
                        'type' => $field['type'] ?? 'text',
                        'required' => isset($field['required']) ? (bool)$field['required'] : false
                    ];
                }
            }
            $data['docx_form_fields'] = $docxFields;
        }

        $kategori->update($data);

        return redirect()
            ->route('adm.kategori-surat')
            ->with('success', 'Kategori surat berhasil diperbarui');
    }

    public function search(Request $request)
    {
        $kategori = KategoriSurat::where('nama', 'like', '%' . $request->search . '%')->get();
        return response()->json($kategori);
    }

    public function destroy(KategoriSurat $kategori)
    {
        // if ($kategori->surat()->exists()) {
        //     return redirect()
        //         ->route('adm.kategori-surat')
        //         ->with('error', 'Kategori surat tidak dapat dihapus karena masih digunakan');
        // }

        $kategori->delete();

        return response()->json(['message' => 'Kategori surat berhasil dihapus']);
    }

    public function previewTemplate(Request $request, KategoriSurat $kategori)
    {
        $template = $kategori->template_surat;
        $variables = $kategori->template_variables ?? [];
        
        // Replace variables with sample data or request data
        foreach ($variables as $variable) {
            $value = $request->input($variable['name'], '[' . $variable['label'] . ']');
            $template = str_replace('{{' . $variable['name'] . '}}', $value, $template);
        }
        
        return response()->json([
            'preview' => $template,
            'variables' => $variables
        ]);
    }

    public function showTemplate(KategoriSurat $kategori)
    {
        // Check template type and redirect to appropriate view
        if ($kategori->template_type === 'blade') {
            return view('adm.kategori-surat.template-blade', compact('kategori'));
        }
        
        // For legacy templates (text, pdf, docx)
        return view('adm.kategori-surat.template-simple', compact('kategori'));
    }

    // Method untuk generate surat menggunakan Blade template
    public function generateBladeTemplate(Request $request, KategoriSurat $kategori)
    {
        \Log::info('Generate Blade Template called', [
            'kategori_id' => $kategori->id,
            'output' => $request->input('output'),
            'request_data' => $request->all()
        ]);
        
        if (!$kategori->hasBladeTemplate()) {
            \Log::error('Template Blade tidak ditemukan', [
                'kategori_id' => $kategori->id,
                'blade_template_name' => $kategori->blade_template_name
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Template Blade tidak ditemukan'
            ], 404);
        }

        // Validasi input berdasarkan template variables
        $rules = [];
        if ($kategori->blade_template_variables) {
            foreach ($kategori->blade_template_variables as $variable) {
                $rule = [];
                
                // Only apply required validation for HTML preview, not for PDF
                if ($variable['required'] && $request->output !== 'pdf') {
                    $rule[] = 'required';
                }
                
                if ($variable['type'] === 'date') {
                    $rule[] = 'nullable|date';
                } elseif ($variable['type'] === 'number') {
                    $rule[] = 'nullable|numeric';
                } else {
                    $rule[] = 'nullable|string';
                }
                
                if (!empty($rule)) {
                    $rules[$variable['name']] = implode('|', $rule);
                }
            }
        }

        // Tambahan validasi untuk surat layanan
        if ($kategori->isLayanan()) {
            $rules['pemohon_id'] = 'required|exists:duk_pelayanan,id';
        }

        // Validate with custom error handling
        try {
            $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'kategori_id' => $kategori->id,
                'errors' => $e->errors(),
                'input' => $request->all(),
                'output_type' => $request->output
            ]);
            
            // For PDF requests, validation should not block generation
            if ($request->output === 'pdf') {
                \Log::info('PDF generation proceeding despite validation errors', [
                    'kategori_id' => $kategori->id,
                    'errors' => $e->errors()
                ]);
                // Continue with PDF generation even with validation errors
            } else {
                // For AJAX requests, return JSON error
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $e->errors()
                    ], 422);
                }
                
                // For regular form submission, redirect back with errors
                throw $e;
            }
        }

        // Prepare data untuk template berdasarkan tipe surat
        $templateData = [
            'kategori' => $kategori,
            'generated_at' => now(),
            'nomor_surat' => $this->generateNomorSurat($kategori)
        ];

        // Jika surat layanan dan ada pemohon_id, ambil data dari DUK
        if ($kategori->isLayanan() && isset($request->pemohon_id)) {
            \Log::info('Processing layanan surat with DUK data', [
                'kategori_id' => $kategori->id,
                'pemohon_id' => $request->pemohon_id,
                'kategori_tipe' => $kategori->tipe_surat
            ]);
            
            // Ambil data dari DUK
            $dukData = $kategori->getVariables($request->pemohon_id);
            
            \Log::info('DUK data retrieved', [
                'duk_data' => $dukData,
                'duk_data_count' => count($dukData)
            ]);
            
            // Ambil data pelayanan dasar
            $pelayananData = \DB::table('duk_pelayanan')
                ->where('id', $request->pemohon_id)
                ->first();
            
            \Log::info('Pelayanan data retrieved', [
                'pelayanan_data' => $pelayananData ? (array) $pelayananData : null
            ]);
            
            // Merge all data: DUK + pelayanan + form input - avoid overwriting with empty values
            $mergedData = $dukData;
            
            // Add pelayanan data if available
            if ($pelayananData) {
                $pelayananArray = (array)$pelayananData;
                foreach ($pelayananArray as $key => $value) {
                    if (!empty($value) || !isset($mergedData[$key])) {
                        $mergedData[$key] = $value;
                    }
                }
            }
            
            // Add form data - giving priority to form input over other sources
            foreach ($request->all() as $key => $value) {
                if (!empty($value) || !isset($mergedData[$key])) {
                    $mergedData[$key] = $value;
                }
            }
            
            $templateData['data'] = $mergedData;
            
            \Log::info('Final template data for layanan surat', [
                'kategori_id' => $kategori->id,
                'pemohon_id' => $request->pemohon_id,
                'duk_data_count' => count($dukData),
                'pelayanan_data' => $pelayananData ? 'found' : 'not found',
                'form_data_count' => count($request->all()),
                'merged_data_keys' => array_keys($templateData['data']),
                'merged_data' => $templateData['data']
            ]);
        } else {
            // Untuk surat non-layanan, gunakan data form langsung
            $templateData['data'] = $request->all();
            
            \Log::info('Processing non-layanan surat with manual data', [
                'kategori_id' => $kategori->id,
                'kategori_tipe' => $kategori->tipe_surat,
                'manual_data_count' => count($request->all()),
                'manual_data' => $request->all()
            ]);
        }

        try {
            // Generate HTML dari Blade template
            $html = view($kategori->getBladeTemplatePath(), $templateData)->render();
            
            \Log::info('Blade template rendered successfully', [
                'kategori_id' => $kategori->id,
                'html_length' => strlen($html),
                'template_path' => $kategori->getBladeTemplatePath()
            ]);
            
            // Convert ke PDF jika diperlukan
            if ($request->output === 'pdf') {
                \Log::info('Generating PDF', [
                    'kategori_id' => $kategori->id,
                    'html_length' => strlen($html)
                ]);
                
                $pdf = Pdf::loadHTML($html)
                    ->setPaper('a4', 'portrait')
                    ->setOptions([
                        'defaultFont' => 'Times-Roman',
                        'isRemoteEnabled' => true,
                        'isHtml5ParserEnabled' => true,
                        'dpi' => 150,
                        'defaultPaperSize' => 'a4'
                    ]);
                
                // Create filename for streaming in browser
                $filename = 'surat_' . str_replace(' ', '_', strtolower($kategori->nama)) . '_' . date('Y-m-d_H-i-s') . '.pdf';
                
                \Log::info('PDF generated successfully', [
                    'filename' => $filename
                ]);
                
                // Return PDF as stream response for preview in browser
                $pdfOutput = $pdf->output();
                return response($pdfOutput)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
                    ->header('Content-Length', strlen($pdfOutput))
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }

            // Return HTML untuk preview atau print
            return response()->json([
                'success' => true,
                'html' => $html,
                'template_data' => $templateData
            ]);

        } catch (\Exception $e) {
            \Log::error('Blade PDF generation failed', [
                'kategori_id' => $kategori->id,
                'error' => $e->getMessage(),
                'template_data_keys' => isset($templateData['data']) ? array_keys($templateData['data']) : []
            ]);
            throw new \Exception('Error generating Blade template: ' . $e->getMessage());
        }
    }

    // Method untuk preview template
    public function previewBladeTemplate(Request $request, KategoriSurat $kategori)
    {
        if (!$kategori->hasBladeTemplate()) {
            return view('adm.kategori-surat.template-not-found', compact('kategori'));
        }

        // Sample data untuk preview
        $sampleData = [];
        if ($kategori->blade_template_variables) {
            foreach ($kategori->blade_template_variables as $variable) {
                switch ($variable['type']) {
                    case 'date':
                        $sampleData[$variable['name']] = date('d F Y');
                        break;
                    case 'number':
                        $sampleData[$variable['name']] = '123';
                        break;
                    default:
                        $sampleData[$variable['name']] = '[' . $variable['label'] . ']';
                }
            }
        }

        $templateData = [
            'kategori' => $kategori,
            'data' => $sampleData,
            'generated_at' => now(),
            'nomor_surat' => 'PREVIEW/001/2024',
            'is_preview' => true
        ];

        return view('adm.kategori-surat.preview-blade', [
            'kategori' => $kategori,
            'template_html' => view($kategori->getBladeTemplatePath(), $templateData)->render()
        ]);
    }

    private function generateNomorSurat($kategori)
    {
        // Logic untuk generate nomor surat otomatis dengan format: jumlah_surat+1/settings.no_surat/bulan/tahun
        
        // Ambil jumlah surat dari register_surat
        $jumlahSurat = \DB::table('register_surat')->count();
        $nomorUrut = str_pad($jumlahSurat + 1, 3, '0', STR_PAD_LEFT);
        
        // Ambil no_surat dari settings
        $setting = \App\Models\Setting::instance();
        $noSuratSetting = $setting->no_surat ?? 'DESA';
        
        // Format bulan dan tahun
        $bulan = date('m'); // Format: 01, 02, dst
        $tahun = date('Y');  // Format: 2025
        
        // Format final: 001/DESA/01/2025
        return "{$nomorUrut}/{$noSuratSetting}/{$bulan}/{$tahun}";
    }
    
    /**
     * Clean up old generated PDF files (optional cleanup method)
     * This method can be called periodically to clean up old files if needed
     */
    private function cleanupOldGeneratedPdfs()
    {
        $generatedPdfsPath = storage_path('app/public/generated_pdfs');
        
        if (is_dir($generatedPdfsPath)) {
            $files = glob($generatedPdfsPath . '/*.pdf');
            $cutoffTime = time() - (24 * 60 * 60); // 24 hours ago
            
            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $cutoffTime) {
                    unlink($file);
                }
            }
        }
    }
    
    public function detectVariablePositionsApi(Request $request, KategoriSurat $kategori)
    {
        try {
            if (!$kategori->pdf_template_path || !file_exists(storage_path('app/public/' . $kategori->pdf_template_path))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template PDF tidak ditemukan'
                ]);
            }
            
            $templatePath = storage_path('app/public/' . $kategori->pdf_template_path);
            $pdfFields = $kategori->pdf_form_fields ?? [];
            
            // Detect positions using our enhanced method
            $detectedPositions = $this->detectVariablePositions($templatePath, $pdfFields);
            
            // Also get PDF text content for preview
            $pdfText = $this->extractPdfTextContent($templatePath);
            
            // Debug: Test variable detection for each field
            $debugInfo = [];
            foreach ($pdfFields as $field) {
                $fieldName = $field['field_name'];
                $position = $this->detectVariablePositionInPdf($templatePath, $fieldName);
                $debugInfo[$fieldName] = [
                    'detected_position' => $position,
                    'configured_position' => [
                        'x' => $field['position_x'] ?? null,
                        'y' => $field['position_y'] ?? null
                    ],
                    'patterns_checked' => [
                        '{{' . $fieldName . '}}',
                        '{' . $fieldName . '}',
                        $fieldName . ':'
                    ]
                ];
            }
            
            return response()->json([
                'success' => true,
                'positions' => $detectedPositions,
                'pdf_text' => $pdfText,
                'debug_info' => $debugInfo,
                'message' => 'Posisi variabel berhasil dideteksi'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    public function testVariableReplacement(Request $request, KategoriSurat $kategori)
    {
        try {
            // Test method untuk debugging variable replacement
            $templatePath = storage_path('app/public/' . $kategori->pdf_template_path);
            $pdfFields = $kategori->pdf_form_fields ?? [];
            
            // Sample test data
            $testData = [
                'nomor' => 'TEST-001/2024',
                'tanggal' => '2024-12-27',
                'nama' => 'John Doe Test',
                'alamat' => 'Jl. Test No. 123'
            ];
            
            // Check if precise mode is requested
            $preciseMode = $request->input('precise_mode', false);
            
            // Generate test PDF using appropriate method
            if ($preciseMode) {
                \Log::info('Using PRECISE mode for test replacement');
                $pdfContent = $this->createPreciseVariableOverlay($templatePath, $testData, $pdfFields);
                $filename = 'test_precise_replacement_' . time() . '.pdf';
                
                // Get template analysis for debugging
                $templateAnalysis = $this->analyzeTemplateForVariables($templatePath);
            } else {
                \Log::info('Using STANDARD mode for test replacement');
                $pdfContent = $this->createAdvancedFilledPdf($templatePath, $testData, $pdfFields);
                $filename = 'test_variable_replacement_' . time() . '.pdf';
                $templateAnalysis = null;
            }
            
            // Return test PDF as base64 for preview
            $base64Pdf = base64_encode($pdfContent);
            
            $response = [
                'success' => true,
                'test_pdf_base64' => $base64Pdf,
                'test_data' => $testData,
                'pdf_fields' => $pdfFields,
                'message' => $preciseMode ? 'PRECISE Test PDF berhasil di-generate' : 'Test PDF berhasil di-generate',
                'mode' => $preciseMode ? 'precise' : 'standard',
                'filename' => $filename
            ];
            
            // Add template analysis if in precise mode
            if ($preciseMode && $templateAnalysis) {
                $response['template_analysis'] = $templateAnalysis;
            }
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    private function extractPdfTextContent($templatePath)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($templatePath);
            $pages = $pdf->getPages();
            
            if (!empty($pages)) {
                $page = $pages[0];
                return $page->getText();
            }
            
            return '';
        } catch (\Exception $e) {
            \Log::warning('PDF text extraction failed: ' . $e->getMessage());
            return '';
        }
    }

    public function generatePdf(Request $request, KategoriSurat $kategori)
    {
        // Check if we have Blade, DOCX, PDF fields, or text template
        $hasBladeTemplate = $kategori->template_type === 'blade' && $kategori->hasBladeTemplate();
        $hasDocxTemplate = $kategori->template_type === 'docx' && $kategori->docx_template_path;
        $hasPdfFields = $kategori->template_type === 'pdf' && $kategori->pdf_form_fields;
        $hasTextTemplate = !empty($kategori->template_surat);
        
        if (!$hasBladeTemplate && !$hasDocxTemplate && !$hasPdfFields && !$hasTextTemplate) {
            return response()->json([
                'success' => false,
                'message' => 'Template tidak tersedia. Silakan buat template terlebih dahulu.'
            ]);
        }

        try {
            // Get form data
            $formData = $request->all();
            
            // Get appropriate fields based on template type
            $templateFields = [];
            if ($kategori->template_type === 'blade') {
                $templateFields = $kategori->blade_template_variables ?? [];
            } elseif ($kategori->template_type === 'docx') {
                $templateFields = $kategori->docx_form_fields ?? [];
            } elseif ($kategori->template_type === 'pdf') {
                $templateFields = $kategori->pdf_form_fields ?? [];
            }
            
            // Debug: Log the form data and template fields
            \Log::info('Form Data:', $formData);
            \Log::info('Template Fields:', $templateFields);
            \Log::info('Template Type:', [$kategori->template_type]);
            \Log::info('Template Path:', [
                'PDF' => $kategori->pdf_template_path ?? 'None',
                'DOCX' => $kategori->docx_template_path ?? 'None'
            ]);
            
            // Generate PDF using appropriate method
            $pdfContent = $this->generatePdfDocument($kategori, $formData, $templateFields);
            
            // Create filename for download
            $filename = 'surat_' . str_replace(' ', '_', strtolower($kategori->nama)) . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            $message = 'PDF berhasil di-generate dengan data yang diisi';
            if ($kategori->template_type === 'blade' && $kategori->hasBladeTemplate()) {
                $message = 'PDF berhasil di-generate dari Blade template dengan akurasi 100%. Semua variabel telah diganti dengan data yang Anda input.';
            } elseif ($kategori->template_type === 'docx' && $kategori->docx_template_path) {
                $message = 'PDF berhasil di-generate dari template DOCX dengan akurasi 100%. Semua variabel ${nama}, ${tanggal}, dll telah diganti dengan data yang Anda input.';
            } elseif ($kategori->template_type === 'pdf' && $kategori->pdf_template_path) {
                $message = 'PDF berhasil di-generate. Variabel {{nomor}}, {{tanggal}}, dll telah diganti dengan data yang Anda input.';
            }
            
            // Stream PDF directly without saving to storage
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->header('Content-Length', strlen($pdfContent))
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    private function generatePdfDocument($kategori, $formData, $pdfFields)
    {
        // Check if we have a Blade template (HIGHEST PRIORITY)
        if ($kategori->template_type === 'blade' && $kategori->hasBladeTemplate()) {
            return $this->generateFromBladeTemplate($kategori, $formData);
        }
        
        // Check if we have a DOCX template file uploaded
        if ($kategori->template_type === 'docx' && $kategori->docx_template_path) {
            return $this->generateFromDocxTemplate($kategori, $formData, $kategori->docx_form_fields ?? []);
        }
        
        // Check if we have a PDF template file uploaded
        if ($kategori->template_type === 'pdf' && $kategori->pdf_template_path) {
            return $this->generateFromPdfTemplate($kategori, $formData, $pdfFields);
        }
        
        // Fallback to text template
        return $this->generateFromTextTemplate($kategori, $formData, $pdfFields);
    }

    private function generateFromBladeTemplate($kategori, $formData)
    {
        // Prepare data untuk template berdasarkan tipe surat
        $templateData = [
            'kategori' => $kategori,
            'generated_at' => now(),
            'nomor_surat' => $this->generateNomorSurat($kategori)
        ];

        // Jika surat layanan dan ada pemohon_id, ambil data dari DUK
        if ($kategori->isLayanan() && isset($formData['pemohon_id'])) {
            // Ambil data dari DUK
            $dukData = $kategori->getVariables($formData['pemohon_id']);
            
            // Ambil data pelayanan dasar - directly access all fields
            $pelayananData = \DB::table('duk_pelayanan')
                ->where('id', $formData['pemohon_id'])
                ->first();
            
            // Log data for debugging
            \Log::info('Generating PDF from Blade Template with data:', [
                'pemohon_id' => $formData['pemohon_id'],
                'duk_data' => $dukData,
                'pelayanan_data' => $pelayananData ? (array)$pelayananData : 'not found',
                'form_data' => $formData
            ]);
            
            // Merge all data: DUK + pelayanan + form input - avoid overwriting with empty values
            $mergedData = $dukData;
            
            // Add pelayanan data if available
            if ($pelayananData) {
                $pelayananArray = (array)$pelayananData;
                foreach ($pelayananArray as $key => $value) {
                    if (!empty($value) || !isset($mergedData[$key])) {
                        $mergedData[$key] = $value;
                    }
                }
            }
            
            // Add form data - giving priority to form input over other sources
            foreach ($formData as $key => $value) {
                if (!empty($value) || !isset($mergedData[$key])) {
                    $mergedData[$key] = $value;
                }
            }
            
            $templateData['data'] = $mergedData;
            
            \Log::info('Final merged data for PDF generation:', [
                'merged_data_count' => count($mergedData),
                'merged_keys' => array_keys($mergedData)
            ]);
        } else {
            // Untuk surat non-layanan, gunakan data form langsung
            $templateData['data'] = $formData;
            
            \Log::info('Blade PDF: Using manual data for non-layanan surat', [
                'kategori_id' => $kategori->id,
                'manual_data_count' => count($formData)
            ]);
        }

        try {
            // Generate HTML dari Blade template
            $html = view($kategori->getBladeTemplatePath(), $templateData)->render();
            
            \Log::info('Blade PDF: HTML generated successfully', [
                'kategori_id' => $kategori->id,
                'html_length' => strlen($html),
                'template_path' => $kategori->getBladeTemplatePath(),
                'template_data_keys' => array_keys($templateData['data'] ?? [])
            ]);
            
            // Convert ke PDF menggunakan DomPDF dengan setting optimal
            $pdf = Pdf::loadHTML($html)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'Times-Roman',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'dpi' => 150,
                    'defaultPaperSize' => 'a4',
                    'chroot' => public_path(),
                    'debugKeepTemp' => false,
                    'debugCss' => false,
                    'debugLayout' => false,
                    'debugLayoutLines' => false,
                    'debugLayoutBlocks' => false,
                    'debugLayoutInline' => false,
                    'debugLayoutPaddingBox' => false
                ]);
            
            // Returned for streaming in browser rather than forced download
            $pdfOutput = $pdf->output();
            return $pdfOutput;

        } catch (\Exception $e) {
            \Log::error('Blade PDF generation failed', [
                'kategori_id' => $kategori->id,
                'error' => $e->getMessage(),
                'template_data_keys' => isset($templateData['data']) ? array_keys($templateData['data']) : []
            ]);
            throw new \Exception('Error generating Blade template: ' . $e->getMessage());
        }
    }
    
    private function generateFromPdfTemplate($kategori, $formData, $pdfFields)
    {
        $templatePath = storage_path('app/public/' . $kategori->pdf_template_path);
        
        if (!file_exists($templatePath)) {
            throw new \Exception('Template PDF tidak ditemukan: ' . $templatePath);
        }
        
        // Gunakan metode overlay yang lebih akurat untuk mengganti variabel
        return $this->createPreciseVariableOverlay($templatePath, $formData, $pdfFields);
    }
    
    private function createFilledPdfWithFpdi($templatePath, $formData, $pdfFields)
    {
        // Use the advanced method with better positioning
        return $this->createAdvancedFilledPdf($templatePath, $formData, $pdfFields);
    }
    
    private function addVariableOverlays($pdf, $formData, $pdfFields, $pageNo)
    {
        // Enhanced positioning system with better coordinates
        $positions = [
            'nomor' => ['x' => 120, 'y' => 60],
            'nomor_surat' => ['x' => 120, 'y' => 60],
            'tanggal' => ['x' => 120, 'y' => 80],
            'tanggal_surat' => ['x' => 120, 'y' => 80],
            'nama' => ['x' => 120, 'y' => 120],
            'nama_pemohon' => ['x' => 120, 'y' => 120],
            'alamat' => ['x' => 120, 'y' => 140],
            'alamat_pemohon' => ['x' => 120, 'y' => 140],
            'perihal' => ['x' => 120, 'y' => 100],
            'no_ktp' => ['x' => 120, 'y' => 160],
            'keperluan' => ['x' => 120, 'y' => 180],
            'tempat_lahir' => ['x' => 120, 'y' => 200],
            'tanggal_lahir' => ['x' => 200, 'y' => 200],
        ];
        
        foreach ($pdfFields as $index => $field) {
            $fieldName = $field['field_name'];
            $fieldValue = $formData[$fieldName] ?? '';
            
            if (!empty($fieldValue)) {
                // Format date fields
                if ($field['type'] === 'date' && !empty($fieldValue)) {
                    $fieldValue = date('d/m/Y', strtotime($fieldValue));
                }
                
                // Use predefined position or calculate dynamic position
                if (isset($positions[$fieldName])) {
                    $x = $positions[$fieldName]['x'];
                    $y = $positions[$fieldName]['y'];
                } else {
                    // Dynamic positioning for unmapped fields
                    $x = 120;
                    $y = 60 + ($index * 25); // Better spacing
                }
                
                // Add text at specified position with better formatting
                $pdf->SetXY($x, $y);
                
                // Handle long text by wrapping
                if (strlen($fieldValue) > 50) {
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->MultiCell(150, 5, $fieldValue, 0, 'L');
                } else {
                    $pdf->SetFont('Arial', '', 12);
                    $pdf->Write(0, $fieldValue);
                }
            }
        }
    }
    
    private function createOverlayPdf($templatePath, $formData, $pdfFields)
    {
        // Create new FPDI instance
        $pdf = new \setasign\Fpdi\Fpdi();
        
        // Get page count
        $pageCount = $pdf->setSourceFile($templatePath);
        
        // Process each page
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            // Add a page
            $pdf->AddPage();
            
            // Import the page
            $templateId = $pdf->importPage($pageNo);
            
            // Use the imported page as template
            $pdf->useTemplate($templateId);
            
            // Set font for text overlay
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            
            // Add text overlays for variables
            $this->addTextOverlays($pdf, $formData, $pdfFields, $pageNo);
        }
        
        // Return PDF content
        return $pdf->Output('S'); // 'S' returns the PDF as string
    }
    
    private function addTextOverlays($pdf, $formData, $pdfFields, $pageNo)
    {
        // Define approximate positions for common variables
        // These coordinates may need adjustment based on your specific PDF template
        $positions = [
            'nomor' => ['x' => 100, 'y' => 50],
            'tanggal' => ['x' => 100, 'y' => 70],
            'nama' => ['x' => 100, 'y' => 90],
            'alamat' => ['x' => 100, 'y' => 110],
            'perihal' => ['x' => 100, 'y' => 130],
            'nama_pemohon' => ['x' => 100, 'y' => 150],
            'alamat_pemohon' => ['x' => 100, 'y' => 170],
            'no_ktp' => ['x' => 100, 'y' => 190],
            'keperluan' => ['x' => 100, 'y' => 210],
        ];
        
        foreach ($pdfFields as $field) {
            $fieldName = $field['field_name'];
            $fieldValue = $formData[$fieldName] ?? '';
            
            if (!empty($fieldValue)) {
                // Format date fields
                if ($field['type'] === 'date' && !empty($fieldValue)) {
                    $fieldValue = date('d/m/Y', strtotime($fieldValue));
                }
                
                // Use predefined position or calculate dynamic position
                if (isset($positions[$fieldName])) {
                    $x = $positions[$fieldName]['x'];
                    $y = $positions[$fieldName]['y'];
                } else {
                    // Dynamic positioning for unmapped fields
                    $index = array_search($field, $pdfFields);
                    $x = 100;
                    $y = 50 + ($index * 20);
                }
                
                // Add text at specified position
                $pdf->SetXY($x, $y);
                $pdf->Write(0, $fieldValue);
            }
        }
    }
    
    private function generatePdfWithTextReplacement($kategori, $formData, $pdfFields)
    {
        // Fallback method: Create a new PDF with filled data
        $templateContent = "SURAT " . strtoupper($kategori->nama) . "\n\n";
        
        foreach ($pdfFields as $field) {
            $fieldName = $field['field_name'];
            $fieldValue = $formData[$fieldName] ?? '[Tidak diisi]';
            
            // Format date fields
            if ($field['type'] === 'date' && !empty($fieldValue) && $fieldValue !== '[Tidak diisi]') {
                $fieldValue = date('d/m/Y', strtotime($fieldValue));
            }
            
            $templateContent .= $field['label'] . ": " . $fieldValue . "\n";
        }
        
        $templateContent .= "\n\nTanggal: " . date('d/m/Y') . "\n";
        $templateContent .= "\n\nHormat kami,\n\n\n\n(_____________________)\n";
        
        // Generate PDF using DomPDF as fallback
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($kategori->nama) . '</title>
    <style>
        body { 
            font-family: "Times New Roman", serif; 
            line-height: 1.6; 
            margin: 30px;
            font-size: 12px;
            color: #333;
        }
        .content {
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="content">' . nl2br(htmlspecialchars($templateContent)) . '</div>
</body>
</html>';

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->output();
    }
    
    private function generateFromTextTemplate($kategori, $formData, $pdfFields)
    {
        // Get the original template content
        $templateContent = $kategori->template_surat ?? '';
        
        // If no template content, create a simple field list
        if (empty($templateContent)) {
            $templateContent = $this->createSimpleFieldTemplate($kategori, $pdfFields);
        }
        
        // Replace variables in template with actual values
        $filledContent = $this->replaceTemplateVariables($templateContent, $formData, $pdfFields);
        
        // Build HTML content for PDF with original template format
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($kategori->nama) . '</title>
    <style>
        body { 
            font-family: "Times New Roman", serif; 
            line-height: 1.6; 
            margin: 0;
            padding: 30px;
            font-size: 12px;
            color: #333;
        }
        .template-content {
            white-space: pre-wrap;
            margin: 20px 0;
            text-align: justify;
        }
        .header-info {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 15px;
            font-size: 10px;
            color: #666;
        }
        .footer-info {
            margin-top: 50px;
            text-align: right;
            border-top: 1px solid #ccc;
            padding-top: 15px;
            font-size: 10px;
            color: #666;
        }
        /* Preserve formatting for template content */
        p { margin: 10px 0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .underline { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="header-info">
        <strong>' . htmlspecialchars($kategori->nama) . '</strong><br>
        Dokumen yang telah diisi - ' . date('d/m/Y H:i:s') . '
    </div>
    
    <div class="template-content">' . nl2br(htmlspecialchars($filledContent)) . '</div>
    
    <div class="footer-info">
        <em>Dokumen ini di-generate secara otomatis pada ' . date('d/m/Y H:i:s') . '</em>
    </div>
</body>
</html>';

        // Generate PDF using DomPDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        // Return PDF content as string
        return $pdf->output();
    }
    
    private function replaceTemplateVariables($template, $formData, $pdfFields)
    {
        $filledTemplate = $template;
        
        // Replace variables from PDF form fields (if available)
        if ($pdfFields && is_array($pdfFields)) {
            foreach ($pdfFields as $field) {
                $fieldName = $field['field_name'];
                $fieldValue = $formData[$fieldName] ?? '[Tidak diisi]';
                
                // Format date fields
                if ($field['type'] === 'date' && !empty($fieldValue) && $fieldValue !== '[Tidak diisi]') {
                    $fieldValue = date('d/m/Y', strtotime($fieldValue));
                }
                
                // Replace both {{variable}} and variable patterns
                $filledTemplate = str_replace('{{' . $fieldName . '}}', $fieldValue, $filledTemplate);
                $filledTemplate = str_replace('{' . $fieldName . '}', $fieldValue, $filledTemplate);
            }
        }
        
        // Also replace any other variables from form data
        foreach ($formData as $key => $value) {
            if (!empty($value)) {
                // Format date values
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $value = date('d/m/Y', strtotime($value));
                }
                
                // Replace both {{variable}} and variable patterns
                $filledTemplate = str_replace('{{' . $key . '}}', $value, $filledTemplate);
                $filledTemplate = str_replace('{' . $key . '}', $value, $filledTemplate);
            }
        }
        
        return $filledTemplate;
    }
    
    private function createSimpleFieldTemplate($kategori, $pdfFields)
    {
        $template = $kategori->nama . "\n\n";
        
        foreach ($pdfFields as $field) {
            $template .= $field['label'] . ": {{" . $field['field_name'] . "}}\n";
        }
        
        $template .= "\n\nTanggal: {{tanggal}}\n";
        $template .= "\n\nHormat kami,\n\n\n\n(_____________________)\n";
        
        return $template;
    }
    
    private function getFilledDataSummary($formData, $pdfFields)
    {
        $summary = [];
        
        // Ensure $pdfFields is an array
        if (!is_array($pdfFields)) {
            return $summary;
        }
        
        foreach ($pdfFields as $field) {
            // Ensure field is an array with required keys
            if (!is_array($field) || !isset($field['field_name']) || !isset($field['label'])) {
                continue;
            }
            
            $fieldName = $field['field_name'];
            $fieldLabel = $field['label'];
            $fieldValue = $formData[$fieldName] ?? null;
            
            $summary[] = [
                'field' => $fieldName,
                'label' => $fieldLabel,
                'value' => $fieldValue,
                'filled' => !empty($fieldValue)
            ];
        }
        return $summary;
    }

    private function detectVariablePositions($templatePath, $pdfFields)
    {
        // Try to detect variable positions dynamically using PDF parser
        try {
            $dynamicPositions = $this->detectVariablePositionsDynamic($templatePath, $pdfFields);
            if (!empty($dynamicPositions)) {
                \Log::info('Dynamic position detection successful', $dynamicPositions);
                return $dynamicPositions;
            }
        } catch (\Exception $e) {
            \Log::warning('Dynamic position detection failed: ' . $e->getMessage());
        }
        
        // Fallback to enhanced smart positioning system
        return $this->getEnhancedSmartPositions($templatePath, $pdfFields);
    }
    
    private function detectVariablePositionsDynamic($templatePath, $pdfFields)
    {
        $detectedPositions = [];
        
        try {
            // Parse PDF using smalot/pdfparser
            $parser = new Parser();
            $pdf = $parser->parseFile($templatePath);
            
            // Get pages
            $pages = $pdf->getPages();
            
            if (empty($pages)) {
                throw new \Exception('No pages found in PDF');
            }
            
            // Process first page (most templates are single page)
            $page = $pages[0];
            $pageText = $page->getText();
            
            \Log::info('PDF Text Content:', ['text' => $pageText]);
            
            // Try to extract text with positions using detailed extraction
            $textDetails = $this->extractTextWithPositions($page);
            
            // Find variable positions in the extracted text
            foreach ($pdfFields as $field) {
                $fieldName = $field['field_name'];
                $variablePattern = '{{' . $fieldName . '}}';
                
                // Search for variable in text details
                $position = $this->findVariableInTextDetails($variablePattern, $textDetails, $pageText);
                
                if ($position) {
                    $detectedPositions[$fieldName] = $position;
                    \Log::info("Found variable {$variablePattern} at position", $position);
                } else {
                    // Try alternative patterns
                    $alternativePatterns = [
                        '{' . $fieldName . '}',
                        '[[' . $fieldName . ']]',
                        '[' . $fieldName . ']',
                        $fieldName . ':',
                        strtoupper($fieldName) . ':',
                        ucfirst($fieldName) . ':'
                    ];
                    
                    foreach ($alternativePatterns as $pattern) {
                        $position = $this->findVariableInTextDetails($pattern, $textDetails, $pageText);
                        if ($position) {
                            $detectedPositions[$fieldName] = $position;
                            \Log::info("Found variable {$pattern} at position", $position);
                            break;
                        }
                    }
                }
            }
            
            return $detectedPositions;
            
        } catch (\Exception $e) {
            \Log::error('PDF parsing failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    private function extractTextWithPositions($page)
    {
        $textDetails = [];
        
        try {
            // Get page details for coordinate calculation
            $pageDetails = $page->getDetails();
            $mediaBox = $pageDetails['MediaBox'] ?? [0, 0, 612, 792]; // Default A4 size
            
            // Extract text objects from page
            $content = $page->getDataTm();
            
            if (is_array($content)) {
                foreach ($content as $item) {
                    if (isset($item[1]) && is_string($item[1])) {
                        // Try to extract position information
                        $x = isset($item[4]) ? (float)$item[4] : 0;
                        $y = isset($item[5]) ? (float)$item[5] : 0;
                        
                        // Convert PDF coordinates to our coordinate system
                        $convertedY = $mediaBox[3] - $y; // Flip Y coordinate
                        
                        $textDetails[] = [
                            'text' => trim($item[1]),
                            'x' => $x,
                            'y' => $convertedY,
                            'original_y' => $y
                        ];
                    }
                }
            }
            
        } catch (\Exception $e) {
            \Log::warning('Text position extraction failed: ' . $e->getMessage());
        }
        
        return $textDetails;
    }
    
    private function findVariableInTextDetails($pattern, $textDetails, $fullText)
    {
        // First, try to find exact pattern in text details
        foreach ($textDetails as $detail) {
            if (strpos($detail['text'], $pattern) !== false) {
                return [
                    'x' => (int)$detail['x'],
                    'y' => (int)$detail['y']
                ];
            }
        }
        
        // If not found in details, try to estimate position based on text content
        $lines = explode("\n", $fullText);
        $lineHeight = 15; // Estimated line height
        $startY = 50; // Estimated start position
        
        foreach ($lines as $lineIndex => $line) {
            if (strpos($line, $pattern) !== false) {
                // Estimate X position based on character position in line
                $charPosition = strpos($line, $pattern);
                $estimatedX = 50 + ($charPosition * 6); // Rough character width estimation
                $estimatedY = $startY + ($lineIndex * $lineHeight);
                
                return [
                    'x' => $estimatedX,
                    'y' => $estimatedY
                ];
            }
        }
        
        return null;
    }
    
    private function getEnhancedSmartPositions($templatePath, $pdfFields)
    {
        $detectedPositions = [];
        
        // Try to analyze PDF structure for better positioning
        $pdfStructure = $this->analyzePdfStructure($templatePath);
        
        $startY = $pdfStructure['content_start_y'] ?? 80;
        $lineHeight = $pdfStructure['line_height'] ?? 20;
        $leftMargin = $pdfStructure['left_margin'] ?? 100;
        
        // Enhanced positioning based on field type and document analysis
        $commonPositions = [
            'nomor' => ['x' => $leftMargin + 50, 'y' => $startY - 30],
            'nomor_surat' => ['x' => $leftMargin + 50, 'y' => $startY - 30],
            'tanggal' => ['x' => $leftMargin + 50, 'y' => $startY - 10],
            'tanggal_surat' => ['x' => $leftMargin + 50, 'y' => $startY - 10],
            'nama' => ['x' => $leftMargin, 'y' => $startY + 20],
            'nama_pemohon' => ['x' => $leftMargin, 'y' => $startY + 20],
            'alamat' => ['x' => $leftMargin, 'y' => $startY + 40],
            'alamat_pemohon' => ['x' => $leftMargin, 'y' => $startY + 40],
            'no_ktp' => ['x' => $leftMargin, 'y' => $startY + 60],
            'tempat_lahir' => ['x' => $leftMargin, 'y' => $startY + 80],
            'tanggal_lahir' => ['x' => $leftMargin + 100, 'y' => $startY + 80],
            'keperluan' => ['x' => $leftMargin, 'y' => $startY + 100],
            'perihal' => ['x' => $leftMargin, 'y' => $startY + 120],
        ];
        
        foreach ($pdfFields as $index => $field) {
            $fieldName = $field['field_name'];
            
            if (isset($commonPositions[$fieldName])) {
                $detectedPositions[$fieldName] = $commonPositions[$fieldName];
            } else {
                // Dynamic positioning for unmapped fields
                $detectedPositions[$fieldName] = [
                    'x' => $leftMargin,
                    'y' => $startY + ($index * $lineHeight)
                ];
            }
        }
        
        return $detectedPositions;
    }
    
    private function analyzePdfStructure($templatePath)
    {
        $structure = [
            'content_start_y' => 80,
            'line_height' => 20,
            'left_margin' => 100,
            'page_width' => 612,
            'page_height' => 792
        ];
        
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($templatePath);
            $pages = $pdf->getPages();
            
            if (!empty($pages)) {
                $page = $pages[0];
                $pageDetails = $page->getDetails();
                
                // Extract page dimensions
                if (isset($pageDetails['MediaBox'])) {
                    $mediaBox = $pageDetails['MediaBox'];
                    $structure['page_width'] = $mediaBox[2] - $mediaBox[0];
                    $structure['page_height'] = $mediaBox[3] - $mediaBox[1];
                }
                
                // Analyze text content to determine margins and spacing
                $textContent = $page->getText();
                $lines = explode("\n", $textContent);
                
                // Estimate content start based on first meaningful line
                $meaningfulLineFound = false;
                foreach ($lines as $index => $line) {
                    $line = trim($line);
                    if (!empty($line) && strlen($line) > 5 && !$meaningfulLineFound) {
                        $structure['content_start_y'] = 60 + ($index * 15);
                        $meaningfulLineFound = true;
                        break;
                    }
                }
                
                // Estimate line height based on content density
                $nonEmptyLines = array_filter($lines, function($line) {
                    return !empty(trim($line));
                });
                
                if (count($nonEmptyLines) > 1) {
                    $estimatedHeight = $structure['page_height'] * 0.7; // Usable content area
                    $structure['line_height'] = $estimatedHeight / count($nonEmptyLines);
                    $structure['line_height'] = max(12, min(25, $structure['line_height'])); // Reasonable bounds
                }
            }
            
        } catch (\Exception $e) {
            \Log::warning('PDF structure analysis failed: ' . $e->getMessage());
        }
        
        return $structure;
    }
    
    private function createAdvancedFilledPdf($templatePath, $formData, $pdfFields)
    {
        // Test FPDI first
        if (!$this->testFpdi()) {
            throw new \Exception('FPDI library not working properly');
        }
        
        // Create new FPDI instance
        $pdf = new \setasign\Fpdi\Fpdi();
        
        // Detect variable positions automatically
        $positions = $this->detectVariablePositions($templatePath, $pdfFields);
        
        // Get page count
        $pageCount = $pdf->setSourceFile($templatePath);
        
        // Process each page
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            // Add a page
            $pdf->AddPage();
            
            // Import the page
            $templateId = $pdf->importPage($pageNo);
            
            // Use the imported page as template
            $pdf->useTemplate($templateId);
            
            // Set font for text overlay
            $pdf->SetFont('Arial', '', 11);
            $pdf->SetTextColor(0, 0, 0);
            
            // Add filled variables using detected positions
            foreach ($pdfFields as $field) {
                $fieldName = $field['field_name'];
                $fieldValue = $formData[$fieldName] ?? '';
                
                // Debug: Log each field processing
                \Log::info("Processing field: {$fieldName} = {$fieldValue}");
                
                if (!empty($fieldValue) && isset($positions[$fieldName])) {
                    // Format date fields
                    if ($field['type'] === 'date' && !empty($fieldValue)) {
                        $fieldValue = date('d/m/Y', strtotime($fieldValue));
                    }
                    
                    $x = $positions[$fieldName]['x'];
                    $y = $positions[$fieldName]['y'];
                    
                    // Add text at detected position
                    $pdf->SetXY($x, $y);
                    
                    // Adjust font size based on content length
                    if (strlen($fieldValue) > 30) {
                        $pdf->SetFont('Arial', '', 9);
                        $pdf->MultiCell(120, 4, $fieldValue, 0, 'L');
                    } else {
                        $pdf->SetFont('Arial', '', 11);
                        $pdf->Write(0, $fieldValue);
                    }
                }
            }
        }
        
        // Return PDF content
        return $pdf->Output('S');
    }

    private function testFpdi()
    {
        try {
            // Simple FPDI test
            $pdf = new \setasign\Fpdi\Fpdi();
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 12);
            $pdf->Text(50, 50, 'FPDI Test - Working!');
            
            \Log::info('FPDI Test: Success - FPDI is working correctly');
            return true;
        } catch (\Exception $e) {
            \Log::error('FPDI Test Failed: ' . $e->getMessage());
            \Log::error('FPDI Test Stack Trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    private function createVariableReplacedPdf($templatePath, $formData, $pdfFields, $kategori)
    {
        // Try to extract text content from PDF and replace variables
        try {
            return $this->extractAndReplacePdfContent($templatePath, $formData, $pdfFields, $kategori);
        } catch (\Exception $e) {
            // Fallback: Create a new PDF that mimics the original template
            $pdfInfo = $this->analyzePdfTemplate($templatePath);
            return $this->generateReplacedContentPdf($kategori, $formData, $pdfFields, $pdfInfo);
        }
    }
    
    private function extractAndReplacePdfContent($templatePath, $formData, $pdfFields, $kategori)
    {
        // Try to read PDF content using basic text extraction
        $pdfContent = $this->extractPdfText($templatePath);
        
        if (!empty($pdfContent)) {
            // Replace variables in the extracted content
            $filledContent = $this->replaceVariablesInText($pdfContent, $formData, $pdfFields);
            
            // Create new PDF with the filled content
            return $this->createPdfFromText($filledContent, $kategori);
        }
        
        throw new \Exception('Could not extract PDF content');
    }
    
    private function extractPdfText($templatePath)
    {
        // Basic PDF text extraction - this is a simplified approach
        // In production, you might want to use libraries like Smalot\PdfParser
        try {
            // Try to read PDF as text (this works for simple PDFs)
            $content = file_get_contents($templatePath);
            
            // Extract text between stream objects (very basic approach)
            if (preg_match_all('/stream\s*\n(.*?)\nendstream/s', $content, $matches)) {
                $textContent = '';
                foreach ($matches[1] as $stream) {
                    // Try to decode simple text streams
                    $decoded = $this->decodePdfStream($stream);
                    if ($decoded) {
                        $textContent .= $decoded . "\n";
                    }
                }
                
                if (!empty($textContent)) {
                    return $textContent;
                }
            }
            
            // If no text found, return empty to trigger fallback
            return '';
            
        } catch (\Exception $e) {
            return '';
        }
    }
    
    private function decodePdfStream($stream)
    {
        // Very basic PDF stream decoding - this is simplified
        // Real PDF parsing would require proper decompression and parsing
        
        // Look for text operations like (text) Tj or [text] TJ
        if (preg_match_all('/\((.*?)\)\s*Tj/', $stream, $matches)) {
            return implode(' ', $matches[1]);
        }
        
        // Look for array text operations
        if (preg_match_all('/\[(.*?)\]\s*TJ/', $stream, $matches)) {
            $text = '';
            foreach ($matches[1] as $match) {
                // Extract text from array format
                if (preg_match_all('/\((.*?)\)/', $match, $textMatches)) {
                    $text .= implode(' ', $textMatches[1]);
                }
            }
            return $text;
        }
        
        return '';
    }
    
    private function replaceVariablesInText($content, $formData, $pdfFields)
    {
        $filledContent = $content;
        
        // Replace variables from PDF form fields
        foreach ($pdfFields as $field) {
            $fieldName = $field['field_name'];
            $fieldValue = $formData[$fieldName] ?? '';
            
            if (!empty($fieldValue)) {
                // Format date fields
                if ($field['type'] === 'date') {
                    $fieldValue = date('d/m/Y', strtotime($fieldValue));
                }
                
                // Replace various variable formats
                $patterns = [
                    '{{' . $fieldName . '}}',
                    '{' . $fieldName . '}',
                    '[[' . $fieldName . ']]',
                    '[' . $fieldName . ']'
                ];
                
                foreach ($patterns as $pattern) {
                    $filledContent = str_replace($pattern, $fieldValue, $filledContent);
                }
            }
        }
        
        return $filledContent;
    }
    
    private function createPdfFromText($content, $kategori)
    {
        // Create HTML from the text content
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($kategori->nama) . '</title>
    <style>
        body { 
            font-family: "Times New Roman", serif; 
            line-height: 1.6; 
            margin: 30px;
            font-size: 12px;
            color: #333;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>' . nl2br(htmlspecialchars($content)) . '</body>
</html>';

        // Generate PDF using DomPDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->output();
    }
    
    private function analyzePdfTemplate($templatePath)
    {
        // Basic PDF analysis - in a real implementation, you might use
        // more sophisticated PDF parsing libraries
        return [
            'page_count' => 1,
            'page_size' => 'A4',
            'orientation' => 'portrait',
            'has_variables' => true
        ];
    }
    
    private function generateReplacedContentPdf($kategori, $formData, $pdfFields, $pdfInfo)
    {
        // Create HTML content that represents the filled template
        $htmlContent = $this->createHtmlFromTemplate($kategori, $formData, $pdfFields);
        
        // Generate PDF from HTML using DomPDF
        $pdf = Pdf::loadHTML($htmlContent);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->output();
    }
    
    private function createHtmlFromTemplate($kategori, $formData, $pdfFields)
    {
        // Create a structured HTML that mimics a typical letter format
        $content = $this->buildLetterContent($kategori, $formData, $pdfFields);
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($kategori->nama) . '</title>
    <style>
        body { 
            font-family: "Times New Roman", serif; 
            line-height: 1.6; 
            margin: 0;
            padding: 30px;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .letter-number {
            text-align: right;
            margin-bottom: 20px;
        }
        .content {
            margin: 20px 0;
            text-align: justify;
        }
        .signature {
            margin-top: 50px;
            text-align: right;
        }
        .field-row {
            margin: 8px 0;
        }
        .field-label {
            display: inline-block;
            width: 150px;
            font-weight: normal;
        }
        .field-value {
            font-weight: bold;
        }
    </style>
</head>
<body>
    ' . $content . '
</body>
</html>';

        return $html;
    }
    
    private function buildLetterContent($kategori, $formData, $pdfFields)
    {
        $content = '<div class="header">';
        $content .= '<h2>' . strtoupper($kategori->nama) . '</h2>';
        $content .= '</div>';
        
        // Add letter number and date if available
        if (isset($formData['nomor']) || isset($formData['nomor_surat'])) {
            $nomor = $formData['nomor'] ?? $formData['nomor_surat'] ?? '';
            $content .= '<div class="letter-number">';
            $content .= 'Nomor: <strong>' . htmlspecialchars($nomor) . '</strong>';
            $content .= '</div>';
        }
        
        if (isset($formData['tanggal']) || isset($formData['tanggal_surat'])) {
            $tanggal = $formData['tanggal'] ?? $formData['tanggal_surat'] ?? '';
            if ($tanggal) {
                $tanggal = date('d/m/Y', strtotime($tanggal));
            }
            $content .= '<div class="letter-number">';
            $content .= 'Tanggal: <strong>' . htmlspecialchars($tanggal) . '</strong>';
            $content .= '</div>';
        }
        
        $content .= '<div class="content">';
        
        // Add all form fields in a structured way
        foreach ($pdfFields as $field) {
            $fieldName = $field['field_name'];
            $fieldLabel = $field['label'];
            $fieldValue = $formData[$fieldName] ?? '';
            
            if (!empty($fieldValue)) {
                // Format date fields
                if ($field['type'] === 'date') {
                    $fieldValue = date('d/m/Y', strtotime($fieldValue));
                }
                
                $content .= '<div class="field-row">';
                $content .= '<span class="field-label">' . htmlspecialchars($fieldLabel) . '</span>';
                $content .= ': <span class="field-value">' . htmlspecialchars($fieldValue) . '</span>';
                $content .= '</div>';
            }
        }
        
        $content .= '</div>';
        
        // Add signature area
        $content .= '<div class="signature">';
        $content .= '<p>Hormat kami,</p>';
        $content .= '<br><br><br>';
        $content .= '<p>(_____________________)</p>';
        $content .= '</div>';
        
        return $content;
    }
    
    private function createSmartOverlayPdf($templatePath, $formData, $pdfFields)
    {
        // Enhanced overlay method with better variable positioning
        $pdf = new \setasign\Fpdi\Fpdi();
        
        // Get page count
        $pageCount = $pdf->setSourceFile($templatePath);
        
        // Process each page
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pdf->AddPage();
            $templateId = $pdf->importPage($pageNo);
            $pdf->useTemplate($templateId);
            
            // Add variables with smart positioning
            $this->addSmartVariableOverlays($pdf, $formData, $pdfFields, $pageNo);
        }
        
        return $pdf->Output('S');
    }
    
    private function addSmartVariableOverlays($pdf, $formData, $pdfFields, $pageNo)
    {
        // Enhanced positioning system that tries to detect variable locations
        $positions = $this->getSmartVariablePositions($pdfFields);
        
        foreach ($pdfFields as $field) {
            $fieldName = $field['field_name'];
            $fieldValue = $formData[$fieldName] ?? '';
            
            if (!empty($fieldValue)) {
                // Format date fields
                if ($field['type'] === 'date') {
                    $fieldValue = date('d/m/Y', strtotime($fieldValue));
                }
                
                // Get position for this variable
                $position = $positions[$fieldName] ?? ['x' => 100, 'y' => 100];
                
                // Set font and color
                $pdf->SetFont('Arial', '', 10);
                $pdf->SetTextColor(0, 0, 0);
                
                // Add text with white background to cover original variable
                $pdf->SetXY($position['x'], $position['y']);
                
                // Calculate text width for better coverage
                $textWidth = $pdf->GetStringWidth($fieldValue) + 2;
                
                // Add a white rectangle to cover the original variable
                $pdf->SetFillColor(255, 255, 255);
                $pdf->Rect($position['x'] - 1, $position['y'] - 1, $textWidth, 6, 'F');
                
                // Add the new text
                $pdf->SetXY($position['x'], $position['y']);
                $pdf->Write(0, $fieldValue);
            }
        }
    }
    
    private function getSmartVariablePositions($pdfFields)
    {
        // Smart positioning based on common document layouts
        $positions = [];
        $startY = 80;
        $lineHeight = 15;
        
        // Common positions for typical letter formats
        $commonPositions = [
            'nomor' => ['x' => 120, 'y' => 50],
            'nomor_surat' => ['x' => 120, 'y' => 50],
            'tanggal' => ['x' => 120, 'y' => 65],
            'tanggal_surat' => ['x' => 120, 'y' => 65],
            'nama' => ['x' => 120, 'y' => 100],
            'nama_pemohon' => ['x' => 120, 'y' => 100],
            'alamat' => ['x' => 120, 'y' => 115],
            'alamat_pemohon' => ['x' => 120, 'y' => 115],
            'no_ktp' => ['x' => 120, 'y' => 130],
            'tempat_lahir' => ['x' => 120, 'y' => 145],
            'tanggal_lahir' => ['x' => 180, 'y' => 145],
            'keperluan' => ['x' => 120, 'y' => 160],
            'perihal' => ['x' => 120, 'y' => 175],
        ];
        
        foreach ($pdfFields as $index => $field) {
            $fieldName = $field['field_name'];
            
            if (isset($commonPositions[$fieldName])) {
                $positions[$fieldName] = $commonPositions[$fieldName];
            } else {
                // Dynamic positioning for unmapped fields
                $positions[$fieldName] = [
                    'x' => 120,
                    'y' => $startY + ($index * $lineHeight)
                ];
            }
        }
        
        return $positions;
    }
    
    private function createPreciseVariableOverlay($templatePath, $formData, $pdfFields)
    {
        try {
            // Log input data for debugging
            \Log::info('Creating precise variable overlay', [
                'template_path' => $templatePath,
                'form_data' => $formData,
                'pdf_fields' => $pdfFields
            ]);
            
            // Metode ini akan mengganti variabel di PDF template asli dengan tepat
            $pdf = new \setasign\Fpdi\Fpdi();
            
            // Import halaman dari template asli
            $pageCount = $pdf->setSourceFile($templatePath);
            \Log::info("PDF has {$pageCount} pages");
            
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $pdf->AddPage();
                $templateId = $pdf->importPage($pageNo);
                $pdf->useTemplate($templateId);
                
                // Ganti variabel dengan data yang diinput menggunakan metode yang lebih akurat
                $this->replaceVariablesInPdfPagePrecise($pdf, $formData, $pdfFields, $templatePath, $pageNo);
            }
            
            return $pdf->Output('S');
            
        } catch (\Exception $e) {
            \Log::error('Error in createPreciseVariableOverlay: ' . $e->getMessage());
            throw $e;
        }
    }
    
    private function replaceVariablesInPdfPageEnhanced($pdf, $formData, $pdfFields, $templatePath, $pageNo)
    {
        \Log::info("Starting variable replacement for page {$pageNo}");
        
        // Cari posisi variabel di PDF dan ganti dengan nilai yang diinput
        foreach ($pdfFields as $field) {
            $fieldName = $field['field_name'];
            $fieldValue = $formData[$fieldName] ?? '';
            
            \Log::info("Processing field: {$fieldName} = '{$fieldValue}'");
            
            if (!empty($fieldValue)) {
                // Format tanggal jika diperlukan
                if (isset($field['type']) && $field['type'] === 'date') {
                    $fieldValue = date('d/m/Y', strtotime($fieldValue));
                    \Log::info("Formatted date value: {$fieldValue}");
                }
                
                // Cari posisi variabel menggunakan metode yang lebih akurat
                $variablePosition = $this->findVariablePositionEnhanced($templatePath, $fieldName, $pageNo, $pdfFields);
                
                if ($variablePosition) {
                    \Log::info("Found position for {$fieldName}: x={$variablePosition['x']}, y={$variablePosition['y']}");
                    
                    // Hitung lebar variabel asli untuk menutupi dengan tepat
                    $variableText = '{{' . $fieldName . '}}';
                    $pdf->SetFont('Arial', '', 10);
                    $variableWidth = $pdf->GetStringWidth($variableText) + 5; // Extra padding
                    
                    // Tutup variabel asli dengan kotak putih yang lebih besar
                    $pdf->SetFillColor(255, 255, 255);
                    $pdf->Rect($variablePosition['x'] - 2, $variablePosition['y'] - 2, $variableWidth, 8, 'F');
                    
                    // Tulis nilai baru di posisi yang sama
                    $pdf->SetFont('Arial', '', 10);
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetXY($variablePosition['x'], $variablePosition['y']);
                    $pdf->Write(0, $fieldValue);
                    
                    \Log::info("Successfully replaced {{$fieldName}} with '{$fieldValue}' at position ({$variablePosition['x']}, {$variablePosition['y']})");
                } else {
                    \Log::warning("Could not find position for variable: {$fieldName}");
                }
            } else {
                \Log::info("Skipping empty field: {$fieldName}");
            }
        }
    }
    
    private function replaceVariablesInPdfPagePrecise($pdf, $formData, $pdfFields, $templatePath, $pageNo)
    {
        \Log::info("Starting PRECISE variable replacement for page {$pageNo}");
        
        // Analisis template PDF untuk mendapatkan informasi yang lebih akurat
        $templateAnalysis = $this->analyzeTemplateForVariables($templatePath);
        \Log::info("Template analysis result:", $templateAnalysis);
        
        // Cari posisi variabel di PDF dan ganti dengan nilai yang diinput
        foreach ($pdfFields as $field) {
            $fieldName = $field['field_name'];
            $fieldValue = $formData[$fieldName] ?? '';
            
            \Log::info("Processing field: {$fieldName} = '{$fieldValue}'");
            
            if (!empty($fieldValue)) {
                // Format tanggal jika diperlukan
                if (isset($field['type']) && $field['type'] === 'date') {
                    $fieldValue = date('d/m/Y', strtotime($fieldValue));
                    \Log::info("Formatted date value: {$fieldValue}");
                }
                
                // Cari posisi variabel dengan prioritas:
                // 1. Dari analisis template yang akurat
                // 2. Dari konfigurasi user
                // 3. Dari deteksi dinamis
                // 4. Fallback default
                $variablePosition = $this->findPreciseVariablePosition($templatePath, $fieldName, $pdfFields, $templateAnalysis);
                
                if ($variablePosition) {
                    \Log::info("Found PRECISE position for {$fieldName}: x={$variablePosition['x']}, y={$variablePosition['y']}");
                    
                    // Gunakan metode overlay yang lebih akurat
                    $this->applyPreciseVariableOverlay($pdf, $fieldName, $fieldValue, $variablePosition);
                    
                    \Log::info("Successfully replaced {{$fieldName}} with '{$fieldValue}' using PRECISE method");
                } else {
                    \Log::warning("Could not find PRECISE position for variable: {$fieldName}");
                }
            } else {
                \Log::info("Skipping empty field: {$fieldName}");
            }
        }
    }
    
    private function analyzeTemplateForVariables($templatePath)
    {
        $analysis = [
            'variables_found' => [],
            'text_content' => '',
            'estimated_positions' => [],
            'page_dimensions' => ['width' => 612, 'height' => 792]
        ];
        
        try {
            // Gunakan PDF parser untuk analisis yang lebih mendalam
            $parser = new Parser();
            $pdf = $parser->parseFile($templatePath);
            $pages = $pdf->getPages();
            
            if (!empty($pages)) {
                $page = $pages[0];
                $pageText = $page->getText();
                $analysis['text_content'] = $pageText;
                
                // Cari semua variabel dalam format {{variable}}
                if (preg_match_all('/\{\{([^}]+)\}\}/', $pageText, $matches)) {
                    $analysis['variables_found'] = $matches[1];
                    
                    // Estimasi posisi untuk setiap variabel yang ditemukan
                    foreach ($matches[0] as $index => $fullMatch) {
                        $variableName = $matches[1][$index];
                        $position = $this->estimateVariablePositionInText($pageText, $fullMatch);
                        if ($position) {
                            $analysis['estimated_positions'][$variableName] = $position;
                        }
                    }
                }
                
                // Analisis dimensi halaman
                $pageDetails = $page->getDetails();
                if (isset($pageDetails['MediaBox'])) {
                    $mediaBox = $pageDetails['MediaBox'];
                    $analysis['page_dimensions'] = [
                        'width' => $mediaBox[2] - $mediaBox[0],
                        'height' => $mediaBox[3] - $mediaBox[1]
                    ];
                }
            }
            
        } catch (\Exception $e) {
            \Log::warning("Template analysis failed: " . $e->getMessage());
        }
        
        return $analysis;
    }
    
    private function estimateVariablePositionInText($fullText, $variablePattern)
    {
        $lines = explode("\n", $fullText);
        
        // Analisis yang lebih akurat berdasarkan struktur dokumen
        $headerEndLine = 0;
        $contentStartLine = 0;
        
        // Cari akhir header (biasanya ada garis atau pemisah)
        foreach ($lines as $index => $line) {
            if (strpos($line, '___') !== false || strpos($line, '---') !== false || 
                strpos($line, 'SURAT') !== false || strpos($line, 'KECAMATAN') !== false) {
                $headerEndLine = $index;
            }
        }
        
        $contentStartLine = max($headerEndLine + 1, 5); // Minimal line 5
        
        foreach ($lines as $lineIndex => $line) {
            if (strpos($line, $variablePattern) !== false) {
                $charPosition = strpos($line, $variablePattern);
                
                // Hitung posisi yang lebih akurat
                $estimatedX = 50 + ($charPosition * 4.5); // Karakter width yang lebih akurat
                
                // Y position berdasarkan posisi line dan apakah di header atau content
                if ($lineIndex <= $headerEndLine) {
                    // Di area header
                    $estimatedY = 40 + ($lineIndex * 12);
                } else {
                    // Di area content
                    $estimatedY = 80 + (($lineIndex - $contentStartLine) * 15);
                }
                
                // Penyesuaian khusus untuk variabel tertentu
                if (strpos($variablePattern, 'nomor') !== false) {
                    $estimatedX = max($estimatedX, 120); // Nomor biasanya di kanan
                    $estimatedY = min($estimatedY, 70);  // Nomor biasanya di atas
                }
                
                \Log::info("Estimated position for '{$variablePattern}': x={$estimatedX}, y={$estimatedY} (line {$lineIndex}, char {$charPosition})");
                
                return [
                    'x' => max(50, min(500, $estimatedX)),
                    'y' => max(30, min(700, $estimatedY)),
                    'line' => $lineIndex,
                    'char_position' => $charPosition
                ];
            }
        }
        
        return null;
    }
    
    private function findPreciseVariablePosition($templatePath, $fieldName, $pdfFields, $templateAnalysis)
    {
        // 1. Prioritas tertinggi: Dari analisis template yang akurat
        if (isset($templateAnalysis['estimated_positions'][$fieldName])) {
            $position = $templateAnalysis['estimated_positions'][$fieldName];
            \Log::info("Using template analysis position for {$fieldName}: x={$position['x']}, y={$position['y']}");
            return $position;
        }
        
        // 2. Dari konfigurasi pengguna
        foreach ($pdfFields as $field) {
            if (isset($field['field_name']) && $field['field_name'] === $fieldName) {
                if (isset($field['position_x']) && isset($field['position_y']) && 
                    $field['position_x'] > 0 && $field['position_y'] > 0) {
                    \Log::info("Using configured position for {$fieldName}: x={$field['position_x']}, y={$field['position_y']}");
                    return [
                        'x' => (int)$field['position_x'],
                        'y' => (int)$field['position_y']
                    ];
                }
            }
        }
        
        // 3. Deteksi dinamis dengan pola yang lebih luas
        try {
            $dynamicPosition = $this->detectVariablePositionInPdf($templatePath, $fieldName);
            if ($dynamicPosition) {
                \Log::info("Using dynamic detection for {$fieldName}: x={$dynamicPosition['x']}, y={$dynamicPosition['y']}");
                return $dynamicPosition;
            }
        } catch (\Exception $e) {
            \Log::warning("Dynamic detection failed for {$fieldName}: " . $e->getMessage());
        }
        
        // 4. Fallback dengan posisi yang disesuaikan berdasarkan template analysis
        $baseY = 60; // Default start
        if (isset($templateAnalysis['page_dimensions'])) {
            // Sesuaikan berdasarkan ukuran halaman
            $pageHeight = $templateAnalysis['page_dimensions']['height'];
            $baseY = $pageHeight > 800 ? 80 : 60;
        }
        
        $precisePositions = [
            'nomor' => ['x' => 280, 'y' => $baseY - 10],      // Lebih ke kanan untuk nomor
            'nomor_surat' => ['x' => 280, 'y' => $baseY - 10],
            'tanggal' => ['x' => 280, 'y' => $baseY + 10],    // Di bawah nomor
            'tanggal_surat' => ['x' => 280, 'y' => $baseY + 10],
            'nama' => ['x' => 120, 'y' => $baseY + 60],
            'nama_pemohon' => ['x' => 120, 'y' => $baseY + 60],
            'alamat' => ['x' => 120, 'y' => $baseY + 80],
            'alamat_pemohon' => ['x' => 120, 'y' => $baseY + 80],
            'no_ktp' => ['x' => 120, 'y' => $baseY + 100],
            'tempat_lahir' => ['x' => 120, 'y' => $baseY + 120],
            'tanggal_lahir' => ['x' => 200, 'y' => $baseY + 120],
            'keperluan' => ['x' => 120, 'y' => $baseY + 140],
            'perihal' => ['x' => 120, 'y' => $baseY + 160],
        ];
        
        $position = $precisePositions[$fieldName] ?? ['x' => 120, 'y' => $baseY + 40];
        \Log::info("Using PRECISE fallback position for {$fieldName}: x={$position['x']}, y={$position['y']}");
        return $position;
    }
    
    private function applyPreciseVariableOverlay($pdf, $fieldName, $fieldValue, $position)
    {
        // Hitung lebar variabel asli dengan lebih akurat
        $variableText = '{{' . $fieldName . '}}';
        $pdf->SetFont('Arial', '', 10);
        $variableWidth = $pdf->GetStringWidth($variableText);
        $valueWidth = $pdf->GetStringWidth($fieldValue);
        
        // Gunakan lebar yang lebih besar untuk menutupi dengan sempurna
        $coverWidth = max($variableWidth, $valueWidth) + 10;
        
        // Tutup area variabel dengan kotak putih yang lebih besar
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect($position['x'] - 3, $position['y'] - 3, $coverWidth, 10, 'F');
        
        // Tulis nilai baru dengan font yang sesuai
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY($position['x'], $position['y']);
        $pdf->Write(0, $fieldValue);
        
        \Log::info("Applied precise overlay for {$fieldName}: covered area ({$position['x']}-3, {$position['y']}-3, {$coverWidth}, 10)");
    }
    
    private function findVariablePositionEnhanced($templatePath, $fieldName, $pageNo, $pdfFields = [])
    {
        // 1. Cari dari konfigurasi pengguna terlebih dahulu (prioritas tertinggi)
        foreach ($pdfFields as $field) {
            if (isset($field['field_name']) && $field['field_name'] === $fieldName) {
                if (isset($field['position_x']) && isset($field['position_y'])) {
                    \Log::info("Using configured position for {$fieldName}: x={$field['position_x']}, y={$field['position_y']}");
                    return [
                        'x' => (int)$field['position_x'],
                        'y' => (int)$field['position_y']
                    ];
                }
            }
        }
        
        // 2. Coba deteksi dinamis menggunakan PDF parser
        try {
            $dynamicPosition = $this->detectVariablePositionInPdf($templatePath, $fieldName);
            if ($dynamicPosition) {
                \Log::info("Using dynamic detection for {$fieldName}: x={$dynamicPosition['x']}, y={$dynamicPosition['y']}");
                return $dynamicPosition;
            }
        } catch (\Exception $e) {
            \Log::warning("Dynamic detection failed for {$fieldName}: " . $e->getMessage());
        }
        
        // 3. Fallback ke posisi default yang lebih akurat
        $enhancedPositions = [
            'nomor' => ['x' => 120, 'y' => 55],
            'nomor_surat' => ['x' => 120, 'y' => 55],
            'tanggal' => ['x' => 120, 'y' => 75],
            'tanggal_surat' => ['x' => 120, 'y' => 75],
            'nama' => ['x' => 120, 'y' => 120],
            'nama_pemohon' => ['x' => 120, 'y' => 120],
            'alamat' => ['x' => 120, 'y' => 140],
            'alamat_pemohon' => ['x' => 120, 'y' => 140],
            'no_ktp' => ['x' => 120, 'y' => 160],
            'tempat_lahir' => ['x' => 120, 'y' => 180],
            'tanggal_lahir' => ['x' => 200, 'y' => 180],
            'keperluan' => ['x' => 120, 'y' => 200],
            'perihal' => ['x' => 120, 'y' => 220],
        ];
        
        $position = $enhancedPositions[$fieldName] ?? ['x' => 120, 'y' => 100];
        \Log::info("Using fallback position for {$fieldName}: x={$position['x']}, y={$position['y']}");
        return $position;
    }
    
    private function detectVariablePositionInPdf($templatePath, $fieldName)
    {
        try {
            // Gunakan smalot/pdfparser untuk deteksi yang lebih akurat
            $parser = new Parser();
            $pdf = $parser->parseFile($templatePath);
            $pages = $pdf->getPages();
            
            if (empty($pages)) {
                return null;
            }
            
            $page = $pages[0]; // Process first page
            $pageText = $page->getText();
            
            // Cari berbagai format variabel
            $patterns = [
                '{{' . $fieldName . '}}',
                '{' . $fieldName . '}',
                '[[' . $fieldName . ']]',
                '[' . $fieldName . ']',
                $fieldName . ':',
                strtoupper($fieldName) . ':',
                ucfirst($fieldName) . ':'
            ];
            
            foreach ($patterns as $pattern) {
                if (strpos($pageText, $pattern) !== false) {
                    \Log::info("Found pattern '{$pattern}' in PDF text");
                    
                    // Estimasi posisi berdasarkan posisi dalam teks
                    $position = $this->estimatePositionFromText($pageText, $pattern);
                    if ($position) {
                        return $position;
                    }
                }
            }
            
            return null;
            
        } catch (\Exception $e) {
            \Log::error("PDF parsing failed for variable detection: " . $e->getMessage());
            return null;
        }
    }
    
    private function estimatePositionFromText($fullText, $pattern)
    {
        $lines = explode("\n", $fullText);
        $lineHeight = 15; // Estimated line height
        $startY = 50; // Estimated start position
        $leftMargin = 50; // Estimated left margin
        
        foreach ($lines as $lineIndex => $line) {
            if (strpos($line, $pattern) !== false) {
                // Estimate X position based on character position in line
                $charPosition = strpos($line, $pattern);
                $estimatedX = $leftMargin + ($charPosition * 6); // Rough character width
                $estimatedY = $startY + ($lineIndex * $lineHeight);
                
                \Log::info("Estimated position for '{$pattern}': x={$estimatedX}, y={$estimatedY}");
                
                return [
                    'x' => max(50, min(500, $estimatedX)), // Bounds checking
                    'y' => max(30, min(700, $estimatedY))
                ];
            }
        }
        
        return null;
    }
    
    private function findVariablePositionInPdf($templatePath, $fieldName, $pageNo, $pdfFields = [])
    {
        // Cari posisi dari konfigurasi pengguna terlebih dahulu
        foreach ($pdfFields as $field) {
            if ($field['field_name'] === $fieldName) {
                return [
                    'x' => $field['position_x'] ?? 100,
                    'y' => $field['position_y'] ?? 100
                ];
            }
        }
        
        // Fallback ke posisi default jika tidak ada konfigurasi
        $mappedPositions = [
            'nomor' => ['x' => 100, 'y' => 60],
            'nomor_surat' => ['x' => 100, 'y' => 60],
            'tanggal' => ['x' => 150, 'y' => 60],
            'tanggal_surat' => ['x' => 150, 'y' => 60],
            'nama' => ['x' => 100, 'y' => 120],
            'nama_pemohon' => ['x' => 100, 'y' => 120],
            'alamat' => ['x' => 100, 'y' => 140],
            'alamat_pemohon' => ['x' => 100, 'y' => 140],
            'no_ktp' => ['x' => 100, 'y' => 160],
            'tempat_lahir' => ['x' => 100, 'y' => 180],
            'tanggal_lahir' => ['x' => 180, 'y' => 180],
            'keperluan' => ['x' => 100, 'y' => 200],
            'perihal' => ['x' => 100, 'y' => 220],
        ];
        
        return $mappedPositions[$fieldName] ?? ['x' => 100, 'y' => 100];
    }
    
    private function detectVariablePosition($templatePath, $fieldName, $pageNo)
    {
        // Implementasi sederhana untuk mendeteksi posisi variabel
        // Dalam implementasi nyata, Anda mungkin perlu library PDF parsing yang lebih canggih
        
        try {
            // Baca konten PDF sebagai teks (metode sederhana)
            $pdfContent = file_get_contents($templatePath);
            
            // Cari pola variabel
            $variablePattern = '{{' . $fieldName . '}}';
            
            // Jika ditemukan, return posisi default berdasarkan urutan
            if (strpos($pdfContent, $variablePattern) !== false) {
                // Posisi default berdasarkan nama field
                $defaultPositions = [
                    'nomor' => ['x' => 100, 'y' => 60],
                    'tanggal' => ['x' => 150, 'y' => 60],
                    'nama' => ['x' => 100, 'y' => 120],
                    'alamat' => ['x' => 100, 'y' => 140],
                ];
                
                return $defaultPositions[$fieldName] ?? ['x' => 100, 'y' => 100];
            }
        } catch (\Exception $e) {
            \Log::error("Error detecting variable position: " . $e->getMessage());
        }
        
        // Fallback ke posisi default
        return ['x' => 100, 'y' => 100];
    }
    
    private function calculateVariableWidth($fieldName)
    {
        // Hitung lebar variabel untuk menutupi teks asli
        $variableText = '{{' . $fieldName . '}}';
        return strlen($variableText) * 3; // Perkiraan lebar
    }
    
    private function generateFromDocxTemplate($kategori, $formData, $docxFields)
    {
        try {
            $templatePath = storage_path('app/public/' . $kategori->docx_template_path);
            
            if (!file_exists($templatePath)) {
                throw new \Exception('Template DOCX tidak ditemukan: ' . $templatePath);
            }
            
            \Log::info('Processing DOCX template: ' . $templatePath);
            \Log::info('Form data: ', $formData);
            \Log::info('DOCX fields: ', $docxFields);
            
            // Create TemplateProcessor instance
            $templateProcessor = new TemplateProcessor($templatePath);
            
            // Replace variables in DOCX template
            foreach ($docxFields as $field) {
                $fieldName = $field['field_name'];
                $fieldValue = $formData[$fieldName] ?? '';
                
                if (!empty($fieldValue)) {
                    // Format date fields
                    if (isset($field['type']) && $field['type'] === 'date') {
                        $fieldValue = date('d/m/Y', strtotime($fieldValue));
                    }
                    
                    // Replace variable in DOCX template
                    $templateProcessor->setValue($fieldName, $fieldValue);
                    \Log::info("Replaced {$fieldName} with: {$fieldValue}");
                } else {
                    // Set empty value for missing fields
                    $templateProcessor->setValue($fieldName, '');
                    \Log::info("Set empty value for: {$fieldName}");
                }
            }
            
            // Also replace any additional form data that might not be in docx_fields
            foreach ($formData as $key => $value) {
                if (!empty($value) && is_string($value)) {
                    // Format date values
                    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                        $value = date('d/m/Y', strtotime($value));
                    }
                    
                    // Try to replace the variable
                    try {
                        $templateProcessor->setValue($key, $value);
                        \Log::info("Additional replacement: {$key} = {$value}");
                    } catch (\Exception $e) {
                        // Variable might not exist in template, that's OK
                        \Log::info("Variable {$key} not found in template (OK)");
                    }
                }
            }
            
            // Save filled DOCX to temporary file
            $tempDocxPath = sys_get_temp_dir() . '/filled_' . uniqid() . '.docx';
            
            $templateProcessor->saveAs($tempDocxPath);
            \Log::info('Filled DOCX saved to: ' . $tempDocxPath);
            
            // Convert DOCX to PDF using DomPDF (fallback method)
            // For better conversion, you might want to use LibreOffice or other tools
            $pdfContent = $this->convertDocxToPdf($tempDocxPath, $kategori, $formData, $docxFields);
            
            // Clean up temporary file
            if (file_exists($tempDocxPath)) {
                unlink($tempDocxPath);
            }
            
            return $pdfContent;
            
        } catch (\Exception $e) {
            \Log::error('DOCX processing failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw new \Exception('Error processing DOCX template: ' . $e->getMessage());
        }
    }
    
    private function convertDocxToPdf($docxPath, $kategori, $formData, $docxFields)
    {
        try {
            // Use enhanced conversion method for better quality
            return $this->createEnhancedPdfFromDocx($docxPath, $kategori);
            
        } catch (\Exception $e) {
            \Log::error('Enhanced DOCX to PDF conversion failed: ' . $e->getMessage());
            
            // Fallback 1: Try original HTML method
            try {
                $htmlContent = $this->convertDocxToHtml($docxPath);
                if (!empty($htmlContent)) {
                    return $this->createPdfFromDocxHtml($htmlContent, $kategori);
                }
            } catch (\Exception $e2) {
                \Log::warning('HTML conversion fallback failed: ' . $e2->getMessage());
            }
            
            // Fallback 2: Extract text and create formatted PDF
            try {
                $textContent = $this->extractTextFromDocx($docxPath);
                if (!empty($textContent)) {
                    return $this->createFormattedPdfFromDocxText($textContent, $kategori);
                }
            } catch (\Exception $e3) {
                \Log::warning('Text extraction fallback failed: ' . $e3->getMessage());
            }
            
            // Ultimate fallback
            return $this->createStructuredPdfFromDocxFields($kategori, $formData, $docxFields);
        }
    }
    
    private function extractTextFromDocx($docxPath)
    {
        try {
            // Use PhpWord to read the filled DOCX
            $phpWord = IOFactory::load($docxPath);
            $textContent = '';
            
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $textContent .= $element->getText() . "\n";
                    } elseif (method_exists($element, 'getElements')) {
                        // Handle nested elements like paragraphs
                        foreach ($element->getElements() as $subElement) {
                            if (method_exists($subElement, 'getText')) {
                                $textContent .= $subElement->getText() . " ";
                            }
                        }
                        $textContent .= "\n";
                    }
                }
            }
            
            return trim($textContent);
            
        } catch (\Exception $e) {
            \Log::warning('Text extraction from DOCX failed: ' . $e->getMessage());
            return '';
        }
    }
    
    private function createPdfFromDocxText($textContent, $kategori)
    {
        // Create HTML from the extracted text
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($kategori->nama) . '</title>
    <style>
        body { 
            font-family: "Times New Roman", serif; 
            line-height: 1.6; 
            margin: 30px;
            font-size: 12px;
            color: #333;
            white-space: pre-wrap;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>' . htmlspecialchars($kategori->nama) . '</h2>
        <small>Generated from DOCX Template - ' . date('d/m/Y H:i:s') . '</small>
    </div>
    <div class="content">' . nl2br(htmlspecialchars($textContent)) . '</div>
</body>
</html>';

        // Generate PDF using DomPDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->output();
    }
    
    private function createStructuredPdfFromDocxFields($kategori, $formData, $docxFields)
    {
        // Create structured content from form fields
        $content = '<div class="header">';
        $content .= '<h2>' . strtoupper($kategori->nama) . '</h2>';
        $content .= '<small>Generated from DOCX Template - ' . date('d/m/Y H:i:s') . '</small>';
        $content .= '</div>';
        
        $content .= '<div class="content">';
        
        // Add form fields in a structured way
        foreach ($docxFields as $field) {
            $fieldName = $field['field_name'];
            $fieldLabel = $field['label'];
            $fieldValue = $formData[$fieldName] ?? '';
            
            if (!empty($fieldValue)) {
                // Format date fields
                if (isset($field['type']) && $field['type'] === 'date') {
                    $fieldValue = date('d/m/Y', strtotime($fieldValue));
                }
                
                $content .= '<div class="field-row">';
                $content .= '<span class="field-label">' . htmlspecialchars($fieldLabel) . '</span>';
                $content .= ': <span class="field-value">' . htmlspecialchars($fieldValue) . '</span>';
                $content .= '</div>';
            }
        }
        
        $content .= '</div>';
        
        // Add signature area
        $content .= '<div class="signature">';
        $content .= '<p>Hormat kami,</p>';
        $content .= '<br><br><br>';
        $content .= '<p>(_____________________)</p>';
        $content .= '</div>';
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($kategori->nama) . '</title>
    <style>
        body { 
            font-family: "Times New Roman", serif; 
            line-height: 1.6; 
            margin: 0;
            padding: 30px;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .content {
            margin: 20px 0;
            text-align: justify;
        }
        .signature {
            margin-top: 50px;
            text-align: right;
        }
        .field-row {
            margin: 8px 0;
        }
        .field-label {
            display: inline-block;
            width: 150px;
            font-weight: normal;
        }
        .field-value {
            font-weight: bold;
        }
    </style>
</head>
<body>
    ' . $content . '
</body>
</html>';

        // Generate PDF using DomPDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->output();
    }
    
    private function convertDocxToHtml($docxPath)
    {
        try {
            // Use PhpWord to read DOCX and convert to HTML
            $phpWord = IOFactory::load($docxPath);
            
            // Create HTML writer with better settings
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
            
            // Configure HTML writer for better formatting
            if (method_exists($htmlWriter, 'setUseDiskCaching')) {
                $htmlWriter->setUseDiskCaching(true);
            }
            
            // Save to temporary HTML file
            $tempHtmlPath = sys_get_temp_dir() . '/temp_' . uniqid() . '.html';
            
            $htmlWriter->save($tempHtmlPath);
            
            // Read HTML content
            $htmlContent = file_get_contents($tempHtmlPath);
            
            // Clean up temporary file
            if (file_exists($tempHtmlPath)) {
                unlink($tempHtmlPath);
            }
            
            return $htmlContent;
            
        } catch (\Exception $e) {
            \Log::warning('DOCX to HTML conversion failed: ' . $e->getMessage());
            return '';
        }
    }
    
    private function createPdfFromDocxHtml($htmlContent, $kategori)
    {
        // Clean and enhance HTML for better PDF rendering
        $cleanHtml = $this->cleanHtmlForPdf($htmlContent);
        
        // Create enhanced HTML with better styling
        $enhancedHtml = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($kategori->nama) . '</title>
    <style>
        body { 
            font-family: "Times New Roman", serif; 
            line-height: 1.6; 
            margin: 30px;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 15px;
        }
        p { 
            margin: 10px 0; 
            text-align: justify;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .underline { text-decoration: underline; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table, th, td {
            border: 1px solid #333;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <small>Generated from DOCX Template - ' . date('d/m/Y H:i:s') . '</small>
    </div>
    ' . $cleanHtml . '
</body>
</html>';

        // Generate PDF using DomPDF
        $pdf = Pdf::loadHTML($enhancedHtml);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->output();
    }
    
    private function cleanHtmlForPdf($htmlContent)
    {
        // Remove unnecessary HTML tags and clean content
        $cleanContent = $htmlContent;
        
        // Remove HTML, HEAD, BODY tags as we'll add our own
        $cleanContent = preg_replace('/<html[^>]*>|<\/html>/i', '', $cleanContent);
        $cleanContent = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $cleanContent);
        $cleanContent = preg_replace('/<body[^>]*>|<\/body>/i', '', $cleanContent);
        
        // Clean up excessive whitespace
        $cleanContent = preg_replace('/\s+/', ' ', $cleanContent);
        $cleanContent = preg_replace('/>\s+</', '><', $cleanContent);
        
        // Preserve paragraph breaks
        $cleanContent = str_replace('<p>', '<p>', $cleanContent);
        $cleanContent = str_replace('</p>', '</p>', $cleanContent);
        
        return trim($cleanContent);
    }
    
    private function createFormattedPdfFromDocxText($textContent, $kategori)
    {
        // Enhanced text formatting for better PDF appearance
        $formattedContent = $this->formatTextForPdf($textContent);
        
        // Create HTML with better formatting
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($kategori->nama) . '</title>
    <style>
        body { 
            font-family: "Times New Roman", serif; 
            line-height: 1.8; 
            margin: 30px;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            font-weight: bold;
        }
        .content {
            white-space: pre-line;
            text-align: justify;
            margin: 20px 0;
        }
        .signature-area {
            margin-top: 50px;
            text-align: right;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .underline { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="header">
        ' . htmlspecialchars($kategori->nama) . '<br>
        <small>Generated from DOCX Template - ' . date('d/m/Y H:i:s') . '</small>
    </div>
    <div class="content">' . $formattedContent . '</div>
</body>
</html>';

        // Generate PDF using DomPDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->output();
    }
    
    private function formatTextForPdf($textContent)
    {
        // Format text content for better PDF appearance
        $formatted = $textContent;
        
        // Convert line breaks to HTML
        $formatted = nl2br(htmlspecialchars($formatted));
        
        // Detect and format headers (lines in ALL CAPS)
        $formatted = preg_replace('/^([A-Z\s]{10,})$/m', '<div class="center bold">$1</div>', $formatted);
        
        // Format lines that look like titles
        $formatted = preg_replace('/^(SURAT [A-Z\s]+)$/m', '<div class="center bold underline">$1</div>', $formatted);
        
        // Format signature areas
        $formatted = preg_replace('/(Hormat kami,?|Kepala Desa|Mengetahui,?)/', '<div class="right">$1</div>', $formatted);
        
        // Format nomor and tanggal lines
        $formatted = preg_replace('/^(Nomor\s*:.*?)$/m', '<div class="right">$1</div>', $formatted);
        $formatted = preg_replace('/^(Tanggal\s*:.*?)$/m', '<div class="right">$1</div>', $formatted);
        
        return $formatted;
    }
    
    private function createEnhancedPdfFromDocx($docxPath, $kategori)
    {
        try {
            // Method 1: Try HTML conversion first (best quality)
            $htmlContent = $this->convertDocxToHtml($docxPath);
            
            if (!empty($htmlContent)) {
                return $this->createPdfFromEnhancedHtml($htmlContent, $kategori);
            }
            
            // Method 2: Fallback to text extraction with better formatting
            $textContent = $this->extractFormattedTextFromDocx($docxPath);
            
            if (!empty($textContent)) {
                return $this->createPdfFromFormattedText($textContent, $kategori);
            }
            
            throw new \Exception('Could not extract content from DOCX');
            
        } catch (\Exception $e) {
            \Log::error('Enhanced DOCX to PDF conversion failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    private function createPdfFromEnhancedHtml($htmlContent, $kategori)
    {
        // Clean and enhance HTML for better PDF rendering
        $cleanHtml = $this->enhanceHtmlForPdf($htmlContent);
        
        // Create enhanced HTML with preserved DOCX styling
        $enhancedHtml = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($kategori->nama) . '</title>
    <style>
        @page {
            margin: 2cm;
            size: A4;
        }
        body { 
            font-family: "Times New Roman", "Liberation Serif", serif; 
            line-height: 1.5; 
            margin: 0;
            padding: 0;
            font-size: 12pt;
            color: #000;
            background: white;
        }
        .docx-content {
            width: 100%;
            max-width: none;
        }
        p { 
            margin: 6pt 0; 
            text-align: justify;
            text-indent: 0;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .text-left { text-align: left !important; }
        .text-justify { text-align: justify !important; }
        .bold, strong, b { font-weight: bold !important; }
        .underline, u { text-decoration: underline !important; }
        .italic, em, i { font-style: italic !important; }
        h1, h2, h3, h4, h5, h6 {
            font-weight: bold;
            margin: 12pt 0 6pt 0;
            line-height: 1.2;
        }
        h1 { font-size: 16pt; }
        h2 { font-size: 14pt; }
        h3 { font-size: 13pt; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 6pt 0;
            font-size: 11pt;
        }
        table, th, td {
            border: 1pt solid #000;
        }
        th, td {
            padding: 4pt 6pt;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .page-break {
            page-break-before: always;
        }
        .no-break {
            page-break-inside: avoid;
        }
        /* Preserve DOCX spacing */
        .docx-paragraph {
            margin: 0;
            padding: 0;
        }
        .docx-spacing-before { margin-top: 6pt; }
        .docx-spacing-after { margin-bottom: 6pt; }
        /* Header and footer styles */
        .header-content {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20pt;
            border-bottom: 1pt solid #000;
            padding-bottom: 10pt;
        }
        .footer-content {
            margin-top: 20pt;
            border-top: 1pt solid #000;
            padding-top: 10pt;
            font-size: 10pt;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="docx-content">
        ' . $cleanHtml . '
    </div>
    <div class="footer-content">
        <em>Dokumen ini di-generate dari template DOCX pada ' . date('d/m/Y H:i:s') . '</em>
    </div>
</body>
</html>';

        // Generate PDF using DomPDF with optimized settings
        $pdf = Pdf::loadHTML($enhancedHtml);
        $pdf->setPaper('A4', 'portrait');
        
        // Set DomPDF options for better rendering
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Times-Roman',
            'dpi' => 150,
            'defaultPaperSize' => 'A4',
            'chroot' => storage_path(),
        ]);
        
        return $pdf->output();
    }
    
    private function enhanceHtmlForPdf($htmlContent)
    {
        // Clean and enhance HTML for better PDF appearance
        $cleanContent = $htmlContent;
        
        // Remove unnecessary HTML structure tags
        $cleanContent = preg_replace('/<html[^>]*>|<\/html>/i', '', $cleanContent);
        $cleanContent = preg_replace('/<head[^>]*>.*?<\/head>/is', '', $cleanContent);
        $cleanContent = preg_replace('/<body[^>]*>|<\/body>/i', '', $cleanContent);
        
        // Clean up excessive whitespace but preserve paragraph structure
        $cleanContent = preg_replace('/\s+/', ' ', $cleanContent);
        $cleanContent = preg_replace('/>\s+</', '><', $cleanContent);
        
        // Enhance paragraph formatting
        $cleanContent = preg_replace('/<p([^>]*)>/', '<p class="docx-paragraph"$1>', $cleanContent);
        
        // Preserve line breaks and spacing
        $cleanContent = str_replace('<br>', '<br/>', $cleanContent);
        $cleanContent = str_replace('<br />', '<br/>', $cleanContent);
        
        // Enhance table formatting
        $cleanContent = preg_replace('/<table([^>]*)>/', '<table class="no-break"$1>', $cleanContent);
        
        // Convert common formatting
        $cleanContent = preg_replace('/<span[^>]*font-weight:\s*bold[^>]*>(.*?)<\/span>/i', '<strong>$1</strong>', $cleanContent);
        $cleanContent = preg_replace('/<span[^>]*text-decoration:\s*underline[^>]*>(.*?)<\/span>/i', '<u>$1</u>', $cleanContent);
        $cleanContent = preg_replace('/<span[^>]*font-style:\s*italic[^>]*>(.*?)<\/span>/i', '<em>$1</em>', $cleanContent);
        
        // Handle text alignment
        $cleanContent = preg_replace('/<p[^>]*text-align:\s*center[^>]*>/i', '<p class="text-center">', $cleanContent);
        $cleanContent = preg_replace('/<p[^>]*text-align:\s*right[^>]*>/i', '<p class="text-right">', $cleanContent);
        $cleanContent = preg_replace('/<p[^>]*text-align:\s*justify[^>]*>/i', '<p class="text-justify">', $cleanContent);
        
        return trim($cleanContent);
    }
    
    private function extractFormattedTextFromDocx($docxPath)
    {
        try {
            $phpWord = IOFactory::load($docxPath);
            $formattedContent = '';
            
            foreach ($phpWord->getSections() as $sectionIndex => $section) {
                if ($sectionIndex > 0) {
                    $formattedContent .= "\n\n"; // Section break
                }
                
                foreach ($section->getElements() as $element) {
                    $elementContent = $this->extractElementContent($element);
                    if (!empty($elementContent)) {
                        $formattedContent .= $elementContent . "\n";
                    }
                }
            }
            
            return trim($formattedContent);
            
        } catch (\Exception $e) {
            \Log::warning('Formatted text extraction from DOCX failed: ' . $e->getMessage());
            return '';
        }
    }
    
    private function extractElementContent($element)
    {
        $content = '';
        
        try {
            // Handle different element types
            $elementClass = get_class($element);
            
            if (strpos($elementClass, 'TextRun') !== false) {
                if (method_exists($element, 'getText')) {
                    $content = $element->getText();
                }
            } elseif (strpos($elementClass, 'Text') !== false) {
                if (method_exists($element, 'getText')) {
                    $content = $element->getText();
                }
            } elseif (method_exists($element, 'getElements')) {
                // Handle container elements like paragraphs
                $subContent = '';
                foreach ($element->getElements() as $subElement) {
                    $subElementContent = $this->extractElementContent($subElement);
                    if (!empty($subElementContent)) {
                        $subContent .= $subElementContent . ' ';
                    }
                }
                $content = trim($subContent);
            } elseif (method_exists($element, 'getText')) {
                $content = $element->getText();
            }
            
        } catch (\Exception $e) {
            \Log::warning('Element content extraction failed: ' . $e->getMessage());
        }
        
        return $content;
    }
    
    private function createPdfFromFormattedText($textContent, $kategori)
    {
        // Enhanced text formatting for better PDF appearance
        $formattedContent = $this->enhanceTextFormatting($textContent);
        
        // Create HTML with enhanced formatting
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($kategori->nama) . '</title>
    <style>
        @page {
            margin: 2cm;
            size: A4;
        }
        body { 
            font-family: "Times New Roman", "Liberation Serif", serif; 
            line-height: 1.6; 
            margin: 0;
            padding: 0;
            font-size: 12pt;
            color: #000;
            background: white;
        }
        .content {
            white-space: pre-line;
            text-align: justify;
            margin: 0;
        }
        .header-section {
            text-align: center;
            margin-bottom: 20pt;
            font-weight: bold;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .left { text-align: left; }
        .bold { font-weight: bold; }
        .underline { text-decoration: underline; }
        .italic { font-style: italic; }
        .signature-area {
            margin-top: 30pt;
            text-align: right;
        }
        .footer-info {
            margin-top: 30pt;
            border-top: 1pt solid #ccc;
            padding-top: 10pt;
            font-size: 10pt;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="content">' . $formattedContent . '</div>
    <div class="footer-info">
        <em>Dokumen ini di-generate dari template DOCX pada ' . date('d/m/Y H:i:s') . '</em>
    </div>
</body>
</html>';

        // Generate PDF using DomPDF
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');
        
        // Set DomPDF options for better rendering
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'Times-Roman',
            'dpi' => 150,
        ]);
        
        return $pdf->output();
    }
    
    private function enhanceTextFormatting($textContent)
    {
        // Format text content for better PDF appearance
        $formatted = htmlspecialchars($textContent);
        
        // Convert line breaks to HTML
        $formatted = nl2br($formatted);
        
        // Detect and format headers (lines in ALL CAPS or with specific patterns)
        $formatted = preg_replace('/^([A-Z\s]{10,})$/m', '<div class="center bold">$1</div>', $formatted);
        
        // Format document titles
        $formatted = preg_replace('/^(SURAT [A-Z\s]+)$/m', '<div class="center bold underline">$1</div>', $formatted);
        $formatted = preg_replace('/^(KETERANGAN [A-Z\s]*)$/m', '<div class="center bold underline">$1</div>', $formatted);
        $formatted = preg_replace('/^(PENGANTAR [A-Z\s]*)$/m', '<div class="center bold underline">$1</div>', $formatted);
        
        // Format signature areas
        $formatted = preg_replace('/(Hormat kami,?|Kepala Desa|Mengetahui,?|Demikian|Sekian)/', '<div class="signature-area">$1</div>', $formatted);
        
        // Format document numbers and dates
        $formatted = preg_replace('/^(Nomor\s*:.*?)$/m', '<div class="right">$1</div>', $formatted);
        $formatted = preg_replace('/^(Tanggal\s*:.*?)$/m', '<div class="right">$1</div>', $formatted);
        
        // Format field labels (text followed by colon)
        $formatted = preg_replace('/^([A-Za-z\s]+\s*:)(.*)$/m', '<strong>$1</strong>$2', $formatted);
        
        // Preserve paragraph spacing
        $formatted = preg_replace('/(<br\s*\/?>){3,}/', '<br/><br/>', $formatted);
        
        return $formatted;
    }
} 