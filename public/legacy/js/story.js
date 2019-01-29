$(document).ready(function() {
	$('.story').each(function() {
	    if (!$(this).hasClass('pinned')) {
	    	var title = $(this).find('h2');
	    	if (title.text().length > 80) {
	    		var newTitle = '';
	    		var words = title.text().split(' ');
	    		words.forEach(function(word, index) {
	    			if (index < words.length - 1) {
	    				newTitle += word + ' ';
	    			}
	    		});
	    		title.text(newTitle.substr(0, newTitle.length - 1) + '...');
	    	}
	    }
	});
});