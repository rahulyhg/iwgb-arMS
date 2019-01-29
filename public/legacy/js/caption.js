$(document).ready(function() {
	$('.md').find('img')
		.after(function() {
			console.log($(this).attr('alt'));
			return $('<div>', {
				'class': 	'caption',
			}).append($('<p>', {
				'text': $(this).attr('alt').split('|')[0]
			})).append($('<p>', {
				'text': $(this).attr('alt').split('|')[1]
			}));
		});
	;
});