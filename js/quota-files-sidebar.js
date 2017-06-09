$(document).ready(function () {

	var $p = $('<p>');
	$p.addClass('quotatext-additional').text(t('external', 'Do you need more space?'));
	$('li#quota').on('click', function(e) {
		OC.redirect(OC.generateUrl('/apps/external/1'));
	});
	$('li#quota div.quota-container').after($p);
});
