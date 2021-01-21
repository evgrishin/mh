<style>
.spanel {
    clear: both;
    float: left;
    width: 100%;
    background: #13b884;
}
.sizepanel{
	clear: both;
    float: left;
    width: 100%;
}
.searceform{
	padding:18px;
}
.searhetitle{
	font-size: 14pt;
    color: white;
    font-weight: bold;
}
.searchelabe{
	font-size: 12pt;
    color: white;
    font-weight: bold;
    padding-top:5px;	
}
.srow{
	padding-top:10px;
	}
.grhead, .grlabel{
    color: black;
    font-weight: bold;
    padding-top:15px;
}
.grhead{
	font-size: 14pt;
	color:#e85222;
}
.grlabel{
	font-size: 11pt;
}
.syr{
	padding:4px 0px 2px 0px;
}

.swh{
	background-color:white;
	margin:2px;
	padding:2px;
}

</style>
<script>
//onClick="form1.action='/3-matrasy/'+$('#ssize' ).val();form1.submit();"
</script>
<div class="row" style="padding:5px; background: #F9F9F9;">
  <div class="col-md-3">
	<div class="spanel">
	<form action="" class="searceform" name="form1">
	<div class="searhetitle">{l s='podbor' mod='egsearche'}:</div>
	<div class="srow"><span class="searchelabe">{l s='size' mod='egsearche'}</span> 
	<select class="select form-control" id="ssize">
		<option value="">{l s='umsize' mod='egsearche'}</option>
		<option value="/size-70x186">70x186 {l s='sm' mod='egsearche'}</option>
		<option value="/size-70x190">70x190 {l s='sm' mod='egsearche'}</option>
		<option value="/size-70x195">70x195 {l s='sm' mod='egsearche'}</option>
		<option value="/size-70x200">70x200 {l s='sm' mod='egsearche'}</option>
		<option value="/size-80x186">80x186 {l s='sm' mod='egsearche'}</option>
		<option value="/size-80x190">80x190 {l s='sm' mod='egsearche'}</option>
		<option value="/size-80x195">80x195 {l s='sm' mod='egsearche'}</option>
		<option value="/size-80x200">80x200 {l s='sm' mod='egsearche'}</option>
		<option value="/size-90x186">90x186 {l s='sm' mod='egsearche'}</option>
		<option value="/size-90x190">90x190 {l s='sm' mod='egsearche'}</option>
		<option value="/size-90x195">90x195 {l s='sm' mod='egsearche'}</option>
		<option value="/size-90x200">90x200 {l s='sm' mod='egsearche'}</option>
		<option value="/size-100x186">100x186 {l s='sm' mod='egsearche'}</option>
		<option value="/size-100x190">100x190 {l s='sm' mod='egsearche'}</option>
		<option value="/size-100x195">100x195 {l s='sm' mod='egsearche'}</option>
		<option value="/size-100x200">100x200 {l s='sm' mod='egsearche'}</option>
		<option value="/size-120x186">120x186 {l s='sm' mod='egsearche'}</option>
		<option value="/size-120x190">120x190 {l s='sm' mod='egsearche'}</option>
		<option value="/size-120x195">120x195 {l s='sm' mod='egsearche'}</option>
		<option value="/size-120x200">120x200 {l s='sm' mod='egsearche'}</option>
		<option value="/size-140x186">140x186 {l s='sm' mod='egsearche'}</option>
		<option value="/size-140x190">140x190 {l s='sm' mod='egsearche'}</option>
		<option value="/size-140x195">140x195 {l s='sm' mod='egsearche'}</option>
		<option value="/size-140x200">140x200 {l s='sm' mod='egsearche'}</option>
		<option value="/size-160x186">160x186 {l s='sm' mod='egsearche'}</option>
		<option value="/size-160x190">160x190 {l s='sm' mod='egsearche'}</option>
		<option value="/size-160x195">160x195 {l s='sm' mod='egsearche'}</option>
		<option value="/size-160x200">160x200 {l s='sm' mod='egsearche'}</option>
		<option value="/size-180x186">180x186 {l s='sm' mod='egsearche'}</option>
		<option value="/size-180x190">180x190 {l s='sm' mod='egsearche'}</option>
		<option value="/size-180x195">180x195 {l s='sm' mod='egsearche'}</option>
		<option value="/size-180x200">180x200 {l s='sm' mod='egsearche'}</option>
		<option value="/size-200x186">200x186 {l s='sm' mod='egsearche'}</option>
		<option value="/size-200x190">200x190 {l s='sm' mod='egsearche'}</option>
		<option value="/size-200x195">200x195 {l s='sm' mod='egsearche'}</option>
		<option value="/size-200x200">200x200 {l s='sm' mod='egsearche'}</option>
		<option value="/size-200">Ø 200 {l s='sm' mod='egsearche'}</option>
		<option value="/size-210">Ø 210 {l s='sm' mod='egsearche'}</option>
		<option value="/size-220">Ø 220 {l s='sm' mod='egsearche'}</option>

	</select>
	</div>
	<div class="srow"><span class="searchelabe">{l s='type' mod='egsearche'}</span> 
	<select class="select form-control" id="mtype">
		<option value="">{l s='all' mod='egsearche'}</option>
		<option value="/tip_matrasa-bespruzhinnyj">{l s='bespr' mod='egsearche'}</option>
		<option value="/tip_matrasa-zavisimye_pruzhiny-nezavisimye_pruzhiny">{l s='pruzh' mod='egsearche'}</option>
	</select>
	</div>
	<div class="srow"><span class="searchelabe">{l s='zhesk' mod='egsearche'}</span> 
	<select class="select form-control" id="zhesk">
		<option value="">{l s='any' mod='egsearche'}</option>
		<option value="87_1">{l s='nizk' mod='egsearche'}</option>
		<option value="87_1">{l s='sred' mod='egsearche'}</option>
		<option value="87_1">{l s='visok' mod='egsearche'}</option>
	</select>
	</div>
	<div class="srow">		
		<p class="buttons_bottom_block no-print">
			<button type="submit" name="Submit" onClick="form1.action='/3-matrasy'+$('#ssize' ).val()+$('#mtype' ).val();form1.submit();" class="exclusive btn btn-default searchelabe" style="width: 100%;background-color: #219E77;"> <span>{l s='searche' mod='egsearche'}</span> </button></p>
	</div>	
	</form>
	</div>
  </div>
  <div class="col-md-9"><div class="sizepanel">
  	<div class="row text-center">
  		<div class="col-md-12 syr"><span class="grhead">{l s='sizes' mod='egsearche'}</span></div>
  	</div>
  	<div class="row text-center">
 
  		<div class="col-md-3 syr">
  			<div class="row">
  				<div class="col-xs-12 syr text-center"><span class="grlabel">{l s='single' mod='egsearche'}</span></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-70x186">70x186</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-90x186">90x186</a></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-70x190">70x190</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-90x190">90x190</a></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-70x195">70x195</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-90x195">90x195</a></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-70x200">70x200</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-90x200">90x200</a></div>
  			</div>
  			<div class="row swh"> 
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-80x186">80x186</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-120x186">120x186</a></div>
  			</div>
  			<div class="row swh"> 
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-80x190">80x190</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-120x190">120x190</a></div>
  			</div>
  			<div class="row swh"> 
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-80x195">80x195</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-120x195">120x195</a></div>
  			</div>
  			<div class="row swh"> 
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-80x200">80x200</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-120x200">120x200</a></div>
  			</div>   			  			
  		</div>
  		
  		<div class="col-md-3 syr">
  			<div class="row">
  				<div class="col-xs-12 syr text-center"><span class="grlabel">{l s='double' mod='egsearche'}</span></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-140x186">140x186</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-180x186">180x186</a></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-140x190">140x190</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-180x190">180x190</a></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-140x195">140x195</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-180x195">180x195</a></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-140x200">140x200</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-180x200">180x200</a></div>
  			</div>  	
  			<div class="row swh"> 
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-160x186">160x186</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-200x186">200x186</a></div>
  			</div>
  			<div class="row swh"> 
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-160x190">160x190</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-200x190">200x190</a></div>
  			</div>
  			<div class="row swh"> 
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-160x195">160x195</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-200x195">200x195</a></div>
  			</div>
  			<div class="row swh"> 
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-160x200">160x200</a></div>
  				<div class="col-xs-6 syr"><a href="//{$host}/3-matrasy/size-200x200">200x200</a></div>
  			</div>    					  			
  		</div>
  		<div class="col-md-6 syr">
  			<div class="row">
  				<div class="col-xs-12 syr text-center"><span class="grlabel">{l s='kids' mod='egsearche'}</span></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-60x120">60x120</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-70x120">70x120</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-80x120">80x120</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-90x120">90x120</a></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-60x130">60x130</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-70x130">70x130</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-80x130">80x130</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-90x130">90x130</a></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-60x140">60x140</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-70x140">70x140</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-80x140">80x140</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-90x140">90x140</a></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-60x150">60x150</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-70x150">70x150</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-80x150">80x150</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-90x150">90x150</a></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-60x160">60x160</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-70x160">70x160</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-80x160">80x160</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-90x160">90x160</a></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-60x170">60x170</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-70x170">70x170</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-80x170">80x170</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-90x170">90x170</a></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-60x180">60x180</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-70x180">70x180</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-80x180">80x180</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-90x180">90x180</a></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-60x190">60x190</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-70x190">70x190</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-80x190">80x190</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-90x190">90x190</a></div>
  			</div>
  			<div class="row swh">
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-60x200">60x200</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-70x200">70x200</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-80x200">80x200</a></div>
  				<div class="col-xs-3 syr"><a href="//{$host}/5-detskie-matrasy/size-90x200">90x200</a></div>
  			</div>  			  			  			
  		</div>
  	 		
  	</div>
  	</div></div>
</div>