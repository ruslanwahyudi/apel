@extends('layouts.admin')

@section('css')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .debug-info {
        font-size: 13px;
    }
    .progress-info {
        margin-top: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Cetak Multiple Surat</h4>
                    <p class="card-description">
                        Cetak beberapa kategori surat sekaligus untuk satu jenis layanan
                    </p>
                    
                    <!-- Help Section -->
                    <div class="alert alert-info mt-2" style="font-size: 13px;">
                        <strong><i class="fa fa-info-circle"></i> Cara Penggunaan:</strong>
                        <ol class="mb-0" style="padding-left: 20px;">
                            <li>Pilih <strong>Jenis Layanan</strong> yang ingin dicetak suratnya</li>
                            <li>Masukkan <strong>ID Pelayanan</strong> dari tabel duk_pelayanan (data akan otomatis diambil dari DUK)</li>
                            <li>Pilih <strong>Kategori Surat</strong> yang ingin dicetak (bisa lebih dari 1)</li>
                            <li>Isi <strong>Data Tambahan</strong> jika diperlukan</li>
                            <li>Pilih <strong>Tipe Output</strong>: ZIP (file terpisah) atau PDF Gabungan</li>
                            <li>Klik <strong>Generate Multiple PDF</strong></li>
                        </ol>
                        <small class="text-muted">
                            💡 Tip: ID Pelayanan dapat dilihat di database tabel duk_pelayanan kolom 'id'. Gunakan tombol "Debug Info" untuk melihat status template dan system info.
                        </small>
                    </div>
                </div>
                <div class="card-body">
                    <form id="multiplePrintForm">
                        @csrf
                        
                        <!-- Pilih Jenis Layanan -->
                        <div class="form-group">
                            <label for="jenis_pelayanan_id">Jenis Layanan <span class="text-danger">*</span></label>
                            <select class="form-control" id="jenis_pelayanan_id" name="jenis_pelayanan_id" required>
                                <option value="">-- Pilih Jenis Layanan --</option>
                                @foreach($jenisLayanan as $jenis)
                                    <option value="{{ $jenis->id }}">{{ $jenis->nama_pelayanan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Pemohon Section -->
                        <div id="pemohon_section" style="display: none;">
                            <div class="form-group">
                                <label for="pemohon_id">ID Pelayanan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="pemohon_id" name="pemohon_id" 
                                           placeholder="Masukkan ID dari tabel duk_pelayanan" min="1">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-info btn-sm" onclick="showRecentPelayananIds()">
                                            <i class="fa fa-search"></i> Lihat ID Terbaru
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <i class="fa fa-info-circle"></i> 
                                    Masukkan ID pelayanan dari tabel duk_pelayanan. Data akan otomatis diambil dari DUK berdasarkan ID ini.
                                </small>
                            </div>
                        </div>

                        <!-- Kategori Surat yang Tersedia -->
                        <div class="form-group" id="kategori_section" style="display: none;">
                            <label>Kategori Surat yang Tersedia</label>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="select_all_kategori">
                                <label class="form-check-label" for="select_all_kategori">
                                    <strong>Pilih Semua</strong>
                                </label>
                            </div>
                            <hr>
                            <div id="kategori_list">
                                <!-- Kategori akan dimuat via AJAX -->
                            </div>
                        </div>

                        <!-- Dynamic Form Fields -->
                        <div id="dynamic_fields" style="display: none;">
                            <h5 class="mt-4">Data yang Diperlukan</h5>
                            <div id="form_fields">
                                <!-- Fields akan dimuat via AJAX -->
                            </div>
                        </div>

                        <!-- Output Options -->
                        <div class="form-group" id="output_section" style="display: none;">
                            <label>Tipe Output</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="output_type" id="output_zip" value="zip" checked>
                                <label class="form-check-label" for="output_zip">
                                    ZIP File (Berisi beberapa PDF terpisah)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="output_type" id="output_combined" value="combined">
                                <label class="form-check-label" for="output_combined">
                                    PDF Gabungan (Semua surat dalam satu PDF)
                                </label>
                            </div>
                        </div>

                        <!-- Manual Test Buttons for Debugging -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <strong>Debug Tools</strong>
                                    </div>
                                    <div class="card-body">
                                        <button type="button" class="btn btn-info btn-sm" onclick="testJqueryAjax()">
                                            Test jQuery & AJAX
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm" onclick="testDropdownManually()">
                                            Test Dropdown Manual
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="testApiEndpoint()">
                                            Test API Endpoint
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm" onclick="testMultiplePrintBasic()">
                                            Test Multiple Print Basic
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="testWithMockRoute()">
                                            Test Mock Route
                                        </button>
                                        <small class="text-muted d-block mt-2">
                                            Gunakan tombol-tombol ini untuk test manual jika dropdown tidak bekerja
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group" id="submit_section" style="display: none;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-download"></i> Generate Multiple PDF
                            </button>
                            <button type="button" class="btn btn-warning" onclick="generateMultiplePdfDebug()">
                                <i class="fa fa-bug"></i> Debug Mode
                            </button>
                            <button type="button" class="btn btn-info" onclick="showDebugInfo()">
                                <i class="fa fa-info-circle"></i> Debug Info
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2" id="loading-text">Generating PDFs...</p>
                <div id="progress-info" style="display: none;">
                    <small class="text-muted">This may take a few moments</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Debug Modal -->
<div class="modal fade" id="debugModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Multiple Print Debug Information</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="debug-content">
                    <div class="text-center">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        Loading debug info...
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
// Initialize toastr
if (typeof toastr === 'undefined') {
    console.error('Toastr not loaded! Using console.log as fallback');
    window.toastr = {
        success: function(msg) { console.log('SUCCESS: ' + msg); },
        error: function(msg) { console.error('ERROR: ' + msg); },
        warning: function(msg) { console.warn('WARNING: ' + msg); },
        info: function(msg) { console.log('INFO: ' + msg); }
    };
} else {
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000"
    };
}

// Setup CSRF token for all AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    console.log('Multiple Print page loaded'); // Debug log
    console.log('jQuery version:', $.fn.jquery); // Check jQuery version
    console.log('CSRF token:', $('meta[name="csrf-token"]').attr('content')); // Check CSRF token
    console.log('Base URL:', window.location.origin); // Check base URL
    
    // Test if dropdown exists
    const dropdown = $('#jenis_pelayanan_id');
    console.log('Dropdown element found:', dropdown.length > 0);
    console.log('Dropdown HTML:', dropdown.html());
    
    // Check if jenis layanan dropdown has options
    const jenisLayananOptions = $('#jenis_pelayanan_id option').length;
    console.log('Jenis layanan options count:', jenisLayananOptions);
    
    if (jenisLayananOptions <= 1) {
        toastr.warning('Tidak ada jenis layanan tersedia. Pastikan ada data jenis layanan dengan kategori surat.');
    } else {
        toastr.success(`Ditemukan ${jenisLayananOptions - 1} jenis layanan tersedia`);
    }
    
    // Event listener untuk perubahan jenis layanan
    $('#jenis_pelayanan_id').change(function() {
        const jenisLayananId = $(this).val();
        const jenisLayananText = $(this).find('option:selected').text();
        console.log('Jenis layanan changed:', jenisLayananId, jenisLayananText); // Debug log
        toastr.info('Jenis layanan dipilih: ' + jenisLayananText); // Visual feedback
        
        if (jenisLayananId) {
            // Tampilkan section pemohon dan load kategori surat
            $('#pemohon_section').show();
            loadKategoriSurat(jenisLayananId);
        } else {
            resetSections();
        }
    });

    // Event listener untuk perubahan pemohon ID
    $('#pemohon_id').on('input change', function() {
        const pemohonId = $(this).val();
        if (pemohonId && pemohonId > 0) {
            console.log('Pemohon ID changed:', pemohonId);
            loadDynamicFields();
        }
    });

    // Event listener untuk select all kategori
    $('#select_all_kategori').change(function() {
        const checked = $(this).is(':checked');
        $('.kategori-checkbox').prop('checked', checked);
        updateOutputSection();
    });

    // Event listener untuk individual kategori checkbox
    $(document).on('change', '.kategori-checkbox', function() {
        updateSelectAll();
        updateOutputSection();
    });

    // Form submit
    $('#multiplePrintForm').submit(function(e) {
        e.preventDefault();
        generateMultiplePdf();
    });
});

