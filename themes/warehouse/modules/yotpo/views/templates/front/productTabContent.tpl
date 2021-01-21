
	{if isset($warehouse_vars.product_tabs) && $warehouse_vars.product_tabs}
	<section class="page-product-box tab-pane fade" id="idTab-yotpo">
	{else}
	<section class="page-product-box" id="idTab-yotpo">
	<h3 class="page-product-heading">{$yotpoWidgetTabName|escape:'html':'UTF-8'}</h3>
	{/if}
	{include file="$yotpoContentTplPath"}	
</section>