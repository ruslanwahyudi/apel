@extends('layouts.admin')

@section('content')
<div class="container-fluid">


    <div class="row">
        <div class="col-xl-12">
            <div class="breadcrumb-holder">
                <h1 class="main-title float-left">Forms</h1>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">Home</li>
                    <li class="breadcrumb-item active">Forms</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- <div class="alert alert-success" role="alert">
					  <h4 class="alert-heading">Forms</h4>
					  <p>Bootstrap’s form controls expand on our Rebooted form styles with classes. Use these classes to opt into their customized displays for a more consistent rendering across browsers and devices. <a target="_blank" href="http://getbootstrap.com/docs/4.0/components/forms/">Bootstrap Forms Documentation</a></p>
			</div> -->

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3><i class="fa fa-check-square-o"></i> Setting Privileges</h3>
                </div>

                <div class="card-body">

                    <form id="rolePrivilegesForm" action="{{ route('admin.permissions.store') }}" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="role_id">Role</label>
                            <select class="form-control" id="role_id" name="role_id" required>
                                <option value="">Pilih Role</option>
                                @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Menampilkan Modul dan Menu -->
                        @foreach ($moduls as $modul)
                        <h3>{{ $modul->name }}</h3>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th width="200">Menu</th>
                                    <th width="100">Visible</th>
                                    <th width="100">Create</th>
                                    <th width="100">Read</th>
                                    <th width="100">Update</th>
                                    <th width="100">Delete</th>
                                    <th width="100">Print</th>
                                </tr>
                            </thead>
                            <tbody class="table-bordered">
                                @foreach ($modul->menus as $menu)
                                <tr>
                                    <td>{{ $menu->name }}</td>
                                    <td>
                                        <input type="checkbox" name="privileges[{{ $menu->id }}][is_visible]" value="1">
                                    </td>
                                    <td>
                                        <input type="checkbox" name="privileges[{{ $menu->id }}][can_create]" value="1">
                                    </td>
                                    <td>
                                        <input type="checkbox" name="privileges[{{ $menu->id }}][can_read]" value="1">
                                    </td>
                                    <td>
                                        <input type="checkbox" name="privileges[{{ $menu->id }}][can_update]" value="1">
                                    </td>
                                    <td>
                                        <input type="checkbox" name="privileges[{{ $menu->id }}][can_delete]" value="1">
                                    </td>
                                    <td>
                                        <input type="checkbox" name="privileges[{{ $menu->id }}][can_print]" value="1">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endforeach

                        @if (can('permissions', 'can_update'))
                            <button type="submit" class="btn btn-primary">Simpan <i class="fa fa-save"></i></button>
                        @endif
                        <a href="{{ route('admin.menus') }}" class="btn btn-secondary">Kembali <i class="fa fa-arrow-left"></i></a>

                    </form>

                </div>
            </div><!-- end card-->
        </div>
    </div>

    @endsection

    @section('js')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#rolePrivilegesForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        swal("Berhasil!", "Data berhasil disimpan!", "success");
                    },
                    error: function(xhr) {
                        swal("Error!", "An error occurred while saving the data.", "error");
                    }
                });
            });

            $('#role_id').change(function() {
                var roleId = $(this).val();
                if (roleId) {
                    $.ajax({
                        url: '{{ route("admin.permissions.getPrivileges") }}',
                        method: 'GET',
                        data: { role_id: roleId },
                        success: function(response) {
                            // Clear existing checkboxes
                            $('input[type="checkbox"]').prop('checked', false);

                            // Set checkboxes based on response
                            $.each(response, function(menuId, actions) {
                                if (actions.can_create) {
                                    $('input[name="privileges[' + menuId + '][can_create]"]').prop('checked', true);
                                }
                                if (actions.is_visible) {
                                    $('input[name="privileges[' + menuId + '][is_visible]"]').prop('checked', true);
                                }
                                if (actions.can_print) {
                                    $('input[name="privileges[' + menuId + '][can_print]"]').prop('checked', true);
                                }
                                if (actions.can_read) {
                                    $('input[name="privileges[' + menuId + '][can_read]"]').prop('checked', true);
                                }
                                if (actions.can_update) {
                                    $('input[name="privileges[' + menuId + '][can_update]"]').prop('checked', true);
                                }
                                if (actions.can_delete) {
                                    $('input[name="privileges[' + menuId + '][can_delete]"]').prop('checked', true);
                                }
                            });
                        },
                        error: function(xhr) {
                            alert('An error occurred while fetching privileges.');
                        }
                    });
                }
            });
        });

        
    </script>
    @endsection