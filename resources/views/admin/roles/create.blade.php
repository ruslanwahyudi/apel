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
								<h3><i class="fa fa-check-square-o"></i> Tambah Role</h3>
							</div>
								
							<div class="card-body">
								
								<form action="{{ route('admin.roles.store') }}" method="post">
                                    @csrf
								  <div class="form-group">
									<label for="name">Nama Role</label>
									<input type="text" class="form-control" id="name" name="name" aria-describedby="emailHelp" placeholder="Masukkan nama role" required>
									<!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
								  </div>
								  
								  
                                  <button type="submit" class="btn btn-primary">Simpan <i class="fa fa-save"></i></button>
                                  <a href="{{ route('admin.roles') }}" class="btn btn-secondary">Kembali <i class="fa fa-arrow-left"></i></a>

                                </form>
																
							</div>														
						</div><!-- end card-->					
                    </div>
            </div>

@endsection

@section('js')
<script>
    
</script>
@endsection

