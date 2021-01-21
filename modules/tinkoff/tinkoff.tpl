<div class="row">
	<div class="col-xs-12 col-md-6">
		<p class="payment_module">
			<a href="javascript:$('#tinkoff').submit();" title="{l s='Pay with Tinkoff' mod='tinkoff'}">
				<img src="{$module_template_dir}tinkoff.png" alt="{l s='Pay with Tinkoff' mod='tinkoff'}" />
				{l s='Pay with Tinkoff' mod='tinkoff'}
			</a>
		</p>
	</div>
</div>

<form id="tinkoff" name="payment" action="/modules/tinkoff/validation.php" method="post" enctype="application/x-www-form-urlencoded" accept-charset="utf-8">
    {foreach $arrFields as $key => $val}
		<input type="hidden" name="{$key}" value="{$val}">
    {/foreach}
</form>