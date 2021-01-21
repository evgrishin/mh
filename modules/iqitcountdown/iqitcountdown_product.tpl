<div class="price-countdown price-countdown-product" {if !isset($specific_prices.to) || (isset($specific_prices.to) && $specific_prices.to=='0000-00-00 00:00:00')} style="display: none;"{/if} >
<strong class="price-countdown-title">{l s='Special price ends on' mod='iqitcountdown'}:</strong>
<div class="count-down-timer" data-countdown="{if isset($specific_prices.to)}{$specific_prices.to}{/if}"> </div>
</div>




