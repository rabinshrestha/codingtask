@extends('app')
@section('content')
{{ Form::open(array('url'=>'clients/add')) }}
{{ Form::text() }}
{{ Form::close() }}
<div>
	Hello World !
</div>
@stop