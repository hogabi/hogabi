jQuery(function($) {
	// Jump to Content
	$(function() {
		$('#skip').on('click', function() {
			var $idf = $("#"+$(this).attr("href").split("#")[1]);
			$idf.attr("tabindex", -1).focus();
		});
	});


	// Main Navigation
	$(function(){
		$('#sf-menu').supersubs({
			minWidth: 12,
			maxWidth: 24,
			extraWidth: 0
		}).superfish({
			delay: 0,
			animation: {opacity:'show'},
			animationOut: {opacity:'hide'},
			speed: 250,
			speedOut: 50
		});
	});


	// Side Navigation
	$(function() {
		var $sm3rd = $('#side_menu').find('.sm3rd-trigger');
		
		$sm3rd.on('click', function(e) {
			$this = $(this);
			if($this.next().is(':hidden')) {
				$sm3rd.removeClass('active').next().slideUp(200);
				$this.addClass('active').next().slideDown(200);
			} else {
				$this.removeClass('active').next().slideUp(200);
			};
			e.preventDefault();
		});
	});


	// Scroll Top
	$(function() {
		var $sTop = $('#sTop');
		$sTop.on('click', function(e){
			e.preventDefault();
			$('html, body').stop(true).animate({scrollTop:0},800,'swing');
		});

		$(window).on('scroll',function() {
			var position = $(window).scrollTop();
			if(position > 200) {
				$sTop.show();
			} else {
				$sTop.hide();
			}
		});
	});

});