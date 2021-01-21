function showRelatedItems(e) {
	var itemName = $jqPm(e).attr("name");
	var itemVal =  $jqPm(e).val();
	switch(itemName){
		case 'centralcache_active':
			if(itemVal==1)
				$jqPm(".centralcache").show("fast");
			else
				$jqPm(".centralcache").hide("fast");
		break;
		case 'modulecache_active':
			if(itemVal==1)
				$jqPm(".modulecache").show("fast");
			else
				$jqPm(".modulecache").hide("fast");
		break;
	}
}

function hookSelection(e){
	var state	= $jqPm(e).attr("checked");
	var name	= $jqPm(e).attr("name");
	var id_hook	= $jqPm(e).attr("rel");
	var use_global = 0;

	if(id_hook == 'undefined')
		alert('An error occured: Please select a valid hook');
	else{
		var lifetime = $jqPm(".hook_lifetime_"+id_hook).val();
		var use_global	= $jqPm('hook_setTime_'+id_hook).attr("checked");
		if(use_global == 'undefined')
			use_global = '0';
		else
			use_global = '1';
		if(state == 'checked'){
			$jqPm('.hook_time_div_'+id_hook).show();
			$jqPm('#pm_hook_module_list_'+id_hook).show('fast');
			if(use_global == '0')
				$jqPm(".hook_lifetime_"+id_hook).removeAttr('disabled');
			getModulesFromHooks(id_hook,true);
		}else {
			if(confirm(msgUnHookedModules)) {
				unHookedModulesFromHooks(id_hook);
			}
		}
	}
}

function moduleSelection(e){
	var state	= $jqPm(e).attr("checked");
	var name	= $jqPm(e).attr("name");
	var id_module	= $jqPm(e).val();
	var hook_name 	= $jqPm(e).attr("rel");

	if(id_module == 'undefined')
		alert('An error occured: Please select a valid module');
	else{
		var lifetime = $jqPm(".module_lifetime_"+hook_name+'_'+name).val();
		var use_global	= $jqPm('module_setTime_'+name).attr("checked");
		if(use_global == 'undefined')
			use_global = 0;
		else
			use_global = 1;
		if(state == 'checked'){
			$.ajax({
				type : "GET",
				url : _base_config_url+"&pm_load_function=hookModuleFromHook&hook_name="+hook_name+"&module_name="+name+"&lifetime="+lifetime+"&use_global="+use_global,
				dataType : "script",
				success: function(data) {
					$jqPm('.module_time_div_'+name).show();
					if(use_global == 0)
						$jqPm(".module_lifetime_"+hook_name+'_'+name).removeAttr('disabled');
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {alert('An error occured');}
			});
		}
		else{
			$.ajax({
				type : "GET",
				url : _base_config_url+"&pm_load_function=unHookModuleFromHook&hook_name="+hook_name+"&module_name="+name,
				dataType : "script",
				success: function(data) {
					$jqPm('.module_time_div_'+name).hide();
					$jqPm('.module_setTime_'+name).removeAttr('checked');
					$jqPm(".module_lifetime_"+hook_name+'_'+name).attr('disabled', 'disabled');
					$jqPm('.module_lifetime_'+hook_name+'_'+name).val($jqPm('.module_default_cache_time').val());
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {alert('An error occured');}
			});
		}
	}
}

function moduleSetTime(e){
	var id_module	= $jqPm(e).val();
	var name		= $jqPm(e).attr("name");
	var hook_name	= $jqPm(e).attr("rel");
	var use_global  = false;
	if($jqPm(e).attr("checked") == 'checked'){
		use_global = '0';
		$jqPm(".module_lifetime_"+hook_name+'_'+name).removeAttr('disabled');
	}
	else{
		use_global = '1';
		$jqPm(".module_lifetime_"+hook_name+'_'+name).attr('disabled', 'disabled').val($jqPm('.module_default_cache_time').val());
	}
	var lifetime = $jqPm(".module_lifetime_"+hook_name+'_'+name).val();
	$.ajax({
		type : "GET",
		url : _base_config_url+"&pm_load_function=hookModuleFromHook&hook_name="+hook_name+"&module_name="+name+"&lifetime="+lifetime+"&use_global="+use_global,
		dataType : "script",
		success: function(data) {
			$jqPm('.module_time_div_'+name).show();
			if(use_global == 0)
				$jqPm(".module_lifetime_"+hook_name+'_'+name).removeAttr('disabled');
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {alert('An error occured');}
	});
}

function moduleTimeChange(e){
	var hook_name	= $jqPm(e).attr("rel");
	var name		= $jqPm(e).attr("name");

	var lifetime = $jqPm(e).val();
	var use_global = false;
	if(name == 'undefined')
		alert('An error occured: Please select a valid module');
	else{
		if($jqPm('module_setTime_'+name).attr("checked") != 'checked')
			use_global = '0';
		else
			use_global = '1';
		$.ajax({
			type : "GET",
			url : _base_config_url+"&pm_load_function=hookModuleFromHook&hook_name="+hook_name+"&module_name="+name+"&lifetime="+lifetime+"&use_global="+use_global,
			dataType : "script",
			success: function(data) {
				$jqPm('.module_time_div_'+name).show();
				if(use_global == 0)
					$jqPm(".module_lifetime_"+hook_name+'_'+name).removeAttr('disabled');
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {alert('An error occured');}
		});
	}
}

function getModulesFromHooks(id_hook,first_init){
	$jqPm('#pm_hook_module_list_'+id_hook).load(_base_config_url+"&pm_load_function=displayModuleHook&id_hook="+id_hook+(first_init ?'&first_init=1':''),function() {$jqPm(this).slideDown('fast');});
}

function unHookedModulesFromHooks(id_hook){
	$.ajax({
		type : "GET",
		url : _base_config_url+"&pm_load_function=unHookedModulesFromHooks&id_hook="+id_hook,
		error: function(XMLHttpRequest, textStatus, errorThrown) {alert('An error occured');},
		success: function(data) {
			$jqPm('#pm_hook_module_list_'+id_hook).slideUp('fast');
		 }
	});
}

$jqPm(document).ready(function() {
	$jqPm("input.hook_select").die('click').live('click', function(){
		hookSelection(this);
	});

	$jqPm("input.module_select").die('click').live('click', function(){
		moduleSelection(this);
	});

	$jqPm("input.module_setTime").die('click').live('click', function(){
		moduleSetTime(this);
	});

	var timeOutSelect2 = false;
	$jqPm("input.module_lifetime").die('keyup').live('keyup', function(){
		var e = this;
		if(timeOutSelect2)
			clearTimeout(timeOutSelect2);
		timeOutSelect2 = setTimeout(function(){
			moduleTimeChange(e);
		},1000);
	});
});