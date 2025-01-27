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
                <h1 class="main-title float-left">Persyaratan Layanan</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Persyaratan Layanan</li>
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
                            <h3><i class="fa fa-table"></i> Persyaratan Layanan</h3>
                        </div>
                        <div class="col-md-8 text-right">
                            <button id="refreshButton" class="btn btn-secondary btn-sm">Refresh <i class="fa fa-refresh"></i></button>
                            <a href="{{ route('layanan.syarat_dokumen.create') }}" class="btn btn-primary btn-sm">Tambah <i class="fa fa-plus"></i></a>
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
                                    <th>No</th>
                                    <th>Jenis Layanan</th>
                                    <th>Nama Persyaratan</th>
                                    <th>Deskripsi</th>
                                    <th>Wajib</th>
                                    <th>Aksi</th>
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

    loadPersyaratan();

    function loadPersyaratan() {
        $.ajax({
            url: "{{ route('layanan.syarat_dokumen') }}",
            method: "GET",
            beforeSend: function() {
                $('#data-table-body').html('<tr><td colspan="6" class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></td></tr>');
            },
            success: function(response) {
                console.log(response);
                let rows = '';
                response.forEach((persyaratan, index) => {
                    rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${persyaratan.jenis_layanan.nama_pelayanan}</td>
                            <td>${persyaratan.nama_dokumen}</td>
                            <td>${persyaratan.deskripsi ? persyaratan.deskripsi : '-'}</td>
                            <td>${persyaratan.required ? '<span class="badge badge-success">Wajib</span>' : '<span class="badge badge-danger">Tidak Wajib</span>'}</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-persyaratan" data-id="${persyaratan.id}">Edit <i class="fa fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm delete-persyaratan" data-id="${persyaratan.id}">Hapus <i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                });
                $('#data-table-body').html(rows);
            }
        });
    }

    $('#refreshButton').click(function() {
        loadPersyaratan();
    });

    // Search functionality
    $('#example4_filter input').on('keyup', function() {
        table.search(this.value).draw();
        var search = this.value;
        $.ajax({
            url: "{{ route('layanan.syarat_dokumen.search', ['search' => ':search']) }}",
            method: "GET",
            data: { search: search },
            success: function(response) {
                console.log(response);
                let rows = '';
                response.forEach((syarat_dokumen, index) => {
                    rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${syarat_dokumen.jenis_layanan.nama_pelayanan}</td>
                            <td>${syarat_dokumen.nama_dokumen}</td>
                            <td>${syarat_dokumen.deskripsi}</td>
                            <td>${syarat_dokumen.required ? '<span class="badge badge-success">Wajib</span>' : '<span class="badge badge-danger">Tidak Wajib</span>'}</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-persyaratan" data-id="${syarat_dokumen.id}">Edit <i class="fa fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm delete-persyaratan" data-id="${syarat_dokumen.id}">Hapus <i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                });
                $('#data-table-body').html(rows);
            }
        });
    });

    // Edit handler
    $('#data-table-body').on('click', '.edit-persyaratan', function() {
        var persyaratanId = $(this).data('id');
        window.location.href = "{{ route('layanan.syarat_dokumen.edit', ['persyaratan' => ':persyaratanId']) }}".replace(':persyaratanId', persyaratanId);
    });

    // Delete handler
    $('#data-table-body').on('click', '.delete-persyaratan', function() {
        var persyaratanId = $(this).data('id');
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
                    url: "{{ route('layanan.syarat_dokumen.destroy', ['persyaratan' => ':persyaratanId']) }}".replace(':persyaratanId', persyaratanId),
                    method: 'DELETE',
                    success: function(response) {
                        swal("Berhasil!", response.message, "success");
                        loadPersyaratan();
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