@extends('layouts.adm.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Kategori Surat (Blade Template)</h3>
                </div>
                
                <form action="{{ route('adm.kategori-surat.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="nama">Nama Kategori</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>

                        <input type="hidden" name="template_type" value="blade">

                        <div class="form-group">
                            <label for="blade_template_name">Nama File Template Blade</label>
                            <input type="text" class="form-control" id="blade_template_name" name="blade_template_name" 
                                   placeholder="contoh: surat-keterangan-domisili">
                            <small class="form-text text-muted">
                                File akan disimpan di: resources/views/templates/surat/[nama-file].blade.php
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Variabel Template</label>
                            <div id="template-variables">
                                <div class="variable-item border p-3 mb-2">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="blade_template_variables[0][name]" placeholder="nama_variabel">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="blade_template_variables[0][label]" placeholder="Label Variabel">
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control" name="blade_template_variables[0][type]">
                                                <option value="text">Text</option>
                                                <option value="textarea">Textarea</option>
                                                <option value="date">Date</option>
                                                <option value="number">Number</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control" name="blade_template_variables[0][default_value]" placeholder="Default Value">
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="blade_template_variables[0][required]" value="1">
                                                <label class="form-check-label">Required</label>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-sm remove-variable">×</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add-variable">+ Tambah Variabel</button>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('adm.kategori-surat') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let variableIndex = 1;

document.getElementById('add-variable').addEventListener('click', function() {
    const container = document.getElementById('template-variables');
    const newVariable = document.createElement('div');
    newVariable.className = 'variable-item border p-3 mb-2';
    newVariable.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <input type="text" class="form-control" name="blade_template_variables[${variableIndex}][name]" placeholder="nama_variabel">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="blade_template_variables[${variableIndex}][label]" placeholder="Label Variabel">
            </div>
            <div class="col-md-2">
                <select class="form-control" name="blade_template_variables[${variableIndex}][type]">
                    <option value="text">Text</option>
                    <option value="textarea">Textarea</option>
                    <option value="date">Date</option>
                    <option value="number">Number</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="blade_template_variables[${variableIndex}][default_value]" placeholder="Default Value">
            </div>
            <div class="col-md-1">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="blade_template_variables[${variableIndex}][required]" value="1">
                    <label class="form-check-label">Required</label>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm remove-variable">×</button>
            </div>
        </div>
    `;
    container.appendChild(newVariable);
    variableIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-variable')) {
        e.target.closest('.variable-item').remove();
    }
});
</script>
@endsection 