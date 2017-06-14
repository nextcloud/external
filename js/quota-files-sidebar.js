$(document).ready(function () {

	var $p = $('<p>'),
		$quotaLink = $('#external_quota_link'),
		$quotaName = $('#external_quota_name');
	$p.text($quotaName.val());
	$('li#quota').on('click', function() {
		OC.redirect($quotaLink.val());
	});
	$('li#quota div.quota-container').after($p);
	$('li.nav-trashbin').attr('style', 'margin-bottom: 36px !important');

	if ($('li.nav-trashbin').exists()) {
		$('#app-navigation > ul li:nth-last-child(2)').attr('style', 'margin-bottom: 124px !important');
	} else {
		$('#app-navigation > ul li:nth-last-child(1)').attr('style', 'margin-bottom: 80px !important');

	}
});