function loadKategoriSurat(jenisLayananId) {
    console.log('Loading kategori surat for jenis layanan:', jenisLayananId); // Debug log
    
    $.ajax({
        url: '{{ route("adm.kategori-surat.get-by-jenis-layanan") }}',
        method: 'GET',
        data: { jenis_pelayanan_id: jenisLayananId },
        beforeSend: function() {
            console.log('Sending request to kategori surat API');
        },
        success: function(response) {
            console.log('Kategori surat response:', response); // Debug log
            
            if (response.success && response.data.length > 0) {
                let html = '';
                
                response.data.forEach(function(kategori) {
                    html += `
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input kategori-checkbox" 
                                   id="kategori_${kategori.id}" name="kategori_ids[]" value="${kategori.id}">
                            <label class="form-check-label" for="kategori_${kategori.id}">
                                ${kategori.nama} <small class="text-muted">(${kategori.template_type})</small>
                            </label>
                        </div>
                    `;
                });
                
                $('#kategori_list').html(html);
                $('#kategori_section').show();
                toastr.success(`${response.data.length} kategori surat ditemukan`);
                
                if (response.data.length === 1) {
                    // Jika hanya ada 1 kategori, otomatis pilih
                    $('.kategori-checkbox').prop('checked', true);
                    $('#select_all_kategori').prop('checked', true);
                    updateOutputSection();
                }
            } else {
                toastr.warning('Tidak ada kategori surat untuk jenis layanan ini');
                $('#kategori_section').hide();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading kategori surat:', xhr.responseText); // Debug log
            toastr.error('Gagal memuat kategori surat: ' + error);
            $('#kategori_section').hide();
        }
    });
}

function updateSelectAll() {
    const totalKategori = $('.kategori-checkbox').length;
    const checkedKategori = $('.kategori-checkbox:checked').length;
    
    $('#select_all_kategori').prop('checked', totalKategori === checkedKategori);
}

function updateOutputSection() {
    const checkedKategori = $('.kategori-checkbox:checked').length;
    
    if (checkedKategori > 0) {
        $('#output_section').show();
        $('#submit_section').show();
        loadDynamicFields();
    } else {
        $('#output_section').hide();
        $('#submit_section').hide();
        $('#dynamic_fields').hide();
    }
}

function loadDynamicFields() {
    const jenisLayananId = $('#jenis_pelayanan_id').val();
    const pemohonId = $('#pemohon_id').val();
    
    if (!jenisLayananId) return;

    $.ajax({
        url: '{{ route("adm.kategori-surat.get-merged-variables") }}',
        method: 'GET',
        data: { 
            jenis_pelayanan_id: jenisLayananId,
            pemohon_id: pemohonId 
        },
        success: function(response) {
            if (response.success && response.variables.length > 0) {
                let html = '';
                
                response.variables.forEach(function(variable) {
                    html += generateFieldHtml(variable);
                });
                
                $('#form_fields').html(html);
                $('#dynamic_fields').show();
                
                // Pre-fill dengan data DUK jika ada
                if (response.duk_data) {
                    fillFormWithDukData(response.duk_data);
                }
            } else {
                $('#dynamic_fields').hide();
            }
        },
        error: function() {
            toastr.error('Gagal memuat form fields');
            $('#dynamic_fields').hide();
        }
    });
}

function generateFieldHtml(variable) {
    const required = variable.required ? 'required' : '';
    const requiredMark = variable.required ? '<span class="text-danger">*</span>' : '';
    
    let input = '';
    
    switch (variable.type) {
        case 'date':
            input = `<input type="date" class="form-control" name="${variable.name}" id="${variable.name}" ${required}>`;
            break;
        case 'number':
            input = `<input type="number" class="form-control" name="${variable.name}" id="${variable.name}" ${required}>`;
            break;
        case 'textarea':
            input = `<textarea class="form-control" name="${variable.name}" id="${variable.name}" rows="3" ${required}></textarea>`;
            break;
        default:
            input = `<input type="text" class="form-control" name="${variable.name}" id="${variable.name}" ${required}>`;
    }
    
    return `
        <div class="form-group">
            <label for="${variable.name}">${variable.label} ${requiredMark}</label>
            ${input}
        </div>
    `;
}

function fillFormWithDukData(dukData) {
    Object.keys(dukData).forEach(function(key) {
        const value = dukData[key];
        const field = $(`[name="${key}"]`);
        
        if (field.length && value) {
            field.val(value);
        }
    });
}

function showDebugInfo() {
    const jenisLayananId = $('#jenis_pelayanan_id').val();
    
    if (!jenisLayananId) {
        toastr.error('Pilih jenis layanan terlebih dahulu');
        return;
    }
    
    $('#debugModal').modal('show');
    
    $.ajax({
        url: '{{ route("adm.kategori-surat.debug-multiple-print") }}',
        method: 'GET',
        data: { jenis_pelayanan_id: jenisLayananId },
        success: function(response) {
            if (response.success) {
                displayDebugInfo(response.debug_info);
            } else {
                $('#debug-content').html('<div class="alert alert-danger">Error: ' + response.message + '</div>');
            }
        },
        error: function() {
            $('#debug-content').html('<div class="alert alert-danger">Gagal memuat debug info</div>');
        }
    });
}

function displayDebugInfo(debugInfo) {
    let html = '<div class="debug-info">';
    
    // System Info
    html += '<h6><i class="fa fa-cog"></i> System Information</h6>';
    html += '<div class="table-responsive"><table class="table table-sm table-bordered">';
    html += '<tr><td><strong>PHP Version</strong></td><td>' + debugInfo.system_info.php_version + '</td></tr>';
    html += '<tr><td><strong>Laravel Version</strong></td><td>' + debugInfo.system_info.laravel_version + '</td></tr>';
    html += '<tr><td><strong>ZipArchive Available</strong></td><td>' + (debugInfo.system_info.zip_available ? '<span class="text-success">✓ Yes</span>' : '<span class="text-danger">✗ No</span>') + '</td></tr>';
    html += '<tr><td><strong>DomPDF Available</strong></td><td>' + (debugInfo.system_info.dompdf_available ? '<span class="text-success">✓ Yes</span>' : '<span class="text-danger">✗ No</span>') + '</td></tr>';
    html += '</table></div>';
    
    // Validation
    if (debugInfo.validation) {
        html += '<h6><i class="fa fa-check-circle"></i> Validation</h6>';
        
        if (debugInfo.validation.errors.length > 0) {
            html += '<div class="alert alert-danger"><strong>Errors:</strong><ul class="mb-0">';
            debugInfo.validation.errors.forEach(function(error) {
                html += '<li>' + error + '</li>';
            });
            html += '</ul></div>';
        }
        
        if (debugInfo.validation.warnings.length > 0) {
            html += '<div class="alert alert-warning"><strong>Warnings:</strong><ul class="mb-0">';
            debugInfo.validation.warnings.forEach(function(warning) {
                html += '<li>' + warning + '</li>';
            });
            html += '</ul></div>';
        }
        
        if (debugInfo.validation.errors.length === 0 && debugInfo.validation.warnings.length === 0) {
            html += '<div class="alert alert-success">✓ All validations passed</div>';
        }
    }
    
    // Categories Info
    html += '<h6><i class="fa fa-files-o"></i> Categories (' + debugInfo.kategori_count + ')</h6>';
    html += '<div class="table-responsive"><table class="table table-sm table-bordered">';
    html += '<thead><tr><th>Name</th><th>Type</th><th>Can Generate</th><th>Variables</th><th>Template</th></tr></thead><tbody>';
    
    debugInfo.categories.forEach(function(cat) {
        html += '<tr>';
        html += '<td>' + cat.nama + '</td>';
        html += '<td><span class="badge badge-info">' + cat.template_type + '</span></td>';
        html += '<td>' + (cat.can_generate ? '<span class="text-success">✓</span>' : '<span class="text-danger">✗</span>') + '</td>';
        html += '<td>' + cat.variables_count + '</td>';
        html += '<td><small>' + (cat.template_path || 'N/A') + '</small></td>';
        html += '</tr>';
    });
    
    html += '</tbody></table></div>';
    html += '</div>';
    
    $('#debug-content').html(html);
}

function generateMultiplePdf() {
    console.log('=== STARTING MULTIPLE PDF GENERATION ===');
    
    const formData = $('#multiplePrintForm').serialize();
    const checkedKategori = $('.kategori-checkbox:checked').length;
    
    console.log('Form data:', formData);
    console.log('Checked kategori count:', checkedKategori);
    
    if (checkedKategori === 0) {
        toastr.error('Pilih minimal satu kategori surat');
        return;
    }
    
    // Validate required fields
    const jenisLayananId = $('#jenis_pelayanan_id').val();
    const pemohonId = $('#pemohon_id').val();
    
    if (!jenisLayananId) {
        toastr.error('Pilih jenis layanan terlebih dahulu');
        return;
    }
    
    if (!pemohonId) {
        toastr.error('Masukkan ID pelayanan terlebih dahulu');
        return;
    }
    
    console.log('Starting AJAX request...');
    
    // Update loading text
    $('#loading-text').text(`Generating ${checkedKategori} PDF files...`);
    $('#progress-info').show();
    $('#loadingModal').modal('show');
    
    // Add progress updates
    let progressStep = 0;
    const progressMessages = [
        'Processing templates...',
        'Merging data from DUK...',
        'Generating PDF files...',
        'Finalizing output...'
    ];
    
    const progressInterval = setInterval(function() {
        if (progressStep < progressMessages.length) {
            $('#loading-text').text(progressMessages[progressStep]);
            progressStep++;
        }
    }, 3000);
    
    // Clear any existing timeout
    if (window.pdfGenerationTimeout) {
        clearTimeout(window.pdfGenerationTimeout);
    }
    
    // Set a manual timeout
    window.pdfGenerationTimeout = setTimeout(function() {
        clearInterval(progressInterval);
        $('#loadingModal').modal('hide');
        toastr.error('Request timeout! Proses terlalu lama. Coba dengan data yang lebih sedikit.');
        console.error('Manual timeout triggered');
    }, 180000); // 3 minutes
    
    // Use fetch API instead of jQuery AJAX to avoid XMLHttpRequest blob issues
    const formDataObj = new FormData();
    const urlParams = new URLSearchParams(formData);
    for (const [key, value] of urlParams) {
        formDataObj.append(key, value);
    }
    
    console.log('Using fetch API for request...');
    
    fetch('{{ route("adm.kategori-surat.generate-multiple") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formDataObj
    })
    .then(response => {
        console.log('Fetch response received:', response);
        
        clearInterval(progressInterval);
        clearTimeout(window.pdfGenerationTimeout);
        $('#loadingModal').modal('hide');
        
        if (!response.ok) {
            // Handle error response
            return response.text().then(text => {
                console.error('Error response text:', text);
                
                let errorMessage = 'Gagal generate PDF';
                try {
                    const errorData = JSON.parse(text);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    if (text.length < 200) {
                        errorMessage = text;
                    }
                }
                
                throw new Error(errorMessage);
            });
        }
        
        // Get filename from Content-Disposition header
        const disposition = response.headers.get('Content-Disposition');
        let filename = 'surat_multiple.pdf';
        
        if (disposition) {
            const matches = /filename="([^"]*)"/.exec(disposition);
            if (matches && matches[1]) {
                filename = matches[1];
            }
        }
        
        console.log('Downloaded filename:', filename);
        
        // Convert response to blob
        return response.blob().then(blob => {
            return { blob, filename };
        });
    })
    .then(({ blob, filename }) => {
        console.log('Blob received:', blob.size, 'bytes');
        
        // Validate response
        if (!blob || blob.size === 0) {
            throw new Error('Response kosong dari server');
        }
        
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        toastr.success(`Berhasil generate ${checkedKategori} surat PDF: ${filename}`);
        console.log('=== PDF GENERATION COMPLETED ===');
    })
    .catch(error => {
        console.error('=== FETCH ERROR ===');
        console.error('Error:', error.message);
        
        clearInterval(progressInterval);
        clearTimeout(window.pdfGenerationTimeout);
        $('#loadingModal').modal('hide');
        
        toastr.error(error.message || 'Gagal generate PDF');
        console.log('=== PDF GENERATION FAILED ===');
    });
}

