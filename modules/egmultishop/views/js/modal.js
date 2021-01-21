
$(document).ready(function() { 
	
	$(document).on('click', '.city-view:visible, .city-view-mobile:visible', function(e){
		e.preventDefault();
		var url = this.rel;
		var anchor = '';

		if (url.indexOf('#') != -1)
		{
			anchor = url.substring(url.indexOf('#'), url.length);
			url = url.substring(0, url.indexOf('#'));
		}

		if (url.indexOf('?') != -1)
			url += '&';
		else
			url += '?';
		
			if (!!$.prototype.fancybox)
				$.fancybox({
					'padding':  20,
					'type':     'ajax',
					'href':     url + 'content_only=1' + anchor
				});	
	});	
	
	$(document).on('click', '.setregion', function(e){
		e.preventDefault();
		var id_region = 0;
		if (this.id == "touch_city")
			id_region = $("#city_list" ).val();
		else
			id_region = (this.id).split("-")[1];
		if (id_region > 0)	
			setRegion(id_region);
	});
	
	  $(function () { 
		    $("[data-toggle='tooltip']").tooltip(); 
	});
});

function setRegion(region)
{
	$.getJSON( egmultishop_citycontroller + "?set_region="+region,
			function(data, status) {
      			location.href = "http://" + data.url + getAbsolutePath(); 
    		});	
}
function noRegion()
{
	parent.$.fancybox.close();
	  $.get( egmultishop_citycontroller + "?set_region=0",
		    function(data, status) {
		  		$('.city-view:visible, .city-view-mobile:visible').trigger('click'); 
		    });
}

function getAbsolutePath() {
    var loc = window.location;
    var pathName = loc.pathname;
    return loc.pathname + loc.search + loc.hash;
    //$("*").context.baseURI
}

