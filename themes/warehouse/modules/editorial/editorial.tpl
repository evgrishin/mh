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

	<!-- Module Editorial -->
	<section id="editorial_block_center" class="editorial_block">
		<div class="row">
			<div class="{if $homepage_logo}col-md-6 {else} col-md-12{/if}">
				{if $editorial->body_title}<h1>{$editorial->body_title|stripslashes}</h1>{/if}
				{if $editorial->body_subheading}<h2>{$editorial->body_subheading|stripslashes}</h2>{/if}
				{if $editorial->body_paragraph}<div class="rte">{$editorial->body_paragraph|stripslashes}</div>{/if}
			</div>{if $homepage_logo}
			<div class="col-md-6">
				{if $editorial->body_home_logo_link}<a href="{$editorial->body_home_logo_link|escape:'html':'UTF-8'}" title="{$editorial->body_title|escape:'html':'UTF-8'|stripslashes}">{/if}
				{if $homepage_logo}<img src="{$link->getMediaLink($image_path)|escape:'html'}" alt="{$editorial->body_title|escape:'html':'UTF-8'|stripslashes}" class="img-responsive" />{/if}
				{if $editorial->body_home_logo_link}</a>{/if}
				{if $editorial->body_logo_subheading}<p id="editorial_image_legend">{$editorial->body_logo_subheading|stripslashes}</p>{/if}
			</div>{/if}

		</div>
	</section>
	<!-- /Module Editorial -->
