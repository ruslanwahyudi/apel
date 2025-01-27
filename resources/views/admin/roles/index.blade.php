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
										<h1 class="main-title float-left">Roles</h1>
										<ol class="breadcrumb float-right">
											<li class="breadcrumb-item">Home</li>
											<li class="breadcrumb-item active">Roles</li>
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
                                            <h3><i class="fa fa-table"></i> Roles</h3>
                                        </div>
                                        <div class="col-md-8 text-right">
                                            <button id="refreshButton" class="btn btn-secondary btn-sm">Refresh <i class="fa fa-refresh"></i></button>
                                            @if (can('roles', 'can_create'))
                                                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">Tambah <i class="fa fa-plus"></i></a>
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
                                
									<!-- The searching functionality that is provided by DataTables is very useful for quickly search through the information in the table - however the search is global, and you may wish to present controls to search on specific columns only. <a target="_blank" href="https://datatables.net/examples/api/multi_filter.html">(more details)</a> -->
                                    <!-- <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addRoleModal">Tambah Role</button> -->
                                <!-- </div> -->
									
								<div class="card-body">
									
									<div class="table-responsive">
									<table id="example4" class="table table-bordered table-hover display">
									<thead>
										<tr>
                                            <th>No</th>
                                            <th>Name</th>
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
			var table = $('#example4').DataTable();
            
            // Ensure the search is called on the DataTable instance
            $('#example4_filter input').on('keyup', function() {
                table.search(this.value).draw();
                console.log(this.value);
                var search = this.value;
                $.ajax({
                    url: "{{ route('admin.roles.search', ['search' => ':search']) }}",
                    method: "GET",
                    data: { search: search },
                    beforeSend: function() {
                        $('#data-table-body').html('<tr><td colspan="3" class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></td></tr>');
                    },
                    success: function(response) {
                        console.log(response);
                        let rows = '';
                        response.forEach((role, index) => {
                            rows += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${role.name}</td>
                                    <td>
                                        @if (can('roles', 'can_update'))
                                            <button class="btn btn-warning btn-sm edit-role" data-id="${role.id}" data-name="${role.name}">Edit <i class="fa fa-edit"></i></button>
                                        @endif
                                        @if (can('roles', 'can_delete'))
                                            <button class="btn btn-danger btn-sm delete-role" data-id="${role.id}">Hapus <i class="fa fa-trash"></i></button>
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

        loadRoles();

        // Fungsi untuk memuat daftar roles
        function loadRoles() {
            // alert("load role");
            $.ajax({
                url: "{{ route('admin.roles') }}",
                method: "GET",
                beforeSend: function() {
                    $('#data-table-body').html('<tr><td colspan="3" class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></td></tr>');
                },
                success: function(response) {
                    let rows = '';
                    response.forEach((role, index) => {
                        rows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${role.name}</td>
                                <td>
                                    @if (can('roles', 'can_update'))    
                                        <button class="btn btn-warning btn-sm edit-role" data-id="${role.id}" data-name="${role.name}">Edit <i class="fa fa-edit"></i></button>
                                    @endif
                                    @if (can('roles', 'can_delete'))
                                        <button class="btn btn-danger btn-sm delete-role" data-id="${role.id}">Hapus <i class="fa fa-trash"></i></button>
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
            loadRoles();
        });

        $('#data-table-body').on('click', '.edit-role', function() {
            var roleId = $(this).data('id');
            var url = "{{ route('admin.roles.edit', ['role' => ':roleId']) }}";
            url = url.replace(':roleId', roleId);
            window.location.href = url;
        });

        $('#data-table-body').on('click', '.delete-role', function() {
            var roleId = $(this).data('id');
            swal({
			  title: "Apakah Anda yakin?",
			  text: "Sekali dihapus, Anda tidak akan dapat mengembalikan ini!",
			  icon: "warning",
			  buttons: true,
			  dangerMode: true,
			})
			.then((willDelete) => {
			  if (willDelete) {
                deleteRole(roleId);
			  }
			});
            
        });

        function deleteRole(roleId) {
            var url = "{{ route('admin.roles.destroy', ['role' => ':roleId']) }}";
            url = url.replace(':roleId', roleId);
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
                    loadRoles();
                },
                error: function(xhr) {
                    alert('Failed to delete role');
                }
            });
        }
            
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
        
    });
</script>
@endsection

