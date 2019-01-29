window.addEventListener("load", function(){
	window.cookieconsent.initialise({
		"palette": {
	    	"popup": {
	    		"background": "#000000",
	    		"text": "#ffffff"
	    	},
		    "button": {
		    	"background": "#999999",
		    	"text": "#ffffff"
		    }
	    },
	  	"theme": "edgeless",
	  	"content": {
		    "message": "This website uses cookies for security reasons only - never to track you, and we don't store your data.",
		    "dismiss": "Nice one"
		  }
	});
});