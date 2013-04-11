$(function(){
	var 
	$tabs = $("#login-tab,#sign-up-tab"),
	$forms = $('#forms'),
	$triangle = $('#form-container .triangle'),
	$formWrapper = $('#form-wrapper');

	$tabs.click(function(){
		var $this = $(this);
		if($this.hasClass('actived')) {return};
		$tabs.toggleClass('actived unactived');
		$forms.toggleClass('sign-up');
		$triangle.toggleClass('bottom');
	});

	$('#forms input').tooltip({placement:'left'});
	if( $('.error-message.signUp').length==0 ){
		$formWrapper.show();
	}else{
		$('#sign-up-tab').click();
		setTimeout(function(){
			$formWrapper.show();
		},300);
	}
});