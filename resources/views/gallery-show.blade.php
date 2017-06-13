@extends('layouts/main')

@section('content')
<div class="form-group row">
	<div class="col-sm-10">
		<input id="image" type="file" class="form-control">
	</div>
	<div class="col-sm-2">
		<a href="javascript:void(0)" class="btn btn-success btn-block" id="addImage">Добавить</a>
	</div>
</div>
<div id="images-list"></div>
<script type="text/javascript" src="{{ asset('js/gallery-show.js') }}"></script>
@endsection