function resetForm() {
    $('#multiplePrintForm')[0].reset();
    resetSections();
}

function resetSections() {
    console.log('Resetting all sections'); // Debug log
    $('#pemohon_section').hide();
    $('#kategori_section').hide();
    $('#dynamic_fields').hide();
    $('#output_section').hide();
    $('#submit_section').hide();
    
    // Reset form values
    $('#pemohon_id').val(''); // Reset input field instead of dropdown
    $('#kategori_list').html('');
    $('#form_fields').html('');
    $('#select_all_kategori').prop('checked', false);
}

// Manual test functions for debugging
function testJqueryAjax() {
    console.log('=== MANUAL TEST: jQuery & AJAX ===');
    console.log('jQuery loaded:', typeof $ !== 'undefined');
    console.log('jQuery version:', $.fn.jquery);
    console.log('toastr loaded:', typeof toastr !== 'undefined');
    
    // Test simple AJAX with kategori surat API
    const testJenisLayananId = $('#jenis_pelayanan_id option[value!=""]').first().val();
    if (testJenisLayananId) {
        $.get('{{ route("adm.kategori-surat.get-by-jenis-layanan") }}', {
            jenis_pelayanan_id: testJenisLayananId
        })
        .done(function(data) {
            console.log('✅ AJAX test successful:', data);
            toastr.success('AJAX test berhasil!');
        })
        .fail(function(xhr) {
            console.error('❌ AJAX test failed:', xhr.responseText);
            toastr.error('AJAX test gagal: ' + xhr.status);
        });
    } else {
        toastr.warning('Tidak ada jenis layanan untuk test AJAX');
    }
}

