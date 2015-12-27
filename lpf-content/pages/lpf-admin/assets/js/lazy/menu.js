function changeClassOnCurrent(selector, linkClass, liClass) {
	var fullPath = window.location.protocol + '//' + window.location.hostname + window.location.pathname;
	$(selector).filter(function () {
		return $(this).prop("href") === fullPath; 
	})
	.addClass(linkClass)
	.parent()
	.addClass(liClass);
}
