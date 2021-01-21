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
	<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7 " lang="{$lang_iso}"><![endif]-->
	<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7" lang="{$lang_iso}"><![endif]-->
	<!--[if IE 8]><html class="no-js lt-ie9 ie8" lang="{$lang_iso}"><![endif]-->
	<!--[if gt IE 8]> <html class="no-js ie9" lang="{$lang_iso}"><![endif]-->
	<html lang="{$lang_iso}">
	<head>
		<title>{$meta_title|escape:'html':'UTF-8'}</title>	
		<meta charset="utf-8" />
{if isset($meta_description)}
	<meta name="description" content="{$meta_description|escape:'html':'UTF-8'}">
{/if}
{if isset($meta_keywords)}
	<meta name="keywords" content="{$meta_keywords|escape:'html':'UTF-8'}">
{/if}
		<meta name="robots" content="{if isset($nobots)}no{/if}index,follow">
	<link rel="shortcut icon" href="{$favicon_url}">
		
        <link href="{$css_dir}_bootstrap.css" rel="stylesheet" type="text/css" media="all" />
        <link href="{$css_dir}maintenance.css" rel="stylesheet" type="text/css" />
        <link href='//fonts.googleapis.com/css?family=Open+Sans:600&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	</head>
	<body id="maintenance-page">
		<header id="header"><div class="container">
			 <div class="logo"><img src="{$logo_url}" {if $logo_image_width}width="{$logo_image_width}"{/if} {if $logo_image_height}height="{$logo_image_height}"{/if} alt="logo" />	</div>
				</div>
		</header>
			<section id="maintenance">
		<div class="container">
             <div id="message">
				{l s='In order to perform website maintenance, our online store will be temporarily offline.'}
				{l s='We apologize for the inconvenience and ask that you please try again later.'}
			 </div>
			 </div>
		</section>
        
        	<footer id="footer">
			<div class="container">
				<div class="row">
			<section id="maintance_text" class="title_block col-xs-12 col-ms-6">
			<h4>{l s='Contact Us'}</h4>
			{l s='Phone: +44 11 222 1221'}<br />
			{l s='E-mail: sample@mail.com'}
			</section>
				{$HOOK_MAINTENANCE}
			</div></div>
			</footer>
	</body>
</html>
