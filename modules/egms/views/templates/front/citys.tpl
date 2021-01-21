{if $question != ""}
<div style="width:300px;"><h3>{l s='your city' mod='egms'} <br/>{$question}?</h3>
<div class="button-container">
<div class="cart-buttons clearfix" style="padding:10px 0px;width:300px;">
	<a id="yes_city" class="btn btn-outline-inverse button pull-left" style="width:85px;" href="javascript:setRegion('{$region}','{$region_link}');" title="{l s='yes' mod='egms'}" rel="nofollow">
		<span>{l s='yes' mod='egms'}</span>
	</a>&nbsp;
	<a id="no_city" class="btn btn-outline-inverse button pull-right" style="width:85px" href="javascript:noRegion();" title="{l s='no' mod='egms'}" rel="nofollow">
		<span>{l s='no' mod='egms'}</span>
	</a>
</div>
</div>
</div>
{else}
<div style="width: 300px"><h3>{l s='touch city' mod='egms'}</h3>
<select style="width:250px;" id="city_list" name="city_list">
	<option value="0"></option>
{foreach from=$citys item=city}
	<option value="{$city.url}">{$city.city_name}</option>
{/foreach}
</select>
<button class="btn btn-outline button button-medium exclusive setregion" type="submit" id="touch_city" name="touch_city">
							<span>
								{l s='touch' mod='egms'}
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
	<li><a href="{$city.url}" rel="nofollow"  id="city-{$city.id}">{$city.city_name}</a></li>
	{/if}
	{if ($i%30==0)}
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
