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

			// Function untuk load data
			function loadData() {
				console.log('=== LOADING DATA ===');
				
				// Show loading
				$('#data-table-body').html('<tr><td colspan="8" class="text-center">Memuat data...</td></tr>');
				
				$.ajax({
					url: "{{ route('adm.register_surat.index') }}",
					method: "GET",
					dataType: 'json',
					cache: false, // Disable cache
					data: { _t: Date.now() }, // Cache busting parameter
					success: function(response) {
						console.log('=== AJAX SUCCESS ===');
						console.log('Response received:', response);
						console.log('Response type:', typeof response);
						console.log('Is array:', Array.isArray(response));
						console.log('Length:', response ? response.length : 'null');
						
						// Debug: Log all status values
						if (response && Array.isArray(response)) {
							console.log('Status values in response:', response.map(item => ({
								id: item.id,
								nomor_surat: item.nomor_surat,
								status: item.status
							})));
						}
						
						if (!response || !Array.isArray(response) || response.length === 0) {
							$('#data-table-body').html('<tr><td colspan="8" class="text-center">Tidak ada data</td></tr>');
							return;
						}
						
						let rows = '';
						
						response.forEach((surat, index) => {
							console.log(`Processing item ${index + 1}/${response.length}:`, surat.nomor_surat, 'Status:', surat.status);
							
							// Format data layanan dengan error handling
							let dataLayanan = '-';
							try {
								if (surat.layanan_data) {
									let layananInfo = [];
									if (surat.layanan_data.user && surat.layanan_data.user.name) {
										layananInfo.push(`<strong>User:</strong> ${surat.layanan_data.user.name}`);
									}
									if (surat.layanan_data.jenis_pelayanan && surat.layanan_data.jenis_pelayanan.nama_pelayanan) {
										layananInfo.push(`<strong>Jenis:</strong> ${surat.layanan_data.jenis_pelayanan.nama_pelayanan}`);
									}
									
									// Cari nama dan NIK dari data identitas
									if (surat.layanan_data.data_identitas && Array.isArray(surat.layanan_data.data_identitas)) {
										// Cari nama
										const namaPriorities = ['nama', 'nama_pemohon', 'nama_anak', 'nama_bayi', 'nama_suami', 'nama_istri', 'nama_ayah', 'nama_ibu', 'nama_ahliwaris'];
										let namaField = null;
										for (let priority of namaPriorities) {
											namaField = surat.layanan_data.data_identitas.find(item => 
												item.identitas_pemohon && item.identitas_pemohon.nama_field === priority
											);
											if (namaField) break;
										}
										if (namaField && namaField.nilai) {
											layananInfo.push(`<strong>Pemohon:</strong> ${namaField.nilai}`);
										}
										
										// Cari NIK
										const nikPriorities = ['nik', 'nik_pemohon', 'nik_anak', 'nik_bayi', 'nik_suami', 'nik_istri', 'nik_ayah', 'nik_ibu', 'nik_ahliwaris'];
										let nikField = null;
										for (let priority of nikPriorities) {
											nikField = surat.layanan_data.data_identitas.find(item => 
												item.identitas_pemohon && item.identitas_pemohon.nama_field === priority
											);
											if (nikField) break;
										}
										if (nikField && nikField.nilai) {
											layananInfo.push(`<strong>NIK:</strong> ${nikField.nilai}`);
										}
									}
									
									dataLayanan = layananInfo.length > 0 ? layananInfo.join('<br>') : '-';
								}
							} catch (e) {
								console.error('Error processing layanan data for item', index, ':', e);
								dataLayanan = 'Error processing data';
							}
							
							// Format tanggal dengan error handling
							let tanggalFormatted = '-';
							try {
								if (surat.tanggal_surat) {
									tanggalFormatted = new Date(surat.tanggal_surat).toLocaleDateString('id-ID', { 
										year: 'numeric', 
										month: 'long', 
										day: 'numeric' 
									});
								}
							} catch (e) {
								console.error('Error formatting date:', e);
								tanggalFormatted = surat.tanggal_surat || '-';
							}
							
							// Build row HTML
							rows += `
								<tr>
									<td>${index + 1}</td>
									<td>    
										<strong>Nomor:</strong> ${surat.nomor_surat || '-'}<br>
										<strong>Tanggal:</strong> ${tanggalFormatted}<br>
										<strong>Perihal:</strong> ${surat.perihal || '-'}<br>
										<strong>Kategori:</strong> ${surat.kategori_surat ? surat.kategori_surat.nama : '-'}
									</td>
									<td>
										<button type="button" class="btn btn-secondary btn-sm view-isi-surat" 
											data-toggle="modal" 
											data-target="#isiSuratModal" 
											data-content="${encodeURIComponent(surat.isi_surat || '')}">
											<i class="fa fa-book"></i> Lihat
										</button>
									</td>
									<td style="font-size: 12px;">${dataLayanan}</td>
									<td>${surat.signer ? surat.signer.name : '-'}</td>
									<td>
										${surat.lampiran ? 
											`<a href="/storage/surat/lampiran/${surat.lampiran}" class="btn btn-primary btn-sm" download>
												Lampiran <i class="fa fa-file"></i>
											</a>` : '<span class="badge bg-secondary text-white">Tidak ada lampiran</span>'}
									</td>
									<td>
										<div class="d-flex flex-column" style="gap: 0.5rem;">
											<span class="badge ${surat.status_surat ? surat.status_surat.description : 'bg-secondary'}">
												${surat.status_surat ? surat.status_surat.value : '-'}
											</span> Status : ${surat.status}
											${surat.status == '2' ? `<button class="btn btn-info btn-sm register_surat-sign" data-id="${surat.id}">Tanda Tangani <i class="fa fa-barcode"></i></button>` : ''}
											${surat.status == '1' ? `<button class="btn btn-secondary btn-sm register_surat-approve" data-id="${surat.id}">Setujui <i class="fa fa-check"></i></button>` : ''}
										</div>  
									</td>
									<td>
										<div class="d-flex flex-column" style="gap: 0.5rem;">`;
									
									// Build action buttons
									if (canUpdate && surat.status != '3') {
										rows += `<a href="/adm/register_surat/${surat.id}/edit" class="btn btn-warning btn-sm">Edit <i class="fa fa-edit"></i></a>`;
									}
									if (canUpdate && surat.status == '3') {
										rows += `<button class="btn btn-danger btn-sm register_surat-revisi" data-id="${surat.id}">Revisi <i class="fa fa-undo"></i></button>`;
									}
									if (canDelete && surat.status != '3') {
										rows += `<button class="btn btn-danger btn-sm delete-register_surat" data-id="${surat.id}">Hapus <i class="fa fa-trash"></i></button>`;
									}
									if (canPrint && surat.status == '3') {
										rows += `<a href="/adm/register-surat/print/${surat.id}" class="btn btn-info btn-sm" target="_blank">Print <i class="fa fa-print"></i></a>`;
									}
									
									rows += `                </div>
									</td>
								</tr>
							`;
						});
						
						console.log('Generated rows HTML length:', rows.length);
						console.log('Setting HTML to tbody...');
						
						// Force clear tbody first
						$('#data-table-body').empty();
						
						// Set HTML to tbody
						$('#data-table-body').html(rows);
						
						console.log('HTML set successfully');
						console.log('Tbody children count:', $('#data-table-body').children().length);
						
						
						console.log('=== DATA LOADED SUCCESSFULLY ===');
					},
					error: function(xhr, status, error) {
						console.error('=== AJAX ERROR ===');
						console.error('Status:', status);
						console.error('Error:', error);
						console.error('Response Text:', xhr.responseText);
						console.error('Status Code:', xhr.status);
						
						$('#data-table-body').html(`<tr><td colspan="8" class="text-center text-danger">Error memuat data: ${error}</td></tr>`);
					}
				});
			}

			// Load data on page load
			console.log('Loading initial data...');
			loadData();

			// Refresh button
			$('#refreshButton').click(function() {
				console.log('Refresh button clicked');
				loadData();
			});

			// Event handlers for buttons
			$('#data-table-body').on('click', '.register_surat-sign', function() {
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

			$('#data-table-body').on('click', '.register_surat-approve', function() {
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

			$('#data-table-body').on('click', '.register_surat-revisi', function() {
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
							loadData();
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
							loadData();
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
							loadData();
						}, 500);
					},
					error: function(xhr) {
						alert('Gagal menghapus register surat');
					}
				});
			}

			$('#data-table-body').on('click', '.view-isi-surat', function() {
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

