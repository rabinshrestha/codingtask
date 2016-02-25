@extends('main')
@section('pageCSS')
{!! HTML::style('public/css/dataTables.bootstrap.min.css') !!}
@endsection
@section('content')
<div class="panel-heading">
	<div>Client List</div>
	<a class="btn btn-primary" href="{{ URL::to('clients/create') }}">Add Client</a>
</div>
@if(Session::get('error')))
	<div class="alert alert-danger">
	  {{Session::get('error')}}
	</div>
@endif
@if(Session::get('msg'))
	<div class="alert alert-info">
	  {{Session::get('msg')}}
	</div>
@endif
<div class="panel-body">
	<table id="clientsTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
	    <thead>
	        <tr>
	            <th>Name</th>
	            <th>Gender</th>
	            <th>Phone</th>
	            <th>Email</th>
	            <th>Address</th>
	            <th>Nationality</th>
	            <th>Date of Birth</th>
	            <th>Education</th>
	            <th>Preferred Contact method</th>
	        </tr>
	    </thead>
	    <tfoot>
	        <tr>
		        <th>Name</th>
		        <th>Gender</th>
		        <th>Phone</th>
		        <th>Email</th>
		        <th>Address</th>
		        <th>Nationality</th>
		        <th>Date of Birth</th>
		        <th>Education</th>
		        <th>Preferred Contact method</th>
	        </tr>
	    </tfoot>
	    <tbody>
	    	@foreach ($clients as $client)
				<tr>
		            <td>{{ $client[0] }}</td>
		            <td>{{ $client[1] }}</td>
		            <td>{{ $client[2] }}</td>
		            <td>{{ $client[3] }}</td>
		            <td>{{ $client[4] }}</td>
		            <td>{{ $client[5] }}</td>
		            <td>{{ $client[6] }}</td>
		            <td>{{ $client[7] }}</td>
		            <td>{{ $client[8] }}</td>
	        	</tr>
			@endforeach
	    </tbody>
	</table>
</div>
@endsection
