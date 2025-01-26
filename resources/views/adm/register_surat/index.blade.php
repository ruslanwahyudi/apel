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
		#isiSuratContent {
			white-space: pre-line;
			font-size: 14px;
			line-height: 1.6;
			padding: 15px;
			background-color: #f8f9fa;
			border-radius: 4px;
			max-height: 500px;
			overflow-y: auto;
		}
		</style>		
		<!-- END CSS for this page -->
@endsection

@section('content')
<div class="container-fluid">
	
				<div class="row">
						<div class="col-xl-12">
								<div class="breadcrumb-holder">
										<h1 class="main-title float-left">Register Surat</h1>
										<ol class="breadcrumb float-right">
											<li class="breadcrumb-item">Home</li>
											<li class="breadcrumb-item active">Register Surat</li>
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
                                            <h3><i class="fa fa-table"></i> Register Surat</h3>
                                        </div>
                                        <div class="col-md-8 text-right">
                                            <button id="refreshButton" class="btn btn-secondary btn-sm">Refresh <i class="fa fa-refresh"></i></button>
                                            @if (can('surat keluar', 'can_create'))
                                                <a href="{{ route('adm.register_surat.create') }}" class="btn btn-primary btn-sm">Tambah <i class="fa fa-plus"></i></a>
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
                                            <th style="width: 20px;">No</th>
                                            <th>Header Surat</th> 
                                            <th style="width: 20px;">Isi Surat</th>
                                            <th>Penandatangan</th>
                                            <th>Lampiran</th>
                                            <th>Status</th>
                                            <!-- <th>Nama</th> -->
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
                    url: "{{ route('adm.register_surat.search', ['search' => ':search']) }}",
                    method: "GET",
                    data: { search: search },
                    beforeSend: function() {
                        $('#data-table-body').html('<tr><td colspan="3" class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></td></tr>');
                    },
                    success: function(response) {
                        console.log(response);
                        let rows = '';
                        response.forEach((surat, index) => {
                            rows += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>    
                                        <strong>Nomor</strong> : ${surat.nomor_surat}<br>
                                        <strong>Tanggal</strong> : ${new Date(surat.tanggal_surat).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}<br>
                                        <strong>Perihal</strong> : ${surat.perihal}<br>
                                        <strong>Kategori</strong> : ${surat.kategori_surat ? surat.kategori_surat.nama : '-'}
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-secondary btn-sm view-isi-surat" 
                                            data-toggle="modal" 
                                            data-target="#isiSuratModal" 
                                            data-content="${encodeURIComponent(surat.isi_surat)}">
                                            <i class="fa fa-book"></i> Lihat
                                        </button>
                                    </td>
                                    <td>${surat.signer ? surat.signer.name : '-'}</td>
                                    <td>
                                        ${surat.lampiran ? 
                                            `<a href="{{ Storage::url('surat/lampiran/') }}/${surat.lampiran}" class="btn btn-primary btn-sm" download>
                                                Lampiran <i class="fa fa-file"></i>
                                            </a>` : '<span class="badge bg-secondary text-white">Tidak ada lampiran</span>'}
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column" style="gap: 0.5rem;">
                                        <span class="badge ${surat.status_surat ? surat.status_surat.description : 'bg-secondary'}">
                                            ${surat.status_surat ? surat.status_surat.value : '-'}
                                        </span>

                                        ${surat.status == '2' ? `<button class="btn btn-info btn-sm register_surat-sign" data-id="${surat.id}">Tanda Tangani <i class="fa fa-barcode"></i></button>` : ''}
                                        ${surat.status == '1' ? `<button class="btn btn-secondary btn-sm register_surat-approve" data-id="${surat.id}">Setujui <i class="fa fa-check"></i></button>` : ''}
                                        </div>  
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column" style="gap: 0.5rem;">  
                                            @if (can('surat keluar', 'can_update'))  
                                                ${surat.status != '3' ? `
                                                    <a href="{{ route('adm.register_surat.edit', '') }}/${surat.id}" class="btn btn-warning btn-sm">Edit <i class="fa fa-edit"></i></a>
                                                ` : ''}
                                                ${surat.status == '3' ? `
                                                    <button class="btn btn-danger btn-sm register_surat-revisi" data-id="${surat.id}">Revisi <i class="fa fa-undo"></i></button>
                                                ` : ''}
                                            @endif
                                            @if (can('surat keluar', 'can_delete'))
                                                ${surat.status != '3' ? `
                                                    <button class="btn btn-danger btn-sm delete-register_surat" data-id="${surat.id}">Hapus <i class="fa fa-trash"></i></button>
                                                ` : ''}
                                            @endif
                                            @if (can('surat keluar', 'can_print'))
                                                ${surat.status == '3' ? `
                                                    <a href="{{ route('adm.register_surat.print', '') }}/${surat.id}" class="btn btn-info btn-sm" target="_blank">Print <i class="fa fa-print"></i></a>
                                                ` : ''}
                                            @endif
                                        </div>
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

        loadRegisterSurat();


        // Fungsi untuk memuat daftar register surat
        function loadRegisterSurat() {
            $.ajax({
                url: "{{ route('adm.register_surat.index') }}",
                method: "GET",
                beforeSend: function() {
                    $('#data-table-body').html('<tr><td colspan="7" class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></td></tr>');
                },
                success: function(response) {
                    console.log(response);
                    let rows = '';
                    response.forEach((surat, index) => {
                        rows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>    
                                    <strong>Nomor</strong> : ${surat.nomor_surat}<br>
                                    <strong>Tanggal</strong> : ${new Date(surat.tanggal_surat).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}<br>
                                    <strong>Perihal</strong> : ${surat.perihal}<br>
                                    <strong>Kategori</strong> : ${surat.kategori_surat ? surat.kategori_surat.nama : '-'}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm view-isi-surat" 
                                        data-toggle="modal" 
                                        data-target="#isiSuratModal" 
                                        data-content="${encodeURIComponent(surat.isi_surat)}">
                                        <i class="fa fa-book"></i> Lihat
                                    </button>
                                </td>
                                <td>${surat.signer ? surat.signer.name : '-'}</td>
                                <td>
                                    ${surat.lampiran ? 
                                        `<a href="{{ Storage::url('surat/lampiran/') }}/${surat.lampiran}" class="btn btn-primary btn-sm" download>
                                            Lampiran <i class="fa fa-file"></i>
                                        </a>` : '<span class="badge bg-secondary text-white">Tidak ada lampiran</span>'}
                                </td>
                                <td>
                                    <div class="d-flex flex-column" style="gap: 0.5rem;">
                                    <span class="badge ${surat.status_surat ? surat.status_surat.description : 'bg-secondary'}">
                                        ${surat.status_surat ? surat.status_surat.value : '-'}
                                    </span>

                                    ${surat.status == '2' ? `<button class="btn btn-info btn-sm register_surat-sign" data-id="${surat.id}">Tanda Tangani <i class="fa fa-barcode"></i></button>` : ''}
                                    ${surat.status == '1' ? `<button class="btn btn-secondary btn-sm register_surat-approve" data-id="${surat.id}">Setujui <i class="fa fa-check"></i></button>` : ''}
                                    </div>  
                                </td>
                                <td>
                                    <div class="d-flex flex-column" style="gap: 0.5rem;">  
                                        @if (can('surat keluar', 'can_update'))  
                                            ${surat.status != '3' ? `
                                                <a href="{{ route('adm.register_surat.edit', '') }}/${surat.id}" class="btn btn-warning btn-sm">Edit <i class="fa fa-edit"></i></a>
                                            ` : ''}
                                            ${surat.status == '3' ? `
                                                <button class="btn btn-danger btn-sm register_surat-revisi" data-id="${surat.id}">Revisi <i class="fa fa-undo"></i></button>
                                            ` : ''}
                                        @endif
                                        @if (can('surat keluar', 'can_delete'))
                                            ${surat.status != '3' ? `
                                                <button class="btn btn-danger btn-sm delete-register_surat" data-id="${surat.id}">Hapus <i class="fa fa-trash"></i></button>
                                            ` : ''}
                                        @endif
                                        @if (can('surat keluar', 'can_print'))
                                            ${surat.status == '3' ? `
                                                <a href="{{ route('adm.register_surat.print', '') }}/${surat.id}" class="btn btn-info btn-sm" target="_blank">Print <i class="fa fa-print"></i></a>
                                            ` : ''}
                                        @endif
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
            loadRegisterSurat();
        });

    

        $('#data-table-body').on('click', '.register_surat-edit', function() {
            var registerId = $(this).data('id');
            var url = "{{ route('adm.register_surat.edit', ['surat' => ':registerId']) }}";
            url = url.replace(':registerId', registerId);
            window.location.href = url;
        });

        $('#data-table-body').on('click', '.delete-register_surat', function() {
            var registerId = $(this).data('id');
            swal({
			  title: "Apakah Anda yakin?",
			  text: "Sekali dihapus, Anda tidak akan dapat mengembalikan ini!",
			  icon: "warning",
			  buttons: true,
			  dangerMode: true,
			})
			.then((willDelete) => {
			  if (willDelete) {
                deleteRegisterSurat(registerId);
			  }
			});
            
        });

        $('#data-table-body').on('click', '.register_surat-sign', function() {
            var registerId = $(this).data('id');
            swal({
			  title: "Yakin Untuk Tanda Tangani Surat?",
			//   text: "Sekali ditandatangani, Anda tidak akan dapat mengembalikan ini!",
			  icon: "warning",
			  buttons: true,
			  dangerMode: true,
			})
			.then((willDelete) => {
			  if (willDelete) {
                signRegisterSurat(registerId);
			  }
			});
            
        });

        $('#data-table-body').on('click', '.register_surat-approve', function() {
            var registerId = $(this).data('id');
            swal({
			  title: "Yakin Untuk Setujui Surat?",
			//   text: "Sekali ditandatangani, Anda tidak akan dapat mengembalikan ini!",
			  icon: "warning",
			  buttons: true,
			  dangerMode: true,
			})
			.then((willDelete) => {
			  if (willDelete) {
                approveRegisterSurat(registerId);
			  }
			});
            
        });

        $('#data-table-body').on('click', '.register_surat-revisi', function() {
            var registerId = $(this).data('id');
            swal({
			  title: "Yakin Untuk Revisi Surat?",
			//   text: "Sekali ditandatangani, Anda tidak akan dapat mengembalikan ini!",
			  content: {
			      element: "input",
			      attributes: {
			          placeholder: "Masukkan keterangan",
			          type: "text",
                      name: "description_add",
                      id: "description_add",
			      },
			  },
			  icon: "warning",
			  buttons: true,
			  dangerMode: true,
			})
			.then((willDelete) => {
			  if (willDelete) {
                // alert($('#description_add').val());
                revisiRegisterSurat(registerId, $('#description_add').val());
			  }
			});
            
        });
        

        function signRegisterSurat(registerId) {
            var url = "{{ route('adm.register_surat.sign', ['surat' => ':suratId']) }}";
            url = url.replace(':suratId', registerId);
            $.ajax({
                url: url,
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

                        setTimeout(function() {
                            $('.alert').alert('close');
                        }, 5000);
                    }
                    loadRegisterSurat();
                },
                error: function(xhr) {
                    alert('Gagal menandatangani surat');
                }
            });
        }

        function approveRegisterSurat(registerId) {
            var url = "{{ route('adm.register_surat.approve', ['surat' => ':suratId']) }}";
            url = url.replace(':suratId', registerId);
            $.ajax({
                url: url,
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

                        setTimeout(function() {
                            $('.alert').alert('close');
                        }, 5000);
                    }
                    loadRegisterSurat();
                },
                error: function(xhr) {
                    alert('Gagal menyetujui surat');
                }
            });
        }

        function revisiRegisterSurat(registerId, description) {
            // alert(description);
            var url = "{{ route('adm.register_surat.revisi', ['surat' => ':suratId', 'description' => ':description']) }}";
            url = url.replace(':suratId', registerId);
            url = url.replace(':description', description);
            $.ajax({
                url: url,
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

                        setTimeout(function() {
                            $('.alert').alert('close');
                        }, 5000);
                    }
                    loadRegisterSurat();
                },
                error: function(xhr) {
                    alert('Gagal mengembalikan surat');
                }
            });
        }

        function deleteRegisterSurat(registerId) {
            var url = "{{ route('adm.register_surat.destroy', ['surat' => ':suratId']) }}";
            url = url.replace(':suratId', registerId);
            $.ajax({
                url: url,
                method: "DELETE",
                success: function(response) {
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
                    loadRegisterSurat();
                },
                error: function(xhr) {
                    alert('Gagal menghapus register surat');
                }
            });
        }
            
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
        
        $('#data-table-body').on('click', '.view-isi-surat', function() {
            var content = decodeURIComponent($(this).data('content'));
            // Replace newlines with <br> tags
            content = content.replace(/\n/g, '<br>');
            $('#isiSuratContent').html(content);
        });

        // Optional: Clear modal content when modal is closed
        $('#isiSuratModal').on('hidden.bs.modal', function () {
            $('#isiSuratContent').html('');
        });
    });
</script>

<!-- Modal View Isi Surat -->
<div class="modal fade" id="isiSuratModal" tabindex="-1" role="dialog" aria-labelledby="isiSuratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="isiSuratModalLabel">Isi Surat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="isiSuratContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