function testDropdownManually() {
    console.log('=== MANUAL TEST: Dropdown ===');
    const dropdown = $('#jenis_pelayanan_id');
    console.log('Dropdown found:', dropdown.length > 0);
    console.log('Options count:', dropdown.find('option').length);
    
    // Manually trigger change with first available option
    const firstOption = dropdown.find('option[value!=""]').first();
    if (firstOption.length > 0) {
        const value = firstOption.val();
        const text = firstOption.text();
        console.log('Setting dropdown to:', value, text);
        dropdown.val(value).trigger('change');
        toastr.info('Manual trigger: ' + text);
    } else {
        console.log('No valid options found');
        toastr.warning('Tidak ada opsi valid di dropdown');
    }
}

function testApiEndpoint() {
    console.log('=== MANUAL TEST: API Endpoint ===');
    const testJenisLayananId = $('#jenis_pelayanan_id option[value!=""]').first().val();
    
    if (testJenisLayananId) {
        console.log('Testing with jenis_pelayanan_id:', testJenisLayananId);
        
        // Test kategori surat API
        $.get('{{ route("adm.kategori-surat.get-by-jenis-layanan") }}', {
            jenis_pelayanan_id: testJenisLayananId
        })
        .done(function(data) {
            console.log('✅ Kategori Surat API test successful:', data);
            toastr.success('API Kategori Surat berhasil!');
        })
        .fail(function(xhr) {
            console.error('❌ Kategori Surat API test failed:', xhr.responseText);
            toastr.error('API Kategori Surat gagal: ' + xhr.status);
        });
    } else {
        console.log('No jenis layanan ID available for testing');
        toastr.warning('Tidak ada ID jenis layanan untuk test');
    }
}

