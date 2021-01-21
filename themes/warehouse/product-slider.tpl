	{if isset($products) && $products}
	<div class="block_content">
		{assign var='nbItemsPerLine' value=4}
		{assign var='nbLi' value=$products|@count}




		<div {if isset($id) && $id} id="{$id}"{/if} {if ! isset($ar)} {if isset($warehouse_vars.carousel_style) && $warehouse_vars.carousel_style == 0} class="alternative-slick-arrows" {/if}{/if} >
			<div class="slick_carousel {if isset($iqitGenerator)}iqitcarousel{else}slick_carousel_defaultp{/if} slick_carousel_style " {if isset($iqitGenerator)}data-slick='{literal}{{/literal}{if $dt}"dots": true, {/if}{if $ap}"autoplay": true, {/if}"slidesToShow": {$line_lg}, "slidesToScroll": {$line_lg}, "responsive": [ 
					{ "breakpoint": 1320, "settings": { "slidesToShow": {$line_md}, "slidesToScroll": {$line_md}}}, { "breakpoint": 1000, "settings": { "slidesToShow": {$line_sm}, "slidesToScroll": {$line_sm}}}, { "breakpoint": 768, "settings": { "slidesToShow": {$line_ms}, "slidesToScroll": {$line_ms}}}, { "breakpoint": 480, "settings": { "slidesToShow": {$line_xs}, "slidesToScroll": {$line_xs}}} ]{literal}}{/literal}'{/if} >
				{foreach from=$products item=product name=homeFeaturedProducts}
				{if isset($colnb)}{if ($smarty.foreach.homeFeaturedProducts.first)}<div class="iqitcarousel-produc">{/if}{/if}
				<div class="ajax_block_product {if $smarty.foreach.homeFeaturedProducts.first}first_item{elseif $smarty.foreach.homeFeaturedProducts.last}last_item{else}item{/if} {if $smarty.foreach.homeFeaturedProducts.iteration%$nbItemsPerLine == 0}last_item_of_line{elseif $smarty.foreach.homeFeaturedProducts.iteration%$nbItemsPerLine == 1} {/if} {if $smarty.foreach.homeFeaturedProducts.iteration > ($smarty.foreach.homeFeaturedProducts.total - ($smarty.foreach.homeFeaturedProducts.total % $nbItemsPerLine))}last_line{/if}">
					<div class="product-container">
						
						<div class="product-image-container">
						{if (isset($product.quantity) && $product.quantity > 0) || (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
							{hook h='displayProductAttributesPL' productid=$product.id_product}
						{/if}
						<a class="product_img_link"	href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" >
							{if isset($productimg[$product.id_product]) && !empty($productimg[$product.id_product])}
							<img class="replace-2x img-responsive img_0 {if isset($productimg[$product.id_product].1) && !empty($productimg[$product.id_product].1)} img_1e{/if}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if}
							data-lazy="{$link->getImageLink($product.link_rewrite,$product.id_product|cat:"-"|cat:$productimg[$product.id_product].0.id_image, 'home_default')|escape:'html':'UTF-8'}"							
							 />	
							{if isset($productimg[$product.id_product].1) && !empty($productimg[$product.id_product].1)}
							<img class="replace-2x img-responsive img_1" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if}  data-lazy="{$link->getImageLink($product.link_rewrite,$product.id_product|cat:"-"|cat:$productimg[$product.id_product].1.id_image, 'home_default')|escape:'html':'UTF-8'}
							" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}g"
							/>	
							{/if} {else}
							<img class="replace-2x img-responsive img_0" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}"
							{if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} />{/if}
							{hook h='displayCountDown' product=$product}
						</a>
						<div class="product-flags">
						{if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
							{if isset($product.online_only) && $product.online_only}
								<span class="online-label {if isset($product.new) && $product.new == 1}online-label2{/if}">{l s='Online only'}</span>
							{/if}
						{/if}
						{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
							{elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
								<span class="sale-label">{l s='Reduced price!'}</span>
							{/if}
						{if isset($product.new) && $product.new == 1}
								<span class="new-label">{l s='New'}</span>
						{/if}
						{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
								<span class="sale-label">{l s='Sale!'}</span>
						{/if}
					</div>
					<div class="functional-buttons functional-buttons-grid clearfix">
						{if isset($quick_view) && $quick_view}
						<div class="quickview col-xs-6">
							<a class="quick-view" href="{$product.link|escape:'html':'UTF-8'}" rel="{$product.link|escape:'html':'UTF-8'}" title="{l s='Quick view'}">
								{l s='Quick view'}
							</a>
							</div>
						{/if}
						{hook h='displayProductListFunctionalButtons' product=$product}
						{if isset($comparator_max_item) && $comparator_max_item}
							<div class="compare col-xs-3">
								<a class="add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}" title="{l s='Add to Compare'}">{l s='Add to Compare'}</a>
							</div>
						{/if}	
					</div>
				{if isset($allow_oosp)}
					{if (!$PS_CATALOG_MODE && $PS_STOCK_MANAGEMENT && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
						{if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
							<span  class="availability availability-slidein">
								{if ($product.allow_oosp || $product.quantity > 0)}
										<link  href="http://schema.org/InStock" />
								{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
									<span class="out-of-stock available-dif">
										<link  href="http://schema.org/LimitedAvailability" />{l s='Product available with different options'}
									</span>
								{else}
									<span class="out-of-stock">
										<link />{l s='Out of stock'}
									</span>
								{/if}
							</span>
						{/if}
					{/if}
				{/if}
				
								{if isset($product.color_list)}
						<div class="color-list-container">{$product.color_list} </div>
					{/if}
					</div><!-- .product-image-container> -->
					{if isset($product.is_virtual) && !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
					{hook h="displayProductPriceBlock" product=$product type="weight"}
					<h5  class="product-name-container">
						{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
					<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" >
							{$product.name|truncate:60:'...'|escape:'html':'UTF-8'}
						</a>
					</h5>
					{if isset($product.reference)}<span class="product-reference">{$product.reference}</span>{/if}
						{if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
					<div class="content_price">
						{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
							<span  class="price product-price">
								{hook h="displayProductPriceBlock" product=$product type="before_price"}
								{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
							</span>
							
							{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
								{hook h="displayProductPriceBlock" product=$product type="old_price"}
								<span class="old-price product-price">
									{displayWtPrice p=$product.price_without_reduction}
								</span>
								{if $product.specific_prices.reduction_type == 'percentage'}
									<span class="price-percent-reduction small">-{$product.specific_prices.reduction * 100}%</span>
								{/if}
								{if $product.specific_prices.reduction_type == 'amount'}
									<span class="price-percent-reduction small">-{convertPrice price=$product.price_without_reduction|floatval-$product.price|floatval}</span>
								{/if}
							{/if}
									{hook h="displayProductPriceBlock" product=$product type="price"}
									{hook h="displayProductPriceBlock" product=$product type="unit_price"}
									
						{/if}
					</div>
					{elseif $PS_CATALOG_MODE}
					{else}<div class="content_price">&nbsp;</div>
					{/if}
					{hook h='displayProductListReviews' product=$product}

					{if isset($warehouse_vars.yotpo_stars)  && $warehouse_vars.yotpo_stars == 1}
					<div class="yotpo bottomLine" 
					data-appkey="{$yotpoAppkey}"
					data-domain="{$yotpoDomain}"
					data-product-id="{$product.id_product}"
					data-product-models=""
					data-name="{$product.name|escape:'htmlall':'UTF-8'}" 
					data-url="{$product.link|escape:'htmlall':'UTF-8'}" 
					data-image-url="{$link->getImageLink($product.link_rewrite, $product.id_image, '')}" 
					data-bread-crumbs="">
				</div> 
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
					
					</div><!-- .product-container> -->


					</div>
					{if isset($colnb)}
					{if ($smarty.foreach.homeFeaturedProducts.iteration%$colnb == 0) && !$smarty.foreach.homeFeaturedProducts.last}</div><div class="iqitcarousel-product">{/if}
					{if $smarty.foreach.homeFeaturedProducts.last}</div>{/if}{/if}
					{/foreach}
				</div>
			</div>
		</div>

{/if}