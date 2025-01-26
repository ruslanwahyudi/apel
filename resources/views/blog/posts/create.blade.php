@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/plugins/trumbowyg/ui/trumbowyg.min.css') }}">
<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid">


	<div class="row">
		<div class="col-xl-12">
			<div class="breadcrumb-holder">
				<h1 class="main-title float-left">Post</h1>
				<ol class="breadcrumb float-right">
					<li class="breadcrumb-item">Home</li>
					<li class="breadcrumb-item active">Post</li>
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
					<h3><i class="fa fa-check-square-o"></i> Tambah Post</h3>
				</div>

				<div class="card-body">


					<form enctype="multipart/form-data" action="{{ route('blog.posts.store') }}" method="POST" id="postForm">
						@csrf
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="title">Judul</label>
									<input type="text" class="form-control" id="title" name="title" aria-describedby="emailHelp" placeholder="Masukkan judul">
								</div>

								<div class="form-group">
									<label for="label_id">Label</label>
									<select class="form-control select2" id="label_id" name="label_id[]" multiple="multiple" style="height: 300px;">
										<option value="">Pilih Label</option>
										@foreach ($label as $item)
										<option value="{{ $item->id }}">{{ $item->nama }}</option>
										@endforeach
									</select>
								</div>

								<div class="form-group">
									<label for="kategori_id">Kategori</label>
									<select class="form-control select2" id="kategori_id" name="kategori_id">
										<option value="">Pilih Kategori</option>
										@foreach ($kategori as $item)
										<option value="{{ $item->id }}">{{ $item->nama }}</option>
										@endforeach
									</select>
								</div>

							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label for="image">Gambar</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">Upload</span>
										</div>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="image" name="image[]" multiple aria-describedby="emailHelp" placeholder="Masukkan gambar">
											<label class="custom-file-label" for="image">Choose files</label>
										</div>
									</div>
									<div id="imagePreview" class="mt-3"></div>
								</div>

							</div>

						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="content">Konten</label>
									<textarea rows="3" class="form-control editor" name="content"></textarea>
								</div>
							</div>
						</div>

						<button type="submit" class="btn btn-primary">Simpan <i class="fa fa-save"></i></button>
						<a href="{{ route('blog.posts') }}" class="btn btn-secondary">Kembali <i class="fa fa-arrow-left"></i></a>
					</form>


				</div>
			</div><!-- end card-->
		</div>
	</div>

	@endsection

	@section('js')
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script src="{{ asset('assets/plugins/trumbowyg/trumbowyg.min.js') }}"></script>
	<script>
		$(document).ready(function() {
			'use strict';
			$('.editor').trumbowyg();

			// handle image preview
			let selectedFiles = [];
			$('#image').on('change', function() {
				const files = Array.from(this.files);
				selectedFiles = selectedFiles.concat(files);

				// Clear the file input to allow re-selection of the same file
				this.value = '';

				$('#imagePreview').html(''); // Clear previous previews
				selectedFiles.forEach((file, index) => {
					const reader = new FileReader();
					reader.onload = function(e) {
						const imgContainer = $('<div>').css('display', 'inline-block').css('position', 'relative').css('margin', '5px');
						const img = $('<img>').attr('src', e.target.result).css('max-width', '100px');
						const removeBtn = $('<button>').text('x').css({
							position: 'absolute',
							top: '0',
							right: '0',
							background: 'red',
							color: 'white',
							border: 'none',
							borderRadius: '50%',
							cursor: 'pointer',
							width: '20px',
							height: '20px',
							lineHeight: '15px',
							textAlign: 'center',
							padding: '0'
						}).on('click', function() {
							selectedFiles.splice(index, 1);
							imgContainer.remove();
						});
						imgContainer.append(img).append(removeBtn);
						$('#imagePreview').append(imgContainer);
					}
					reader.readAsDataURL(file);
					// set new multiple image to image[]
					
				});
			});

			$('#postForm').on('submit', function(e) {
				const dataTransfer = new DataTransfer();
				selectedFiles.forEach(file => dataTransfer.items.add(file));
				$('#image')[0].files = dataTransfer.files;

				e.preventDefault();
				let formData = new FormData(this);

				$.ajax({
					type: 'POST',
					url: $(this).attr('action'),
					data: formData,
					contentType: false,
					processData: false,
					success: function(response) {
						swal({
							title: response.status,
							text: response.message,
							icon: "success",
							buttons: true,
							dangerMode: true,
						})
						.then((result) => {
							if (result) {
								window.location.href = "{{ route('blog.posts') }}";
							}
						});
					},
					error: function(xhr) {
						swal({
							title: 'Gagal Simpan!',
							text: xhr.responseJSON.message,
							icon: 'error',
							buttons: true,
							dangerMode: true,
						}).then((result) => {
							if (result) {
							// Check for empty fields and highlight them
							let errorMessage = '';
							if (!$('#title').val()) {
								errorMessage += 'Title is required.\n';
								$('#title').addClass('is-invalid');
							} else {
								$('#title').removeClass('is-invalid');
							}
							if (!$('#content').val()) {
								errorMessage += 'Content is required.\n';
								$('#content').addClass('is-invalid');
							} else {
								$('#content').removeClass('is-invalid');
							}
							if (!$('#kategori_id').val()) {
								errorMessage += 'Category is required.\n';
								$('#kategori_id').addClass('is-invalid');
							} else {
								$('#kategori_id').removeClass('is-invalid');
							}
							if (!$('#label_id').val()) {
								errorMessage += 'Label is required.\n';
								$('#label_id').addClass('is-invalid');
							} else {
								$('#label_id').removeClass('is-invalid');
							}
							}
						});
					}
				});
			});

			// handle form submit
		});
	</script>
	<script>
		$(document).ready(function() {
			
		});
	</script>

	<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
	<script>
		$(document).ready(function() {
			$('.select2').select2();
		});
	</script>

	@endsection