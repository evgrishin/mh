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
{if $scenes}
<div id="scenes">
	<div>
		{foreach $scenes as $scene_key=>$scene}
		<div class="screen_scene" id="screen_scene_{$scene->id}" style="background:transparent url({if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/{$scene->id}-scene_default.jpg); height:{$largeSceneImageType.height}px; width:{$largeSceneImageType.width}px;{if !$scene@first} display:none;{/if}">
			{foreach $scene->products as $product_key=>$product}
			{if isset($product.id_image)}
				{assign var=imageIds value="`$product.id_product`-`$product.id_image`"}
			{/if}
				<a href="{$product.link|escape:'html':'UTF-8'}" class="popover-button" style="width:{$product.zone_width}px; height:{$product.zone_height}px; left:{$product.x_axis}px ; top:{$product.y_axis}px;" data-id_product_scene="{$scene_key|intval}_{$product_key|intval}_{$product.id_product|intval}">
					<span style="margin-top:{math equation='a/2 -10' a=$product.zone_height}px; margin-left:{math equation='a/2 -10' a=$product.zone_width}px;"></span>
				</a>
				<div id="scene_products_cluetip_{$scene_key}_{$product_key}_{$product.id_product}" style="display:none;" class="scene_products_cluetip">
                	<div class="product-image-container">
						{if isset($imageIds)}
							<img class="img-responsive replace-2x" src="{$link->getImageLink($product.id_product, $imageIds, 'small_default')|escape:'html':'UTF-8'}" alt="" />
						{/if}
                    </div>
					<p class="product-name"><span class="product_name">{$product.details->name}</span></p>
                    {if !$PS_CATALOG_MODE AND $product.details->show_price}
					<div class="prices">
						<p class="price product-price">{if $priceDisplay}{convertPrice price=$product.details->getPrice(false, $product.details->getDefaultAttribute($product.id_product))}{else}{convertPrice price=$product.details->getPrice(true, $product.details->getDefaultAttribute($product.id_product))}{/if}</p>
					</div>
					{/if}
				</div>
			{/foreach}
		</div>
		{/foreach}
	</div>
	{if isset($scenes.1)}
	<div class="thumbs_banner clearfix">
		<div id="scenes_list" class="clearfix">
			<ul>
			{foreach $scenes as $scene}
				<li id="scene_thumb_{$scene->id}" style="{if !$scene@last} padding-right:10px;{/if}">
					<a style="width:{$thumbSceneImageType.width}px; height:{$thumbSceneImageType.height}px" title="{$scene->name|escape:'html':'UTF-8'}" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" data-id_scene="{$scene->id|intval}" class="scene_thumb">
						<img alt="{$scene->name|escape:'html':'UTF-8'}" src="{$content_dir}img/scenes/thumbs/{$scene->id}-m_scene_default.jpg" width="{$thumbSceneSize.width}" height="{$thumbSceneSize.height}" />
					</a>
				</li>
		 	{/foreach}
		 	</ul>
		</div>
	</div>
	{/if}
</div>
{strip}
{addJsDefL name=i18n_scene_close}{l s='Close' js=1}{/addJsDefL}
{addJsDef li_width=($thumbSceneImageType.width|intval+10)}
{/strip}
{/if}