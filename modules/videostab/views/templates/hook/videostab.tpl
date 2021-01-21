{if $videos}

{if isset($warehouse_vars.product_tabs) && $warehouse_vars.product_tabs}
    <section class="page-product-box tab-pane fade" id="videosTab">
    {else}
    <section class="page-product-box" id="videosTab">
    <h3 class="page-product-heading">{$yotpoWidgetTabName|escape:'html':'UTF-8'}</h3>
    {/if}

    {foreach from=$videos item=video}
    {if $video.type ==0}
        <div class="videoWrapper videotab_video">{$video.video|escape:'quotes':'UTF-8'}</div>
        {else}
        <div class="mp4content videotab_video">
        <video style="width:100%;height:100%;" src="{$this_path}uploads/{$video.video|escape:'quotes':'UTF-8'}" type="video/mp4" 
	id="player1" 
	controls="controls" preload="none"></video>
    </div>
        {/if}
    {/foreach}
</section>
{/if}