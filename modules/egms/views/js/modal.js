
$(document).ready(function() { 
	
	$(document).on('click', '.city-view:visible, .city-view-mobile:visible', function(e){
		e.preventDefault();
		var url = this.href;
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
        location.href = $("#city_list" ).val();

		/*
		var id_region = 0;
		if (this.id == "touch_city")
			id_region = $("#city_list" ).val();
		else
			id_region = (this.id).split("-")[1];
		if (id_region > 0)	
			setRegion(id_region);
			*/
	});

	  $(function () { 
		    $("[data-toggle='tooltip']").tooltip(); 
	});
});

function setRegion(region, link)
{
	$.getJSON( egms_citycontroller + "?set_region="+region,
			function(data, status) {
      			location.href = link;
    		});
    $('.cityname').show();
}

function noRegion()
{
	parent.$.fancybox.close();
	  $.get( egms_citycontroller + "?set_region=0",
		    function(data, status) {
		  		$('.city-view:visible, .city-view-mobile:visible').trigger('click'); 
		    });
   // $('.cityname').show();
}
/*
function getAbsolutePath() {
    var loc = window.location;
    var pathName = loc.pathname;
    return loc.pathname + loc.search + loc.hash;
    //$("*").context.baseURI
}
*/
