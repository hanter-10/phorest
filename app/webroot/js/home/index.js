$(function(){
	var 
	$tabs = $("#login-tab,#sign-up-tab"),
	$forms = $('#forms'),
	$triangle = $('#form-container .triangle');

	$tabs.click(function(){
		var $this = $(this);
		if($this.hasClass('actived')) {return};
		$tabs.toggleClass('actived unactived');
		$forms.toggleClass('sign-up');
		$triangle.toggleClass('bottom');
	});
});