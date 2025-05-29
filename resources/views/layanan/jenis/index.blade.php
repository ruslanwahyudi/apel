@extends('layouts.admin')

@section('css')
<!-- BEGIN CSS for this page -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css"/>    
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Jenis Layanan</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Jenis Layanan</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">                      
            <div class="card mb-3">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-4">
                            <h3><i class="fa fa-table"></i> Jenis Layanan</h3>
                        </div>
                        <div class="col-md-8 text-right">
                            <button id="refreshButton" class="btn btn-secondary btn-sm">Refresh <i class="fa fa-refresh"></i></button>
                            <a href="{{ route('layanan.jenis.create') }}" class="btn btn-primary btn-sm">Tambah <i class="fa fa-plus"></i></a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div style="height: 20px;"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="alert-container">
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example4" class="table table-bordered table-hover display">
                            <thead>
                                <tr>
                                    <th style="width: 20px;">No</th>
                                    <th>Nama Layanan</th>
                                    <th>Identitas Diperlukan</th>
                                    <th>Syarat Dokumen</th>
                                    <th>Action</th>
                                </tr>
                            </thead>                                   
                            <tbody id="data-table-body">
                            </tbody>
                        </table>
                    </div>
                </div>                          
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Identitas -->
<div class="modal fade" id="identitasModal" tabindex="-1" role="dialog" aria-labelledby="identitasModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="identitasModalLabel">Tambah Identitas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="identitasForm">
                @csrf
                <input type="hidden" name="jenis_pelayanan_id" id="jenis_pelayanan_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="klasifikasi_id">Klasifikasi <span class="text-danger">*</span></label>
                        <select class="form-control" id="klasifikasi_id" name="klasifikasi_id" required>
                            <option value="">Pilih Klasifikasi</option>
                            @php
                                $klasifikasiList = \App\Models\Layanan\KlasifikasiIdentitasPemohon::where('status', true)
                                    ->orderBy('urutan')
                                    ->get();
                            @endphp
                            @foreach($klasifikasiList as $klasifikasi)
                                <option value="{{ $klasifikasi->id }}">{{ $klasifikasi->nama_klasifikasi }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nama_field">Nama Field <span class="text-danger">*</span></label>
                        <input type="hidden" name="id" id="id">
                        <input type="text" class="form-control" id="nama_field" name="nama_field" required>
                    </div>

                    <div class="form-group">
                        <label for="tipe_field">Tipe Field <span class="text-danger">*</span></label>
                        <select class="form-control" id="tipe_field" name="tipe_field" required>
                            <option value="">Pilih Tipe Field</option>
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="email">Email</option>
                            <option value="date">Date</option>
                            <option value="textarea">Textarea</option>
                            <option value="select">Select</option>
                            <option value="radio">Radio</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="file">File</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="required" name="required" value="1">
                            <label class="custom-control-label" for="required">Required</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Form Syarat Dokumen -->
<div class="modal fade" id="syaratDokumenModal" tabindex="-1" role="dialog" aria-labelledby="syaratDokumenModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="syaratDokumenModalLabel">Tambah Syarat Dokumen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="syaratDokumenForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama_dokumen">Nama Dokumen</label>
                            <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen" required>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" id="required" name="required" value="1">
                        <input type="hidden" id="jenis_pelayanan_id" name="jenis_pelayanan_id">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- BEGIN Java Script for this page -->
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#example4').DataTable();
    
    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    loadJenis();

    function loadJenis() {
        $.ajax({
            url: "{{ route('layanan.jenis') }}",
            method: "GET",
            beforeSend: function() {
                $('#data-table-body').html('<tr><td colspan="6" class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></td></tr>');
            },
            success: function(response) {
                console.log(response);
                let rows = '';
                response.forEach((jenis, index) => {
                    rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>
                                Nama Pelayanan: ${jenis.nama_pelayanan}
                                <br>
                                Deskripsi: ${jenis.deskripsi}
                            </td>
                            <td>
                                <div id="identitas-container-${jenis.id}" style="display: flex; flex-direction: column; gap: 10px;">
                                    <div id="identitas-form-${jenis.id}">
                                        <button class="btn btn-primary btn-sm add-identitas" data-id="${jenis.id}"><i class="fa fa-plus"> Tambah Identitas </i></button>
                                    </div>
                                    <div id="identitas-list-${jenis.id}" class="ml-0">
                                        ${jenis.identitas_pemohon.length > 0 ? 
                                            (() => {
                                                // Group identitas_pemohon by klasifikasi_id
                                                const groupedData = {};
                                                jenis.identitas_pemohon.forEach(item => {
                                                    const klasifikasiId = item.klasifikasi_id || 'undefined';
                                                    if (!groupedData[klasifikasiId]) {
                                                        groupedData[klasifikasiId] = [];
                                                    }
                                                    groupedData[klasifikasiId].push(item);
                                                });
                                                
                                                // Render each group with heading
                                                let html = '<div style="display: flex; flex-direction: column; gap: 15px;">';
                                                
                                                for (const [klasifikasiId, items] of Object.entries(groupedData)) {
                                                    if (klasifikasiId !== 'undefined') {
                                                        const klasifikasi = items[0].klasifikasi ? items[0].klasifikasi.nama_klasifikasi : 'Lainnya';
                                                        html += `<div class="card">
                                                            <div class="card-header bg-primary text-white p-2">
                                                                ${klasifikasi}
                                                            </div>
                                                            <div class="card-body p-2">
                                                                <ul style="display: flex; flex-direction: column; gap: 1px; padding-left: 8px; margin-bottom: 0;">
                                                                ${items.map(identitas_pemohon => `<li>
                                                                    <button class="btn btn-danger btn-sm delete-identitas" data-id="${identitas_pemohon.id}"><i class="fa fa-trash"></i></button>
                                                                    <button class="btn btn-primary btn-sm edit-identitas" data-id="${identitas_pemohon.id}"><i class="fa fa-edit"></i></button>
                                                                    ${identitas_pemohon.nama_field} (${identitas_pemohon.required ? 'Wajib' : 'Tidak Wajib'})
                                                                </li>`).join('')}
                                                                </ul>
                                                            </div>
                                                        </div>`;
                                                    } else {
                                                        html += `<div class="card">
                                                            <div class="card-header bg-secondary text-white p-2">
                                                                Belum Diklasifikasi
                                                            </div>
                                                            <div class="card-body p-2">
                                                                <ul style="display: flex; flex-direction: column; gap: 1px; padding-left: 8px; margin-bottom: 0;">
                                                                ${items.map(identitas_pemohon => `<li>
                                                                    <button class="btn btn-danger btn-sm delete-identitas" data-id="${identitas_pemohon.id}"><i class="fa fa-trash"></i></button>
                                                                    <button class="btn btn-primary btn-sm edit-identitas" data-id="${identitas_pemohon.id}"><i class="fa fa-edit"></i></button>
                                                                    ${identitas_pemohon.nama_field} (${identitas_pemohon.required ? 'Wajib' : 'Tidak Wajib'})
                                                                </li>`).join('')}
                                                                </ul>
                                                            </div>
                                                        </div>`;
                                                    }
                                                }
                                                
                                                html += '</div>';
                                                return html;
                                            })()
                                         : '-'}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div id="syarat-dokumen-container-${jenis.id}" style="display: flex; flex-direction: column; gap: 10px;">
                                    <div id="syarat-dokumen-form-${jenis.id}">
                                        <button class="btn btn-primary btn-sm add-syarat-dokumen" data-id="${jenis.id}"><i class="fa fa-plus"> Tambah Syarat Dokumen</i></button>
                                    </div>
                                    <div id="syarat-dokumen-list-${jenis.id}" class="ml-0">
                                        ${jenis.syarat_dokumen.length > 0 ? `<ul style="display: flex; flex-direction: column; gap: 10px; padding-left: 8px;">${jenis.syarat_dokumen.map(syarat => `<li>
                                        <button class="btn btn-danger btn-sm delete-syarat-dokumen" data-id="${syarat.id}"><i class="fa fa-trash"></i></button>
                                        <button class="btn btn-primary btn-sm edit-syarat-dokumen" data-id="${syarat.id}"><i class="fa fa-edit"></i></button>
                                        ${syarat.nama_dokumen} (${syarat.required ? 'Wajib' : 'Tidak Wajib'})  </li>`).join('')}</ul>` : '-'}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column" style="gap: 3px;">
                                    <button class="btn btn-warning btn-sm edit-jenis " data-id="${jenis.id}"> Edit <i class="fa fa-edit"></i></button>
                                    <button class="btn btn-danger btn-sm delete-jenis " data-id="${jenis.id}"> Hapus <i class="fa fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                $('#data-table-body').html(rows);
            }
        });
    }

    $('#refreshButton').click(function() {
        loadJenis();
    });

    // Show modal when add-identitas button is clicked
    $(document).on('click', '.add-identitas', function() {
        var jenisId = $(this).data('id');
        $('#identitasForm').find('#jenis_pelayanan_id').val(jenisId);
        $('#identitasModal').modal('show');
    });

    $(document).on('click', '.edit-identitas', function() {
        var identitasId = $(this).data('id');
        
        // Reset form sebelum memuat data baru
        $('#identitasForm')[0].reset();
        
        // Tampilkan loading indicator
        $('#identitasModal .modal-body').append('<div id="loading-indicator" class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
        
        $.ajax({
            url: "{{ route('layanan.identitas_pemohon.show', ['identitas' => ':identitasId']) }}".replace(':identitasId', identitasId),
            method: 'GET',
            success: function(response) {
                // Hapus loading indicator
                $('#loading-indicator').remove();
                
                console.log('Edit identitas response:', response);
                
                // Isi form dengan data yang diterima
                $('#identitasForm').find('input[name="jenis_pelayanan_id"]').val(response.jenis_pelayanan_id);
                $('#identitasForm').find('input[name="nama_field"]').val(response.nama_field);
                $('#identitasForm').find('select[name="tipe_field"]').val(response.tipe_field);
                
                // Tangani klasifikasi_id dengan lebih baik
                var klasifikasiId = response.klasifikasi_id;
                console.log('Setting klasifikasi_id to:', klasifikasiId);
                
                // Debug - tampilkan semua option yang tersedia
                var options = [];
                $('#identitasForm select[name="klasifikasi_id"] option').each(function() {
                    options.push({ value: $(this).val(), text: $(this).text() });
                });
                console.log('Available klasifikasi options:', options);
                
                $('#identitasForm').find('select[name="klasifikasi_id"]').val(klasifikasiId);
                
                // Check jika nilai klasifikasi_id dipilih dengan benar
                setTimeout(function() {
                    var selectedVal = $('#identitasForm').find('select[name="klasifikasi_id"]').val();
                    console.log('Selected klasifikasi_id after setting:', selectedVal);
                    
                    // Jika tidak terpilih dengan benar, coba lagi
                    if (klasifikasiId && selectedVal != klasifikasiId) {
                        $('#identitasForm').find('select[name="klasifikasi_id"]').val(klasifikasiId);
                        console.log('Re-setting klasifikasi_id to:', klasifikasiId);
                    }
                }, 100);
                
                $('#identitasForm').find('input[name="required"]').prop('checked', response.required);
                $('#identitasForm').find('input[name="id"]').val(response.id);
            },
            error: function(xhr) {
                // Hapus loading indicator
                $('#loading-indicator').remove();
                
                console.error('Error loading identitas data:', xhr);
                swal("Error!", "Terjadi kesalahan saat memuat data identitas.", "error");
            }
        });

        $('#identitasModal').modal('show');
    });

    // Show modal when add-syarat-dokumen button is clicked
    $(document).on('click', '.add-syarat-dokumen', function() {
        var jenisId = $(this).data('id');
        $('#syaratDokumenForm').find('#jenis_pelayanan_id').val(jenisId);  
        $('#syaratDokumenModal').modal('show');
    });

    $(document).on('click', '.edit-syarat-dokumen', function() {
        
        var syaratDokumenId = $(this).data('id');
        
        $.ajax({
            url: "{{ route('layanan.syarat_dokumen.show', ['persyaratan' => ':persyaratanId']) }}".replace(':persyaratanId', syaratDokumenId),
            method: 'GET',
            success: function(response) {
                $('#syaratDokumenForm')[0].reset();
                $('#syaratDokumenForm').find('input[name="jenis_pelayanan_id"]').val(response.jenis_pelayanan_id);
                $('#syaratDokumenForm').find('input[name="nama_dokumen"]').val(response.nama_dokumen);
                $('#syaratDokumenForm').find('textarea[name="deskripsi"]').val(response.deskripsi);
                $('#syaratDokumenForm').find('input[name="required"]').prop('checked', response.required);
                $('#syaratDokumenForm').find('input[name="id"]').val(response.id);
            }
        });

        $('#syaratDokumenModal').modal('show');
    });

    $(document).on('click', '.delete-syarat-dokumen', function() {
        var syaratDokumenId = $(this).data('id');
        console.log(syaratDokumenId);
        swal({
            title: "Apakah Anda yakin?",
            text: "Sekali dihapus, Anda tidak akan dapat mengembalikan data ini!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: "{{ route('layanan.syarat_dokumen.destroy', ['persyaratan' => ':syarat_dokumenId']) }}".replace(':syarat_dokumenId', syaratDokumenId),
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        swal("Berhasil!", response.message, "success");
                        loadJenis();
                    },
                    error: function(xhr) {
                        swal("Error!", "Terjadi kesalahan saat menghapus data.", "error");
                    }
                });
            }
        });
    });


    // Form submit handler for Syarat Dokumen
    $('#syaratDokumenForm').submit(function(e) {
        e.preventDefault();
        let url = '';
        let method = '';
        if ($('#syaratDokumenForm').find('input[name="id"]').val() == '') {
            url = "{{ route('layanan.syarat_dokumen.store') }}";
            method = 'POST';
        }else{
            url = "{{ route('layanan.syarat_dokumen.update', ['persyaratan' => ':persyaratanId']) }}".replace(':persyaratanId', $('#syaratDokumenForm').find('input[name="id"]').val());
            method = 'PUT';
        }   

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(response) {
                $('#syaratDokumenModal').modal('hide');
                swal("Berhasil!", response.message, "success");
                loadJenis();
                $('#syaratDokumenForm')[0].reset();
            },
            error: function(xhr) {
                swal("Error!", "Terjadi kesalahan saat menyimpan data.", "error");
            }
        });
    });

    // Form submit handler
    $('#identitasForm').submit(function(e) {
        e.preventDefault();
        
        // Explicitly handle required checkbox
        let isRequired = $('#identitasForm').find('input[name="required"]').is(':checked') ? 1 : 0;
        
        // Create a FormData object for more control
        let formData = new FormData(this);
        
        // Set required value explicitly
        formData.set('required', isRequired);
        
        // Convert FormData to URL-encoded string for ajax
        let formParams = new URLSearchParams(formData).toString();
        
        console.log('Form data to submit:', formParams);
        
        // Log form field values for debugging
        let debugData = {
            id: $('#identitasForm').find('input[name="id"]').val(),
            jenis_pelayanan_id: $('#identitasForm').find('input[name="jenis_pelayanan_id"]').val(),
            klasifikasi_id: $('#identitasForm').find('select[name="klasifikasi_id"]').val(),
            nama_field: $('#identitasForm').find('input[name="nama_field"]').val(),
            tipe_field: $('#identitasForm').find('select[name="tipe_field"]').val(),
            required: isRequired
        };
        console.log('Debug form values:', debugData);
        
        // Determine URL and method
        let url = '';
        let method = '';
        if ($('#identitasForm').find('input[name="id"]').val() == '') {
            url = "{{ route('layanan.identitas_pemohon.store') }}";
            method = 'POST';
        } else {
            url = "{{ route('layanan.identitas_pemohon.update', ['identitas' => ':identitasId']) }}".replace(':identitasId', $('#identitasForm').find('input[name="id"]').val());
            method = 'PUT';
        }
        
        // Show loading indicator
        $('#identitasModal .modal-footer').prepend('<div id="submit-loading" class="mr-2"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
        
        $.ajax({
            url: url,
            method: method,
            data: formParams,
            success: function(response) {
                // Remove loading indicator
                $('#submit-loading').remove();
                
                console.log('Submit success response:', response);
                $('#identitasModal').modal('hide');
                swal("Berhasil!", response.message, "success");
                loadJenis();
                $('#identitasForm')[0].reset();
            },
            error: function(xhr) {
                // Remove loading indicator
                $('#submit-loading').remove();
                
                console.error('Submit error:', xhr);
                let errorMsg = 'Terjadi kesalahan saat menyimpan data.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                swal("Error!", errorMsg, "error");
            }
        });
    });

    // Search functionality
    $('#example4_filter input').on('keyup', function() {
        table.search(this.value).draw();
        var search = this.value;
        $.ajax({
            url: "{{ route('layanan.jenis.search', ['search' => ':search']) }}",
            method: "GET",
            data: { search: search },
            success: function(response) {
                let rows = '';
                response.forEach((jenis, index) => {
                    rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>
                                Nama Pelayanan: ${jenis.nama_pelayanan}
                                <br>
                                Deskripsi: ${jenis.deskripsi}
                            </td>
                            <td>
                                <div id="identitas-container-${jenis.id}">
                                    ${jenis.identitas.length > 0 ? `<ul>${jenis.identitas.map(identitas => `<li>${identitas.nama}</li>`).join('')}</ul>` : '-'}
                                </div>
                                <button class="btn btn-primary btn-sm add-identitas" data-id="${jenis.id}">Tambah <i class="fa fa-plus"></i></button>
                            </td>
                            <td>
                                <div id="syarat-dokumen-container-${jenis.id}" style="display: flex; flex-direction: row; gap: 10px;">
                                    ${jenis.syarat_dokumen.length > 0 ? `<ul>${jenis.syarat_dokumen.map(syarat => `<li>${syarat.nama_dokumen}</li>`).join('')}</ul>` : '-'}
                                    <div id="syarat-dokumen-form-${jenis.id}">
                                        <button class="btn btn-primary btn-sm add-syarat-dokumen" data-id="${jenis.id}">Tambah Syarat <i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-jenis" data-id="${jenis.id}">Edit <i class="fa fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm delete-jenis" data-id="${jenis.id}">Hapus <i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                });
                $('#data-table-body').html(rows);
            }
        });
    });

    // Edit handler
    $('#data-table-body').on('click', '.edit-jenis', function() {
        var jenisId = $(this).data('id');
        window.location.href = "{{ route('layanan.jenis.edit', ['jenis' => ':jenisId']) }}".replace(':jenisId', jenisId);
    });

    // Delete handler
    $('#data-table-body').on('click', '.delete-jenis', function() {
        var jenisId = $(this).data('id');
        swal({
            title: "Apakah Anda yakin?",
            text: "Sekali dihapus, Anda tidak akan dapat mengembalikan data ini!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: "{{ route('layanan.jenis.destroy', ['jenis' => ':jenisId']) }}".replace(':jenisId', jenisId),
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        swal("Berhasil!", response.message, "success");
                        loadJenis();
                    },
                    error: function(xhr) {
                        swal("Error!", "Terjadi kesalahan saat menghapus data.", "error");
                    }
                });
            }
        });
    });

    // Delete identitas handler
    $(document).on('click', '.delete-identitas', function() {
        var identitasId = $(this).data('id');
        swal({
            title: "Apakah Anda yakin?",
            text: "Sekali dihapus, Anda tidak akan dapat mengembalikan data ini!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: "{{ route('layanan.identitas_pemohon.destroy', ['identitas' => ':identitasId']) }}".replace(':identitasId', identitasId),
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        swal("Berhasil!", response.message, "success");
                        loadJenis();
                    },
                    error: function(xhr) {
                        swal("Error!", "Terjadi kesalahan saat menghapus data.", "error");
                    }
                });
            }
        });
    });
});
</script>
@endsection 