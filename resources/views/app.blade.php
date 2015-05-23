<!DOCTYPE html>
<html lang="en" ng-app="app">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{env('APP_NAME')}}</title>
	<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	<link href="{{ asset('/css/app.css') }}" rel="stylesheet">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="{{ asset('/js/html5shiv-3.7.2.js') }}"></script>
	<script src="{{ asset('/js/respond-1.4.2.js') }}"></script>
	<![endif]-->
</head>
<body>
@include('nav.global.top')

<div class="container-fluid">
	@yield('header')
</div>
<div class="container-fluid">
	@yield('notifications')
</div>
<div class="container-fluid">
	@yield('content')
</div>
<div class="container-fluid">
	@yield('footer')
</div>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
@yield('js-includes')
<script src="/js/main.js"></script>
@yield('beforebodyend')
</body>
</html>
