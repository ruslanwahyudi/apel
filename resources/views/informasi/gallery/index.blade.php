@extends('layouts.admin')

@section('css')
<style>
    .gallery-item {
        position: relative;
        margin-bottom: 30px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .gallery-item:hover {
        transform: translateY(-5px);
    }

    .gallery-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .gallery-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        padding: 20px;
        color: white;
    }

    .gallery-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .gallery-description {
        font-size: 0.9rem;
        margin: 5px 0 0;
        opacity: 0.8;
    }

    .gallery-actions {
        position: absolute;
        top: 10px;
        right: 10px;
        display: none;
    }

    .gallery-item:hover .gallery-actions {
        display: block;
    }

    .btn-gallery {
        padding: 5px 10px;
        font-size: 0.8rem;
        margin-left: 5px;
        border-radius: 5px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Galeri</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Galeri</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3><i class="fas fa-images"></i> Galeri Foto</h3>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('info.gallery.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus"></i> Tambah Foto
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row">
                        @forelse($galleries as $gallery)
                            <div class="col-md-4 col-lg-3">
                                <div class="gallery-item">
                                    <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" class="gallery-img">
                                    <div class="gallery-overlay">
                                        <h5 class="gallery-title">{{ $gallery->title }}</h5>
                                        @if($gallery->description)
                                            <p class="gallery-description">{{ Str::limit($gallery->description, 50) }}</p>
                                        @endif
                                    </div>
                                    <div class="gallery-actions">
                                        <a href="{{ route('info.gallery.edit', $gallery) }}" class="btn btn-warning btn-gallery">
                                            <span class="fa fa-edit"></span>
                                        </a>
                                        <button class="btn btn-danger btn-gallery delete-gallery" data-id="{{ $gallery->id }}">
                                            <span class="fa fa-trash"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    Belum ada foto dalam galeri. Silakan tambahkan foto baru.
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            {{ $galleries->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
$(document).ready(function() {
    $('.delete-gallery').click(function() {
        var galleryId = $(this).data('id');
        
        swal({
            title: "Apakah Anda yakin?",
            text: "Sekali dihapus, Anda tidak akan dapat mengembalikan foto ini!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: `/gallery/${galleryId}`,
                    type: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        swal("Berhasil!", response.message, "success")
                        .then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        swal("Error!", "Terjadi kesalahan saat menghapus foto.", "error");
                    }
                });
            }
        });
    });
});
</script>
@endsection 