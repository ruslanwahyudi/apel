@extends('layouts.app')

@section('title', 'Detail Surat')

@section('content')
<div class="container-fluid">
    <!-- Page header -->
    <div class="card bg-light-primary shadow-none position-relative overflow-hidden mb-4">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Detail Surat</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a class="text-muted" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a class="text-muted" href="{{ route('adm.register-surat.index') }}">Register Surat</a></li>
                            <li class="breadcrumb-item" aria-current="page">Detail</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-3">
                    <div class="text-center mb-n5">
                        <img src="{{ asset('assets/images/breadcrumb/mail.png') }}" alt="" class="img-fluid mb-n4">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Informasi Surat</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Nomor Surat</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $surat->nomor_surat }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Jenis Surat</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $surat->jenis_surat }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Perihal</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $surat->perihal }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Isi Ringkas</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $surat->isi_ringkas }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tujuan</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $surat->tujuan }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Pengirim</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $surat->pengirim }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tanggal Surat</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $surat->tanggal_surat->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tanggal Diterima</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $surat->tanggal_diterima->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Status</strong>
                        </div>
                        <div class="col-md-8">
                            {!! $surat->status_badge !!}
                        </div>
                    </div>

                    @if($surat->keterangan)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Keterangan</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $surat->keterangan }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">File Surat</h5>
                </div>
                <div class="card-body">
                    @if($surat->file_surat)
                    <div class="text-center">
                        <div class="mb-3">
                            <i class="ti ti-file-text text-primary" style="font-size: 48px;"></i>
                        </div>
                        <h6>{{ $surat->file_surat }}</h6>
                        <div class="mt-3">
                            <a href="{{ $surat->file_url }}" class="btn btn-primary btn-sm me-2" target="_blank">
                                <i class="ti ti-eye"></i> Lihat
                            </a>
                            <a href="{{ route('adm.register_surat.download', $surat) }}" class="btn btn-success btn-sm">
                                <i class="ti ti-download"></i> Download
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="text-center text-muted">
                        <i class="ti ti-file-off" style="font-size: 48px;"></i>
                        <p class="mt-2">Tidak ada file surat</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('adm.register_surat.print', $surat) }}" class="btn btn-primary" target="_blank">
                            <i class="ti ti-printer"></i> Print Surat
                        </a>
                        <a href="{{ route('adm.register_surat.edit', $surat) }}" class="btn btn-warning">
                            <i class="ti ti-edit"></i> Edit Surat
                        </a>
                        <form action="{{ route('adm.register_surat.destroy', $surat) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="ti ti-trash"></i> Hapus Surat
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-title {
        margin-bottom: 0;
    }
    
    .row strong {
        color: #566a7f;
    }
    
    .btn-group-vertical .btn {
        text-align: left;
    }
    
    .badge {
        padding: 0.5em 0.75em;
    }
</style>
@endpush 