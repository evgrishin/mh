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
{if isset($category)}
	{if $category->id AND $category->active}
    	{if $scenes || $category->id_image}
			<div class="content_scene_cat     {if $scenes} content_scene_cat_scene{/if}">
            	 {if $scenes}
                 	<div class="content_scene">
                        <!-- Scenes -->
                        {include file="$tpl_dir./scenes.tpl" scenes=$scenes}
                        </div>
                    {else}
                    <!-- Category image -->
                   <div class="content_scene_cat_bg">
                    {if $category->id_image}
                    <img src="{$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')|escape:'html':'UTF-8'}" alt="" class="img-responsive cat_img_bg"/>{/if}
                  {if isset($warehouse_vars.desc_style) && $warehouse_vars.desc_style == 0 && $warehouse_vars.show_desc} 
                          {if $category->description}
                            <div class="cat_desc">
                            {if Tools::strlen($category->description) > 350}
                                <div id="category_description_short" class="rte">{$description_short}</div>
                            {else}
                                <div class="rte">{$category->description}</div>
                            {/if}
                            </div>
                        {/if}
                     {/if}
                     </div>
                  {/if}
            </div>
		{/if}
		<div class="page-heading{if (isset($subcategories) && !$products) || (isset($subcategories) && $products) || !isset($subcategories) && $products} product-listing{/if} clearfix"><h1 class="page-heading{if (isset($subcategories) && !$products) || (isset($subcategories) && $products) || !isset($subcategories) && $products} product-listing{/if}"><span class="cat-name">{$category->name|escape:'html':'UTF-8'}{if isset($categoryNameComplement)}&nbsp;{$categoryNameComplement|escape:'html':'UTF-8'}{/if}</span></h1>{include file="$tpl_dir./category-count.tpl"}</div>

        {if isset($warehouse_vars.desc_style) && $warehouse_vars.desc_style != 2}
        {if ((isset($warehouse_vars.desc_style) && $warehouse_vars.desc_style == 1  && $warehouse_vars.show_desc) && $category->description) || ($category->description && !$scenes && !$category->id_image && $warehouse_vars.show_desc)} 

                    {if $category->description}
                            <div class="cat_desc cat_desc_container clearfix">
                            {if Tools::strlen($category->description) > 350}
                                <div id="category_description_short" class="rte">{$description_short}</div>
                                <div id="category_description_full" class="unvisible rte">{$category->description}
                                <a onclick="$(this).parent().hide();  $(this).parent().prev().show(); $(this).parent().parent().find('.lnk_more').show();  return false;" href="{$link->getCategoryLink($category->id_category, $category->link_rewrite)|escape:'html':'UTF-8'}" class="btn btn-default pull-right" c><i class="icon-plus"></i> {l s='Less'}</a>
                                </div>
                                <a href="{$link->getCategoryLink($category->id_category, $category->link_rewrite)|escape:'html':'UTF-8'}"  class="lnk_more btn btn-default pull-right"><i class="icon-plus"></i> {l s='More'}</a>
                            {else}
                                <div class="rte">{$category->description}</div>
                            {/if}
                            </div>
                        {/if}

        {/if}
        {/if}
		      {if isset($warehouse_vars.show_subcategories) && $warehouse_vars.show_subcategories == 1} 
        {if isset($subcategories)}
		<!-- Subcategories -->
		<div id="subcategories">
			<ul class="row clearfix">
			{foreach from=$subcategories item=subcategory}
				<li class="col-xs-{$warehouse_vars.subcats_grid_size_xs} col-sm-{$warehouse_vars.subcats_grid_size_sm} col-md-{$warehouse_vars.subcats_grid_size_md}">
                	<div class="subcategory-image">
						<a href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategory.name|escape:'html':'UTF-8'}" class="img">
						{if $subcategory.id_image}
							<img class="img-responsive replace-2x" src="{$link->getCatImageLink($subcategory.link_rewrite, $subcategory.id_image, 'category_thumbs')|escape:'html':'UTF-8'}" alt="" />
						{else}
							<img class="img-responsive replace-2x" src="{$img_cat_dir}{$lang_iso}-default-category_thumbs.jpg" alt="" />
						{/if}
					</a>
                   	</div>
					<h5><a class="subcategory-name" href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}">{$subcategory.name|truncate:25:'...'|escape:'html':'UTF-8'|truncate:350}</a></h5>
				</li>
			{/foreach}
			</ul>
		</div>
		{/if}
            {/if}
		{if $products}
			<div class="content_sortPagiBar clearfix">
            	<div class="sortPagiBar clearfix">
            		{include file="./product-sort.tpl"}
                	{include file="./nbr-product-page.tpl"}
                    {hook h='aboveProductList'}
                <div class="top-pagination-content clearfix">
                    {include file="$tpl_dir./pagination.tpl"}
                </div>
				</div>
			</div>
			{include file="./product-list.tpl" products=$products}
			<div class="content_sortPagiBar">
				<div class="bottom-pagination-content clearfix">
                    {include file="./pagination.tpl" paginationId='bottom'}
				</div>
			</div>
		{/if}
        {if isset($warehouse_vars.desc_style) && $warehouse_vars.desc_style != 1}
     {if ((isset($warehouse_vars.desc_style) && $warehouse_vars.desc_style == 2  && $warehouse_vars.show_desc) && $category->description) || ($category->description && !$scenes && !$category->id_image && $warehouse_vars.show_desc)} 

                    {if $category->description}
                            <div class="cat_desc cat_desc_container clearfix">
                            {if Tools::strlen($category->description) > 350}
                                <div id="category_description_short" class="rte">{$description_short}</div>
                                <div id="category_description_full" class="unvisible rte">{$category->description}
                                <a onclick="$(this).parent().hide();  $(this).parent().prev().show(); $(this).parent().parent().find('.lnk_more').show();  return false;" href="{$link->getCategoryLink($category->id_category, $category->link_rewrite)|escape:'html':'UTF-8'}" class="btn btn-default pull-right" c><i class="icon-plus"></i> {l s='Less'}</a>
                                </div>
                                <a href="{$link->getCategoryLink($category->id_category, $category->link_rewrite)|escape:'html':'UTF-8'}"  class="lnk_more btn btn-default pull-right"><i class="icon-plus"></i> {l s='More'}</a>
                            {else}
                                <div class="rte">{$category->description}</div>
                            {/if}
                            </div>
                        {/if}

        {/if}    {/if} 
	{elseif $category->id}
		<p class="alert alert-warning">{l s='This category is currently unavailable.'}</p>
	{/if}
{/if}