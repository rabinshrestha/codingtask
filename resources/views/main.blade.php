<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Example of Bootstrap 3 Vertical Form Layout</title>
{!! HTML::style('public/css/bootstrap.min.css') !!}
{!! HTML::style('public/css/bootstrap-theme.min.css') !!}
{!! HTML::style('public/css/jquery-ui.css') !!}
{!! HTML::style('public/css/main.css') !!}
@yield('pageCSS')
</head>
<body>
<div class="row">
	<div class="panel panel-info">
		@yield('content')
	</div>
</div>
</body>
{!! HTML::script('public/js/jquery-1.12.1.min.js') !!}
{!! HTML::script('public/js/bootstrap.min.js') !!}
{!! HTML::script('public/js/jquery-ui.js') !!}
{!! HTML::script('public/js/jquery-validate/jquery.validate.min.js') !!}
{!! HTML::script('public/js/jquery.dataTables.min.js') !!}
{!! HTML::script('public/js/dataTables.bootstrap.min.js') !!}
{!! HTML::script('public/js/main.js') !!}
@yield('pageJs')
</html>                                		