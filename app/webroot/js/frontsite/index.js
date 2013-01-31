$(function(){

var 
$imgContainer       = $('#img-container'),
$controller         = $('#controller'),
$albumsContainer    = $('#albumsContainer'),
$indicator          = $('#indicator'),
$title              = $("#footer .title"),
$phorest_slideshow;
//データの取得を開始
function init()
{
	var 
	username = $('meta[name="owner"]').attr('content'),
	url = 'http://localhost:8888/phorest/datalbums/userSearch/' + username;
//	url = 'http://localhost:81/Phorest/datalbums/userSearch/' + username;
//	url = 'http://development/phorest/datalbums/userSearch/' + username;
//	url = 'http://pk-brs.xsrv.jp/datalbums/userSearch/' + username;
	$.getJSON(url,function(userArr){
		var 
		albumArr = userArr[0]['DatAlbum'],
		albumIndex = 0;

		$.each(albumArr,function(index,album){
			var datPhoto = album.DatPhoto;
			album.photos = datPhoto;
			delete album.DatPhoto;
		});

		addThumb(albumArr[albumIndex]);
		addAlbum(albumArr);
		startslide(albumArr[albumIndex]);
		// $imgContainer.mCustomScrollbar({scrollInertia:0,advanced:{ updateOnContentResize: true,horizontalScroll: true }});
	});

}

function addThumb(album)
{
	function getWidth(photoModel){
		var 
        ratio = photoModel.width/photoModel.height,
        width = ratio*33;
        return Math.round(width);
	}
	var 
	photos = album['photos'],
	firstPhoto = photos[0],
	width = getWidth(firstPhoto),
	margin = 3*2,
	left = margin;

	$imgContainer.empty();
	$indicator.width(width);
	$.each(photos,function(index,photo){
		var url = photo.thumUrl;
		$('<img height="33" alt="thum">').attr({src:url}).css({left:left}).data({index:index,title:photo.photoName}).appendTo($imgContainer);
		left += getWidth(photo)+margin;
	});
}


function startslide(album)
{
	var 
    photos    = album['photos'],
    imgUrls   = _.pluck(photos,'imgUrl_m');

	slideshow = 
	$.slideshow({
		imgs: imgUrls,
		fade: 700,
		delay: 5000,
		onchange:change,
		onstop:stop,
		onplay:play,
		prevbtn:'#prevbtn',
		nextbtn:'#nextbtn'
	});
	slideshow.play();
	
	$phorest_slideshow = $("#phorest_slideshow");
	
	$imgContainer.on('click','img',function(){
		var index = $(this).data('index');
		slideshow.jumpTo(index);
	});
	
	function stop(index)
	{

	}

	function change(index)
	{
		var
        $img    = $imgContainer.find('img').eq(index),
        title   = $img.data('title'),
        left    = $img.offset().left,
        width   = $img.width();
		TweenMax.to( $indicator, 0.5, { css:{left:left,width:width}, ease:Back.easeInOut });
		$title.text(title);
	}

	function play(index)
	{

	}

}

function addAlbum(albumArr)
{
	var 
	template = _.template($('#temp_album').html());
	$.each(albumArr,function(index,album){
		var 
		cover = album['photos'][0],
		$el = $( template({thumUrl:cover['thumUrl'],  albumName:album["albumName"]}) );

		$albumsContainer.append($el);
		$el.find('.wrapper').data('album_info',album).click(changeAlbum);
		fillimg( $el.find('img'), cover["width"], cover["height"] );
	});

	function fillimg($img,w,h)
	{
		if(w/h>1){ //横長
			$img.attr('height',150);
		}else{
			$img.attr('width',150);
		}
	}

	function changeAlbum()
	{
		var 
		$this = $(this),
		album_info = $this.data('album_info'),
		imgUrls = _.pluck(album_info['photos'],'imgUrl_m');

		slideshow.option({imgs:imgUrls});
		addThumb(album_info);
	}
}



init();




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

	$imgs.attr('height',33).each(function(){
		var width = $(this).css('left',left).width();
		left += $(this).width+margin;

	});
	$('#img-container').append($imgs);
}





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

$('#albums').click(function(){
	$('#show-albums').click();
});

function toggleAlbums()
{
	if(if_albums_open){
		$controller.show();
		hideAlbums();
	}else{
		showAlbums();
		$controller.hide();
	}
}
function showAlbums()
{
	slideshow.stop();
	$albums.addClass('show');
	// $phorest_slideshow.addClass('blur');
	if_albums_open = true;
}

function hideAlbums()
{
	slideshow.play();
	$albums.removeClass('show');
	// $phorest_slideshow.removeClass('blur');
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