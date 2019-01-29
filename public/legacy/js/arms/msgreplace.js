$(document).ready(function(){
    if (window.location.href.indexOf("?") > -1) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});