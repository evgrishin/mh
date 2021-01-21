{if $question != ""}
<div style=""><h3>{l s='your city' mod='egmultishop'} {$question}?</h3>
<div class="button-container">
<p class="cart-buttons clearfix" style="padding:10px 50px;">
	<a id="yes_city" class="btn btn-outline-inverse button pull-left" href="javascript:setRegion('{$region}');" title="{l s='yes' mod='egmultishop'}" rel="nofollow">
		<span>{l s='yes' mod='egmultishop'}</span>
	</a>
	<a id="no_city" class="btn btn-outline-inverse button pull-right" href="javascript:noRegion();" title="{l s='no' mod='egmultishop'}" rel="nofollow">
		<span>{l s='no' mod='egmultishop'}</span>
	</a>
</p>
</div>
</div>
{else}
<div style="width: 300px"><h3>{l s='touch city' mod='egmultishop'}</h3>
<select style="width:250px;" id="city_list" name="city_list">
	<option value="0"></option>
{foreach from=$citys item=city}
	<option value="{$city.id}">{$city.city_name}</option>
{/foreach}
</select>
<button class="btn btn-outline button button-medium exclusive setregion" type="submit" id="touch_city" name="touch_city">
							<span>
								{l s='touch' mod='egmultishop'}
							</span>
</button>
</div>
<div style="margin-top:10px; padding-top:20px; border-top:solid 1px grey;">
<div style="margin-left:20px;"  class="clearfix pull-left">	
	<ul style="list-style-type: circle">	
{$i=0}				
{foreach from=$citys item=city}
	{$i=$i+1}
	{if ($host==$city.domain)}
	<li><b>{$city.city_name}</b></li>
	{else}
	<li><a href="http://{$city.domain}{$current_page}" rel="nofollow" class="setregion" id="city-{$city.id}">{$city.city_name}</a></li>
	{/if}
	{if ($i%15==0)}
	</ul>
	</div>
	<div style="margin-left:20px;"  class="clearfix pull-left">	
	<ul style="list-style-type: circle">
	{/if}	
{/foreach}
</ul>
</div>

</div>
{/if}
