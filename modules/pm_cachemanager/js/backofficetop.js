function showCachePageLoader() {
	//Remove prev loader if exists
	if($('#pm_cache_page_loader').length) $('#pm_cache_page_loader').remove();
	//Body append loader
	$('body').append('<div id="pm_cache_page_loader" style="display:none;"><span id="msg_page_loader">'+pm_msgWaitClear+'</span></div>');
	//Hide body scroll
	$('body,html').css('overflow','hidden');
	$("#msg_page_loader").css("color","#333333");
	//Set page loader size recursively
	timerLoader = setInterval(function() {
		var documentHeight = $(document).height();
		var documentWidth = $(document).width();
		$('#pm_cache_page_loader').css('height',documentHeight+'px');
		$('#pm_cache_page_loader').css('width',documentWidth+'px');
	}, 100);
	//Show page loader
	$('#pm_cache_page_loader').fadeTo('slow', 0.9);
}
function hideCachePageLoader() {
	//Hide page loader
	setTimeout(function() {
		$('body,html').css('overflow','auto');
		if(typeof(timerLoader) != 'undefined' && timerLoader) { clearInterval(timerLoader); timerLoader = false;}
		if($('#pm_cache_page_loader').length) {
			$('#pm_cache_page_loader').fadeOut('slow',function() {$(this).remove();});
		}
	},500);
}
function clearAllCache(with_overlay) {
	if(with_overlay)
		showCachePageLoader();
	$.ajax( {
		type : "GET",
		url : pm_clearCacheUrl,
		dataType : "script",
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			if(with_overlay)
				hideCachePageLoader();
		}
	});
}
$(document).ready(function() {
	if ($("div#header ul#menu").size() > 0)
		$("div#header ul#menu").append("<li id=\"pm_cachemanagertab\" class=\"submenu_size maintab\"><a href=\"javascript:void(0);\" id=\"pm_clear_all_cache\" class=\"title\"><img src=\""+pm_clearCacheImg+"\" /><strong>"+pm_clearCacheLabel+"</strong></a></li>");
	else if ($("div#header_quick").size() > 0)
		$("div#header_quick").after("<a href=\"javascript:void(0);\" id=\"pm_clear_all_cache\" style=\"float: right;\" class=\"button\"><img src=\""+pm_clearCacheImg+"\" /><strong>"+pm_clearCacheLabel+"</strong></a>");
	else if ($("nav#nav-sidebar ul.menu").size() > 0)
		$("nav#nav-sidebar ul.menu").append("<li id=\"maintabPMCM\" class=\"maintab\"><a href=\"javascript:void(0);\" id=\"pm_clear_all_cache\" class=\"title\"><img src=\""+pm_clearCacheImg+"\" /><span>"+pm_clearCacheLabel+"</span></a></li>");
	$("#pm_clear_all_cache").click(function() {
		clearAllCache(true);
	});
});