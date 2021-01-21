
<div class="clearfix pull-left ddd" style="text-align: center;">
	{if ($city_lists==ture)}
	<div class="cityname"><a class="city-view cityname" style="font-size: 18pt;" href="{$city_link}" rel="{$city_link}">{$city_name|escape:'html':'UTF-8'}<span></span></a></div>
	{else}
	<div class="cityname"><a class="cityname" href="#">{$city_name|escape:'html':'UTF-8'}<span></span></a></div>
	{/if}
	<div></div>
</div>

