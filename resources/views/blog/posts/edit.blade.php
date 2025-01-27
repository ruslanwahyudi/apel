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
				<h1 class="main-title float-left">Edit Post</h1>
				<ol class="breadcrumb float-right">
					<li class="breadcrumb-item">Home</li>
					<li class="breadcrumb-item active">Edit Post</li>
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
					<h3><i class="fa fa-check-square-o"></i> Edit Post</h3>
				</div>

				<div class="card-body">

					<form id="editPostForm" enctype="multipart/form-data">
						@csrf
						@method('PUT')

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="title">Judul</label>
									<input type="text" class="form-control" value="{{ $post->title }}" id="title" name="title" aria-describedby="emailHelp" placeholder="Masukkan judul" required>
								</div>
								<div class="form-group">
									<label for="kategori_id">Kategori</label>
									<select class="form-control select2" id="kategori_id" name="kategori_id" aria-describedby="emailHelp" placeholder="Masukkan kategori" required>
										<option value="">Pilih Kategori</option>
										@foreach ($kategori as $item)
										<option value="{{ $item->id }}" {{ $post->kategori_id == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group">
									<label for="label_id">Label</label>
									<select class="form-control select2" id="label_id" name="label_id[]" multiple="multiple" style="height: 300px;">
										<option value="">Pilih Label</option>
										@foreach ($label as $item)
										<option value="{{ $item->id }}" {{ in_array($item->id, json_decode($post->label_id, true)) ? 'selected' : '' }}>{{ $item->nama }}</option>
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
									<div id="imagePreview" class="mt-3 row">

									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label for="content">Content</label>
									<textarea class="form-control editor" id="content" name="content" aria-describedby="emailHelp" placeholder="Masukkan content" required>{{ $post->content }}</textarea>
								</div>
							</div>
						</div>

						<button type="button" class="btn btn-primary" id="submitForm">Simpan <i class="fa fa-save"></i></button>
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



		});
	</script>

	<script>
		$(document).ready(function() {
			
		});
	</script>

	<script>
		$(document).ready(function() {
			let images = @json($image); // Assuming $image is an array of image URLs
			let selectedFiles = []; // Initialize selectedFiles array

			// Function to convert image URLs to Blobs and update selectedFiles
			async function updateSelectedFiles(images) {
				for (const imgSrc of images) {
					console.log(imgSrc);
					imgSrcTemp = imgSrc.image;
					if (typeof imgSrcTemp === 'string') {
						try {
							// ambil image dari storage
							console.log('imgSrcTemp', imgSrcTemp);
							const ini_file = "{{ asset('') }}/" + imgSrcTemp;
							console.log('ini_file', ini_file);
							const response = await fetch(ini_file);
							const blob = await response.blob();
							const file = new File([blob], ini_file.split('/').pop(), { type: blob.type });
							selectedFiles.push(file);
						} catch (error) {
							console.error('Error fetching image:', error);
						}
					} else {
						console.error('Invalid image source:', imgSrcTemp);
					}
				}
				previewImage(images);
			}

			updateSelectedFiles(images);

			// Handle file input change
			$('#image').on('change', function() {
				const files = Array.from(this.files);
				files.forEach(file => {
					selectedFiles.push(file); // Add new files to selectedFiles
					const reader = new FileReader();
					reader.onload = function(e) {
						const imgContainer = $('<div>').css('display', 'inline-block').css('position', 'relative').css('margin', '5px');
						const img = $('<img>').attr('src', e.target.result).css('max-width', '100px');
						const removeBtn = createRemoveButton(() => {
							selectedFiles = selectedFiles.filter(f => f !== file); // Remove file from selectedFiles
							imgContainer.remove();
						});
						imgContainer.append(img).append(removeBtn);
						$('#imagePreview').append(imgContainer);
					}
					reader.readAsDataURL(file);
				});
			});

			function previewImage(images) {
				const imagePreview = $('#imagePreview');
				imagePreview.html(''); // Clear previous previews

				images.forEach((imgSrc, index) => {
					console.log(imgSrc);
					imgSrcTemp = imgSrc.image;
					imgSrcTemp = "{{ asset('') }}" + imgSrcTemp;
					if (typeof imgSrcTemp === 'string') {

						const imgContainer = $('<div>').css('display', 'inline-block').css('position', 'relative').css('margin', '5px');
						const img = $('<img>').attr('src', imgSrcTemp).css('max-width', '100px');
						const removeBtn = createRemoveButton(() => {
							images.splice(index, 1); // Remove image from array
							selectedFiles.splice(index, 1); // Remove corresponding blob from selectedFiles
							imgContainer.remove();
						});
						imgContainer.append(img).append(removeBtn);
						imagePreview.append(imgContainer);
					} else {
						console.error('Invalid image source bro:', imgSrcTemp);
					}
				});
			}

			function createRemoveButton(onClick) {
				return $('<button>').text('x').css({
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
				}).on('click', onClick);
			}
			$('#submitForm').on('click', function() {
				const dataTransfer = new DataTransfer();
				console.log('selectedFiles', selectedFiles);
				selectedFiles.forEach(file => dataTransfer.items.add(file));
				$('#image')[0].files = dataTransfer.files;


				let formData = new FormData($('#editPostForm')[0]);
				console.log(formData);
				console.log('Number of images selected:', formData.getAll('image[]').length);
				$.ajax({
					url: "{{ route('blog.posts.update', $post->id) }}",
					type: 'POST',
					data: formData,
					contentType: false,
					processData: false,
					success: function(response) {
						console.log(response);
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
							title: 'Gagal!',
							text: 'An error occurred while updating the post.',
							icon: 'error',
							buttons: true,
							dangerMode: true,
						});
					}
				});
			});

		});
	</script>
	<script
		<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
	<script>
		$(document).ready(function() {
			$('.select2').select2();
		});
	</script>
	@endsection