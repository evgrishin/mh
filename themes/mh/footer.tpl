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

{if !isset($content_only) || !$content_only}
						{include file="$tpl_dir./layout/{$LEO_LAYOUT_DIRECTION}/footer.tpl"  }

                
                	</div>
				</div>
				
            </section>
<!-- Footer -->
            {if isset($HOOK_BOTTOM) && !empty($HOOK_BOTTOM)}
			
				<section id="bottom">
				<div class="container">	
				{if isset($left_column_size) && isset($right_column_size)}{assign var='cols' value=(12 - $left_column_size - $right_column_size)}{else}{assign var='cols' value=12}{/if}
				<div class="{if {$cols|intval} != 12}row{else}inner{/if}">
					{$HOOK_BOTTOM}
				</div>
				</div>
				</section>
			{/if}
		<!-- Footer -->
			<footer id="footer" class="footer-container">       
				<div class="container"> 
					<div class="inner">	
						{$HOOK_FOOTER}							
					</div> 
				</div>
            </footer>
		</section><!-- #page -->
{/if}
<p id="back-top">
<a href="#top" title="{l s='Scroll To Top'}">{l s='Scroll To Top'}</a>
</p>
{include file="$tpl_dir./global.tpl"}
{if isset($LEO_PANELTOOL) && $LEO_PANELTOOL}
    {include file="$tpl_dir./info/paneltool.tpl"}
{/if}

{*
*

<!-- {literal} -->
<script type='text/javascript'>
    window['li'+'ve'+'T'+'e'+'x'] = true,
    window['li'+'ve'+'TexI'+'D'] = 105231,
    window['liveT'+'ex_o'+'bject'] = true;
    (function() {
        var t = document['crea'+'te'+'E'+'lemen'+'t']('script');
        t.type ='text/javascript';
        t.async = true;
        t.src = '//cs15.livet'+'ex.ru/j'+'s/client.js';
        var c = document['getEle'+'mentsByTag'+'Name']('script')[0];
        if ( c ) c['paren'+'tNod'+'e']['inser'+'tB'+'efore'](t, c);
        else document['docume'+'ntElem'+'e'+'nt']['f'+'ir'+'st'+'Chil'+'d']['ap'+'pendC'+'hild'](t);
    })();
</script>
<!-- {/literal} -->
**}
{**

<!-- BEGIN JIVOSITE CODE {literal} -->
<script type='text/javascript'>
(function(){ var widget_id = '054guu5QvV';
var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);})();</script>
<!-- {/literal} END JIVOSITE CODE -->
*}
{**
<script>
    var __REPLAIN_ = '36e1a6f4-6052-464b-9812-540599bca00a';
    (function(u){var s=document.createElement('script');s.type='text/javascript';s.async=true;s.src=u;
    var x=document.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);
    })('https://widget.replain.cc/dist/client.js');
*}
<script>
    (function(w,d,u){
        var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.$
        var h=d.getElementsByTagName('script')[0];h.parentNode.insertBe$
    })(window,document,'https://cdn.bitrix24.ru/b9752459/crm/site_button/lo$
</script>

	</body>
</html>