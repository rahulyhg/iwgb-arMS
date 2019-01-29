$(document).ready(function() {

	var images = [{
		image: 'header1.gif',
		caption: 'Deliveroo riders in Camden protest a change in their contracts',
	}, 
	{
		image: 'header2.jpg',
		caption: 'London couriers protest outside a CitySprint client',
	}, 
	{
		image: 'header3.jpg',
		caption: 'Driver Yaseem Aslam speaks outside the appeal against Uber that he would go on to win',
	}];

	var imageIndex = -1;
	var cycle = window.setInterval(cycleImage, 10000);
	images.forEach(function(image, index) {
		var className = imageIndex == index ? 'current' : '';
		var dot = $('<a>', {
			id: 'image' + index,
			class: className,
		});
		dot.click(function() {
			imageIndex = dot.attr('id').substr(-1);
			clearInterval(cycle);
			cycle = window.setInterval(cycleImage, 10000);
			replaceImage(imageIndex);
		});
		$('#gallery-dots').append(dot);
	});

	cycleImage();
	function cycleImage() {
		imageIndex++;
		if (imageIndex == images.length) {
			imageIndex = 0;
		}
		replaceImage(imageIndex);
	}

	function replaceImage(index) {
		if ($('.hero').css('display') != 'none') {
			$('.hero').fadeTo(400, 0.2, function() {
				$('.hero').css({
					background: 'url(/img/' + images[index].image + ') center/cover',
				});
				$('.caption').text(images[index].caption);
				$('#gallery-dots').find('a').removeClass('current');
				$('#image' + index).addClass('current');
			}).fadeTo(400, 1);
		}
	}
});