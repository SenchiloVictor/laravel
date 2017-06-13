<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link type="text/css" rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
		<link type="text/css" rel="stylesheet" href="{{ asset('css/bootstrap-theme.min.css') }}">
		<script type="text/javascript" src="{{ asset('js/functions.js') }}"></script>
		<title>Laravel</title>
	</head>
	<body>
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">LOGO</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li>
							<a href="{{url('gallery/list')}}">Список галерей</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="container theme-showcase">
			@yield('content')
		</div>
	</body>
</html>
