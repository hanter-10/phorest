(function(){

//================ transitionEnd ==================
$.support.transition = (function () {

	var transitionEnd = (function () {

		var el = document.createElement('slideshow')
		, transEndEventNames = {
			'WebkitTransition' : 'webkitTransitionEnd'
			,  'MozTransition'    : 'transitionend'
			,  'OTransition'      : 'oTransitionEnd otransitionend'
			,  'transition'       : 'transitionend'
		}
		, name

		for (name in transEndEventNames){
			if (el.style[name] !== undefined) {
				return transEndEventNames[name]
			}
		}

	}())

	return transitionEnd && {
		end: transitionEnd
	}

})();
//==================================================
var ww = $(window).width();
$(window).resize(function(){
	ww = $(window).width();
});

$.slideshow = function(setting)
{
	//グロバル変数たち

	var 
	options = 
	{
        imgs:       null,
        effect:     'fade',
        fade:       700,
        delay:      3000,
        size:       'auto', //contain , cover も可能
        nextbtn:    null,
        prevbtn:    null,
        playbtn:    null,
        stopbtn:    null,
        onchange:   empty,
        onstop:     empty,
        onplay:     empty
	},
    $loadicon   = $( '<div id="phorest_loadicon">' ),
    $slideshow  = $( '<div id="phorest_slideshow"></div>' ),
    pointer     = 2,
    allowJump   = true,
    timer,
	$imgs,
	$slides,
	$current,
	$next;

	$.extend( options, setting );

	function getReady()
	{
		var len = options.imgs.length;
		$slideshow.empty();
        $.each(options.imgs,function(index,url){
        	var 
        	$img = $('<img>').load(function(){ resize($(this)); }),
        	$div = $('<div class="slide">').append($img);

        	if(options.effect=='fade'){
        		$div.css('transition','opacity '+options.fade+'ms ease-out');
        	}

        	$img[0].src=url;
        	$slideshow.append($div);
        });
        $('body').prepend($slideshow);

        
        $imgs       = $slideshow.find('img');
        $slides     = $slideshow.find('.slide');
        $current    = $slides.eq(0).addClass('current');
        $next       = $slides.eq(1).addClass('next');
        pointer     = 2;
	}

	function resize($img)
    {
    	if( $img.height() == 0 ) {
            //まだロードできてないので、return
            return;
        }
        console.log( 'resized' );
        var 
        ww = $( window ).width(),
        wh = $( window ).height(),
        iw = $img.width(),
        ih = $img.height(),
        rw = ww / wh, //whを1と見なして、値が大きいほど横に長い
        ri = iw / ih;

        switch (options.size){
        	case 'auto' :
        		if(ri/rw > 2 || ri/rw < 0.5){ //歪み率が大きすぎた場合、フル表示する
        			contain($img,ww,wh,iw,ih,rw,ri);
        		}else{
        			cover($img,ww,wh,iw,ih,rw,ri);
        		}
        		break;
        	case 'cover' :
        		cover($img,ww,wh,iw,ih,rw,ri);
        		break;
        	case 'contain' :
        		contain($img,ww,wh,iw,ih,rw,ri);
        		break;
        }

        
    }

    function contain($img,ww,wh,iw,ih,rw,ri)
    {
    	var newWidth,newHeight,newLeft,newTop;
    	if(rw > ri){ //windowのほうが横長い
    		newHeight = wh;
    		newWidth = ri*newHeight;
    		newTop = 0;
    		newLeft = (ww - newWidth)/2;
    	}else{
    		newWidth = ww;
    		newHeight = newWidth/ri;
    		newLeft = 0;
    		newTop = (wh - newHeight)/2;
    	}
    	$img.css({
    		width:newWidth,
    		height:newHeight,
    		top:newTop,
    		left:newLeft
    	});
    }

    function cover($img,ww,wh,iw,ih,rw,ri)
    {
    	var newWidth,newHeight,newLeft,newTop;
    	if(rw > ri){ //windowのほうが横長い
    		newWidth = ww;
    		newHeight = newWidth/ri;
    		newLeft = 0;
    		newTop = (newHeight - wh)/2;
    	}else{
    		newHeight = wh;
    		newWidth = ri*newHeight;
    		newTop = 0;
    		newLeft = (newWidth - ww)/2;
    	}
    	$img.css({
    		width:newWidth,
    		height:newHeight,
    		top:-newTop,
    		left:-newLeft
    	});
    }

	var init = function(options)
	{
        getReady();

        
        function adapt()
        {
        	$imgs.each(function(){
        		resize($(this));
        	});
        }

        

        $(window).on('resize',adapt);
        //adapt();
        $(window).load(function(){ adapt(); });
	}

	var methods =
	{
		play: function(immediacy,dir)
		{
			clearTimeout( timer );
			options.onplay();
			var 
			len = options.imgs.length,
			effect = options.effect;

			function doSlideshow(byUser)
			{
				
				(pointer)===len && (pointer=0)

				// console.log( pointer );
				// $current.show();
				// $next.show();
				$current.css('visibility','visible');
				$next.css('visibility','visible');
				allowJump = false;
				/*$current.fadeOut(options.fade,function(){
					$(this).removeClass('current');
					$current.removeClass('next').addClass('current');
					$next.addClass('next');
					allowJump = true;
				});*/

				//

				//
				switch(effect) {
					case 'slide':
						(function($current_copy){
							var
                            current_from    = 0,
                            current_to      = -ww,
                            next_from       = ww,
                            next_to         = 0;

							if(byUser && dir){
								current_from    = 0,
	                            current_to      = ww,
	                            next_from       = -ww,
	                            next_to         = 0;
							}
							/*TweenMax.fromTo($current,0.8,{ css:{left:current_from} },{ css:{left:current_to}, ease:Power2.easeInOut });
							TweenMax.fromTo($next, 0.8, {css:{left:next_from}}, {css:{left:next_to}, ease:Power2.easeInOut,onComplete:function(){
								$current_copy.removeClass('current');
								$current.removeClass('next').addClass('current');
								$next.addClass('next');
								allowJump = true;
							}});*/
							//
							$current.css('left',current_from).animate({left:current_to},800);
							$next.css('left',next_from).animate({left:next_to},800,function(){
								$current_copy.removeClass('current');
								$current.removeClass('next').addClass('current');
								$next.addClass('next');
								allowJump = true;
							});
							//
						})($current);

						break;

					case 'fade':
					if($.support.transition){
						$current.addClass('opacity-0').one($.support.transition.end,function(){
							$(this).removeClass('current opacity-0').css('visibility','hidden');
							$current.removeClass('next').addClass('current');
							$next.addClass('next');
							allowJump = true;
						});
					}else{
						(function($current_copy){
							TweenMax.to($current_copy, options.fade/1000, 
							{
								css:{opacity:0},onComplete:function(){
									$current_copy.removeClass('current').css('opacity',1);
									$current.removeClass('next').addClass('current');
									$next.addClass('next');
									allowJump = true;
								}
							});
						})($current);
					}

				}

				
				
				//fire change event
				options.onchange(pointer-1<0 ? len-1 : pointer-1);
				$current = $next;
				$next = $slides.eq(pointer++);
				timer = setTimeout( doSlideshow, options.delay );
			}

			if(immediacy){
				doSlideshow(true);
			}else{
				timer = setTimeout( doSlideshow, options.delay );
			}
			return this;
		},
		stop: function()
		{
			clearTimeout( timer );
			return this;
		},
		pause: function()
		{
			clearTimeout( timer );
			pointer=2;
			$current.removeClass('current');
			$next.removeClass('next');
			$current = $slides.eq(0).addClass('current').show();
			$next = $slides.eq(1).addClass('next').show();
			return this;
		},
		next: function()
		{
			if(!allowJump){return this}
			this.stop().play(true);
		},
		prev: function()
		{
			var len = options.imgs.length;
			if(!allowJump){return this}

			pointer-=2;
			if(pointer<0) pointer = len-1;
			$next.removeClass('next');
			$next = $slides.eq(pointer-1).addClass('next');
			this.stop().play(true,'prev');
		},
		jumpTo: function(index)
		{
			var len = options.imgs.length;
			if(index>=len) index = len-1;
			if(index<0) index = 0;

			// console.log( 'index:'+index,'pointer:'+pointer );
			if(!allowJump || index+2 === pointer){ return this;}
			if(index===len-1 && pointer===1){ return this;}
			
			pointer = index+1;
			$next.removeClass('next');
			$next = $slides.eq(index).addClass('next');
			this.stop().play(true);
			return this;
		},
		getImgs: function()
		{ return $imgs; },
		option: function(newoptions)
		{
			if( options.effect=='slide' && newoptions.effect=='fade'){
				$slideshow.find('.slide').css('left','');
			}

			$.extend(options,newoptions);
			if(newoptions.imgs)
			{
				this.pause();
				getReady();
				this.play();
			}



			$(window).resize();
			return this;
		}
	}

	$.extend(init.prototype,methods);
	var inc = new init(options);
	if(options.nextbtn) $(options.nextbtn).on('click',function(){ inc.next(); });
    if(options.prevbtn) $(options.prevbtn).on('click',function(){ inc.prev(); });
    if(options.play) $(options.play).on('click',function(){ inc.play(); });
    if(options.stop) $(options.stop).on('click',function(){ inc.stop(); });
	return inc;

};

function empty(){}

})();