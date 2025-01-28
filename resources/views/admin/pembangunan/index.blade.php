@extends('layouts.admin')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css"/>	
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Data Kegiatan Pembangunan</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Kegiatan Pembangunan</li>
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
                            <h3><i class="fa fa-table"></i> Data Kegiatan Pembangunan</h3>
                        </div>
                        <div class="col-md-8 text-right">
                            <button id="refreshButton" class="btn btn-secondary btn-sm">Refresh <i class="fa fa-refresh"></i></button>
                            @if (can('pembangunan desa', 'can_create'))
                                <a href="{{ route('admin.pembangunan.create') }}" class="btn btn-primary btn-sm">Tambah <i class="fa fa-plus"></i></a>
                            @endif
                        </div>
                    </div>
                    <div class="row mt-3">
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
                        <table id="dataTable" class="table table-bordered table-hover display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Lokasi</th>
                                    <th>Anggaran</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
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
<script>
    $(document).ready(function() {
        // Tambahkan CSRF token ke semua AJAX request
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        loadKegiatan();

        function loadKegiatan() {
            $.ajax({
                url: "{{ route('admin.pembangunan') }}",
                method: "GET",
                success: function(response) {
                    var tableBody = '';
                    response.forEach(function(item, index) {
                        var latestProgres = item.progres.length > 0 ? item.progres[item.progres.length-1].persentase + '%' : '0%';
                        
                        tableBody += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.nama_kegiatan}</td>
                                <td>${item.lokasi}</td>
                                <td>Rp ${formatNumber(item.anggaran)}</td>
                                <td>${formatDate(item.tanggal_mulai)}</td>
                                <td><span class="badge badge-${getStatusBadge(item.status)}">${item.status}</span></td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: ${latestProgres}" 
                                            aria-valuenow="${latestProgres}" aria-valuemin="0" aria-valuemax="100">
                                            ${latestProgres}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.pembangunan.progres.create', '') }}/${item.id}" 
                                            class="btn btn-info btn-sm" title="Update Progress">
                                            <i class="fa fa-refresh"></i>
                                        </a>
                                        @if(can('pembangunan desa', 'can_update'))
                                            <a href="{{ route('admin.pembangunan.edit', '') }}/${item.id}" 
                                                class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                        @endif
                                        @if(can('pembangunan desa', 'can_delete'))
                                            <button type="button" class="btn btn-danger btn-sm btn-delete" 
                                                data-id="${item.id}" title="Hapus">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    $('#dataTable tbody').html(tableBody);
                }
            });
        }

        function formatNumber(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        function formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        }

        function getStatusBadge(status) {
            switch(status) {
                case 'Belum Dimulai': return 'secondary';
                case 'Dalam Pengerjaan': return 'primary';
                case 'Selesai': return 'success';
                case 'Terhenti': return 'danger';
                default: return 'secondary';
            }
        }

        $('#refreshButton').click(function() {
            loadKegiatan();
        });

        // Delete handler
        $(document).on('click', '.btn-delete', function() {
            var kegiatanId = $(this).data('id');
            
            swal({
                title: "Apakah Anda yakin?",
                text: "Sekali dihapus, Anda tidak akan dapat mengembalikan data ini!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    deleteKegiatan(kegiatanId);
                }
            });
        });

        function deleteKegiatan(kegiatanId) {
            $.ajax({
                url: `{{ url('admin/pembangunan') }}/${kegiatanId}`,
                method: "GET",
                success: function(response) {
                    if (response.success) {
                        var alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                ${response.message}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        `;
                        $('#alert-container').html(alertHtml);
                        loadKegiatan();
                    }
                },
                error: function(xhr) {
                    swal("Error!", "Gagal menghapus data kegiatan", "error");
                }
            });
        }
    });
</script>
@endsection 