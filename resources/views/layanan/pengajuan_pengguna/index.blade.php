@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Pengajuan Pengguna</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item">Layanan</li>
                    <li class="breadcrumb-item active">Pengajuan Pengguna</li>
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
                        <div class="col-md-6">
                            <h3><i class="fa fa-table"></i> Daftar Pengajuan Pengguna</h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <button id="refreshButton" class="btn btn-secondary btn-sm">Refresh <i class="fa fa-refresh"></i></button>
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
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{ session('error') }}
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
                        <table id="example4" class="table table-bordered table-hover display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>NIK</th>
                                    <th>Dusun</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="data-table-body">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>              
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Pengajuan -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Pengajuan User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Detail content will be loaded here -->
                <div class="text-center" id="loading-indicator">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div id="detail-content" style="display: none;">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <img id="user-photo" src="" alt="Foto Profil" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Nama</th>
                                    <td id="detail-nama"></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td id="detail-email"></td>
                                </tr>
                                <tr>
                                    <th>Telepon</th>
                                    <td id="detail-phone"></td>
                                </tr>
                                <tr>
                                    <th>NIK</th>
                                    <td id="detail-nik"></td>
                                </tr>
                                <tr>
                                    <th>Dusun</th>
                                    <td id="detail-dusun"></td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td id="detail-alamat"></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Lahir</th>
                                    <td id="detail-tanggal-lahir"></td>
                                </tr>
                                <tr>
                                    <th>Jenis Kelamin</th>
                                    <td id="detail-gender"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <form id="approveForm">
                    <input type="hidden" id="approve-user-id" name="user_id">
                    <button type="submit" class="btn btn-success">Approve</button>
                </form>
                <button type="button" class="btn btn-danger" id="rejectButton">Tolak</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Tolak Pengajuan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="rejectForm">
                    <input type="hidden" id="reject-user-id" name="user_id">
                    <div class="form-group">
                        <label for="reason">Alasan Penolakan</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="submitReject">Tolak Pengajuan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
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

    loadPengajuanPengguna();

    function loadPengajuanPengguna() {
        $.ajax({
            url: "{{ route('layanan.pengajuan_pengguna') }}",
            method: "GET",
            beforeSend: function() {
                $('#data-table-body').html('<tr><td colspan="7" class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></td></tr>');
            },
            success: function(response) {
                let rows = '';
                response.forEach((pengajuan, index) => {
                    rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${pengajuan.name}</td>
                            <td>${pengajuan.email || '-'}</td>
                            <td>${pengajuan.phone || '-'}</td>
                            <td>${pengajuan.profile ? (pengajuan.profile.nik || '-') : '-'}</td>
                            <td>${pengajuan.dusun ? pengajuan.dusun.nama_dusun : '-'}</td>
                            <td>
                                <button class="btn btn-info btn-sm detail-pengajuan" data-id="${pengajuan.id}">Detail <i class="fa fa-eye"></i></button>
                            </td>
                        </tr>
                    `;
                });
                
                if (response.length === 0) {
                    rows = '<tr><td colspan="7" class="text-center">Tidak ada data pengajuan</td></tr>';
                }
                
                $('#data-table-body').html(rows);
            },
            error: function(xhr) {
                $('#data-table-body').html('<tr><td colspan="7" class="text-center text-danger">Terjadi kesalahan saat memuat data</td></tr>');
                console.error(xhr.responseText);
            }
        });
    }

    // Refresh button handler
    $('#refreshButton').click(function() {
        loadPengajuanPengguna();
    });

    // Detail handler
    $('#data-table-body').on('click', '.detail-pengajuan', function() {
        var userId = $(this).data('id');
        
        // Show modal
        $('#detailModal').modal('show');
        
        // Show loading, hide content
        $('#loading-indicator').show();
        $('#detail-content').hide();
        
        // Load user details
        $.ajax({
            url: "{{ route('layanan.pengajuan_pengguna.show', ['id' => ':id']) }}".replace(':id', userId),
            method: "GET",
            success: function(response) {
                // Set user ID for approve/reject forms
                $('#approve-user-id').val(userId);
                $('#reject-user-id').val(userId);
                
                // Fill in user details
                $('#detail-nama').text(response.name);
                $('#detail-email').text(response.email || '-');
                $('#detail-phone').text(response.phone || '-');
                
                if (response.profile) {
                    $('#detail-nik').text(response.profile.nik || '-');
                    $('#detail-alamat').text(response.profile.alamat || '-');
                    $('#detail-tanggal-lahir').text(response.profile.tanggal_lahir || '-');
                    $('#detail-gender').text(response.profile.jenis_kelamin || '-');
                    
                    // Set profile photo if available
                    if (response.profile.foto_selfie) {
                        $('#user-photo').attr('src', '/storage/selfies/' + response.profile.foto_selfie);
                    } else {
                        $('#user-photo').attr('src', '/assets/images/avatars/default.png');
                    }
                } else {
                    $('#detail-nik, #detail-alamat, #detail-tanggal-lahir, #detail-gender').text('-');
                    $('#user-photo').attr('src', '/assets/images/avatars/default.png');
                }
                
                $('#detail-dusun').text(response.dusun ? response.dusun.name : '-');
                
                // Hide loading, show content
                $('#loading-indicator').hide();
                $('#detail-content').show();
            },
            error: function(xhr) {
                $('#loading-indicator').hide();
                $('#detail-content').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat data pengguna</div>').show();
                console.error(xhr.responseText);
            }
        });
    });

    // Approve handler
    $('#approveForm').submit(function(e) {
        e.preventDefault();
        
        const userId = $('#approve-user-id').val();
        
        swal({
            title: "Apakah Anda yakin?",
            text: "Pengajuan pengguna ini akan disetujui",
            icon: "warning",
            buttons: ["Batal", "Ya, Setuju"],
            dangerMode: false,
        })
        .then((willApprove) => {
            if (willApprove) {
                $.ajax({
                    url: "{{ route('layanan.pengajuan_pengguna.approve') }}",
                    method: "POST",
                    data: { user_id: userId },
                    beforeSend: function() {
                        swal({
                            title: "Memproses...",
                            text: "Mohon tunggu sebentar",
                            icon: "info",
                            buttons: false,
                            closeOnClickOutside: false,
                        });
                    },
                    success: function(response) {
                        swal({
                            title: "Berhasil!",
                            text: "Pengajuan pengguna telah disetujui",
                            icon: "success",
                        }).then(() => {
                            $('#detailModal').modal('hide');
                            loadPengajuanPengguna();
                        });
                    },
                    error: function(xhr) {
                        swal({
                            title: "Gagal!",
                            text: "Terjadi kesalahan saat menyetujui pengajuan",
                            icon: "error",
                        });
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });

    // Reject button handler
    $('#rejectButton').click(function() {
        $('#detailModal').modal('hide');
        $('#rejectModal').modal('show');
    });

    // Submit reject form
    $('#submitReject').click(function() {
        const userId = $('#reject-user-id').val();
        const reason = $('#reason').val();
        
        if (!reason) {
            alert('Alasan penolakan wajib diisi');
            return;
        }
        
        swal({
            title: "Apakah Anda yakin?",
            text: "Pengajuan pengguna ini akan ditolak",
            icon: "warning",
            buttons: ["Batal", "Ya, Tolak"],
            dangerMode: true,
        })
        .then((willReject) => {
            if (willReject) {
                $.ajax({
                    url: "{{ route('layanan.pengajuan_pengguna.reject') }}",
                    method: "POST",
                    data: { 
                        user_id: userId,
                        reason: reason
                    },
                    beforeSend: function() {
                        swal({
                            title: "Memproses...",
                            text: "Mohon tunggu sebentar",
                            icon: "info",
                            buttons: false,
                            closeOnClickOutside: false,
                        });
                    },
                    success: function(response) {
                        swal({
                            title: "Berhasil!",
                            text: "Pengajuan pengguna telah ditolak",
                            icon: "success",
                        }).then(() => {
                            $('#rejectModal').modal('hide');
                            loadPengajuanPengguna();
                        });
                    },
                    error: function(xhr) {
                        swal({
                            title: "Gagal!",
                            text: "Terjadi kesalahan saat menolak pengajuan",
                            icon: "error",
                        });
                        console.error(xhr.responseText);
                    }
                });
            }
        });
    });

    // Search handler
    $('#example4_filter input').on('keyup', function() {
        table.search(this.value).draw();
        var search = this.value;
        
        if (search.length > 2) {
            $.ajax({
                url: "{{ route('layanan.pengajuan_pengguna.search', ['search' => ':search']) }}".replace(':search', search),
                method: "GET",
                success: function(response) {
                    let rows = '';
                    response.forEach((pengajuan, index) => {
                        rows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${pengajuan.name}</td>
                                <td>${pengajuan.email || '-'}</td>
                                <td>${pengajuan.phone || '-'}</td>
                                <td>${pengajuan.profile ? (pengajuan.profile.nik || '-') : '-'}</td>
                                <td>${pengajuan.dusun ? pengajuan.dusun.name : '-'}</td>
                                <td>
                                    <button class="btn btn-info btn-sm detail-pengajuan" data-id="${pengajuan.id}">Detail <i class="fa fa-eye"></i></button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    if (response.length === 0) {
                        rows = '<tr><td colspan="7" class="text-center">Tidak ada data yang sesuai</td></tr>';
                    }
                    
                    $('#data-table-body').html(rows);
                }
            });
        } else if (search.length === 0) {
            loadPengajuanPengguna();
        }
    });
});
</script>
@endsection 