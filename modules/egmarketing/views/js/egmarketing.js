/**
 * 
 */
$(document).ready(function() { 
	
	//loadCallme();
	//window.setTimeout('special()',10000);
	
	//metrika
	// callback
	$(document).on("click", '#callmebtn, #uptocall-mini', function(e){
		metrikaReach('callback');
	});
	
	$(document).on("click", '#add_to_cart button', function(e){
		metrikaReach('cart');
	}); 
	
	//confirm order
	$(window).load(function() {
		if (window.location.href.indexOf('order-confirmation') != -1)
		{
			metrikaReach('confirmorder');
			//dataPurchase();
		}
	}); 
	
	// fast order
	$(document).on('click', '#oorder', function(e){
		metrikaReach('fastorder');
		metrikaReach('cart');
		metrikaReach('confirmorder');
	}); 
	

	// analitica
	$(document).on("click", '.cart_block_list .ajax_cart_block_remove_link', function(e){
		dataRemove();
	}); 
	$(window).load(function(){
		//alert('pur');
	});	
		
});

function dataPurchaseFast(order, id_product, name, price_in, brand, category)
{
	var price = Math.floor(price_in);
	var variant = $(".attribute_select").find(":selected").text();
	var revenue = Math.floor(price/100*20);
	var shipping;
	if (Math.floor(priceWithDiscountsDisplay) >= egms_free_price )
		shipping = 0;
	else
		shipping = egms_delivery_price;
    dataPurchase(order, id_product, name, price, brand, category, variant, revenue, shipping);
/*	window.dataLayer.push({
	    "ecommerce": {
	        "purchase": {
	            "actionField": {
	                "id" : order,
	            },
	            "products": [
	                {
	                    "id": id_product,
	                    "name": name,
	                    "price": price,
	                    "brand": brand,
	                    "category": category,
	                    "variant": variant,
	                    "revenue": revenue,
	                    "shipping": shipping
	                }
	            ]
	        }
	    }
	});
	*/
	/*
	ga('ec:setAction', 'purchase', {
		  'id': order,
		  'affiliation': location.host,
		  'revenue': price+shipping,
		  'shipping': shipping
		});

	ga('send', 'pageview');
	*/  
}

function dataPurchase(order, id_product, name, price, brand, category, variant, revenue, shipping)
{
    window.dataLayer.push({
        "ecommerce": {
            "purchase": {
                "actionField": {
                    "id" : order,
                },
                "products": [
                    {
                        "id": id_product,
                        "name": name,
                        "price": price,
                        "brand": brand,
                        "category": category,
                        "variant": variant,
                        "revenue": revenue,
                        "shipping": shipping
                    }
                ]
            }
        }
    });
}

function dataDisplay(currency,host,id_product,name,price_in,brand,category)
{
	var price = Math.floor(price_in);
	var variant = $(".attribute_select").find(":selected").text();
	window.dataLayer.push({
	   "ecommerce": {
	   	"currencyCode": currency,
	       "detail": {
	           "actionField": {
	               "affiliation": host
	           },
	           "products": [{
		                    "id": id_product,
		                    "name": name,
		                    "price": price,
		                    "brand": brand,
		                    "category": category,
		                    "variant": variant
		                }]
		        }
		    }
	});
	window.dataLayer.push([{
		   "ecommerce": {
		   	"currencyCode": currency,
		       "detail": {
		           "actionField": {
		               "affiliation": host
		           },
		           "products": [{
			                    "id": id_product,
			                    "name": name,
			                    "price": price,
			                    "brand": brand,
			                    "category": category,
			                    "variant": variant
			                }]
			        }
			    }
		}]);	
/*	ga('ec:addProduct', {
		  'id': id_product,
		  'name': name, 
		  'category': category,   
		  'brand': brand,   
		  'variant': variant  
		});
		 
	ga('ec:setAction', 'detail');
	ga('send', 'pageview');*/
}

function dataAdd(id_product,name,price_in,brand,category,quantity)
{
	var price = Math.floor(price_in);
	var variant = $(".attribute_select").find(":selected").text();
	window.dataLayer.push({
	    "ecommerce": {
	        "add": {
	            "products": [
	                {
	                    "id": id_product,
	                    "name": name,
	                    "price": price,
	                    "brand": brand,
	                    "category": category,
	                    "variant": variant,
	                    "quantity": quantity
	                }
	            ]
	        }
	    }
	});	
	
	window.dataLayer.push([{
	    "ecommerce": {
	        "add": {
	            "products": [
	                {
	                    "id": id_product,
	                    "name": name,
	                    "price": price,
	                    "brand": brand,
	                    "category": category,
	                    "variant": variant,
	                    "quantity": quantity
	                }
	            ]
	        }
	    }
	}]);			
/*	
	ga('ec:addProduct', {
		  'id': id_product,
		  'name': name,
		  'category': category,
		  'brand': brand,
		  'variant': variant,
		  'price': price,
		  'quantity': quantity
		});
	ga('ec:setAction', 'add');
	ga('send', 'event', 'UX', 'click', 'add to cart');
	*/ 
}

function dataRemove(id, name, variant, quantity)
{
/*
	window.dataLayer.push({
	    "ecommerce": {
	        "remove": {
	            "products": [
	                {
	                    "id": id,
	                    "name": name,
	                    "variant": variant,
	                    "quantity": quantity
	                }
	            ]
	        }
	    }
	});
	*/		
}	

function cityQuestion()
{
	var y_city = ymaps.geolocation.city;
	var href = $('.city-view').attr('href')+'?json';
	var hrefq = $('.city-view').attr('href')+'?question='+encodeURIComponent(y_city);
	$.ajax({
         type: 'POST',
         dataType: "json",
         url: href,
         success: function(result) {
        	 var r = result.filter(function(city) {
        		  return city.city_name == y_city;
        	 });
        	 if (r.length>0){
        		 
				$.fancybox({
					'padding':  20,
    				'type':     'ajax',
    				'href':     hrefq+ "&region=" + r[0].id,
    			});	
        	 } else {
        		 $('.city-view:visible, .city-view-mobile:visible').click();
        	 }
         }
    });
}


function metrikaReach(goal_name) {
	for (var i in window) {
		if (/^yaCounter\d+/.test(i)) {
			window[i].reachGoal(goal_name);
		}
	}
}
