<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
if (!class_exists('pm_cachemanager'))
	include_once(dirname(__FILE__).'/pm_cachemanager.php');
if (Tools::getIsset('secure_key') && Tools::getValue('secure_key')) {
	$pm_cm = new pm_cachemanager();
	$pm_cm->runCrontab(Tools::getValue('secure_key'));
}
