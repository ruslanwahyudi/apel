@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
<style>
    .dataTables_wrapper .dataTables_processing {
        background: rgba(0, 0, 0, 0.1);
        border: none;
        box-shadow: none;
    }
    .btn-group-xs > .btn, .btn-xs {
        padding: .25rem .4rem;
        font-size: .875rem;
        line-height: .5;
        border-radius: .2rem;
    }
    table.dataTable td {
        vertical-align: middle;
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Pengumuman</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Pengumuman</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fa fa-bullhorn"></i> Daftar Pengumuman
                    </h3>
                    <div>
                        <a href="{{ route('blog.announcements') }}" class="btn btn-secondary">
                            <i class="fa fa-refresh"></i> Refresh
                        </a>
                        <a href="{{ route('blog.announcements.create') }}" class="btn btn-primary">
                            <i class="fa fa-plus"></i> Buat Pengumuman
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="example4" width="100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th style="width: 30%;">Judul</th>
                                    <th>Gambar</th>
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
	    // START CODE Individual column searching (text inputs) DATA TABLE 		
		$(document).ready(function() {
			// DataTable
			var table = $('#example4').DataTable();
            
            // Ensure the search is called on the DataTable instance
            $('#example4_filter input').on('keyup', function() {
                table.search(this.value).draw();
                console.log(this.value);
                var search = this.value;
                $.ajax({
                    url: "{{ route('blog.announcements.search', ['search' => ':search']) }}",
                    method: "GET",
                    data: { search: search },
                    beforeSend: function() {
                        $('#data-table-body').html('<tr><td colspan="3" class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></td></tr>');
                    },
                    success: function(response) {
                        console.log(response);
                        let rows = '';
                        response.forEach((announcement, index) => {
                            rows += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>
                                        <b>Judul :</b> ${announcement.title}
                                        <br>
                                        <b>Kategori :</b> ${announcement.kategori.nama}
                                        <br>
                                        <b>Isi Pengumuman :</b> ${announcement.content}
                                    </td>
                                    <td>${announcement.kategori.nama}</td>
                                    <td><img src="/storage/${announcement.image}" alt="${announcement.title}" style="width: 100px; height: 100px;"></td>
                                    <td>
                                        @if (can('pengumuman', 'can_update'))
                                            <button class="btn btn-warning btn-sm edit-announcement" data-id="${announcement.id}" data-title="${announcement.title}">Edit <i class="fa fa-edit"></i></button>
                                        @endif
                                        @if (can('pengumuman', 'can_delete'))
                                            <button class="btn btn-danger btn-sm delete-announcement" data-id="${announcement.id}">Hapus <i class="fa fa-trash"></i></button>
                                        @endif
                                    </td>
                                </tr>
                            `;
                        });
                        $('#data-table-body').html(rows);
                    }
                });
            });
		});
		// END CODE Individual column searching (text inputs) DATA TABLE 	 	
	</script>	
<!-- END Java Script for this page -->

<script>
    $(document).ready(function() {

        // Set CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        loadAnnouncements();

        // Fungsi untuk memuat daftar roles
        function loadAnnouncements() {
            // alert("load role");
            $.ajax({
                url: "{{ route('blog.announcements') }}",
                method: "GET",
                beforeSend: function() {
                    $('#data-table-body').html('<tr><td colspan="3" class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></td></tr>');
                },
                success: function(response) {
                    let rows = '';
                    response.forEach((announcement, index) => {
                        rows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>
                                    <b>Judul :</b> ${announcement.title}
                                    <br>
                                    <b>Kategori :</b> ${announcement.kategori.nama}
                                    <br>
                                    <b>Isi Pengumuman :</b> ${announcement.content}
                                </td>
                                <td><img src="/storage/${announcement.image}" alt="${announcement.title}" style="width: 100px; height: 100px;"></td>
                                
                                <td>
                                    @if (can('pengumuman', 'can_update'))
                                        <button class="btn btn-warning btn-sm edit-announcement" data-id="${announcement.id}" data-title="${announcement.title}">Edit <i class="fa fa-edit"></i></button>
                                    @endif
                                    @if (can('pengumuman', 'can_delete'))
                                            <button class="btn btn-danger btn-sm delete-announcement" data-id="${announcement.id}">Hapus <i class="fa fa-trash"></i></button>
                                    @endif
                                </td>
                            </tr>
                        `;
                    });
                    $('#data-table-body').html(rows);
                }
            });
        }

        $('#refreshButton').click(function() {
            loadAnnouncements();
        });

        $('#data-table-body').on('click', '.edit-announcement', function() {
            var announcementId = $(this).data('id');
            var url = "{{ route('blog.announcements.edit', ['announcement' => ':announcementId']) }}";
            url = url.replace(':announcementId', announcementId);
            window.location.href = url;
        });

        $('#data-table-body').on('click', '.delete-announcement', function() {
            var announcementId = $(this).data('id');
            swal({
			  title: "Apakah Anda yakin?",
			  text: "Sekali dihapus, Anda tidak akan dapat mengembalikan ini!",
			  icon: "warning",
			  buttons: true,
			  dangerMode: true,
			})
			.then((willDelete) => {
			  if (willDelete) {
                deleteAnnouncement(announcementId);
			  }
			});
            
        });

        function deleteAnnouncement(announcementId) {
            var url = "{{ route('blog.announcements.destroy', ['announcement' => ':announcementId']) }}";
            url = url.replace(':announcementId', announcementId);
            $.ajax({
                url: url,
                method: "DELETE",
                success: function(response) {
                    // Menampilkan pesan dalam bentuk session di atas tabel
                    if (response.message) {
                        
                    var alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${response.message}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `;
                    $('#alert-container').html(alertHtml);

                    setTimeout(function() {
                        $('.alert').alert('close');
                    }, 5000);
                    }
                    loadAnnouncements();
                },
                error: function(xhr) {
                    alert('Failed to delete announcement');
                }
            });
        }
            
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
        
    });
</script>
@endsection