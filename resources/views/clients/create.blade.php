@extends('main')
@section('content')
	<div class="row">
		<div class="panel panel-info col-md-8 col-centered">
			<div class="panel-heading">
				Please insert client information.
			</div>
			<div class="panel-body">
			    {!! Form::open(array('url'=>'clients')) !!}
			    	<div class="form-group">
			            {!! Form::label('Name', '', array('for'=>'fullName')) !!}
			            {!! Form::text('fullName', '', array('class'=>'form-control', 'placeholder'=>'Name', 'id'=>'fullName')) !!}
			        </div>
			        <div class="form-group">
			        	{!! Form::label('Gender', '', array('for'=>'inputGender')) !!}
			        	{!! Form::select('inputGender', array('male'=>'Male','female'=>'Female','other'=>'Other'),'male',array('class'=>'form-select form-control')) !!}
			        </div>
			        <div class="form-group">
			            {!! Form::label('Phone', '', array('for'=>'phone')) !!}
			            {!! Form::text('phone', '', array('class'=>'form-control', 'placeholder'=>'Phone', 'id'=>'phone')) !!}
			        </div>
			        <div class="form-group">
			            {!! Form::label('Email', '', array('for'=>'email')) !!}
			            {!! Form::email('email', '', array('class'=>'form-control', 'placeholder'=>'Email', 'id'=>'email')) !!}
			        </div>
			        <div class="form-group">
			            {!! Form::label('Address', '', array('for'=>'address')) !!}
			            {!! Form::text('address', '', array('class'=>'form-control', 'placeholder'=>'Address', 'id'=>'address')) !!}
			        </div>
			        <div class="form-group">
			            {!! Form::label('Nationality', '', array('for'=>'nationality')) !!}
			            {!! Form::text('nationality', '', array('class'=>'form-control', 'placeholder'=>'Nationality', 'id'=>'nationality')) !!}
			        </div>
			        <div class="form-group">
			            {!! Form::label('Date of birth', '', array('for'=>'dateOfBirth')) !!}
			            {!! Form::text('dateOfBirth', '', array('class'=>'form-control datepicker', 'placeholder'=>'Date of birth', 'id'=>'dateOfBirth')) !!}
			        </div>
			        <div class="form-group">
			            {!! Form::label('Education Background', '', array('for'=>'education')) !!}
			            {!! Form::text('education', '', array('class'=>'form-control', 'placeholder'=>'Education Background', 'id'=>'education')) !!}
			        </div>
			        <div class="form-group">
			        	{!! Form::label('Preferred mode of contact', '', array('for'=>'contactMode')) !!}
			        	{!! Form::select('contactMode', array('phone'=>'Phone','email'=>'Email','none'=>'None'),'phone',array('class'=>'form-select form-control')) !!}
			        </div>
			        {!! Form::button('Submit',array('type'=>'submit','class'=>'btn btn-primary')) !!}
			    {!! Form::close() !!}	
			</div>
		</div>
	</div>
@endsection

<script type="text/javascript">
	$(document).ready(function(
		$('.datepicker').datepicker();
	));
</script>