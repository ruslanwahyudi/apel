@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">Dokumen Tidak Ditemukan</div>

                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h4 class="text-center mb-4">Dokumen yang Anda cari tidak dapat ditemukan</h4>
                    
                    <p class="text-center">
                        Kode verifikasi tidak valid atau dokumen telah dihapus dari sistem.
                    </p>
                    
                    <div class="text-center mt-4">
                        <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 