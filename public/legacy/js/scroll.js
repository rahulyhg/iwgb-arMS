$(document).ready(function() {
	$('#joinfab').fadeTo('fast', 0);

	$('#scroll-to-content').click(function() {
		scrollTo($('#nav'), false);
	});

	$('#to-the-top').click(function() {
		scrollTo($('#nav'), false);
	});

	$('#boycott-why').click(function() {
		scrollTo($('#boycott-why-anchor'), false);
	});

	$('#boycott-promote').click(function() {
		scrollTo($('#boycott-promote-anchor'), false);
	});

	function scrollTo(element, nav) {
		var navAdjust = nav ? - $('.nav-container').height() : 0;
		$('html, body').animate({
			'scrollTop': element.offset().top + navAdjust,
		}, 800);
	}

	$(document).scroll(function() {
		if ($(document).scrollTop() > $(window).height() / 3 && !$('#scroll-to-content').hasClass('faded')) {
			$('#scroll-to-content').fadeTo('fast', 0);
			$('#scroll-to-content').toggleClass('faded');
		} else if ($(document).scrollTop() < $(window).height() / 3 && $('#scroll-to-content').hasClass('faded')) {
			$('#scroll-to-content').fadeTo('fast', 1);
			$('#scroll-to-content').toggleClass('faded');
		}

		if ($(document).scrollTop() > $(window).height() / 3 && !$('#joinfab').hasClass('faded')) {
			$('##joinfab').fadeTo('fast', 1);
			$('##joinfab').toggleClass('faded');
		} else if ($(document).scrollTop() < $(window).height() / 3 && $('#joinfab').hasClass('faded')) {
			$('##joinfab').fadeTo('fast', 0);
			$('##joinfab').toggleClass('faded');
		}
	});
});