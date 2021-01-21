{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if isset($products) && $products}
<div class="block_content {if ! isset($ar)} {if isset($warehouse_vars.carousel_style) && $warehouse_vars.carousel_style == 0}alternative-slick-arrows {/if}{/if}">
	<!-- Products list -->
	<div {if isset($accesories)}id="accesories-slick-slider"{/if} class="product_list_small {if isset($generatorGrid)}row  product_list_small_grid{/if} {if isset($iqitGenerator)} slick_carousel iqitcarousel{/if} slick_carousel_style"   
	{if isset($iqitGenerator)}data-slick='{literal}{{/literal}{if isset($dt) && $dt}"dots": true,{/if} {if isset($ap) && $ap}"autoplay": true, {/if}"slidesToShow": {$line_lg}, "slidesToScroll": {$line_lg}, "responsive": [ 
					{ "breakpoint": 1320, "settings": { "slidesToShow": {$line_md}, "slidesToScroll": {$line_md}}}, { "breakpoint": 1000, "settings": { "slidesToShow": {$line_sm}, "slidesToScroll": {$line_sm}}}, { "breakpoint": 768, "settings": { "slidesToShow": {$line_ms}, "slidesToScroll": {$line_ms}}}, { "breakpoint": 480, "settings": { "slidesToShow": {$line_xs}, "slidesToScroll": {$line_xs}}} ]{literal}}{/literal}'{/if} >
	{foreach from=$products item=product name=products}
	{if isset($colnb)}{if ($smarty.foreach.products.first)}<div class="iqitcarousel-product">{/if}{/if}
		<div class="ajax_block_product {if isset($generatorGrid)} {$generatorGrid} {/if} {if isset($colnb)} {$colnb}{/if}">
			<div class="product-container clearfix">
				<div class="left-block">
					<div class="product-image-container">
						{if (isset($product.quantity) && $product.quantity > 0) || (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
							{hook h='displayProductAttributesPL' productid=$product.id_product}
						{/if}
						<a class="product_img_link"	href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" >
							{if isset($productimg[$product.id_product]) && !empty($productimg[$product.id_product])}
							<img class="replace-2x img-responsive img_0 {if isset($productimg[$product.id_product].1) && !empty($productimg[$product.id_product].1)} img_1e{/if}" src="{$link->getImageLink($product.link_rewrite,$product.id_product|cat:"-"|cat:$productimg[$product.id_product].0.id_image, 'medium_default')|escape:'html':'UTF-8'}
							" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if}  />	
							{if isset($productimg[$product.id_product].1) && !empty($productimg[$product.id_product].1)}
							<img class="replace-2x img-responsive img_1" src="{$link->getImageLink($product.link_rewrite,$product.id_product|cat:"-"|cat:$productimg[$product.id_product].1.id_image, 'medium_default')|escape:'html':'UTF-8'}
							" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if}  />	
							{/if} {else}
							<img class="replace-2x img-responsive img_0" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'medium_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} />
						{/if}
						</a>
					</div>
					{if isset($product.is_virtual) && !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
					{hook h="displayProductPriceBlock" product=$product type="weight"}
				</div>
				<div class="right-block">
					<h5 class="product-name-container">
						{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
						<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}"  >
							{$product.name|truncate:60:'...'|escape:'html':'UTF-8'}
						</a>
					</h5>
					{hook h='displayProductListReviews' product=$product}			
					{if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
					<div  class="content_price">
						{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
							<span class="price product-price">
								{hook h="displayProductPriceBlock" product=$product type="before_price"}
								{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
							</span>
							<meta  content="{$currency->iso_code}" />
							{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
								{hook h="displayProductPriceBlock" product=$product type="old_price"}
								<span class="old-price product-price">
									{displayWtPrice p=$product.price_without_reduction}
								</span>
							{/if}
									{hook h="displayProductPriceBlock" product=$product type="price"}
									{hook h="displayProductPriceBlock" product=$product type="unit_price"}
						{/if}
					</div>
					{elseif $PS_CATALOG_MODE}
					{else}<div class="content_price">&nbsp;</div>
					{/if}

					{if isset($product.available_for_order) && isset($product.allow_oosp)}
					<div class="button-container">
						{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.customizable != 2 && !$PS_CATALOG_MODE}
							{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
								{capture}add=1&amp;id_product={$product.id_product|intval}{if isset($static_token)}&amp;token={$static_token}{/if}{/capture}
								<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart', true, NULL, $smarty.capture.default, false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart'}" data-id-product-attribute="{$product.id_product_attribute|intval}" data-id-product="{$product.id_product|intval}" data-minimal_quantity="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity >= 1}{$product.product_attribute_minimal_quantity|intval}{else}{$product.minimal_quantity|intval}{/if}">
									<span>{l s='Add to cart'}</span>
								</a>
								<div class="pl-quantity-input-wrapper">
									<input type="text" name="qty" class="form-control qtyfield quantity_to_cart_{$product.id_product|intval}"  value="{if isset($product.product_attribute_minimal_quantity) && $product.product_attribute_minimal_quantity > 1}{$product.product_attribute_minimal_quantity|intval}{else}{if isset($product.minimal_quantity)}{$product.minimal_quantity|intval}{else}1{/if}{/if}"/>
									<div class="quantity-input-b-wrapper">
										<a href="#" data-field-qty="quantity_to_cart_{$product.id_product|intval}" class="transition-300 pl_product_quantity_down">
											<span><i class="icon-caret-down"></i></span>
										</a>
										<a href="#" data-field-qty="quantity_to_cart_{$product.id_product|intval}" class="transition-300 pl_product_quantity_up ">
											<span><i class="icon-caret-up"></i></span>
										</a>
									</div>
								</div>
							{else}
								<a  class="button lnk_view btn" href="{$product.link|escape:'html':'UTF-8'}" title="{l s='View'}">
							<span>{if (isset($product.customization_required) && $product.customization_required)}{l s='Customize'}{else}{l s='More'}{/if}</span>
						</a>
							{/if}
							{else}
								<a class="button lnk_view btn" href="{$product.link|escape:'html':'UTF-8'}" title="{l s='View'}">
							<span>{if (isset($product.customization_required) && $product.customization_required)}{l s='Customize'}{else}{l s='More'}{/if}</span>
						</a>
						{/if}
						{hook h="displayProductPriceBlock" product=$product type='after_price'}
					</div>
					{/if}
					
				
				</div>

			</div><!-- .product-container> -->
		
		</div>
		{if isset($colnb)}
		{if ($smarty.foreach.products.iteration%$colnb == 0) && !$smarty.foreach.products.last}</div><div class="iqitcarousel-product">{/if}
		{if $smarty.foreach.products.last}</div>{/if}
		{/if}
	{/foreach}
	</div>
</div>
{/if}