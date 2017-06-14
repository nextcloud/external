$(document).ready(function () {

	var $p = $('<p>'),
		$quotaLink = $('#external_quota_link'),
		$quotaName = $('#external_quota_name');
	$p.text($quotaName.val());
	$('li#quota').on('click', function() {
		OC.redirect($quotaLink.val());
	});
	$('li#quota div.quota-container').after($p);
	var style = '#app-navigation li.nav-trashbin {margin-bottom: 36px !important; }';

	if ($('li.nav-trashbin').exists()) {
		style += ' #app-navigation > ul li:nth-last-child(2) { margin-bottom: 124px !important; }';
	} else {
		style += ' #app-navigation > ul li:nth-last-child(1) { margin-bottom: 80px !important; }';
	}
	$('head').append('<style type="text/css">' + style + '</style>');
});
