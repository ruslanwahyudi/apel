@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">Dokumen Terverifikasi</div>

                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h4 class="text-center mb-4">Dokumen yang Anda verifikasi adalah asli</h4>
                    
                    <p class="text-center">
                        Dokumen ini telah diverifikasi dan terdaftar di database kami.
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