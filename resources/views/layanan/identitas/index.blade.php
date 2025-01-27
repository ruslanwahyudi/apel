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
                <h1 class="main-title float-left">Identitas Layanan</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Identitas Layanan</li>
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
                            <h3><i class="fa fa-table"></i> Identitas Layanan</h3>
                        </div>
                        <div class="col-md-8 text-right">
                            <button id="refreshButton" class="btn btn-secondary btn-sm">Refresh <i class="fa fa-refresh"></i></button>
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addIdentitasModal">
                                Tambah <i class="fa fa-plus"></i>
                            </button>
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
                        <table class="table table-bordered table-hover display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Layanan</th>
                                    <th>Nama Field</th>
                                    <th>Tipe Field</th>
                                    <th>Required</th>
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

<!-- Add Modal -->
<div class="modal fade" id="addIdentitasModal" tabindex="-1" role="dialog" aria-labelledby="addIdentitasModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addIdentitasModalLabel">Tambah Identitas Layanan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addIdentitasForm" action="{{ route('layanan.identitas_pemohon.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="jenis_pelayanan_id">Jenis Layanan <span class="text-danger">*</span></label>
                        <select class="form-control" id="jenis_pelayanan_id" name="jenis_pelayanan_id" required>
                            <option value="">Pilih Jenis Layanan</option>
                            @foreach($jenis_layanan ?? [] as $jenis)
                                <option value="{{ $jenis->id }}">{{ $jenis->nama_pelayanan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nama_field">Nama Field <span class="text-danger">*</span></label>
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
@endsection

@section('js')
<!-- BEGIN Java Script for this page -->
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
$(document).ready(function() {
    // Load data function
    function loadIdentitas() {
        $.ajax({
            url: "{{ route('layanan.identitas_pemohon.index') }}",
            method: 'GET',
            success: function(response) {
                let rows = '';
                response.forEach((identitas, index) => {
                    rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${identitas.jenis_pelayanan ? identitas.jenis_pelayanan.nama_pelayanan : '-'}</td>
                            <td>${identitas.nama_field}</td>
                            <td>${identitas.tipe_field}</td>
                            <td>${identitas.required ? '<span class="badge badge-success">Ya</span>' : '<span class="badge badge-danger">Tidak</span>'}</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-identitas" data-id="${identitas.id}">Edit <i class="fa fa-edit"></i></button>
                                <button class="btn btn-danger btn-sm delete-identitas" data-id="${identitas.id}">Hapus <i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                });
                $('#data-table-body').html(rows);
            }
        });
    }

    // Initial load
    loadIdentitas();

    // Refresh button handler
    $('#refreshButton').click(function() {
        loadIdentitas();
    });

    // Form submit handler
    $('#addIdentitasForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#addIdentitasModal').modal('hide');
                swal("Berhasil!", response.message, "success");
                loadIdentitas();
                $('#addIdentitasForm')[0].reset();
            },
            error: function(xhr) {
                swal("Error!", "Terjadi kesalahan saat menyimpan data.", "error");
            }
        });
    });

    // Edit handler
    $('#data-table-body').on('click', '.edit-identitas', function() {
        var identitasId = $(this).data('id');
        window.location.href = "{{ route('layanan.identitas_pemohon.edit', ['identitas' => ':identitasId']) }}".replace(':identitasId', identitasId);
    });

    // Delete handler
    $('#data-table-body').on('click', '.delete-identitas', function() {
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
                    success: function(response) {
                        swal("Berhasil!", response.message, "success");
                        loadIdentitas();
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