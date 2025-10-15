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
                                            <th>Data Layanan</th>
                                            <th>Penandatangan</th>
                                            <th>Lampiran</th>
                                            <th>Status</th>
                                            <!-- <th>Nama</th> -->
                                            <th>Action</th>
										</tr>
									</thead>									
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
		$(document).ready(function() {
			console.log('Document ready');

			// Set CSRF token for all AJAX requests
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				}
			});

			// Permission variables
			var canUpdate = true;
			var canDelete = true;
			var canPrint = true;

			console.log('Permissions:', {canUpdate, canDelete, canPrint});

			// Initialize DataTable with server-side processing
			var table = $('#example4').DataTable({
				processing: true,
				serverSide: true,
				ajax: {
					url: "{{ route('adm.register_surat.index') }}",
					type: 'GET'
				},
				columns: [
					{ data: 0, name: 'id', orderable: true },
					{ data: 1, name: 'nomor_surat', orderable: true },
					{ data: 2, name: 'isi_surat', orderable: false },
					{ data: 3, name: 'layanan_data', orderable: false },
					{ data: 4, name: 'signer_id', orderable: false },
					{ data: 5, name: 'lampiran', orderable: false },
					{ data: 6, name: 'status', orderable: true },
					{ data: 7, name: 'action', orderable: false, searchable: false }
				],
				order: [[0, 'desc']],
				pageLength: 10,
				lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
				language: {
					processing: "Memproses...",
					lengthMenu: "Tampilkan _MENU_ data per halaman",
					zeroRecords: "Data tidak ditemukan",
					info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
					infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
					infoFiltered: "(disaring dari _MAX_ total data)",
					search: "Cari:",
					paginate: {
						first: "Pertama",
						last: "Terakhir",
						next: "Selanjutnya",
						previous: "Sebelumnya"
					}
				},
				responsive: true,
				autoWidth: false
			});

			// Refresh button
			$('#refreshButton').click(function() {
				console.log('Refresh button clicked');
				table.ajax.reload();
			});

			// Event handlers for buttons
			$('#example4').on('click', '.register_surat-sign', function() {
				var registerId = $(this).data('id');
				swal({
					title: "Yakin Untuk Tanda Tangani Surat?",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				})
				.then((willSign) => {
					if (willSign) {
						signRegisterSurat(registerId);
					}
				});
			});

			$('#example4').on('click', '.register_surat-approve', function() {
				var registerId = $(this).data('id');
				swal({
					title: "Yakin Untuk Setujui Surat?",
					icon: "warning",
					buttons: true,
					dangerMode: true,
				})
				.then((willApprove) => {
					if (willApprove) {
						approveRegisterSurat(registerId);
					}
				});
			});

			$('#example4').on('click', '.register_surat-revisi', function() {
				var registerId = $(this).data('id');
				swal({
					title: "Yakin Untuk Revisi Surat?",
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
				.then((willRevisi) => {
					if (willRevisi) {
						revisiRegisterSurat(registerId, $('#description_add').val());
					}
				});
			});

			$('#example4').on('click', '.delete-register_surat', function() {
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

			// Functions for CRUD operations
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
						// Delay reload untuk memastikan server sudah update
						setTimeout(function() {
							table.ajax.reload();
						}, 500);
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
						// Delay reload untuk memastikan server sudah update
						setTimeout(function() {
							table.ajax.reload();
						}, 500);
					},
					error: function(xhr) {
						alert('Gagal menyetujui surat');
					}
				});
			}

			function revisiRegisterSurat(registerId, description) {
				console.log('=== REVISI SURAT ===');
				console.log('Register ID:', registerId);
				console.log('Description:', description);
				
				var url = "{{ route('adm.register_surat.revisi', ['surat' => ':suratId', 'description' => ':description']) }}";
				url = url.replace(':suratId', registerId);
				url = url.replace(':description', description);
				
				console.log('Revisi URL:', url);
				
				$.ajax({
					url: url,
					method: "GET",
					success: function(response) {
						console.log('Revisi response:', response);
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
						// Untuk revisi, gunakan reload page karena status berubah drastis
						console.log('Reloading page after revisi...');
						setTimeout(function() {
							window.location.reload();
						}, 1000);
					},
					error: function(xhr) {
						console.error('Error in revisi:', xhr);
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
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
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
						// Delay reload untuk memastikan server sudah update
						setTimeout(function() {
							table.ajax.reload();
						}, 500);
					},
					error: function(xhr) {
						alert('Gagal menghapus register surat');
					}
				});
			}

			$('#example4').on('click', '.view-isi-surat', function() {
				var content = decodeURIComponent($(this).data('content'));
				content = content.replace(/\n/g, '<br>');
				$('#isiSuratContent').html(content);
			});

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

