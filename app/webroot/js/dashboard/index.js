$(function(){

var
//root = 'http://localhost:8888/phorest/',
//root = 'http://localhost:81/Phorest/',
root = 'http://development/phorest/',
//root = 'http://pk-brs.xsrv.jp/',
$albums = $('#albums .row');

function init()
{
	var 
	username = $('meta[name="owner"]').attr('content'),
//	url = 'http://localhost:8888/phorest/DatUsers/' + username;
//	url = 'http://localhost:8888/phorest/datalbums/userSearch/' + username;
//	url = 'http://localhost:81/Phorest/datalbums/userSearch/' + username;
	url = 'http://development/phorest/datalbums/userSearch/' + username;
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

		addAlbum(albumArr);
	});
}


function addAlbum(albumArr)
{
	var 
	template = _.template($('#temp_album').html());
	$.each(albumArr,function(index,album){
		var 
		cover = album['photos'][0],
		albumName = album["albumName"],
		href = root + "albums/" + albumName,
		$el = $( template({thumUrl:cover['thumUrl_square'],  albumName:albumName,  href:href}) );

		$albums.append($el);
		
		// fillimg( $el.find('img'), cover["width"], cover["height"] );
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
		imgUrls = _.pluck(album_info['photos'],'imgUrl');

		slideshow.option({imgs:imgUrls});
		addThumb(album_info);
	}
}


init();

});