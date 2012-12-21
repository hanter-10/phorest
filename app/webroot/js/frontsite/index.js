$(function(){

var imgs=[
'images/frontsite/3.jpeg',
'images/frontsite/1.jpeg',
'images/frontsite/2.jpeg',
'images/frontsite/4.jpeg',
'images/frontsite/5.jpg'
];
 slideshow =
$.slideshow({
	imgs: imgs,
	fade: 1600,
	delay: 5000,
	onchange:change,
	onstop:stop,
	onplay:play,
	prevbtn:'#prevbtn',
	nextbtn:'#nextbtn'
});
slideshow.play()
function stop(index)
{

}

function change(index)
{
	var
	$img = $('#img-container img').eq(index),
	left = $img.offset().left,
	width = $img.width();
	$('#indicator').animate({ left:left,width:width },500);
}

function play(index)
{

}


//サムネールのロードと配置
function createThum(imgurls)
{
	$('#img-container').empty();

	var
	margin = 3,
	left = margin,
	$imgs = $.loadimg({
	imgs:imgurls,
	process: function(percentage,index){ console.log( percentage+"%" );},
	load: function(){},
	allLoad: function(){},
	}).getImgs();

	/*$imgs.attr('height',33).each(function(){
		var width = $(this).css('left',left).width();
		left += $(this).width+margin;

	});*/
	// $('#img-container').append($imgs);
}
var imgurls = [
'../images/sample.jpg',
'../images/sample2.jpg',
'../images/sample3.jpg',
'../images/sample4.jpg',
'images/3.jpeg',
'images/1.jpeg',
'images/2.jpeg',
'images/4.jpeg',
'images/5.jpg'
];

// createThum(imgurls);



//------------------ UI ------------------
// $('#controlPanel select').selectStyler();
var
$footer = $('#footer'),
$show_photos = $('#show-photos'),
$albums = $('#albums'),
if_albums_open = false;

//#show-photos
$show_photos.toggle(function(){
	$(this).toggleClass('hover');
	$footer.stop().animate({bottom:0},300,'swing');
},function(){
	$(this).toggleClass('hover');
	$footer.stop().animate({bottom:-58},300,'swing');
});

//#show-albums
$('#show-albums').click(function(){
	parseInt($footer.css('bottom'))===0 && ($show_photos.click())
	$(this).toggleClass('hover');
	toggleAlbums();
});

function toggleAlbums()
{
	if(if_albums_open){
		hideAlbums();
	}else{
		showAlbums();
	}
}
function showAlbums()
{
	slideshow.stop();
	$albums.addClass('show');
	if_albums_open = true;
}

function hideAlbums()
{
	slideshow.play();
	$albums.removeClass('show');
	if_albums_open = false;
}
















//----------------------------------config----------------------------------
(function(){

var
$clickReceiver  = $('#click-receiver'),
$controlPanel   = $('#controlPanel'),
$config         = $('#config'),
$options        = $("#controlPanel .options"),
width           = $('#controlPanel .right').outerWidth();

$options.css({right:width});
$options.each(function(){
	var $this = $(this);
	$this.width((width+1) * $this.find('li').length);
});

/*$('#controlPanel .right').click(function(){
	$('.options',this).fadeIn();
});*/

function toggleConfig(onbody)
{
	$config.toggleClass('hover');

	if($config.hasClass('hover')){  //show
		$clickReceiver.show();
		slideshow.stop();
		$controlPanel.stop().show().animate({bottom:110,opacity:1},300,'swing');
	}else{  //hide
		$clickReceiver.hide();
		slideshow.play();
		$controlPanel.stop().animate({bottom:'-=40',opacity:0},300,'swing',function(){ $controlPanel.hide(); });
	}
}

$config.click(function(e){
	console.log( e.target );
	e.stopPropagation();
	toggleConfig();
});

$('#click-receiver').click(function(e){
	console.log( e.target );
	toggleConfig();
});


$('#controlPanel .options>li').click(function(){
	var
    $this       = $(this),
    $parent     = $this.parent(),
    type        = $this.data('type'),
    val         = $this.data('value');

	switch (type){
		case 'duration':
			slideshow.option({delay:val*1000});
			break;
		case 'effect':
			slideshow.option({effect:val});
			break;
		case 'size':
			slideshow.option({size:val});
			break;
		case 'fullscreen':
			if(val==='on'){
				screenfull.request();
			}else{
				screenfull.exit();
			}
			break;
	}

	$parent.find('.current').removeClass('current');
	$parent.prev().text($this.text());
	$this.addClass('current');

});

})();

});