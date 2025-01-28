@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Tambah Dusun</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Tambah Dusun</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">						
            <div class="card mb-3">
                <div class="card-header">
                    <h3><i class="fa fa-check-square-o"></i> Tambah Dusun</h3>
                </div>
                    
                <div class="card-body">
                    <form action="{{ route('admin.dusun.store') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="nama_dusun">Nama Dusun</label>
                            <input type="text" class="form-control @error('nama_dusun') is-invalid @enderror" 
                                id="nama_dusun" name="nama_dusun" placeholder="Masukkan nama dusun" required>
                            @error('nama_dusun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="user_id">Kepala Dusun</label>
                            <select class="form-control @error('user_id') is-invalid @enderror" 
                                id="user_id" name="user_id" required>
                                <option value="">Pilih Kepala Dusun</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="jumlah_kk">Jumlah KK</label>
                            <input type="number" class="form-control @error('jumlah_kk') is-invalid @enderror" 
                                id="jumlah_kk" name="jumlah_kk" placeholder="Masukkan jumlah KK" required>
                            @error('jumlah_kk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="jumlah_pr">Jumlah Perempuan</label>
                            <input type="number" class="form-control @error('jumlah_pr') is-invalid @enderror" 
                                id="jumlah_pr" name="jumlah_pr" placeholder="Masukkan jumlah perempuan" required>
                            @error('jumlah_pr')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="jumlah_lk">Jumlah Laki-laki</label>
                            <input type="number" class="form-control @error('jumlah_lk') is-invalid @enderror" 
                                id="jumlah_lk" name="jumlah_lk" placeholder="Masukkan jumlah laki-laki" required>
                            @error('jumlah_lk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan <i class="fa fa-save"></i></button>
                        <a href="{{ route('admin.dusun') }}" class="btn btn-secondary">Kembali <i class="fa fa-arrow-left"></i></a>
                    </form>
                </div>														
            </div><!-- end card-->					
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    
</script>
@endsection

