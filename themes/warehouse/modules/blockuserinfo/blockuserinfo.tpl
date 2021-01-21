<div class="header_user_info col-xs-12 col-sm-{4-$warehouse_vars.logo_width/2}">
	{if $is_logged}
		<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow"><i class="icon-user"></i> <span>{$cookie->customer_firstname|substr:0:1}. {$cookie->customer_lastname}</span></a> <span class="log-separator">/</span> 
		<a class="logout" href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Log me out' mod='blockuserinfo'}">
			{l s='Sign out' mod='blockuserinfo'} <i class="icon-signout"></i>
		</a>
	{else}
		<a class="login" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Login to your customer account' mod='blockuserinfo'}">
			<i class="icon-signin"></i> {l s='Sign in' mod='blockuserinfo'}
		</a>
	{/if}
</div>