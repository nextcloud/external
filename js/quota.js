$(document).ready(function () {
	var $quotaLink = $('#quota_link'),
		$quotaBox = $('#quota');

	$quotaBox.after($quotaLink);
	$quotaLink.css('padding-top', 0);
	$quotaLink.removeClass('hidden');
});
