{strip}
{addJsDef egmultishop_free_price=(int)$free_price}
{addJsDef egmultishop_delivery_price=(int)$delivery_price}
{/strip}
<p>
<span class="availability_date_label"><strong>{l s='delivery' mod='egmultishop'}</strong></span>
<span class="availability_value2" id="delivery_con"></span>
</p>

<script type="text/javascript">
$(document).ready(function(){
	updateDelivery();
	$(document).on('change', '.attribute_select', function(e){
		updateDelivery();
	});	
});

function updateDelivery(){
	if (Math.floor(priceWithDiscountsDisplay) >= egmultishop_free_price )
		$('#delivery_con').text('{l s='free' mod='egmultishop'}');
	else
		$('#delivery_con').text(formatCurrency(egmultishop_delivery_price, currencyFormat, currencySign, currencyBlank));
}
</script>