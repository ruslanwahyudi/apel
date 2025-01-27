@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Tambah Persyaratan Layanan</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item">Layanan</li>
                    <li class="breadcrumb-item active">Tambah Persyaratan</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fa fa-plus"></i> Tambah Persyaratan Layanan</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('layanan.persyaratan.store') }}" method="POST" id="form-persyaratan">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_pelayanan_id">Jenis Layanan <span class="text-danger">*</span></label>
                                    <select class="form-control @error('jenis_pelayanan_id') is-invalid @enderror" name="jenis_pelayanan_id" id="jenis_pelayanan_id" required>
                                        <option value="">Pilih Jenis Layanan</option>
                                        @foreach($jenisLayanan as $jenis)
                                            <option value="{{ $jenis->id }}">{{ $jenis->nama_pelayanan }}</option>
                                        @endforeach
                                    </select>
                                    @error('jenis_pelayanan_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-persyaratan">
                                <thead>
                                    <tr>
                                        <th>Nama Dokumen <span class="text-danger">*</span></th>
                                        <th>Deskripsi</th>
                                        <th>Wajib?</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="persyaratan-row">
                                        <td>
                                            <input type="text" class="form-control @error('nama_dokumen.0') is-invalid @enderror" name="nama_dokumen[]" required>
                                            @error('nama_dokumen.0')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <textarea class="form-control @error('deskripsi.0') is-invalid @enderror" name="deskripsi[]" rows="3"></textarea>
                                            @error('deskripsi.0')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </td>
                                        <td>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="required_0" name="required[]" value="1" checked>
                                                <label class="custom-control-label" for="required_0">Ya</label>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm delete-row" disabled>
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <button type="button" class="btn btn-success btn-sm" id="add-row">
                                                <i class="fa fa-plus"></i> Tambah Baris
                                            </button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('layanan.persyaratan') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    .delete-row:disabled {
        cursor: not-allowed;
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Add new row
    $('#add-row').click(function() {
        var rowCount = $('.persyaratan-row').length;
        var newRow = $('.persyaratan-row').first().clone();
        
        // Clear values
        newRow.find('input[type="text"]').val('');
        newRow.find('textarea').val('');
        
        // Update names and IDs
        newRow.find('input[type="text"]').attr('name', 'nama_dokumen[]');
        newRow.find('textarea').attr('name', 'deskripsi[]');
        
        // Update required checkbox
        newRow.find('input[type="checkbox"]').attr({
            'name': 'required[]',
            'id': 'required_' + rowCount
        });
        newRow.find('label').attr('for', 'required_' + rowCount);
        
        // Enable delete button
        newRow.find('.delete-row').prop('disabled', false);
        
        // Append new row
        $('#table-persyaratan tbody').append(newRow);
        
        // Enable/disable delete buttons based on row count
        updateDeleteButtons();
    });

    // Delete row
    $(document).on('click', '.delete-row', function() {
        $(this).closest('tr').remove();
        updateDeleteButtons();
    });

    // Update delete buttons state
    function updateDeleteButtons() {
        var rowCount = $('.persyaratan-row').length;
        if (rowCount === 1) {
            $('.delete-row').prop('disabled', true);
        } else {
            $('.delete-row').prop('disabled', false);
        }
    }
});
</script>
@endsection 