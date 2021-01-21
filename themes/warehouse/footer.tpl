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
{if !$content_only}
					</div><!-- #center_column -->
						{if isset($warehouse_vars.left_on_phones) && $warehouse_vars.left_on_phones == 1}
					{if isset($left_column_size) && !empty($left_column_size)}
					<div id="left_column" class="column col-xs-12 col-sm-{$left_column_size|intval} col-sm-pull-{12 - $left_column_size - $right_column_size}">{$HOOK_LEFT_COLUMN}</div>
					{/if}
					{/if}
					{if isset($right_column_size) && !empty($right_column_size)}
						<div id="right_column" class="col-xs-12 col-sm-{$right_column_size|intval} column">{$HOOK_RIGHT_COLUMN}</div>
					{/if}
					</div><!-- .row -->
				</div><!-- #columns -->
			</div><!-- .columns-container -->
			<!-- Footer -->
			{hook h='footerTopBanner'} 

			<div class="footer-container {if isset($warehouse_vars.f_wrap_width) && $warehouse_vars.f_wrap_width == 0} container {/if}">
				{if isset($warehouse_vars.footer_width) && $warehouse_vars.footer_width == 1}
				{if isset($warehouse_vars.footer1_status) && $warehouse_vars.footer1_status == 1}
				<div class="footer-container-inner1">
				<footer id="footer1"  class="container">
					<div class="row">{hook h='displayAdditionalFooter'}</div>
				</footer>
				</div>
				{/if}
				{if isset($HOOK_FOOTER)}
				<div class="footer-container-inner">
				<footer id="footer"  class="container">
					<div class="row">{$HOOK_FOOTER}</div>
				</footer>
				</div>
				{/if}
				{elseif isset($warehouse_vars.footer_width) && $warehouse_vars.footer_width == 0}
				{if isset($warehouse_vars.footer1_status) && $warehouse_vars.footer1_status == 1}
				<footer id="footer1"  class="container footer-container-inner1">
						
					<div class="row">{hook h='displayAdditionalFooter'}</div>
					
				</footer>
				{/if}
				{if isset($HOOK_FOOTER)}
				<footer id="footer"  class="container footer-container-inner">
						
					<div class="row">{$HOOK_FOOTER}</div>
					
				</footer>
				{/if}
				{/if}
			{if isset($warehouse_vars.second_footer)  && $warehouse_vars.second_footer == 1 && isset($warehouse_vars.header_style) && ($warehouse_vars.header_style == 0 || $warehouse_vars.header_style == 2)}
            <div class="footer_copyrights">
            <footer class="container clearfix">
            	<div class="row">
            {if isset($warehouse_vars.copyright_text)}<div class="col-sm-6"> {$warehouse_vars.copyright_text}  </div>{/if}

             {if isset($warehouse_vars.footer_img_src) and $warehouse_vars.footer_img_src}<div class="paymants_logos col-sm-6"><img class="img-responsive" src="{$link->getMediaLink($warehouse_vars.image_path)|escape:'html'}" alt="footerlogo" /></div>{/if}



            </div>
            </footer></div>
            {/if}

			</div><!-- #footer -->
		</div><!-- #page -->
{/if}
{if !$content_only}<div id="toTop" class="transition-300"></div>
{hook h='belowFooter'}{/if}
{include file="$tpl_dir./global.tpl"}
{if $page_name == 'index'}
<script type="application/ld+json">
{literal}
{
  "@context": "http://schema.org",
  "@type": "WebSite",
  "url": "{/literal}{$base_dir_ssl}{literal}",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "{/literal}{$base_dir_ssl}{literal}index.php?controller=search&search_query={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
{/literal}
</script>
{/if}
	</body>
</html>