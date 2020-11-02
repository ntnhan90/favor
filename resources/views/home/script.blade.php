<script src="{!! asset('admin/plugins/jQuery/jQuery-2.2.0.min.js') !!}"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<script src="{!! asset('admin/plugins/select2/select2.full.min.js') !!}"></script>
<script src="{!! asset('admin/js/jquery.validate.js') !!}"></script>
<script type="text/javascript">
	

//validate form
jQuery(document).on('submit', '.form-validate-login', function(e){
	var error = "";

	var check = 0;
	jQuery(".password-login").each(function() {
		if(this.value == '') {
			jQuery(this).css('border-color', 'red');
			jQuery(this).parents(".input-group").addClass('has-error');
			jQuery(this).next(".error-content").removeAttr('hidden');
			error = "has error";
		}else{
			jQuery(this).css('border-color', '#dee2e6');
			jQuery(this).parents(".input-group").removeClass('has-error');
			jQuery(this).next(".error-content").attr('hidden', true);
		}

	});

	jQuery(".email-validate-login").each(function() {
			var validEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
			if(this.value != '' && validEmail.test(this.value)) {
			jQuery(this).css('border-color', '#dee2e6');
				jQuery(this).parents(".input-group").removeClass('has-error');
				jQuery(this).next(".error-content").attr('hidden', true);
			}else{
				jQuery(this).css('border-color', 'red');
				jQuery(this).parents(".input-group").addClass('has-error');
				jQuery(this).next(".error-content").removeAttr('hidden');
				error = "has error";
			}
	});
	if(error=="has error"){
		return false;
	}
});

//validate form
jQuery(document).on('submit', '.form-validate', function(e){
	var error = "";

	jQuery(".field-validate").each(function() {
		if(this.value == '') {
			jQuery(this).css('border-color', 'red');
			jQuery(this).parents(".input-group").addClass('has-error');
			jQuery(this).next(".error-content").removeAttr('hidden');
			error = "has error";
		}else{
			jQuery(this).css('border-color', '#dee2e6');
			jQuery(this).parents(".input-group").removeClass('has-error');
			jQuery(this).next(".error-content").attr('hidden', true);
		}
	});
	var check = 0;

	jQuery(".password").each(function() {
		var regex = "^\\s+$";

		if(this.value == '') {
			jQuery(this).css('border-color', 'red');
			jQuery(this).parents(".input-group").addClass('has-error');
			jQuery(this).next(".error-content").removeAttr('hidden');
			error = "has error";
		}else{
			if(this.value.match(regex)) {
			jQuery(this).css('border-color', 'red');
			jQuery(this).parents(".input-group").addClass('has-error');
			jQuery(this).next(".error-content").removeAttr('hidden');
			error = "has error";
		}else{
			if(check == 1){
				var res = passwordMatch();

				if(res=='matched'){
					jQuery(this).css('border-color', '#dee2e6');
					jQuery('.password').parents(".input-group").removeClass('has-error');
					jQuery('.re-password-content').attr('hidden', true);
				}else if(res=='error'){
					jQuery(this).css('border-color', 'red');
					jQuery('.password').parents(".input-group").addClass('has-error');
					jQuery('.re-password-content').removeAttr('hidden');
					error = "has error";
				}
			}else{
				jQuery(this).css('border-color', '#dee2e6');
				jQuery(this).parents(".input-group").removeClass('has-error');
				jQuery(this).next(".error-content").attr('hidden', true);
			}
			check++;
			}
		}
		
	});

	jQuery(".number-validate").each(function() {
			if(this.value == '' || isNaN(this.value)) {
			jQuery(this).css('border-color', 'red');
				jQuery(this).parents(".input-group").addClass('has-error');
				jQuery(this).next(".error-content").removeAttr('hidden');
				error = "has error";
			}else{
				jQuery(this).css('border-color', '#dee2e6');
				jQuery(this).parents(".input-group").removeClass('has-error');
				jQuery(this).next(".error-content").attr('hidden', true);
			}
	});

	jQuery(".email-validate").each(function() {
		var validEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
		if(this.value != '' && validEmail.test(this.value)) {
			jQuery(this).css('border-color', '#dee2e6');
			jQuery(this).parents(".input-group").removeClass('has-error');
			jQuery(this).next(".error-content").attr('hidden', true);
		}else{
			jQuery(this).css('border-color', 'red');
			jQuery(this).parents(".input-group").addClass('has-error');
			jQuery(this).next(".error-content").removeAttr('hidden');
			error = "has error";
		}
	});

	jQuery(".checkbox-validate").each(function() {
		if(jQuery(this).prop('checked') == true){
			jQuery(this).css('border-color', '#dee2e6');
			jQuery(this).parents(".input-group").removeClass('has-error');
			jQuery(this).closest('.checkbox-parent').children('.error-content').attr('hidden', true);
		}else{
			jQuery(this).css('border-color', 'red');
			jQuery(this).parents(".input-group").addClass('has-error');
			jQuery(this).closest('.checkbox-parent').children('.error-content').removeAttr('hidden');
			error = "has error";
		}

	});

	jQuery(".phone-validate").each(function() {
		if(this.value == '' || isNaN(this.value) || this.value.length < 7) {
			jQuery(this).css('border-color', 'red');
			jQuery(this).parents(".input-group").addClass('has-error');
			jQuery(this).next(".error-content").removeAttr('hidden');
			error = "has error";
		}else{
			jQuery(this).css('border-color', '#dee2e6');
			jQuery(this).parents(".input-group").removeClass('has-error');
			jQuery(this).next(".error-content").attr('hidden', true);
		}

	});

	if(error=="has error"){
		return false;
	}
});


function passwordMatch(){

	var password = jQuery('#password').val();
	var re_password = jQuery('#re_password').val();

	if(password == re_password){
		return 'matched';
	}else{
		return 'error';
	}
}
</script>