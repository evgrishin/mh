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
<div class="header_user_info pull-right e-scale">
	<div data-toggle="dropdown" class="popup-title"><span>{l s='Top links' mod='blockuserinfo'} </span></div>	
	<ul class="links">
		<li class="first">
			<a href="{$link->getPageLink('module-egms-contacts')|escape:'html':'UTF-8'}" title="{l s='contacts' mod='blockuserinfo'}" rel="nofollow">
				{l s='contacts' mod='blockuserinfo'}
			</a>
		</li>
		<li>
			<a href="{$link->getPageLink('module-egms-delivery')|escape:'html':'UTF-8'}" title="{l s='delivery' mod='blockuserinfo'}" rel="nofollow">
				{l s='delivery' mod='blockuserinfo'}
			</a>
		</li>
		<li>
			<a href="{$link->getPageLink('module-egms-shipself')|escape:'html':'UTF-8'}" title="{l s='shipself' mod='blockuserinfo'}" rel="nofollow">
				{l s='shipself' mod='blockuserinfo'}
			</a>
		</li>			
		<li>
			<a href="{$link->getPageLink('products-comparison')|escape:'html':'UTF-8'}" title="{l s='Compare' mod='blockuserinfo'}" rel="nofollow">
				{l s='Compare' mod='blockuserinfo'}
			</a>
		</li>			
		{if $is_logged}
			<li>
				<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow">
					<span>{l s='Hello' mod='blockuserinfo'}, {$cookie->customer_firstname} {$cookie->customer_lastname}</span>
				</a>
			</li>
		{/if}

		{if $is_logged}
			<li><a class="logout" href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Log me out' mod='blockuserinfo'}">
				{l s='Sign out' mod='blockuserinfo'}
			</a></li>
		{else}
			<li><a class="login" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Login to your customer account' mod='blockuserinfo'}">
				{l s='Sign in' mod='blockuserinfo'}
			</a></li>
		{/if}  
		{*
		<li>
			<a id="wishlist-total" href="{$link->getModuleLink('blockwishlist', 'mywishlist', array(), true)|addslashes}" title="{l s='My wishlists' mod='blockuserinfo'}">
			{l s='Wish List' mod='blockuserinfo'}</a>
		</li>
		*}
		
	</ul>
</div>	