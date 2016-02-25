@extends('main')
@section('content')
	<div class="row">
		<div class="panel panel-info col-md-8 col-centered">
			<div class="panel-heading">
				Please insert client information.
			</div>
			@if (count($errors) > 0)
			    <div class="alert alert-danger">
			        <ul>
			            @foreach ($errors->all() as $error)
			                <li>{{ $error }}</li>
			            @endforeach
			        </ul>
			    </div>
			@endif

			<div class="panel-body">
			    {!! Form::open(array('url'=>'clients','id'=>'create-client')) !!}
			    	<div class="form-group {{ $errors->has('fullName')?'has-error':'' }}">
			            {!! Form::label('Full Name *', '', array('for'=>'fullName')) !!}
			            {!! Form::text('fullName', Input::old('fullName'), array('class'=>'form-control', 'placeholder'=>'Full Name', 'id'=>'fullName')) !!}
			        </div>
			        <div class="form-group {{ $errors->has('inputGender')?'has-error':'' }}">
			        	{!! Form::label('Gender', '', array('for'=>'inputGender')) !!}
			        	{!! Form::select('inputGender', array('male'=>'Male','female'=>'Female','other'=>'Other'),Input::old('inputGender'),array('class'=>'form-select form-control')) !!}
			        </div>
			        <div class="form-group {{ $errors->has('phone')?'has-error':'' }}">
			            {!! Form::label('Phone', '', array('for'=>'phone')) !!}
			            {!! Form::text('phone', Input::old('phone'), array('class'=>'form-control', 'placeholder'=>'Phone', 'id'=>'phone')) !!}
			        </div>
			        <div class="form-group {{ $errors->has('email')?'has-error':'' }}">
			            {!! Form::label('Email', '', array('for'=>'email')) !!}
			            {!! Form::email('email', Input::old('email'), array('class'=>'form-control', 'placeholder'=>'Email', 'id'=>'email')) !!}
			        </div>
			        <div class="form-group {{ $errors->has('address')?'has-error':'' }}">
			            {!! Form::label('Address', '', array('for'=>'address')) !!}
			            {!! Form::text('address', Input::old('address'), array('class'=>'form-control', 'placeholder'=>'Address', 'id'=>'address')) !!}
			        </div>
			        <div class="form-group {{ $errors->has('nationality')?'has-error':'' }}">
			            {!! Form::label('Nationality *', '', array('for'=>'nationality')) !!}
			            {!! Form::text('nationality', Input::old('nationality'), array('class'=>'form-control', 'placeholder'=>'Nationality', 'id'=>'nationality')) !!}
			        </div>
			        <div class="form-group {{ $errors->has('dateOfBirth')?'has-error':'' }}">
			            {!! Form::label('Date of birth *', '', array('for'=>'dateOfBirth')) !!}
			            {!! Form::text('dateOfBirth', Input::old('dateOfBirth'), array('class'=>'form-control datepicker', 'placeholder'=>'Date of birth', 'id'=>'dateOfBirth')) !!}
			        </div>
			        <div class="form-group {{ $errors->has('education')?'has-error':'' }}">
			            {!! Form::label('Education Background', '', array('for'=>'education')) !!}
			            {!! Form::text('education', Input::old('education'), array('class'=>'form-control', 'placeholder'=>'Education Background', 'id'=>'education')) !!}
			        </div>
			        <div class="form-group {{ $errors->has('contactMode')?'has-error':'' }}">
			        	{!! Form::label('Preferred mode of contact', '', array('for'=>'contactMode')) !!}
			        	{!! Form::select('contactMode', array('phone'=>'Phone','email'=>'Email','none'=>'None'), Input::old('contactMode'),array('class'=>'form-select form-control')) !!}
			        </div>
			        {!! Form::button('Submit',array('type'=>'submit','class'=>'btn btn-primary')) !!}
			    {!! Form::close() !!}	
			</div>
		</div>
	</div>
{!! HTML::script('public/js/jquery-1.12.1.min.js') !!}
{!! HTML::script('public/js/bootstrap.min.js') !!}
{!! HTML::script('public/js/jquery-ui.js') !!}
{!! HTML::script('public/js/jquery-validate/jquery.validate.min.js') !!}
{!! HTML::script('public/js/main.js') !!}

@endsection
