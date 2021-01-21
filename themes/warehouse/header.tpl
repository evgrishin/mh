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
<!DOCTYPE HTML>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<html{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}>
	<head>
		<meta charset="utf-8" />
		<title>{$meta_title|escape:'html':'UTF-8'}</title>
		{if isset($meta_description) AND $meta_description}
			<meta name="description" content="{$meta_description|escape:'html':'UTF-8'}" />
		{/if}
		{if isset($meta_keywords) AND $meta_keywords}
			<meta name="keywords" content="{$meta_keywords|escape:'html':'UTF-8'}" />
		{/if}
		<meta name="generator" content="PrestaShop" />
		<meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
		<meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width">
		<meta name="apple-mobile-web-app-capable" content="yes" /> 
		<link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url}?{$img_update_time}" />
		<link rel="shortcut icon" type="image/x-icon" href="{$favicon_url}?{$img_update_time}" />
		{if isset($css_files)}
			{foreach from=$css_files key=css_uri item=media}
				<link rel="stylesheet" href="{$css_uri|escape:'html':'UTF-8'}" type="text/css" media="{$media|escape:'html':'UTF-8'}" />
			{/foreach}
		{/if}
		{if isset($js_defer) && !$js_defer && isset($js_files) && isset($js_def)}
			{$js_def}
			{foreach from=$js_files item=js_uri}
			<script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
			{/foreach}
		{/if}
		{$HOOK_HEADER}

		{if isset($warehouse_vars.font_include)}
			{foreach from=$warehouse_vars.font_include item=font name=fonts}
				<link rel="stylesheet" href="http{if Tools::usingSecureMode()}s{/if}://{$font}" type="text/css" media="all" />
			{/foreach}
		{/if}

		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
		<meta property="og:title" content="{$meta_title|escape:'htmlall':'UTF-8'}"/>
		<meta property="og:url" content="http://{$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}"/>
		<meta property="og:site_name" content="{$shop_name|escape:'htmlall':'UTF-8'}"/>
		
		<meta property="og:description" content="{$meta_description|escape:html:'UTF-8'}">
		{if $page_name=='product'}
		<meta property="og:type" content="product">
		{if isset($have_image)}
		{if $have_image.0}<meta property="og:image" content="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')}">{/if}
		{else}
		<meta property="og:image" content="{$img_ps_dir}logo.jpg" />
		{/if}
		{else}
		<meta property="og:type" content="website">
		<meta property="og:image" content="{$img_ps_dir}logo.jpg" />
		{/if}
	</head>
	<body{if isset($page_name)} id="{$page_name|escape:'html':'UTF-8'}"{/if} class="{if !isset($page_name) || (isset($page_name) && $page_name != 'index')}not-index {/if}{if isset($page_name)}{$page_name|escape:'html':'UTF-8'}{/if}{if isset($body_classes) && $body_classes|@count} {implode value=$body_classes separator=' '}{/if}{if $hide_left_column} hide-left-column{/if}{if $hide_right_column} hide-right-column{/if}{if !$hide_left_column} show-left-column{/if}{if !$hide_right_column} show-right-column{/if} {if isset($content_only) && $content_only} content_only{/if} lang_{$lang_iso}  {if isset($warehouse_vars.is_rtl) && $warehouse_vars.is_rtl} is_rtl{/if} 	{if $is_logged} isLogged{/if} {if isset($warehouse_vars.header_style) && $warehouse_vars.header_style == 1} is-sidebar-header{/if}">
	{if !isset($content_only) || !$content_only}
	{if isset($warehouse_vars.preloader) && $warehouse_vars.preloader}
	<div id="preloader">
	<div id="status">&nbsp;</div>
	</div>
	{/if}
	{hook h='freeFblock'}
	{if isset($restricted_country_mode) && $restricted_country_mode}
	<div id="restricted-country">
		<p>{l s='You cannot place a new order from your country.'}{if isset($geolocation_country) && $geolocation_country} <span class="bold">{$geolocation_country|escape:'html':'UTF-8'}</span>{/if}</p>
	</div>
	{/if}

	<div id="page">
		<div class="header-container{if isset($warehouse_vars.header_style)}{if $warehouse_vars.header_style == 1} sidebar-header{elseif $warehouse_vars.header_style == 2} inline-header{/if}{/if}{if isset($warehouse_vars.cart_style) && $warehouse_vars.cart_style == 1} alt-cart{/if}">
			<header id="header">
		
			
						<div class="banner">
					<div class="container">
						<div class="row">
							{hook h="displayBanner"}
						</div>
					</div>
				</div>
					{if isset($warehouse_vars.top_width) && $warehouse_vars.top_width == 1  && $warehouse_vars.top_bar}
				<div class="nav">
					<div class="container">
						<div class="row">
							<nav>
								{hook h="displayNav"}
								{include file="./product-compare.tpl"}
								<a href="{$link->getModuleLink('blockwishlist', 'mywishlist', array(), true)}" title="{l s='My wishlist'}" class="wishlist_top_link pull-right"><i class="icon-heart-empty"></i>  {l s='My wishlist'}</a>
							</nav>
						</div>
					</div>
				</div>
				{/if}
				<div>
					<div class="container container-header">
										{if isset($warehouse_vars.top_width) && $warehouse_vars.top_width == 0 && $warehouse_vars.top_bar}
				<div class="nav">
						<div class="row">
							<nav>
								{hook h="displayNav"}
								{include file="./product-compare.tpl"}
								<a href="{$link->getModuleLink('blockwishlist', 'mywishlist', array(), true)}" title="{l s='My wishlist'}" class="wishlist_top_link pull-right"><i class="icon-heart-empty"></i>  {l s='My wishlist'}</a>
							</nav>
						</div>
					
				</div>
				{/if}
			
					{if isset($warehouse_vars.header_style) && $warehouse_vars.header_style == 2}
					<div class="row">
					<div class="inline-table">
					<div class="inline-row">
					<div id="header_logo" class="col-xs-12 inline-cell">

						<a href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{$shop_name|escape:'html':'UTF-8'}">
							<img class="logo img-responsive replace-2xlogo" src="{$logo_url}" {if isset($warehouse_vars.retina_logo) and $warehouse_vars.retina_logo}data-retinalogo="{$warehouse_vars.retina_logo}" {/if} {if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if} alt="{$shop_name|escape:'html':'UTF-8'}" />
						</a>
					</div> 
					<div class="inline-cell display-menu">
						{hook h='iqitMegaMenu'}
					</div>
					<div class="displayTop inline-cell">
						{if isset($HOOK_TOP)}{$HOOK_TOP}{/if}
					</div>
					</div>
					</div>
					</div>
					{else}
					<div class="row">
					<div id="header_logo" class="col-xs-12 col-sm-{4+$warehouse_vars.logo_width} {if isset($warehouse_vars.logo_position) && !$warehouse_vars.logo_position} col-sm-push-{4-$warehouse_vars.logo_width/2} centered-logo  {/if}">

						<a href="{if $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{$shop_name|escape:'html':'UTF-8'}">
							<img class="logo img-responsive replace-2xlogo" src="{$logo_url}" {if isset($warehouse_vars.retina_logo) and $warehouse_vars.retina_logo}data-retinalogo="{$warehouse_vars.retina_logo}" {/if} {if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if} alt="{$shop_name|escape:'html':'UTF-8'}" />
						</a>
					</div>
					{if isset($HOOK_TOP)}{$HOOK_TOP}{/if}
					{hook h='iqitMegaMenu'}
					</div>
					{/if}

				
					</div>
				</div>
				<div class="fw-pseudo-wrapper">{hook h='maxHeader'} 	</div>
			{if isset($warehouse_vars.header_style) && $warehouse_vars.header_style == 1}
				<div class="sidebar-footer">
				{if isset($warehouse_vars.footer_img_src) and $warehouse_vars.footer_img_src}<div class="paymants_logos col-xs-12"><img class="img-responsive" src="{$link->getMediaLink($warehouse_vars.image_path)|escape:'html'}" alt="footerlogo" /></div>{/if}
					{if isset($warehouse_vars.copyright_text)}<div class="col-xs-12 cpr-txt"> {$warehouse_vars.copyright_text}  </div>{/if}
             		 
             	</div>
             {/if}

			</header>

			{if $page_name == 'index'}
			<div class="fw-pseudo-wrapper fw-pseudo-wrapper-slider">
			{hook h="displayTopColumn"}
			{hook h='maxSlideshow'} 
		</div>
			{/if}
		</div>
		{if $page_name !='index' && $page_name !='pagenotfound'}{if isset($warehouse_vars.breadcrumb_width) && $warehouse_vars.breadcrumb_width == 0}{include file="$tpl_dir./breadcrumb.tpl"}{/if}{/if}
		<div class="columns-container">
			<div id="columns" class="container">


				
				{if $page_name !='index' && $page_name !='pagenotfound'}
				{if isset($warehouse_vars.breadcrumb_width) && $warehouse_vars.breadcrumb_width == 1}{include file="$tpl_dir./breadcrumb.tpl"}{/if}
				{/if}
								<div class="fw-pseudo-wrapper">
				{if $page_name == 'index'}
				{hook h='maxInfos'} 
				{/if}
				{hook h='maxInfos2'} 
				
					</div>
				<div class="row content-inner">
					{if isset($warehouse_vars.left_on_phones) && $warehouse_vars.left_on_phones == 0}
					{if isset($left_column_size) && !empty($left_column_size)}
					<div id="left_column" class="column col-xs-12 col-sm-{$left_column_size|intval}">{$HOOK_LEFT_COLUMN}</div>
					{/if}
					{/if}
					{if isset($left_column_size) && isset($right_column_size)}{assign var='cols' value=(12 - $left_column_size - $right_column_size)}{else}{assign var='cols' value=12}{/if}
					<div id="center_column" class="center_column col-xs-12 col-sm-{$cols|intval} {if isset($warehouse_vars.left_on_phones) && $warehouse_vars.left_on_phones == 1} col-sm-push-{$left_column_size|intval}{/if}">
						{/if}