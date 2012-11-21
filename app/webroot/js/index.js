$(document).ready(function(){

	var
	$userPanel = $("#user-panel"),
	$userPanelHover = $("#user-panel-hover");

	$("#user-panel-hover").toggle(function(){
		$userPanel.fadeIn();
	},function(){
		$userPanel.fadeOut();
	});

});