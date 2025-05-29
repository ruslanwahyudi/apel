@extends('layouts.admin')

@section('css')
<!-- BEGIN CSS for this page -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css"/>    
<style>
    /* ... style yang ada ... */
    .modal-lg {
        max-width: 90%;
    }
    .btn {
        margin-bottom: 5px;
    }
    td .btn:last-child {
        margin-bottom: 0;
    }
    .preview-content {
        max-height: 600px;
        overflow-y: auto;
        padding: 20px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Daftar Layanan</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Daftar Layanan</li>
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
                            <h3><i class="fa fa-table"></i> Daftar Layanan</h3>
                        </div>
                        <div class="col-md-8 text-right">
                            <button id="refreshButton" class="btn btn-secondary btn-sm">Refresh <i class="fa fa-refresh"></i></button>
                            <a href="{{ route('layanan.daftar.create') }}" class="btn btn-primary btn-sm">Tambah <i class="fa fa-plus"></i></a>
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
                                    <th>Identitas Pemohon</th>
                                    <th>Dokumen Pengajuan</th>
                                    <th>Status</th>
                                    <th style="width: 200px;">Action</th>
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

<!-- Modal untuk preview dokumen -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Preview Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="previewFrame" style="width: 100%; height: 500px;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk preview surat -->
<div class="modal fade" id="previewSuratModal" tabindex="-1" role="dialog" aria-labelledby="previewSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 95%; margin: 1.75rem auto;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="previewSuratModalLabel">
                    <i class="fa fa-eye"></i> Preview Surat Layanan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div id="previewSuratContent" class="preview-content">
                    <!-- Konten preview akan dimuat disini -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Tutup
                </button>
                <button type="button" class="btn btn-primary" id="printPreview">
                    <i class="fa fa-print"></i> Print
                </button>
            </div>
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

    loadDaftarLayanan();

    function loadDaftarLayanan() {
        $.ajax({
            url: "{{ route('layanan.daftar') }}",
            method: "GET",
            beforeSend: function() {
                $('#data-table-body').html('<tr><td colspan="6" class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></td></tr>');
            },
            success: function(response) {
                let rows = '';
                response.forEach((layanan, index) => {
                    // Format tanggal dari created_at
                    let tanggal = new Date(layanan.created_at).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    });

                    // Format data identitas menjadi list
                    let dataIdentitas = '';
                    if (layanan.data_identitas && layanan.data_identitas.length > 0) {
                        dataIdentitas = '<ul class="list-unstyled m-0">';
                        layanan.data_identitas.forEach(data => {
                            // Tampilkan label dari identitas_pemohon dan nilai
                            dataIdentitas += `
                                <li>
                                    <small>
                                        ${data.identitas_pemohon ? data.identitas_pemohon.nama_field : ''}: 
                                        <b>${data.nilai || '-'}</b>
                                    </small>
                                </li>`;
                        });
                        dataIdentitas += '</ul>';
                    } else {
                        dataIdentitas = '<small>Tidak ada data identitas</small>';
                    }

                    // Format dokumen pengajuan menjadi list dengan preview
                    let dokumenPengajuan = '';
                    if (layanan.dokumen_pengajuan && layanan.dokumen_pengajuan.length > 0) {
                        dokumenPengajuan = '<ul class="list-unstyled m-0">';
                        layanan.dokumen_pengajuan.forEach(dok => {
                            let fullPath = `{{ url('/storage') }}/${dok.path_dokumen}`;
                            dokumenPengajuan += `
                                <li>
                                    <small>
                                        ${dok.syarat_dokumen.nama_dokumen} :
                                        <b><a href="javascript:void(0)" onclick="previewDokumen('${fullPath}')" class="text-primary">
                                            Lihat Dokumen
                                        </a></b>
                                    </small>
                                </li>`;
                        });
                        dokumenPengajuan += '</ul>';
                    } else {
                        dokumenPengajuan = '<small>Tidak ada dokumen</small>';
                    }
                    
                    rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td> Pengirim : <br><b>${layanan.user.name}</b><br>
                                Jenis Layanan : <br><b>${layanan.jenis_pelayanan.nama_pelayanan}</b><br>
                                Tanggal Pengajuan : <br><b>${tanggal}</b><br>
                            </td>
                            <td>${dataIdentitas}</td>
                            <td>${dokumenPengajuan}</td>
                            <td><span class="badge badge-${getStatusBadgeClass(layanan.status.description)}">${layanan.status.description}</span></td>
                            <td>
                                <div class="btn-group-vertical w-100" role="group">
                                    <button class="btn btn-info btn-sm preview-surat mb-1" data-id="${layanan.id}" title="Preview Surat">
                                        <i class="fa fa-eye"></i> Preview
                                    </button>
                                    ${layanan.status.description === 'Belum Diproses' ? 
                                        `<button class="btn btn-success btn-sm approve-layanan mb-1" data-id="${layanan.id}" title="Approve Layanan">
                                            <i class="fa fa-check"></i> Approve
                                        </button>` : ''
                                    }
                                    <button class="btn btn-warning btn-sm edit-layanan mb-1" data-id="${layanan.id}" title="Edit Layanan">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm delete-layanan" data-id="${layanan.id}" title="Hapus Layanan">
                                        <i class="fa fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                $('#data-table-body').html(rows);
            }
        });
    }

    // Function untuk status badge class
    function getStatusBadgeClass(status) {
        switch (status) {
            case 'Belum Diproses': return 'secondary';
            case 'Sedang Diproses': return 'warning';
            case 'Selesai': return 'success';
            case 'Draft': return 'light';
            case 'Ditolak': return 'danger';
            default: return 'info';
        }
    }

    $('#refreshButton').click(function() {
        loadDaftarLayanan();
    });

    // Search functionality
    $('#example4_filter input').on('keyup', function() {
        table.search(this.value).draw();
        var search = this.value;
        $.ajax({
            url: "{{ route('layanan.daftar.search', ['search' => ':search']) }}",
            method: "GET",
            data: { search: search },
            success: function(response) {
                let rows = '';
                response.forEach((layanan, index) => {
                    rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${layanan.nama}</td>
                            <td>${layanan.jenis}</td>
                            <td>${layanan.deskripsi}</td>
                            <td>${layanan.status}</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-layanan" data-id="${layanan.id}">Edit <i class="fa fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm delete-layanan" data-id="${layanan.id}">Hapus <i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                });
                $('#data-table-body').html(rows);
            }
        });
    });

    // Preview surat handler
    $('#data-table-body').on('click', '.preview-surat', function() {
        var layananId = $(this).data('id');
        
        $.ajax({
            url: "{{ route('layanan.daftar.preview-surat', ['id' => ':id']) }}".replace(':id', layananId),
            method: 'GET',
            beforeSend: function() {
                $('#previewSuratContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2">Memuat preview surat...</p></div>');
                $('#previewSuratModal').modal('show');
            },
            success: function(response) {
                if (response.success) {
                    $('#previewSuratContent').html(response.html);
                } else {
                    $('#previewSuratContent').html(
                        '<div class="alert alert-warning text-center">' +
                            '<i class="fa fa-exclamation-triangle"></i>' +
                            response.message +
                        '</div>'
                    );
                }
            },
            error: function(xhr) {
                $('#previewSuratContent').html(
                    '<div class="alert alert-danger text-center">' +
                        '<i class="fa fa-times-circle"></i>' +
                        'Terjadi kesalahan saat memuat preview surat' +
                    '</div>'
                );
            }
        });
    });

    // Print preview handler
    $('#printPreview').click(function() {
        var printContent = $('#previewSuratContent').html();
        var printWindow = window.open('', '_blank');
        printWindow.document.write(
            '<html>' +
                '<head>' +
                    '<title>Preview Surat</title>' +
                    '<style>' +
                        'body { font-family: Arial, sans-serif; margin: 20px; }' +
                        '@media print { body { margin: 0; } }' +
                    '</style>' +
                '</head>' +
                '<body>' +
                    printContent +
                    '<script>window.print(); window.close();<\/script>' +
                '</body>' +
            '</html>'
        );
        printWindow.document.close();
    });

    // Edit handler
    $('#data-table-body').on('click', '.edit-layanan', function() {
        var layananId = $(this).data('id');
        window.location.href = "{{ route('layanan.daftar.edit', ['daftar' => ':layananId']) }}".replace(':layananId', layananId);
    });

    // Delete handler
    $('#data-table-body').on('click', '.delete-layanan', function() {
        var layananId = $(this).data('id');
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
                    url: "{{ route('layanan.daftar.destroy', ['daftar' => ':layananId']) }}".replace(':layananId', layananId),
                    method: 'DELETE',
                    success: function(response) {
                        swal("Berhasil!", response.message, "success");
                        loadDaftarLayanan();
                    },
                    error: function(xhr) {
                        swal("Error!", "Terjadi kesalahan saat menghapus data.", "error");
                    }
                });
            }
        });
    });

    // Tambahkan handler untuk approve
    $('#data-table-body').on('click', '.approve-layanan', function() {
        var layananId = $(this).data('id');
        swal({
            title: "Apakah Anda yakin?",
            text: "Layanan ini akan disetujui!",
            icon: "warning",
            buttons: true,
            dangerMode: false,
        })
        .then((willApprove) => {
            if (willApprove) {
                $.ajax({
                    url: "{{ route('layanan.approve', ['id' => ':id']) }}".replace(':id', layananId),
                    method: 'POST',
                    success: function(response) {
                        swal("Berhasil!", "Layanan berhasil disetujui", "success");
                        loadDaftarLayanan();
                    },
                    error: function(xhr) {
                        swal("Error!", "Terjadi kesalahan saat menyetujui layanan.", "error");
                    }
                });
            }
        });
    });
});

// Fungsi untuk preview dokumen
function previewDokumen(url) {
    // Update src iframe
    $('#previewFrame').attr('src', url);
    
    // Tampilkan modal
    $('#previewModal').modal('show');
}
</script>
@endsection