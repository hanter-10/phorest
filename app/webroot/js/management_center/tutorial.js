(function(){
	var 
	$mask = $('<div id="mask"> ').hide().appendTo('body'),
	$step1 = $('<div id="t_step1">').append('<div class="t_btn">').appendTo($mask),
	$step2 = $('<div id="t_step2">').append('<div class="t_btn">').appendTo($mask),
	$step3 = $('<div id="t_step3">').append('<div class="t_btn">').appendTo($mask),
	$step4 = $('<div id="t_step4">').append('<div class="t_btn">').appendTo($mask),
	$all_steps = $step1.add($step2).add($step3).add($step4),
	$try_btn = $mask.find('.t_btn'),
	$track1 = $('#uploadArea .text'),
	$track3 = $('#delete-photo'),
	$track4 = $('#status-check'),
	status_index = 0,
	$current_step = $step1;

	//step1
	setTimeout(function(){ 
		$mask.fadeIn(500 ,function(){ popIn($step1) });
	} , 500 );

	//step2
	$.app.Events.once('allUploaded',function(){
		setTimeout(function(){
			//step3
			$mask.fadeIn(500 ,function(){ 
				popIn($step2);
				$('#albums .cover').on('click',function(){
					$('#albums .cover').off('click',arguments.callee);
					$mask.fadeIn(500, function(){ 
						popIn($step3);
						//step4
						$.app.Events.once('moveToPhotoAreaEnd',function(){
							$mask.fadeIn(500,function(){
								popIn($step4);
							});
						});
					});
				});
			});
		},500);
	});

	

	$(window).resize(function(){
		var 
		track1_pos = $track1.offset(),
		track3_pos = $track3.offset(),
		track4_pos = $track4.offset(),
		left1 = track1_pos.left + $track1.width()/2 - 160,
		top1 = track1_pos.top - 240,
		left3 = track3_pos.left - 30,
		left4 = track4_pos.left;

		$step1.css({left:left1,top:top1});
		$step3.css({left:left3});
		$step4.css({left:left4});
		// console.log( $track1.offset() );
	}).resize();

	$mask.click(function(){
		$mask.fadeOut(800);
		popOut($current_step);
		$current_step = $all_steps.eq(++status_index);
	});


	function popIn($el){
		TweenMax.set( $el, {css:{scale:0.2, display:'block'}} );
		TweenMax.to( $el, 0.6, {css:{scale:1,opacity:1}, ease:Back.easeOut} );
	}

	function popOut($el){
		TweenMax.to( $el, 0.6, {css:{scale:0.4,opacity:0,rotationZ:15}, ease:Back.easeOut,onComplete:function(){ $el.hide(); }} );
	}

})();