$(document).ready(function () {

	var $p = $('<p>'),
		$quotaLink = $('#external_quota_link'),
		$quotaName = $('#external_quota_name');
	$p.text($quotaName.val());
	$('li#quota').on('click', function() {
		OC.redirect($quotaLink.val());
	});
	$('li#quota div.quota-container').after($p);
});