function showRecentPelayananIds() {
    const jenisLayananId = $('#jenis_pelayanan_id').val();
    
    if (!jenisLayananId) {
        toastr.warning('Pilih jenis layanan terlebih dahulu');
        return;
    }
    
    console.log('Loading recent pelayanan IDs for jenis layanan:', jenisLayananId);
    
    $.get(`{{ url('adm/kategori-surat/recent-pelayanan-ids') }}/${jenisLayananId}`)
        .done(function(response) {
            if (response.success && response.data.length > 0) {
                let html = '<div class="alert alert-info"><strong>10 ID Pelayanan Terbaru:</strong><br>';
                
                response.data.forEach(function(item) {
                    html += `<button type="button" class="btn btn-outline-primary btn-sm m-1" 
                             onclick="selectPelayananId(${item.id})">
                             ID: ${item.id} <small>(${item.created_at})</small>
                             </button>`;
                });
                
                html += '<br><small class="text-muted">Klik ID untuk memilih</small></div>';
                
                // Tampilkan di bawah input
                $('#pelayanan-ids-list').remove(); // Remove existing
                $(html).attr('id', 'pelayanan-ids-list').insertAfter('#pemohon_section .form-group');
                
                toastr.success(`${response.data.length} ID pelayanan ditemukan`);
            } else {
                toastr.info('Tidak ada data pelayanan untuk jenis layanan ini');
            }
        })
        .fail(function(xhr) {
            console.error('Error loading recent pelayanan IDs:', xhr.responseText);
            toastr.error('Gagal memuat ID pelayanan: ' + xhr.status);
        });
}

