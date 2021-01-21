<?php
/**
 *
 * @category  Override
 * @author    Presta-Module.com <support@presta-module.com>
 * @copyright Presta-Module 2014
 * _______  ____    ____
 * |_   __ \|_   \  /   _|
 * | |__) | |   \/   |
 * |  ___/  | |\  /| |
 * _| |_    _| |_\/_| |_
 * |_____|  |_____||_____|
 *
 *
 *************************************
 **           Cache Manager          *
 **   http://www.presta-module.com   *
 *************************************
 **   PS: 1.4                        *
 *************************************
 */
class Module extends ModuleCore
{
	public static $hookModuleToIncludeFromCache;
	private static $hook_need_comment = array('extraleft','productfooter','extraright','productoutofstock','productactions','producttab','producttabcontent','displayleftcolumnproduct','displayrightcolumnproduct','actionproductoutofstock','displayfooterproduct','displayproductbuttons','displayproducttab','displayproducttabcontent');
	private static $hook_need_comment_result = array();

	private static function hookNeedComment($hook_name) {
		if (isset(self::$hook_need_comment_result[$hook_name])) return self::$hook_need_comment_result[$hook_name];
		if (in_array(strtolower($hook_name), self::$hook_need_comment)) self::$hook_need_comment_result[$hook_name] = true;
		else self::$hook_need_comment_result[$hook_name] = false;
		return self::$hook_need_comment_result[$hook_name];
	}

