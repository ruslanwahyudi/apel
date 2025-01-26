@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fa fa-home"></i> Dashboard</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mt-3">
            <!-- Pengumuman Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Pengumuman</h6>
                                <h3 class="mb-0">{{ $stats['announcements'] }}</h3>
                            </div>
                            <div class="ms-3">
                                <i class="fa fa-bullhorn fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Artikel Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Artikel</h6>
                                <h3 class="mb-0">{{ $stats['articles'] }}</h3>
                            </div>
                            <div class="ms-3">
                                <i class="fa fa-newspaper fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Layanan Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Layanan</h6>
                                <h3 class="mb-0">{{ $stats['services'] }}</h3>
                            </div>
                            <div class="ms-3">
                                <i class="fa fa-cogs fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Aktif Card -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">User Aktif</h6>
                                <h3 class="mb-0">{{ $stats['active_users'] }}</h3>
                            </div>
                            <div class="ms-3">
                                <i class="fa fa-users fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">
            <!-- Recent Services -->
            <div class="col-xl-8 col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fa fa-list"></i> Layanan Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Pemohon</th>
                                        <th>Jenis Layanan</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentServices as $index => $service)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $service->user->name }}</td>
                                            <td>{{ $service->jenisPelayanan->nama }}</td>
                                            <td>
                                                <span class="badge bg-{{ $service->status == 'pending' ? 'warning' : ($service->status == 'approved' ? 'success' : 'danger') }}">
                                                    {{ ucfirst($service->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $service->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data layanan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="col-xl-4 col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fa fa-users"></i> User Login Aktif</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Login Terakhir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activeUsers as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <i class="fa fa-user-circle fa-2x text-secondary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $user->name }}</div>
                                                        <div class="small text-muted">{{ $user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ \Carbon\Carbon::createFromTimestamp($user->last_activity)->diffForHumans() }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center">Tidak ada user aktif</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 