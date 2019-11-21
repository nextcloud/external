$(document).ready(function () {

	var $p = document.createElement('p'),
		$quotaLink = $('#external_quota_link'),
		$quotaName = $('#external_quota_name');
	$p.innerText = $quotaName.val();
	$p.setAttribute('style', 'margin-top: -22px');
	$('li#quota').on('click', function() {
		OC.redirect($quotaLink.val());
	});
	document.getElementById('quota').getElementsByTagName('a')[0].appendChild($p);
});
