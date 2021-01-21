<div>
{if $messageTextErr!=''}
<script>
//alert("{$messageTextErr}");
</script>
{/if}
<form method='POST' name='myForm' id='myForm'>
<textarea>{$messageTextErr}</textarea><br>
<input type='text' name='url' value='{$url}'><br>
<input type='hidden' name="u" value='1'>
<input type='submit' value='Update Price'>
</form>
</div>