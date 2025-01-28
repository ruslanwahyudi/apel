@extends('layouts.admin')

@section('css')
<!-- BEGIN CSS for this page -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css"/>	
		<style>	
		td.details-control {
		background: url('assets/plugins/datatables/img/details_open.png') no-repeat center center;
		cursor: pointer;
		}
		tr.shown td.details-control {
		background: url('assets/plugins/datatables/img/details_close.png') no-repeat center center;
		}
		</style>		
		<!-- END CSS for this page -->
@endsection

@section('content')
<div class="container-fluid">
	
				<div class="row">
						<div class="col-xl-12">
								<div class="breadcrumb-holder">
										<h1 class="main-title float-left">Data Dusun</h1>
										<ol class="breadcrumb float-right">
											<li class="breadcrumb-item">Home</li>
											<li class="breadcrumb-item active">Data Dusun</li>
										</ol>
										<div class="clearfix"></div>
								</div>
						</div>
				</div>
				<!-- end row -->

				<div class="row">
						
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">						
							<div class="card mb-3">
								<div class="card-header">
									<div class="row">
                                        <div class="col-md-4">
                                            <h3><i class="fa fa-table"></i> Data Dusun</h3>
                                        </div>
                                        <div class="col-md-8 text-right">
                                            <button id="refreshButton" class="btn btn-secondary btn-sm">Refresh <i class="fa fa-refresh"></i></button>
                                            @if (can('data dusun', 'can_create'))
                                                <a href="{{ route('admin.dusun.create') }}" class="btn btn-primary btn-sm">Tambah <i class="fa fa-plus"></i></a>
                                            @endif

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
									<table id="example1" class="table table-bordered table-hover display">
									<thead>
										<tr>
                                            <th>No</th>
                                            <th>Nama Dusun</th>
                                            <th>Kepala Dusun</th>
                                            <th>Jumlah KK</th>
                                            <th>Jumlah Perempuan</th>
                                            <th>Jumlah Laki-laki</th>
                                            <th>Total Penduduk</th>
                                            <th>Action</th>
										</tr>
									</thead>									
									<tbody id="data-table-body">
										
									</tbody>
									</table>
									</div>

								</div>							
							</div><!-- end card-->					
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
			var table = $('#example1').DataTable();
            
            // Ensure the search is called on the DataTable instance
            $('#example1_filter input').on('keyup', function() {
                table.search(this.value).draw();
                console.log(this.value);
                var search = this.value;
                $.ajax({
                    url: "{{ route('admin.dusun.search', ['search' => ':search']) }}",
                    method: "GET",
                    data: { search: search },
                    beforeSend: function() {
                        $('#data-table-body').html('<tr><td colspan="3" class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></td></tr>');
                    },
                    success: function(response) {
                        console.log(response);
                        let rows = '';
                        response.forEach((dusun, index) => {
                            const totalPenduduk = parseInt(dusun.jumlah_pr) + parseInt(dusun.jumlah_lk);
                            rows += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${dusun.nama_dusun}</td>
                                    <td>${dusun.user.name}</td>
                                    <td>${dusun.jumlah_kk}</td>
                                    <td>${dusun.jumlah_pr}</td>
                                    <td>${dusun.jumlah_lk}</td>
                                    <td>${totalPenduduk}</td>
                                    <td>
                                        @if (can('dusun', 'can_update'))
                                            <button class="btn btn-warning btn-sm edit-dusun" data-id="${dusun.id}">
                                                Edit <i class="fa fa-edit"></i>
                                            </button>
                                        @endif
                                        @if (can('dusun', 'can_delete'))
                                            <button class="btn btn-danger btn-sm delete-dusun" data-id="${dusun.id}">
                                                Hapus <i class="fa fa-trash"></i>
                                            </button>
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

        loadDusun();

        // Fungsi untuk memuat daftar roles
        function loadDusun() {
            // alert("load role");
            $.ajax({
                url: "{{ route('admin.dusun') }}",
                method: "GET",
                beforeSend: function() {
                    $('#data-table-body').html('<tr><td colspan="3" class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></td></tr>');
                },
                success: function(response) {
                    let rows = '';
                    response.forEach((dusun, index) => {
                        const totalPenduduk = parseInt(dusun.jumlah_pr) + parseInt(dusun.jumlah_lk);
                        rows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${dusun.nama_dusun}</td>
                                <td>${dusun.user.name}</td>
                                <td>${dusun.jumlah_kk}</td>
                                <td>${dusun.jumlah_pr}</td>
                                <td>${dusun.jumlah_lk}</td>
                                <td>${totalPenduduk}</td>
                                
                                <td>
                                    @if (can('data dusun', 'can_update'))
                                        <button class="btn btn-warning btn-sm edit-dusun" data-id="${dusun.id}">
                                            Edit <i class="fa fa-edit"></i>
                                        </button>
                                    @endif
                                    @if (can('data dusun', 'can_delete'))
                                        <button class="btn btn-danger btn-sm delete-dusun" data-id="${dusun.id}">
                                            Hapus <i class="fa fa-trash"></i>
                                        </button>
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
            loadDusun();
        });

        $('#data-table-body').on('click', '.edit-dusun', function() {
            var dusunId = $(this).data('id');
            window.location.href = `{{ url('admin/dusun/edit') }}/${dusunId}`;
        });

        $('#data-table-body').on('click', '.delete-dusun', function() {
            var dusunId = $(this).data('id');
            swal({
              title: "Apakah Anda yakin?",
              text: "Sekali dihapus, Anda tidak akan dapat mengembalikan data ini!",
              icon: "warning",
              buttons: true,
              dangerMode: true,
            })
            .then((willDelete) => {
              if (willDelete) {
                deleteDusun(dusunId);
              }
            });
            
        });

        function deleteDusun(dusunId) {
            $.ajax({
                url: `{{ url('admin/dusun') }}/${dusunId}`,
                method: "DELETE",
                success: function(response) {
                    // Menampilkan pesan dalam bentuk session di atas tabel
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

                    setTimeout(function() {
                        $('.alert').alert('close');
                    }, 5000);
                    }
                    loadDusun();
                },
                error: function(xhr) {
                    swal("Error!", "Gagal menghapus data dusun", "error");
                }
            });
        }
            
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
        
    });
</script>
@endsection

