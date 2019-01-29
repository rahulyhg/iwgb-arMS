$(document).ready(function() {
	$('#hamburger').click(function() {
		$(this).toggleClass('open');
		$(this).toggleClass('closed');
		$('.mobile-menu').slideToggle('slow');
	});
});