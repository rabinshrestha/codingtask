$(document).ready(function(){
	$('#dateOfBirth').datepicker();
	formValidator();
	/**
	* Set error status for form-group div 
	*/
	function showError(validator){
		$(validator.errorList).each(function(i,v){
			$(v.element.parentElement).addClass('has-error');
		});
	}
	/**
	* Reset all form-group div error status
	*/
	function resetError(){
		$('label.error').hide();
		$('div.form-group').removeClass('has-error');
	}

	/**
	* Used for Form validation
	*/
	function formValidator(){
		var form = $("#create-client");
		$("#create-client").on('submit',function(){
			var submitButton = $('#create-client button');
			submitButton.attr('disabled',true);
			resetError();
			form.validate({
				rules: {
					fullName: {
						required: true,
						minlength: 5,
						maxlength: 50,
					},
					phone: {
						digits: true,
						maxlength: 10,	
					},
					email: {
						email: true,
						maxlength: 100,
					},
					address: {
						maxlength: 100,
					},
					nationality: {
						required: true,
						maxlength: 50,
					},
					dateOfBirth: {
						required: true,
						date: true,
					},
					education: {
						maxlength: 100,
					},
	  			},
	  			invalidHandler: function(event, validator) {
					var errors = validator.numberOfInvalids();
					if(errors){
						showError(validator);
					}
				}
			});
			if(form.valid()){
				form.submit();
				return true;
			}
			submitButton.attr('disabled',false);
			return false;
		});
	}
});
