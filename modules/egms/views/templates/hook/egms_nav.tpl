{strip}
    {addJsDef egms_ajaxcontroller=$ajaxcontroller}
{/strip}
{if $city=='show'}
    <script type="text/javascript">
        $( window ).load(function(){
            cityQuestion();
        });
    </script>
{/if}
<script type="application/ld+json">
{literal}{{/literal}"@context" : "http://schema.org",
 "@type": "Organization",
 "name": "{$sitename} - {$city_name|escape:'html':'UTF-8'}",
 "url": "{$url}",
 "address": { "@type": "PostalAddress", "streetAddress": "{$address}" {literal}}{/literal} ,
 "email": "{$email}",
 "contactPoint": {literal}{{/literal} "@type": "ContactPoint", "contactType": "customer support", "telephone": "{$phone}" {literal}}{/literal}
 {literal}}{/literal}
</script>
<div class="clearfix pull-left ddd" style="text-align: center;">
    {if ($city_lists==ture)}
        <div class="cityname"><span class="glyphicon glyphicon-map-marker"></span><a class="city-view cityname" href="{$city_link}">{$city_name|escape:'html':'UTF-8'}<span class="arr"></span></a></div>
    {else}
        <div class="cityname"><span class="glyphicon glyphicon-map-marker"></span><a class="cityname" href="#">{$city_name|escape:'html':'UTF-8'}<span class="arr"></span></a></div>
    {/if}
    <div></div>
</div>
<script type="text/javascript">
    /* Blockusreinfo */

    $(document).ready( function(){
        if( $(window).width() < 1025 ){
            $(".header_user_info").addClass('popup-over');
            $(".header_user_info .links").addClass('popup-content');
        }
        else{
            $(".header_user_info").removeClass('popup-over');
            $(".header_user_info .links").removeClass('popup-content');
        }
        $(window).resize(function() {
            if( $(window).width() < 1025 ){
                $(".header_user_info").addClass('popup-over');
                $(".header_user_info .links").addClass('popup-content');
            }
            else{
                $(".header_user_info").removeClass('popup-over');
                $(".header_user_info .links").removeClass('popup-content');
            }
        });
    });
</script>
<!-- Block user information module NAV  -->
<div class="header_user_info pull-right">
    <div data-toggle="dropdown" class="popup-title"><span>{l s='Top links' mod='egms'} </span></div>
    <ul class="links">
        <li>
            <a href="{$link->getPageLink('module-egms-contacts')|escape:'html':'UTF-8'}" title="{l s='contacts' mod='egms'}" rel="nofollow">
                {l s='contacts' mod='egms'}
            </a>
        </li>
        <li>
            <a href="{$link->getPageLink('module-egms-delivery')|escape:'html':'UTF-8'}" title="{l s='delivery' mod='egms'}" rel="nofollow">
                {l s='delivery' mod='egms'}
            </a>
        </li>
        <li>
            <a href="{$link->getPageLink('module-egms-shipself')|escape:'html':'UTF-8'}" title="{l s='shipself' mod='egms'}" rel="nofollow">
                {l s='shipself' mod='egms'}
            </a>
        </li>
        <li>
            <a href="{$link->getPageLink('products-comparison')|escape:'html':'UTF-8'}" title="{l s='Compare' mod='egms'}" rel="nofollow">
                {l s='Compare' mod='egms'}
            </a>
        </li>
        {if $is_logged}
            <li>
                <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='View my customer account' mod='egms'}" class="account" rel="nofollow">
                    <span>{l s='Hello' mod='egms'}, {$cookie->customer_firstname} {$cookie->customer_lastname}</span>
                </a>
            </li>
        {/if}

        {if $is_logged}
            <li><a class="logout" href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Log me out' mod='egms'}">
                    {l s='Sign out' mod='egms'}
                </a></li>
        {else}
            <li><a class="login" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Login to your customer account' mod='egms'}">
                    {l s='Sign in' mod='egms'}
                </a></li>
        {/if}

    </ul>
</div>