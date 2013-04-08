$(function(){

var
root = 'http://localhost:8888/phorest/',
username = $('meta[name="owner"]').attr('content'),
sitename = username,
$albums = $('#albums .row');

function init()
{

	var url = 'http://localhost:8888/phorest/datalbums/userSearch/' + username;
	//$('#site-name').text(sitename+"のアルバム");
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
		href = root + username + "/albums/" + albumName,
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