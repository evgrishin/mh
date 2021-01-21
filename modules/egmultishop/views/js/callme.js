/**
 * 
 */
$(document).ready(function() { 
	$("#callmeform").submit(function() { return false; });
	$(document).on('click', '#callmebtn', function(e){
		e.preventDefault();
		var url = this.rel+"?ajax";

		if (!!$.prototype.fancybox)
				$.fancybox({
					'padding':  20,
					'type':     'ajax',
					'href':     url
		});	
	});	
	$(document).on('click', '#submitcallme', function(e){
		e.preventDefault();
	    $("#submitcallme").prop( "disabled", true );

	    $.ajax({
	         type: 'POST',
	         url: $("#urlaction").val(),
	         data: $("#callmeform").serialize(),
	         success: function(data) {
	        	 $("#callmeform").fadeOut("fast", function(){
	        		 $("#callmemess").html(data);
	        		 setTimeout("$.fancybox.close()", 1500);
	        	 });
	         }
	    });		
	});	
});

/*
if(data == "true") {
	               $("#callmeform").fadeOut("fast", function(){
	                  setTimeout("$.fancybox.close()", 1000);
	               });
	            }
*/