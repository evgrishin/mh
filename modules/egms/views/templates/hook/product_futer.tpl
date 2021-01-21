<!-- product futer -->

{if isset($tags)}
<div class="primary_block">
<ul class="list-inline">
    <li><b>{l s='tags' mod='egms'}</b></li>
{foreach from=$tags item=tag}
    <li><a href="{$tag.tag_link}" title="{$tag.tag_name}">{$tag.tag_name}</a></li>
{/foreach}
</ul>
</div>
{/if}