function selectPelayananId(id) {
    $('#pemohon_id').val(id).trigger('change');
    $('#pelayanan-ids-list').fadeOut('slow');
    toastr.success('ID Pelayanan ' + id + ' dipilih');
}

function testMultiplePrintBasic() {
    console.log('=== TESTING MULTIPLE PRINT BASIC ===');
    
    $.get('{{ route("adm.kategori-surat.test-multiple-print") }}')
        .done(function(response) {
            console.log('✅ Multiple Print Basic Test successful:', response);
            
            if (response.success) {
                let message = `Test berhasil!<br>
                               Jenis Layanan: ${response.data.jenis_layanan}<br>
                               Kategori Count: ${response.data.kategori_count}<br>
                               Memory: ${response.data.memory_usage}<br>
                               PHP: ${response.data.php_version}`;
                
                toastr.success(message);
                
                // Show detailed info in console
                console.log('Kategori List:', response.data.kategori_list);
            } else {
                toastr.error('Test gagal: ' + response.message);
            }
        })
        .fail(function(xhr) {
            console.error('❌ Multiple Print Basic Test failed:', xhr.responseText);
            toastr.error('Test gagal: ' + xhr.status + ' - ' + xhr.statusText);
        });
}

function testWithMockRoute() {
    console.log('=== TESTING WITH MOCK ROUTE ===');
    
    const formData = $('#multiplePrintForm').serialize();
    const checkedKategori = $('.kategori-checkbox:checked').length;
    
    if (checkedKategori === 0) {
        toastr.error('Pilih minimal satu kategori surat untuk test mock');
        return;
    }
    
    // Update loading text
    $('#loading-text').text('Testing with mock route...');
    $('#progress-info').show();
    $('#loadingModal').modal('show');
    
    // Prepare form data
    const formDataObj = new FormData();
    const urlParams = new URLSearchParams(formData);
    for (const [key, value] of urlParams) {
        formDataObj.append(key, value);
    }
    
    fetch('{{ route("adm.kategori-surat.mock-multiple-print") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formDataObj
    })
    .then(response => {
        console.log('Mock response received:', response);
        $('#loadingModal').modal('hide');
        
        if (!response.ok) {
            throw new Error(`Mock request failed: ${response.status}`);
        }
        
        // Get filename from header
        const disposition = response.headers.get('Content-Disposition');
        let filename = 'mock_test.zip';
        if (disposition) {
            const matches = /filename="([^"]*)"/.exec(disposition);
            if (matches && matches[1]) {
                filename = matches[1];
            }
        }
        
        return response.blob().then(blob => ({ blob, filename }));
    })
    .then(({ blob, filename }) => {
        console.log('Mock blob received:', blob.size, 'bytes');
        
        // Create download link for mock file
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        toastr.success(`✅ Mock test berhasil! File: ${filename}`);
        console.log('=== MOCK TEST COMPLETED ===');
    })
    .catch(error => {
        console.error('=== MOCK TEST ERROR ===');
        console.error('Error:', error.message);
        
        $('#loadingModal').modal('hide');
        toastr.error('Mock test gagal: ' + error.message);
    });
}

