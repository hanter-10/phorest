$(function(){

var
username            = $('meta[name="owner"]').attr('content'),
rooturl             = '/phorest/',
$imgContainer       = $('#img-container'),
$controller         = $('#controller'),
$albumsContainer    = $('#albumsContainer'),
$indicator          = $('#indicator'),
$photoName          = $("#footer .photo-name"),
$albumName          = $("#footer .album-name"),
$underpart          = $('#footer .underpart'),
$phorest_slideshow;


function startslide(album)
{
	var
    photos    = album['photos'],
    imgUrls   = _.pluck(photos,'imgUrl_m');
    $albumName.text(album.albumName);
    $photoName.text(album.photos[0].photoName);

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
        photoName   = $img.data('photoName'),
        scroll_left = parseInt($('#footer .mCSB_container').css('left')),
        left    = $img.offset().left-8-scroll_left,
        width   = $img.width();
		TweenMax.to( $indicator, 0.5, { css:{left:left,width:width}, ease:Back.easeInOut });
		$photoName.text(photoName);
	}

	function play()
	{

	}

}





//all start here
var _routes={};
_routes[username+'/albums/:albumName']='init';

var Router = Backbone.Router.extend({
	routes: _routes,
	isInited: false,
	init: function(albumName){
		var albumName = decodeURI(albumName);
		if(this.isInited==true){
			this.loadAlbum(albumName);
			return false;
		}
		this.isInited = true;

		//init processing
		var
        url     = rooturl+'datalbums/userSearch/' + username,
        _this   = this;

		$.getJSON(url,function(userArr){
			var albumArr = userArr[0]['DatAlbum'];

			//配列の構造を最適化
			$.each(albumArr,function(index,album){
				var datPhoto = album.DatPhoto;
				album.photos = datPhoto;
				delete album.DatPhoto;
			});

			//アルバム配列をメンバープロパティにする
			_this.albumArr = albumArr;

			//アルバムをロード
			_this.initAlbum(albumArr);
			_this.loadAlbum(albumName,true);

			// $underpart.mCustomScrollbar({scrollInertia:0, horizontalScroll: true, advanced:{ autoExpandHorizontalScroll:true }});
			$(".underpart").mCustomScrollbar({horizontalScroll:true,scrollInertia:0,advanced:{autoExpandHorizontalScroll:true}});
		});
	},


	initAlbum : function(albumArr)
	{
		var
		template = _.template($('#temp_album').html());
		$.each(albumArr,function(index,album){
			var
			cover = album['photos'][0],
			$el = $( template({thumUrl:cover['thumUrl_square'],  albumName:album["albumName"]}) );

			$albumsContainer.append($el);
			$el.find('.wrapper').click(changeAlbum);
		});

		function changeAlbum()
		{
			var
			albumName = $(this).parent().find('figcaption').text(),
			newurl = username+'/albums/'+albumName;

			router.navigate(newurl, {trigger: true});
			$albumName.text(albumName);
			
		}
	},

	loadAlbum : function(albumName,init)
	{
		var
		_this = this,
		albumIndex,
		imgArr = [],
		albumArr = this.albumArr;

		//どのアルバムかを確定する
		$.each(albumArr,function(index,album){
			if(album.albumName==albumName) albumIndex=index;
		});

		//このアルバム内に入っている写真のURLを絞り出す
		$.each(albumArr[albumIndex].photos,function(index,photo){
			imgArr.push(photo.imgUrl_m);
		});


		$.loadimg({
			imgs: imgArr,
			process: function(percentage){
				console.log( percentage );
				// _this.startslide(albumArr[albumIndex]);
			},
			allLoad: function(){
				// console.log( 'all loaed' );
				_this.addThumb( albumArr[albumIndex] );
				if(init){
					startslide(albumArr[albumIndex]);
				}else{
					slideshow.option({imgs:imgArr});
				}
				var
				$img    = $imgContainer.find('img').eq(0),
		        photoName   = $img.data('photoName');
				$photoName.text(photoName);

			}
		});

	},

	addThumb : function(album)
	{
		var
		$div = $('<div>'),
		photos = album['photos'],
		firstPhoto = photos[0],
		indicator_width = getWidth(firstPhoto),
		container_width = 0;

		$imgContainer.empty().width(container_width); //empty previous thumbnail in container
		$indicator.css({ width:indicator_width, left:3 }); //reset position and width of indicator

		$.each(photos,function(index,photo){
			var
			url = photo.thumUrl,
			img_width = $('<img height="33" alt="thum">').attr({src:url}).data({index:index,photoName:photo.photoName}).appendTo($div);
			container_width += getWidth(photo)+6;
		});

		$imgContainer.width(container_width);
		$div.children().appendTo($imgContainer);
		$underpart.mCustomScrollbar("update");

		function getWidth(photoModel){
			var
	        ratio = photoModel.width/photoModel.height,
	        width = ratio*33;
	        return Math.round(width);
		}
	}
});

var router = new Router();
Backbone.history.start({pushState: true, root: rooturl});



//------------------ UI ------------------
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