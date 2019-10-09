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

  var credentials = {
		"username": $("#external_username").val(),
		"password": $("#external_password").val(),
	};

	$.ajax({
		url: $("#external_loginurl").val(),
		type: "POST",
		dataType: 'json',
		/*
		contentType: 'application/json',
		data: JSON.stringify(credentials),
		processData: false,
		*/
		data: credentials,
		xhrFields: {
			'withCredentials': true
		},
		headers: JSON.parse($("#external_headers").val()),
		success: function(data){
				$("#ifm").attr('src', $("#external_url").val());
				//var win = window.open($("#external_url").val());
		},
		error: function(xhr, text, error) {
			alert(text + ":" + error);
		}
	});


});
