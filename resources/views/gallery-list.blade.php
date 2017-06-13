@extends('layouts/main')

@section('content')
<div class="form-group row">
	<div class="col-sm-10">
		<input id="gallery" type="text" class="form-control" placeholder="Gallery name">
	</div>
	<div class="col-sm-2">
		<a href="javascript:void(0)" class="btn btn-success btn-block" id="createGallery">Добавить</a>
	</div>
</div>
<hr>
<h2>Gallery list</h2>
<div id="galleries-list"></div>
<script type="text/javascript" src="{{ asset('js/gallery-create.js') }}"></script>
@endsection