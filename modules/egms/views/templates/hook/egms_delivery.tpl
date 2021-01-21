{strip}
{addJsDef egms_free_price=(int)$free_price}
{addJsDef egms_delivery_price=(int)$delivery_price}
{/strip}
<p>
{if $delivery_price>0 }
<span class="availability_date_label"><strong>{l s='delivery' mod='egms'}</strong></span>
<span class="availability_value2 delivery_con">
{if $free_price==0}
    {l s='free' mod='egms'}
{else}
    {$delivery_price} {l s='rub' mod='egms'}
{/if}
</span>
{else}
    {if $category_hide=='0'}
        <span class="availability_date_label"><strong><a href="{$link_shipself}" style="color:#e85222;">{l s='delivery condition' mod='egms'}</a></strong></span>
    {/if}
{/if}
</p>
