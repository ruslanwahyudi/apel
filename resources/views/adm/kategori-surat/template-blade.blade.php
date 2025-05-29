@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Template Blade: {{ $kategori->nama }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('adm.kategori-surat') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        @if($kategori->hasBladeTemplate())
                        <a href="{{ route('adm.kategori-surat.preview-blade', $kategori) }}" class="btn btn-info btn-sm" target="_blank">
                            <i class="fas fa-eye"></i> Preview Template
                        </a>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    @if(!$kategori->hasBladeTemplate())
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Template Tidak Ditemukan!</h5>
                            Template Blade <strong>{{ $kategori->blade_template_name }}.blade.php</strong> tidak ditemukan di direktori 
                            <code>resources/views/templates/surat/</code>
                            <br><br>
                            Silakan buat file template terlebih dahulu atau hubungi administrator.
                        </div>
                    @else
                        <div class="alert alert-success">
                            <h5><i class="icon fas fa-check"></i> Template Siap Digunakan!</h5>
                            Template Blade <strong>{{ $kategori->blade_template_name }}.blade.php</strong> ditemukan dan siap digunakan.
                        </div>

                        <!-- Tipe Surat Info -->
                        <div class="alert alert-{{ $kategori->isLayanan() ? 'primary' : 'secondary' }}">
                            <h5><i class="icon fas fa-{{ $kategori->isLayanan() ? 'database' : 'edit' }}"></i> 
                                Tipe Surat: {{ $kategori->isLayanan() ? 'Layanan (Data dari DUK)' : 'Non-Layanan (Input Manual)' }}
                            </h5>
                            @if($kategori->isLayanan())
                                <p class="mb-0">Data akan diambil otomatis dari database DUK berdasarkan pemohon yang dipilih.</p>
                                @if($kategori->jenisPelayanan)
                                    <small>Jenis Pelayanan: <strong>{{ $kategori->jenisPelayanan->nama }}</strong></small>
                                @endif
                            @else
                                <p class="mb-0">Data diisi manual melalui form di bawah ini.</p>
                            @endif
                        </div>

                        <!-- Display Validation Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <h5><i class="icon fas fa-ban"></i> Validation Error!</h5>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form id="templateForm" method="POST" action="{{ route('adm.kategori-surat.generate-blade', $kategori) }}">
                            @csrf
                            
                            @if($kategori->isLayanan())
                                <div class="form-group">
                                    <label for="pemohon_id">Pilih Data Pelayanan <span class="text-danger">*</span></label>
                                    <select class="form-control @error('pemohon_id') is-invalid @enderror" 
                                            id="pemohon_id" name="pemohon_id" required>
                                        <option value="">Pilih Data Pelayanan</option>
                                        @php
                                            $pelayananList = \DB::table('duk_pelayanan')
                                                ->where('jenis_pelayanan_id', $kategori->jenis_pelayanan_id)
                                                ->orderBy('created_at', 'desc')
                                                ->get();
                                        @endphp
                                        @foreach($pelayananList as $pelayanan)
                                            <option value="{{ $pelayanan->id }}">
                                                Pelayanan #{{ $pelayanan->id }} - {{ $pelayanan->created_at }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Data akan diambil otomatis dari database DUK berdasarkan pelayanan yang dipilih
                                    </small>
                                    @error('pemohon_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Preview Data Pemohon -->
                                <div id="pemohon-preview" style="display: none;">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-info-circle"></i> Preview Data Pelayanan</h6>
                                        <div id="pemohon-data">
                                            <!-- Data akan dimuat via AJAX -->
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @if($kategori->blade_template_variables && count($kategori->blade_template_variables) > 0)
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-{{ $kategori->isLayanan() ? 'plus' : 'edit' }}"></i> 
                                            {{ $kategori->isLayanan() ? 'Data Tambahan' : 'Isi Data Surat' }}
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        @if($kategori->isLayanan())
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i> 
                                                <strong>Info:</strong> Data dasar akan diambil dari DUK. Field di bawah ini untuk data tambahan yang tidak ada di DUK.
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i> 
                                                <strong>Info:</strong> Field yang bertanda <span class="text-danger">*</span> wajib diisi untuk preview HTML. 
                                                Untuk PDF, Anda dapat men-generate tanpa mengisi semua field untuk melihat template kosong.
                                            </div>
                                        @endif
                                        
                                        <div class="row">
                                            @foreach($kategori->blade_template_variables as $variable)
                                                <div class="col-md-6 mb-3">
                                                    <label for="{{ $variable['name'] }}" class="form-label">
                                                        {{ $variable['label'] }}
                                                        @if($variable['required'] && !$kategori->isLayanan())
                                                            <span class="text-danger">*</span>
                                                        @endif
                                                    </label>
                                                    
                                                    @if($variable['type'] === 'textarea')
                                                        <textarea 
                                                            class="form-control" 
                                                            id="{{ $variable['name'] }}" 
                                                            name="{{ $variable['name'] }}"
                                                            rows="3"
                                                            @if($variable['required'] && !$kategori->isLayanan()) required @endif
                                                            placeholder="{{ $variable['default_value'] ?? '' }}"
                                                        >{{ old($variable['name'], $variable['default_value'] ?? '') }}</textarea>
                                                    @elseif($variable['type'] === 'date')
                                                        <input 
                                                            type="date" 
                                                            class="form-control" 
                                                            id="{{ $variable['name'] }}" 
                                                            name="{{ $variable['name'] }}"
                                                            @if($variable['required'] && !$kategori->isLayanan()) required @endif
                                                            value="{{ old($variable['name'], $variable['default_value'] ?? '') }}"
                                                        >
                                                    @elseif($variable['type'] === 'number')
                                                        <input 
                                                            type="number" 
                                                            class="form-control" 
                                                            id="{{ $variable['name'] }}" 
                                                            name="{{ $variable['name'] }}"
                                                            @if($variable['required'] && !$kategori->isLayanan()) required @endif
                                                            value="{{ old($variable['name'], $variable['default_value'] ?? '') }}"
                                                            placeholder="{{ $variable['default_value'] ?? '' }}"
                                                        >
                                                    @else
                                                        <input 
                                                            type="text" 
                                                            class="form-control" 
                                                            id="{{ $variable['name'] }}" 
                                                            name="{{ $variable['name'] }}"
                                                            @if($variable['required'] && !$kategori->isLayanan()) required @endif
                                                            value="{{ old($variable['name'], $variable['default_value'] ?? '') }}"
                                                            placeholder="{{ $variable['default_value'] ?? '' }}"
                                                        >
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    @if($kategori->isLayanan())
                                        Template ini menggunakan data dari DUK dan tidak memerlukan input tambahan.
                                    @else
                                        Template ini tidak memiliki variabel yang perlu diisi.
                                    @endif
                                </div>
                            @endif

                            <div class="mt-4">
                                <button type="button" class="btn btn-primary" onclick="generateTemplate('html')">
                                    <i class="fas fa-eye"></i> Preview HTML
                                </button>
                                <button type="button" class="btn btn-danger" onclick="generateTemplate('pdf')">
                                    <i class="fas fa-file-pdf"></i> Generate PDF
                                </button>
                            </div>
                        </form>

                        <!-- Preview Area -->
                        <div id="previewArea" class="mt-4" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Preview Template</h5>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="printPreview()">
                                            <i class="fas fa-print"></i> Print
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="copyToClipboard()">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="previewContent"></div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Handle pemohon selection for layanan surat
@if($kategori->isLayanan())
document.getElementById('pemohon_id').addEventListener('change', function() {
    const pemohonId = this.value;
    const previewDiv = document.getElementById('pemohon-preview');
    const dataDiv = document.getElementById('pemohon-data');
    
    if (pemohonId) {
        // Show loading
        dataDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat data pemohon...';
        previewDiv.style.display = 'block';
        
        // Fetch pemohon data
        fetch(`/api/pemohon/${pemohonId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<div class="row">';
                
                // Tampilkan data pelayanan
                html += '<div class="col-md-6">';
                html += '<h6>Data Pelayanan:</h6>';
                html += '<ul class="list-unstyled">';
                html += `<li><strong>ID:</strong> ${data.data.pelayanan.id}</li>`;
                html += `<li><strong>Tanggal:</strong> ${data.data.pelayanan.created_at}</li>`;
                html += '</ul>';
                html += '</div>';
                
                // Tampilkan data identitas
                html += '<div class="col-md-6">';
                html += '<h6>Data Identitas:</h6>';
                html += '<ul class="list-unstyled">';
                
                if (data.data.identitas && data.data.identitas.length > 0) {
                    data.data.identitas.forEach(item => {
                        html += `<li><strong>${item.nama_field}:</strong> ${item.nilai || '-'}</li>`;
                    });
                } else {
                    html += '<li><em>Tidak ada data identitas</em></li>';
                }
                
                html += '</ul>';
                html += '</div>';
                html += '</div>';
                
                dataDiv.innerHTML = html;
            } else {
                dataDiv.innerHTML = `<div class="text-danger">Error: ${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            dataDiv.innerHTML = '<div class="text-danger">Terjadi kesalahan saat memuat data</div>';
        });
    } else {
        previewDiv.style.display = 'none';
    }
});
@endif

function generateTemplate(output) {
    const form = document.getElementById('templateForm');

    if (output === 'pdf') {
        // For PDF, create a temporary form and submit to new window/tab
        const tempForm = document.createElement('form');
        tempForm.method = 'POST';
        tempForm.action = form.action;
        tempForm.target = '_blank'; // Open in new tab
        tempForm.style.display = 'none';
        
        // Copy all form data
        const formData = new FormData(form);
        for (let [key, value] of formData.entries()) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            tempForm.appendChild(input);
        }
        
        // Add output parameter
        const outputInput = document.createElement('input');
        outputInput.type = 'hidden';
        outputInput.name = 'output';
        outputInput.value = 'pdf';
        tempForm.appendChild(outputInput);
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        tempForm.appendChild(csrfInput);
        
        // Submit form
        document.body.appendChild(tempForm);
        tempForm.submit();
        document.body.removeChild(tempForm);
        
    } else {
        // For HTML preview, use AJAX
        const formData = new FormData(form);
        formData.append('output', output);
        
        // Show loading
        document.getElementById('previewContent').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Generating preview...</div>';
        document.getElementById('previewArea').style.display = 'block';
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('previewContent').innerHTML = data.html;
                
                // Scroll to preview
                document.getElementById('previewArea').scrollIntoView({ behavior: 'smooth' });
            } else {
                document.getElementById('previewContent').innerHTML = `<div class="alert alert-danger">Error: ${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('previewContent').innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat generate template</div>';
        });
    }
}

function printPreview() {
    const content = document.getElementById('previewContent').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Print Preview</title>
                <style>
                    body { font-family: 'Times New Roman', serif; margin: 0; padding: 20px; }
                    @media print { body { margin: 0; padding: 0; } }
                </style>
            </head>
            <body>
                ${content}
                <script>window.onload = function() { window.print(); window.close(); }<\/script>
            </body>
        </html>
    `);
    printWindow.document.close();
}

function copyToClipboard() {
    const content = document.getElementById('previewContent').innerText;
    navigator.clipboard.writeText(content).then(function() {
        alert('Konten berhasil disalin ke clipboard');
    }, function(err) {
        console.error('Could not copy text: ', err);
        alert('Gagal menyalin konten');
    });
}
</script>
@endsection 