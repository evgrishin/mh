{*
	************************
		Creat by leo themes
	*************************
*}
{if !isset($callFromModule) || $callFromModule==0}
    {include file="$tpl_dir./layout/setting.tpl"}
{/if}
{**if !isset($moduleCalling) || $moduleCalling != "blocklayered"}
{hook h='displayProductListLayered' product=$product}
{else**}
<div class="product-container product-block">
	<div class="left-block">
		<div class="product-image-container image">
		    <div class="leo-more-info" data-idproduct="{$product.id_product}"></div>
			<a class="product_img_link" href="{$link->getProductLink($product.id_product, null, null, null, null, null, $product.id_product_attribute, Configuration::get('PS_REWRITING_SETTINGS'), false, true)|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}">
				<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" />
				<span class="product-additional" data-idproduct="{$product.id_product}"></span>
			</a>
            {hook h='displayProductAdv'}
			{if isset($product.new) && $product.new == 1}				
				<span class="label label-new">{l s='New'}</span>				
			{/if}
			{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
				<span class="label label-sale">{l s='Sale!'}</span>
			{/if}
			{if isset($product.condition) && $product.condition == 'gift'}
				<div tabindex="0" id="special-label-{$product.id_product|intval}" class="special-label-{$product.id_product|intval} focus-off" data-toggle="popover" data-container="body" data-html="true" style="position: absolute;top: 2px;left: 2px;width:87px;height:87px;background-image: url(../img/5.png);"></div>
			{/if}
			{if isset($product.condition) && $product.condition == 'best'}
			<div data-placement="right" style="position: absolute;top: 10px;left: 2px;width:90px;height:90px;background-image: url(../img/best.png)"></div>			
			{/if}			
			{if isset($product.condition) && $product.condition == 'garaty'}
			<div data-placement="right" style="position: absolute;top: 10px;left: 2px;width:90px;height:90px;background-image: url(../img/garanty.png)"></div>			
			{/if}			
		</div>

		{hook h="displayProductPriceBlock" product=$product type="weight"}
		
		<div class="content-buttons clearfix">

			{if isset($quick_view) && $quick_view}
				<div class="btn-small">
					<a class="btn quick-view" href="{$link->getProductLink($product.id_product, null, null, null, null, null, $product.id_product_attribute, Configuration::get('PS_REWRITING_SETTINGS'), false, true)|escape:'html':'UTF-8'}" title="{l s='Quick view'}">
					<i class="fa fa-eye"></i>
				</a>
			</div>
			{/if}

			{if $ENABLE_WISHLIST}			
			<div class="wishlist btn-small">					
				{hook h='displayProductListFunctionalButtons' product=$product}				
			</div>	
			{/if}

            {if isset($comparator_max_item) && $comparator_max_item}
				<a class="add_to_compare compare btn btn-outline" href="{$link->getProductLink($product.id_product, null, null, null, null, null, $product.id_product_attribute, Configuration::get('PS_REWRITING_SETTINGS'), false, true)|escape:'html':'UTF-8'}}"
				   data-id-product="{$product.id_product}" title="{l s='Add to compare' mod='blocklayered'}">
					<i class="fa fa-align-center"></i> 
				</a>										
			{/if}

		</div>	
	</div>

	<div class="right-block">
		<div class="product-meta">
			<div class="product-title">
				<h5 class="name">
					{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
					<a class="product-name" href="{$link->getProductLink($product.id_product, null, null, null, null, null, $product.id_product_attribute, Configuration::get('PS_REWRITING_SETTINGS'), false, true)|escape:'html':'UTF-8'}">
						{$product.name|truncate:90:'...'|escape:'html':'UTF-8'}
					</a>
				</h5>
				{if !isset($moduleCalling) || $moduleCalling != "productcategory"}
					{hook h='displayProductListReviews' product=$product}
				{/if} 
			</div>
			<div class="product-desc">
                {$product.description_short}
			</div>
			<div class="product-features" style="">
				<!--p-->
                {$features_count = 0}
                {foreach from=$product.features item=feature name=features}

                    {if ($feature.id_category_default == $product.id_category_default)}
                        {if ($feature.show_in_text == 1)}
                            {if ($features_count > 0)}<br>{/if}
                            {$feature.name|escape:'htmlall':'UTF-8'}: {$feature.value|escape:'htmlall':'UTF-8'}
                            {$features_count = $features_count + 1}
                        {/if}

                    {/if}
                {/foreach}

			</div>

            {hook h="displayProductDeliveryTime" product=$product}

			{if isset($product.color_list) && $ENABLE_COLOR}
				<div class="color-list-container">{$product.color_list} </div>
			{/if}
			
<div class="functional-buttons">
{if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
<div class="content_price">
{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
  {foreach from=$product.attributes item='attribute'}
   <div style="font-size:12px;font-weight: bold;">{$attribute.name}: {$attribute.value}</div>
  {/foreach}
  {if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
   {hook h="displayProductPriceBlock" product=$product type="old_price"}
   <span class="old-price product-price">{displayWtPrice p=$product.price_without_reduction}</span>
  {/if}								
							
  <nobr><span class="price product-price">
  {if !isset($product.attributes)}
  {l s='price from'}
  {/if}							
  {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
  </span></nobr>
  {if $product.specific_prices.reduction_type == 'percentage'}
  <span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
  {/if}															
  {hook h="displayProductPriceBlock" product=$product type="price"}
  {hook h="displayProductPriceBlock" product=$product type="unit_price"}
{/if}
</div>
    {**/if**}

{if $page_name !='product'}	
					<div class="cart">
						{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
							{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
								{if isset($static_token)}
									<a class="button ajax_add_to_cart_button btn" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$product.id_product|intval}">
										<i class="fa fa-shopping-cart"></i><span>{l s='Add to cart'}</span>
									</a>
								{else}
									<a class="button ajax_add_to_cart_button btn" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product="{$product.id_product|intval}">
										<i class="fa fa-shopping-cart"></i><span>{l s='Add to cart'}</span>
									</a>
								{/if}
							{/if}
						{/if}
					</div>
					<div class="view">
						<a class="button btn btn-outline" href="{$link->getProductLink($product.id_product, null, null, null, null, null, $product.id_product_attribute, Configuration::get('PS_REWRITING_SETTINGS'), false, true)|escape:'html':'UTF-8'}" title="{l s='View'}">
							<span>{l s='More'}</span>
					</a>
					</div>  
{/if}

</div>

		</div> <!-- end product meta -->
		
	</div>
</div>

{/if}