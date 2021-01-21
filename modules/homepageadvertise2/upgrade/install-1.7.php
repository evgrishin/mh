<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_7($object)
{
	$object->registerHook('displayAdditionalFooter');
	return true;
}
