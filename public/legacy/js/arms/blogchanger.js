$('#blogs').on('change', function(e) {
	window.location.replace('../../../arms/feed/' + encodeURIComponent($('#blogs').find('option:selected').val()));
});