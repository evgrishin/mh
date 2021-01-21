{*
*  @author Evgeny Grishin <e.v.grishin@yandex.ru>
*  @copyright  2015 Evgeny grishin
*}
{if $view=="form"}
<div id="eg_callmemess" style="display:none;">{l s='submited' mod='egcallme'}</div>
<form id="eg_callmeform" name="eg_callmeform" action="#" method="post">
<fieldset>
	<h3>{l s='order call back' mod='egcallme'}</h3>
	<div class="clearfix">
		<label for="eg_phone">{l s='phone' mod='egcallme'}*</label>
			<input class="form-control grey validate required" type="phone" required  placeholder="+7___ ___ __ __" id="eg_phone" name="eg_phone" maxlength="30">
	</div>
	{if $fname!='Hide'}
	<div class="clearfix">
		<label for="eg_fnamee">{l s='fname' mod='egcallme'}{if $fname=='Required'}*{/if}</label>
			<input class="form-control grey validate class{if $fname=='Required'} required{/if}" type="text" id="eg_fname" name="eg_fname" maxlength="20">
	</div>
	{/if}
	{if $lname!='Hide'}	
	<div class="clearfix">
		<label for="eg_lname">{l s='lname' mod='egcallme'}{if $lname=='Required'}*{/if}</label>
			<input class="form-control grey validate{if $lname=='Required'} required{/if}" type="text" id="eg_lname" name="eg_lname" maxlength="20">
	</div>	
	{/if}
	<input type="hidden" id="ajax" name="ajax" value="">
	<input type="hidden" id="action" name="action" value="new">
	<input type="hidden" id="eg_urlaction" name="eg_urlaction" value="{$ajaxcontroller|escape:'htmlall':'UTF-8'}">
	{if $mess!='Hide'}
	<label for="eg_message">{l s='message' mod='egcallme'}{if $mess=='Required'}*{/if}</label>
    	<textarea class="form-control{if $mess=='Required'} required{/if}" id="eg_message" name="eg_message"></textarea>
	</div>
	{/if}
	<div class="submit" style="margin-top: 13px;">
		<button type="submit" name="eg_submitcallme" id="eg_submitcallme" class="button btn btn-outline button-medium"><span>{l s='call me' mod='egcallme'}</span></button>
	</div>
</fieldset>
</form>
<script type="text/javascript">
{if trim($mask)!=""}
$(document).ready(function(){
    $("#eg_phone").mask(egcallme_mask);
    $("#eg_phone").attr("placeholder", "+7___ ___ __ __");
});
{/if}
$.validator.messages.required = "{l s='Field is required!' mod='egcallme'}";
</script>
{/if}