// Debug version without blob responseType for easier error debugging
function generateMultiplePdfDebug() {
    console.log('=== STARTING DEBUG MULTIPLE PDF GENERATION ===');
    
    const formData = $('#multiplePrintForm').serialize();
    const checkedKategori = $('.kategori-checkbox:checked').length;
    
    console.log('Form data:', formData);
    console.log('Checked kategori count:', checkedKategori);
    
    if (checkedKategori === 0) {
        toastr.error('Pilih minimal satu kategori surat');
        return;
    }
    
    // Validate required fields
    const jenisLayananId = $('#jenis_pelayanan_id').val();
    const pemohonId = $('#pemohon_id').val();
    
    if (!jenisLayananId) {
        toastr.error('Pilih jenis layanan terlebih dahulu');
        return;
    }
    
    if (!pemohonId) {
        toastr.error('Masukkan ID pelayanan terlebih dahulu');
        return;
    }
    
    console.log('Starting DEBUG request with fetch API...');
    
    // Update loading text
    $('#loading-text').text(`DEBUG: Generating ${checkedKategori} PDF files...`);
    $('#progress-info').show();
    $('#loadingModal').modal('show');
    
    // Prepare form data
    const formDataObj = new FormData();
    const urlParams = new URLSearchParams(formData);
    for (const [key, value] of urlParams) {
        formDataObj.append(key, value);
    }
    
    fetch('{{ route("adm.kategori-surat.generate-multiple") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json' // Request JSON response for debug
        },
        body: formDataObj
    })
    .then(response => {
        console.log('DEBUG Fetch response received:', response);
        $('#loadingModal').modal('hide');
        
        if (!response.ok) {
            return response.text().then(text => {
                console.error('DEBUG Error response:', text);
                throw new Error(`HTTP ${response.status}: ${text.substring(0, 100)}`);
            });
        }
        
        return response.json();
    })
    .then(data => {
        console.log('DEBUG Response data:', data);
        
        if (data.success) {
            toastr.success('DEBUG: ' + data.message);
            
            if (data.debug_info) {
                console.log('DEBUG Info:', data.debug_info);
                
                // Show summary in toastr
                const summary = `
                    Kategori: ${data.debug_info.kategori_count}<br>
                    Memory: ${data.debug_info.memory_usage}<br>
                    Time: ${data.debug_info.execution_time}s
                `;
                toastr.info(summary);
            }
        } else {
            toastr.error('DEBUG: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('=== DEBUG FETCH ERROR ===');
        console.error('Error:', error.message);
        
        $('#loadingModal').modal('hide');
        toastr.error('DEBUG: ' + error.message);
        console.log('=== DEBUG PDF GENERATION FAILED ===');
    });
}
</script>
@endsection 