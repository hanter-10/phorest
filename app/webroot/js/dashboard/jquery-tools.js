(function($){

var init = function(setting)
{
	var 
	options = 
	{
		imgs: null, //array
		process: empty,
		load: empty,
		allLoad: empty
	},
	loaded = 0,
	$imgs = $('<div>');
	this.getImgs = function(){ return $imgs.children(); }
	$.extend(options,setting);
	var len = options.imgs.length;
	$.each(options.imgs,function(index,url){
		var $img = $('<img>').load(function(){
			loaded++;
			if(loaded==len){
				options.allLoad();
				options.process(100);
			}else{
				options.process(loaded/len*100,loaded);
				options.load(loaded);
			}
		});
		$img[0].src=url;
		$imgs.append($img);
	});
};

function empty(){}

$.loadimg = function(setting){
 return new init(setting);
};

})(jQuery);