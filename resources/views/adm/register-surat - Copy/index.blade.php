@extends('layouts.admin')

@section('title', 'Register Surat')

@section('content')
<div class="container-fluid">
    <!-- Page header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0">Register Surat</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Register Surat</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <form action="{{ route('adm.register-surat.index') }}" method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control" placeholder="Cari surat..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </form>
                </div>
                <div class="col-md-8 text-end">
                    <a href="{{ route('adm.register_surat.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah Surat
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nomor Surat</th>
                            <th>Jenis</th>
                            <th>Perihal</th>
                            <th>Pengirim</th>
                            <th>Tujuan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($surat as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->nomor_surat }}</td>
                            <td>{{ $item->jenis_surat }}</td>
                            <td>{{ $item->perihal }}</td>
                            <td>{{ $item->pengirim }}</td>
                            <td>{{ $item->tujuan }}</td>
                            <td>{{ $item->tanggal_surat->format('d/m/Y') }}</td>
                            <td>{!! $item->status_badge !!}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#previewModal{{ $item->id }}" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="{{ route('adm.register_surat.edit', $item) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($item->lampiran)
                                    <a href="{{ $item->lampiran_url }}" class="btn btn-sm btn-secondary" target="_blank" title="Lihat Lampiran">
                                        <i class="bi bi-paperclip"></i>
                                    </a>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-danger" onclick="if(confirm('Apakah Anda yakin ingin menghapus surat ini?')) document.getElementById('delete-form-{{ $item->id }}').submit();" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $item->id }}" action="{{ route('adm.register_surat.destroy', $item) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                <!-- Preview Modal -->
                                <div class="modal fade" id="previewModal{{ $item->id }}" tabindex="-1" aria-labelledby="previewModalLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="previewModalLabel{{ $item->id }}">Detail Surat</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <dl class="row mb-0">
                                                            <dt class="col-sm-4">Nomor Surat</dt>
                                                            <dd class="col-sm-8">{{ $item->nomor_surat }}</dd>
                                                            
                                                            <dt class="col-sm-4">Jenis Surat</dt>
                                                            <dd class="col-sm-8">{{ $item->jenis_surat }}</dd>
                                                            
                                                            <dt class="col-sm-4">Perihal</dt>
                                                            <dd class="col-sm-8">{{ $item->perihal }}</dd>
                                                            
                                                            <dt class="col-sm-4">Pengirim</dt>
                                                            <dd class="col-sm-8">{{ $item->pengirim }}</dd>
                                                            
                                                            <dt class="col-sm-4">Tujuan</dt>
                                                            <dd class="col-sm-8">{{ $item->tujuan }}</dd>
                                                        </dl>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <dl class="row mb-0">
                                                            <dt class="col-sm-4">Tanggal Surat</dt>
                                                            <dd class="col-sm-8">{{ $item->tanggal_surat->format('d/m/Y') }}</dd>
                                                            
                                                            <dt class="col-sm-4">Tanggal Diterima</dt>
                                                            <dd class="col-sm-8">{{ $item->tanggal_diterima->format('d/m/Y') }}</dd>
                                                            
                                                            <dt class="col-sm-4">Status</dt>
                                                            <dd class="col-sm-8">{!! $item->status_badge !!}</dd>
                                                            
                                                        @if($item->lampiran)
                                                            <dt class="col-sm-4">Lampiran</dt>
                                                            <dd class="col-sm-8">
                                                                <a href="{{ $item->lampiran_url }}" target="_blank">Lihat Lampiran</a>
                                                            </dd>
                                                        @endif
                                                            
                                                        @if($item->keterangan)
                                                            <dt class="col-sm-4">Keterangan</dt>
                                                            <dd class="col-sm-8">{{ $item->keterangan }}</dd>
                                                        @endif
                                                        </dl>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <h6 class="fw-bold">Isi Ringkas:</h6>
                                                    <p class="mb-0">{{ $item->isi_ringkas }}</p>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold">Isi Surat:</h6>
                                                    <div class="border rounded p-3">
                                                        {!! $item->isi_surat !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                <a href="{{ route('adm.register_surat.print', $item) }}" class="btn btn-primary" target="_blank">
                                                    <i class="bi bi-printer"></i> Cetak
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $surat->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
<style>
    .btn-group {
        gap: 0.25rem;
    }
    
    .badge {
        font-size: 0.875em;
        padding: 0.25em 0.5em;
    }
    
    dt {
        font-weight: 600;
    }
    
    .table > :not(caption) > * > * {
        padding: 0.75rem;
    }
</style>
@endpush 