{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{include file="$tpl_dir./errors.tpl"}
{if isset($category)}
	{if $category->id AND $category->active}
        <h1 class="page-heading{if (isset($subcategories) && !$products) || (isset($subcategories) && $products) || !isset($subcategories) && $products} product-listing{/if}">
            <span class="cat-name">{$filter_category_name|escape:'html':'UTF-8'} </span>
            {include file="$tpl_dir./category-count.tpl"}
        </h1>
		{if isset($subcategories)}
        {if (isset($display_subcategories) && $display_subcategories eq 1) || !isset($display_subcategories) }


		<!-- Subcategories -->	
        <div id="subcategories">
			<div class="clearfix row">
                <div style="margin: 15px;">
                    {if (isset($subcats))}
                        <p>{l s='popular'}</p>
                        {foreach from=$subcats item=subcat}
                            <a style="text-decoration: underline;display: inline-block;padding: 0 20px 7px 0;"
                               class="subcategory-name"
                               href="{$subcat.category_link}{$subcat.url}">{$subcat.link_name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                        {/foreach}
                        {**

                        {foreach from=$subcategories item=subcategory}
                            <a style="text-decoration: underline;display: inline-block;padding: 0 20px 7px 0;" class="subcategory-name" href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}">{$subcategory.name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
                        {/foreach}
                         **}
                    {/if}
                </div>
			</div>
		</div>
            {hook h='displayCategoryInfo'}
        {/if}
		{/if}

        {if !isset($filter_category_description)}
            {$filter_category_description = $category->description}
        {/if}

        {if $filter_category_description}
            <div class="cat_desc rte">
            {if Tools::strlen($category->description) > 350}
                <div id="category_description_short">{Tools::truncateString($filter_category_description)}</div>
                <div id="category_description_full" class="unvisible">{$filter_category_description}</div>
                <a href="{$link->getCategoryLink($category->id_category, $category->link_rewrite)|escape:'html':'UTF-8'}" class="lnk_more">{l s='More'}</a>
            {else}
                <div>{$filter_category_description}</div>
            {/if}
            </div>
        {/if}
        {hook h='displayCategoryHeader'}
		{if $products}
			{include file="$tpl_dir./sub/product/product-list-form.tpl"}
		{/if}
	{elseif $category->id}
		<p class="alert alert-warning">{l s='This category is currently unavailable.'}</p>
	{/if}
    {hook h='displayCategoryFooter'}
{/if}