{if $view=="form"}
<div id="callmemess"></div>
<form id="callmeform" name="callmeform" action="#" method="post">
<fieldset>
	<h3>{l s='order call back' mod='egmultishop'}</h3>
	<div class="clearfix">
		<label for="phone">{l s='phone' mod='egmultishop'}</label>
			<input class="form-control grey validate" type="text" id="phone" name="phone" data-validate="isEmail" value="">
	</div>
	<input type="hidden" id="ajax" name="ajax" value="">
	<input type="hidden" id="action" name="action" value="new">
	<input type="hidden" id="urlaction" name="urlaction" value="{$ajaxcontroller}">
	<label for="message">{l s='message' mod='egmultishop'}</label>
    	<textarea class="form-control" id="message" name="message"></textarea>
	</div>
	<div class="submit">
		<button type="submit" name="submitcallme" id="submitcallme" class="button btn btn-outline button-medium"><span>{l s='call me' mod='egmultishop'}</span></button>
	</div>
</fieldset>
</form>
{else}
<span>{l s='submited' mod='egmultishop'}</span>
{/if}