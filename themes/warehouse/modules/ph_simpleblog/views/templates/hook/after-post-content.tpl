{if Configuration::get('PH_BLOG_DISPLAY_SHARER')}
<div class="post-block simpleblog-socialshare">
	<h4 class="page-subheading">{l s='Share this post' mod='ph_simpleblog'}</h4>

	<div class="simpleblog-socialshare-icons">
		<button data-type="twitter" type="button" class="btn btn-default btn-twitter">
			<i class="icon icon-twitter"></i> {l s="Tweet" mod='ph_simpleblog'}
		</button>
		<button data-type="facebook" type="button" class="btn btn-default btn-facebook">
			<i class="icon icon-facebook"></i> {l s="Share" mod='ph_simpleblog'}
		</button>
		<button data-type="google-plus" type="button" class="btn btn-default btn-google-plus">
			<i class="icon icon-google-plus"></i> {l s="Google+" mod='ph_simpleblog'}
		</button>
		<button data-type="pinterest" type="button" class="btn btn-default btn-pinterest">
			<i class="icon icon-pinterest"></i> {l s="Pinterest" mod='ph_simpleblog'}
		</button>
	</div><!-- simpleblog-socialshare-icons. -->
</div><!-- .simpleblog-socialshare -->
{/if}