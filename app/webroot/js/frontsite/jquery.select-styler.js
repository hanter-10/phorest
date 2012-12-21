(function($){
	$.fn.selectStyler = function(setting)
	{
		var options = {

		};

		

		$.extend(options,setting);
		this.each(function(){
			var 
			$this = $(this),
			$opts = $this.find('option'),
			$newSel = $('<span class="select-styler-container">').width( $this.width() ).append('<span class="activeOption"></span> <span class="arrow-container" unselectable="on"> <span class="arrow" unselectable="on"></span> </span>'),
			$dropdownMenu = $('<ul class="dropdown-menu">').hide().appendTo($newSel),
			$activeOption = $newSel.find('.activeOption').text($opts.eq(0).text());

			$activeOption.css({ float:$this.css('float'), width:$this.css('width'), position:$this.css('position') });
			$.each($opts,function(){
				var $newOpts = $('<li class="option">').text(this.value);
				$newOpts.appendTo($dropdownMenu);
				$newOpts.click(function(){
					$this.val( $newOpts.text() );
					$dropdownMenu.fadeOut();
				});
			});

			$newSel.click(function(){
				$dropdownMenu.stop().fadeIn();
			});
			$('body').click(function(){
				// $dropdownMenu.stop().fadeOut();
			});
			$this.hide().after($newSel);

		});
	};
})(jQuery);