	public static function hookExecCache($hook_name, $hookArgs = array(), $id_module = NULL)
	{
		global $cookie, $css_files, $js_files;
		if ((!empty($id_module) AND !Validate::isUnsignedId($id_module)) OR !Validate::isHookName($hook_name))
			die(Tools::displayError());

		global $cart, $cookie;
		$iModulesHook = 1;
		$modulesHook = array();
		if (!isset($hookArgs['cookie']) OR !$hookArgs['cookie'])
			$hookArgs['cookie'] = $cookie;
		if (!isset($hookArgs['cart']) OR !$hookArgs['cart'])
			$hookArgs['cart'] = $cart;
		$hook_name = strtolower($hook_name);

		if (!isset(self::$_hookModulesCache))
		{
			$db = Db::getInstance(_PS_USE_SQL_SLAVE_);
			$result = $db->ExecuteS('
			SELECT h.`name` as hook, m.`id_module`, h.`id_hook`, m.`name` as module, h.`live_edit`
			FROM `'._DB_PREFIX_.'module` m
			LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
			LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
			AND m.`active` = 1
			ORDER BY hm.`position`', false);
			self::$_hookModulesCache = array();
			if ($result)
				while ($row = $db->nextRow()) {
					$row['hook'] = strtolower($row['hook']);
					if (!isset(self::$_hookModulesCache[$row['hook']]))
						self::$_hookModulesCache[$row['hook']] = array();
					self::$_hookModulesCache[$row['hook']][] = array('id_hook' => $row['id_hook'], 'module' => $row['module'], 'id_module' => $row['id_module'], 'live_edit' => $row['live_edit']);
				}
		}

		if (!$id_hook = Hook::get($hook_name))
			if (self::hookNeedComment($hook_name))
				return '<!-- PM_CM START '.$hook_name.' --><!-- PM_CM END '.$hook_name.' -->';
			else return '';

		//Get hook to include with cache
		if (!isset(self::$hookModuleToIncludeFromCache) )
		{
			$db = Db::getInstance(_PS_USE_SQL_SLAVE_);
			$result = $db->ExecuteS('
			SELECT `hook_name`, `module_name`, `lifetime`, `use_global`
			FROM `'._DB_PREFIX_.'pm_cachemanager_hooks` ch', false);
			self::$hookModuleToIncludeFromCache = array();
			if ($result)
				while ($row = $db->nextRow())
				{
					if (!isset(self::$hookModuleToIncludeFromCache[$row['hook_name']]))self::$hookModuleToIncludeFromCache[$row['hook_name']] = array();
					$module_name_cache = strtolower($row['module_name']);
					unset($row['module_name']);
					self::$hookModuleToIncludeFromCache[$row['hook_name']][$module_name_cache] = $row;
				}
		}

		// Look on modules list
		$altern = 0;
		$output = '';
		if (self::hookNeedComment($hook_name))
			$output .= '<!-- PM_CM START '.$hook_name.' -->';

		if (isset(self::$_hookModulesCache[$hook_name]) && pm_cachemanager::_isFilledArray(self::$_hookModulesCache[$hook_name])) {
			foreach (self::$_hookModulesCache[$hook_name] AS $array)
			{
				if ($id_module AND $id_module != $array['id_module'])
					continue;
				if (!($moduleInstance = Module::getInstanceByName($array['module'])))
					continue;

				$exceptions = $moduleInstance->getExceptions((int)$array['id_hook'], (int)$array['id_module']);
				foreach ($exceptions AS $exception)
					if (strstr(basename($_SERVER['PHP_SELF']).'?'.$_SERVER['QUERY_STRING'], $exception['file_name']) && !strstr($_SERVER['QUERY_STRING'], $exception['file_name']))
						continue 2;

				if (is_callable(array($moduleInstance, 'hook'.$hook_name)))
				{

					//Cache id
					$cache_id = $moduleInstance->name.'-'.$hook_name.'-'.FrontController::pmCM_getCacheId();
					//Check if module is allready output in this hook
					if (in_array($moduleInstance->name.'-'.$hook_name, $modulesHook)) {
						$cache_id .= $iModulesHook++;
					}
					$cache_id = md5($cache_id);
					//Take in consideration multiple instance per module & per hook (eg addblocks)
					$modulesHook[] = $moduleInstance->name.'-'.$hook_name;
					
					//Load global configuration
					$global_cache_configuration = pm_cachemanager::getGlobalConfiguration();

					//Get current cache lifetime
					if (isset(self::$hookModuleToIncludeFromCache[$hook_name][$moduleInstance->name]) && !self::$hookModuleToIncludeFromCache[$hook_name][$moduleInstance->name]['use_global'])
						$cache_lifetime = (int) self::$hookModuleToIncludeFromCache[$hook_name][$moduleInstance->name]['lifetime'] * 60;
					else $cache_lifetime = (int) $global_cache_configuration['modulecache_lifetime'] * 60;

					$hookArgs['altern'] = ++$altern;
					// Module in cache or excluded ?
					if (isset(self::$hookModuleToIncludeFromCache[$hook_name][$moduleInstance->name]) && ($dbCacheContent = pm_cachemanager::getDBCacheContentNotExpired($cache_id,true)) !== false)
					{
						//Output DB cache
						$output .= $dbCacheContent['content'];
						if (pm_cachemanager::_isFilledArray($dbCacheContent['css_diff']))
							$css_files = array_merge($css_files, $dbCacheContent['css_diff']);
						if (pm_cachemanager::_isFilledArray($dbCacheContent['js_diff']))
							$js_files = array_merge($js_files, $dbCacheContent['js_diff']);
					}
					else
					{
						$css_diff = $css_files;
						$js_diff = $js_files;
						$display = call_user_func(array($moduleInstance, 'hook'.$hook_name), $hookArgs);
						$css_diff = array_diff_assoc((array)$css_files,(array)$css_diff);
						$js_diff = array_diff_assoc((array)$js_files,(array)$js_diff);
						$output .= $display;
						
						//Save cache if necessary
						if (isset(self::$hookModuleToIncludeFromCache[$hook_name][$moduleInstance->name])) {
							pm_cachemanager::setDBCacheContentNotExpired($cache_id,$display,$id_hook,$moduleInstance->id,$css_diff,$js_diff,array(),$cache_lifetime);
						}
					}
				}
			}
		}
		if (self::hookNeedComment($hook_name))
			$output .= '<!-- PM_CM END '.$hook_name.' -->';
		return $output;
	}

	private static $_isLiveEditCache = null;
	private static function _pmIsLiveEdit() {
		if (self::$_isLiveEditCache == null)
			self::$_isLiveEditCache = ((Tools::isSubmit('live_edit') AND Tools::getValue('ad') AND (Tools::getValue('liveToken') == sha1(Tools::getValue('ad')._COOKIE_KEY_))));
		return self::$_isLiveEditCache;
	}

	private static $_loadCacheManagerClassCache = null;
	protected static function loadCacheManagerClass() {
		if (self::$_loadCacheManagerClassCache == null) 
			self::$_loadCacheManagerClassCache = (file_exists(_PS_ROOT_DIR_ . '/modules/pm_cachemanager/pm_cachemanager.php') && require_once(_PS_ROOT_DIR_ . '/modules/pm_cachemanager/pm_cachemanager.php'));
		return self::$_loadCacheManagerClassCache;
	}

	public static function hookExec($hook_name, $hookArgs = array(), $id_module = NULL)
	{
		if (
			!self::_pmIsLiveEdit()
			&& self::loadCacheManagerClass()
			&& pm_cachemanager::isActivated()
			&& (int)pm_cachemanager::getSpecificGlobalConfiguration('modulecache_active')
			&& !pm_cachemanager::hookIsExcluded($hook_name)
		) {
			// Cache Manager - Activated
			$result = self::hookExecCache($hook_name, $hookArgs, $id_module);
			return $result;
		} else {
			return parent::hookExec($hook_name, $hookArgs, $id_module);
		}
	}
}
