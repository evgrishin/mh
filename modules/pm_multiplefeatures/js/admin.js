$(document).ready(function() {
	$('div#addons-rating-container p.dismiss a').click(function() {
		$('div#addons-rating-container').hide(500);
		$.ajax({type : "GET", url : window.location+'&dismissRating=1' });
		return false;
	});
});