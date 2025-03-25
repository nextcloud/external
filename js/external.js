$(document).ready(function () {
	function pageY(elem) {
		return elem.offsetParent ? (elem.offsetTop + pageY(elem.offsetParent)) : elem.offsetTop;
	}

	var buffer = 0; //scroll bar buffer
	function resizeIframe() {
		var height = document.documentElement.clientHeight;
		height -= pageY(document.getElementById('ifm')) + buffer;
		height = (height < 0) ? 0 : height;
		document.getElementById('ifm').style.height = height + 'px';
	}

	document.getElementById('ifm').onload = resizeIframe;
	window.onresize = resizeIframe;
	resizeIframe();
	// hash routing support
	if(window.location.hash && window.location.hash.length) {
		updateHash();
	}

	window.addEventListener("hashchange", function(event) {
		updateHash();
	});

	function updateHash() {
		const iframeURL = new URL(document.getElementById('ifm').src);
		iframeURL.hash = window.location.hash;
		document.getElementById('ifm').src = iframeURL.toString();
	}
});
