jQuery(function($) {
	var $cpreview = $('#cpreview'),
		$previewBut = $cpreview.find('.previewBut'),
		$previewBody = $cpreview.find('.previewBody'),
		$colorsetList = $('#colorsetList'),
		$colorset = $colorsetList.find('a.colorset'),
		$typeList = $('#typeList'),
		$type = $typeList.find('.type');

	$previewBut.on('mouseenter',function(){
		$cpreview.stop().animate({left:0}, 250);
	});

	$cpreview.on('mouseleave', function(){
		$cpreview.stop().animate({left:'-187px'}, 250);
	});

	$colorset.on('click',function(){
		var $className = $(this).attr('title');

		// Colorset CSS Load
		var head = document.getElementsByTagName('head')[0];
		var link = document.createElement('link');
		link.rel = 'stylesheet';
		link.type = 'text/css';
		link.href = 'layouts/smart/css/'+$className+'.css';
		link.media = 'all';
		head.appendChild(link);

		$('body').removeClass().addClass($className);
		return false;
	});

	$type.on('click',function(){
		var $idName = 'style-' + $(this).attr('title');
		$('body').attr('id',$idName);
		$typeList.find('.active').removeClass('active');
		$(this).addClass('active');
		return false;
	});
	
	$cpreview.delay(250).animate({left:'-187px'}, 250);
});