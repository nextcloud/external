$(document).ready(function () {
	var $quotaLink = $('#quota_link'),
		$quotaBox = $('#quota');

	$quotaBox.after($quotaLink);
	$quotaLink.removeClass('hidden section');
});
