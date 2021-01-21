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


{include file="$tpl_dir./errors.tpl"}

{if !isset($errors) OR !sizeof($errors)}
	<h1 class="page-heading product-listing">
		{l s='List of products by manufacturer'}&nbsp;{$manufacturer->name|escape:'html':'UTF-8'}
					{assign var="myfile" value="img/m/{$manufacturer->id_manufacturer}-mf_image2.jpg"}
{if file_exists($myfile)}<img itemprop="image" class="pull-right" src="{$base_dir_ssl}img/m/{$manufacturer->id_manufacturer}-mf_image2.jpg" />
{/if}
	</h1>
	{if !empty($manufacturer->description) || !empty($manufacturer->short_description)}
		<div class="description_box clearfix rte">
			{if !empty($manufacturer->short_description)}
				<div class="short_desc">
					{$manufacturer->short_description}
				</div>
				<div class="hide_desc">
					{$manufacturer->description}
				</div>
				{if !empty($manufacturer->description)}
					<a href="#" class="lnk_more" onclick="$(this).prev().slideDown('slow'); $(this).hide();$(this).prev().prev().hide(); return false;">
						{l s='More'}
					</a>
				{/if}
			{else}
				<div>
					{$manufacturer->description}
				</div>
			{/if}
		</div>
	{/if}

	{if $products}
<div class="content_sortPagiBar clearfix">
            	<div class="sortPagiBar clearfix">
            		{include file="./product-sort.tpl"}
                	{include file="./nbr-product-page.tpl"}
                <div class="top-pagination-content clearfix">
                    {include file="$tpl_dir./pagination.tpl" no_follow=1}
                </div>
				</div>
			</div>
			{include file="./product-list.tpl" products=$products}
			<div class="content_sortPagiBar">
				<div class="bottom-pagination-content clearfix">
                    {include file="./pagination.tpl" paginationId='bottom' no_follow=1}
				</div>
			</div>
	{else}
		<p class="alert alert-warning">{l s='No products for this manufacturer.'}</p>
	{/if} 
{/if}
