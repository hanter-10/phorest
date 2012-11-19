$(document).ready(function(){
   
   
   $("#main .row").each(function()
   {
   	var left = 0;
   	$("img",this).each(function()
	{
		var img = this;
		$(img).css("left",left);
		left+=img.width+5;
	});


   });


   //resizing
   var 
   $mainInfo = $("#main-info"),
   $aboutMe = $("#about-me"),
   border = parseInt($("#main-info").css("padding-left"))  *  2;

   $(window).resize(function(){
   	var 
   	winWidth = $(window).width(),
   	mainInfoW = parseInt(winWidth*0.68)-border,
   	aboutMeW = winWidth - (mainInfoW+border)-border-1;
   	$mainInfo.width(mainInfoW);
   	$aboutMe.width(aboutMeW);

   }).resize();


   //more-info-handle hover
   var $moreInfo = $("#more-info"),
   $moreInfoHandle = $("#more-info-handle"),
   originalTop = $("#more-info").css("top"),
   originalText = $moreInfoHandle.text();

   $("#more-info-handle").toggle(
	   	function()
	   	{
		  $moreInfo.animate({top:0},700,"swing");
		  $moreInfoHandle.text("CLOSE ✕");
	    },
	   function()
	   {
	   	  $moreInfo.animate({top:originalTop},700,"swing");
		  $moreInfoHandle.text(originalText);
	   }
   );
   
   
});