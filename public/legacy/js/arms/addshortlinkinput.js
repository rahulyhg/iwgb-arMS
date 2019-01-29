$('#blogs').on('change', function(e) {
	if ($('#blogs').find('option:selected').parent().attr('label') == 'Page categories') {
		$('#shortlink').css({
			'visibility': 'visible'
		});
	} else {
		$('#shortlink').css({
			'visibility': 'hidden'
		});
	}
});