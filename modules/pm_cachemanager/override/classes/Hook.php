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
 **   PS: 1.5, 1.6                   *
 *************************************
 */
class Hook extends HookCore
{

	public static $hookModuleToIncludeFromCache;
	private static $hook_need_comment = array('extraleft','productfooter','extraright','productoutofstock','productactions','producttab','producttabcontent','displayleftcolumnproduct','displayrightcolumnproduct','actionproductoutofstock','displayfooterproduct','displayproductbuttons','displayproducttab','displayproducttabcontent');
	private static $hook_need_comment_result = array();
	private static $_DispatcherControllerInstance = null;

	private static function hookNeedComment($hook_name) {
		if (isset(self::$hook_need_comment_result[$hook_name])) return self::$hook_need_comment_result[$hook_name];
		if (in_array(strtolower($hook_name), self::$hook_need_comment)) self::$hook_need_comment_result[$hook_name] = true;
		else self::$hook_need_comment_result[$hook_name] = false;
		return self::$hook_need_comment_result[$hook_name];
	}

	public static function execCache($hook_name, $hook_args = array(), $id_module = null, $array_return = false, $check_exceptions = true, $use_push = false, $id_shop = null)
	{
		if (version_compare(_PS_VERSION_, '1.5.6.1', '>=')) {
			static $disable_non_native_modules = null;
			if ($disable_non_native_modules === null)
				$disable_non_native_modules = (bool)Configuration::get('PS_DISABLE_NON_NATIVE_MODULE');
		}
		
		// Check arguments validity
		if (($id_module && !is_numeric($id_module)) || !Validate::isHookName($hook_name))
			throw new PrestaShopException('Invalid id_module or hook_name');

		// If no modules associated to hook_name or recompatible hook name, we stop the function
		if (!$module_list = Hook::getHookModuleExecList($hook_name))
			return '';

		// Check if hook exists
		if (!$id_hook = Hook::getIdByName($hook_name))
			if (in_array($hook_name,self::$hook_need_comment))
				return '<!-- PM_CM START '.$hook_name.' --><!-- PM_CM END '.$hook_name.' -->';
			else return '';

		$iModulesHook = 1;
		$modulesHook = array();

		// Store list of executed hooks on this page
		Hook::$executed_hooks[$id_hook] = $hook_name;

		$context = Context::getContext();
		if (!isset($hook_args['cookie']) || !$hook_args['cookie'])
			$hook_args['cookie'] = $context->cookie;
		if (!isset($hook_args['cart']) || !$hook_args['cart'])
			$hook_args['cart'] = $context->cart;

		//Get hook to include with cache
		if (!isset(self::$hookModuleToIncludeFromCache) )
		{
			$db = Db::getInstance(_PS_USE_SQL_SLAVE_);
			$result = $db->ExecuteS('SELECT `hook_name`, `module_name`, `lifetime`, `use_global` FROM `'._DB_PREFIX_.'pm_cachemanager_hooks`', false);
			self::$hookModuleToIncludeFromCache = array();
			if ($result)
				while ($row = $db->nextRow()) {
					if (!isset(self::$hookModuleToIncludeFromCache[$row['hook_name']]))self::$hookModuleToIncludeFromCache[$row['hook_name']] = array();
					$module_name_cache = strtolower($row['module_name']);
					unset($row['module_name']);
					self::$hookModuleToIncludeFromCache[$row['hook_name']][$module_name_cache] = $row;
				}
		}
		
		// HookName strtolower
		$hookNameLowerCase = strtolower($hook_name);
		
		// Look on modules list
		$altern = 0;
		
		if ($array_return) $output[] = '';
		else $output = '';
		
		if (version_compare(_PS_VERSION_, '1.5.6.1', '>=')) {
			if ($disable_non_native_modules && !isset(Hook::$native_module))
				Hook::$native_module = Module::getNativeModuleList();
		}
		
		if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
			$different_shop = false;
			if ($id_shop !== null && Validate::isUnsignedId($id_shop) && $id_shop != $context->shop->getContextShopID()) {
				$old_context_shop_id = $context->shop->getContextShopID();
				$old_context = $context->shop->getContext();
				$old_shop = clone $context->shop;
				$shop = new Shop((int)$id_shop);
				if (Validate::isLoadedObject($shop)) {
					$context->shop = $shop;
					$context->shop->setContext(Shop::CONTEXT_SHOP, $shop->id);
					$different_shop = true;
				}
			}
		}
		
		if (self::hookNeedComment($hook_name)) {
			if ($array_return) $output[] = '<!-- PM_CM START '.$hook_name.' -->';
			else $output .= '<!-- PM_CM START '.$hook_name.' -->';
		}
		if (self::$_DispatcherControllerInstance == null) self::$_DispatcherControllerInstance = Dispatcher::getInstance()->getController();
		$controller = self::$_DispatcherControllerInstance;
		foreach ($module_list as $array)
		{
			// Check errors
			if ($id_module && $id_module != $array['id_module'])
				continue;
			
			if (version_compare(_PS_VERSION_, '1.5.6.1', '>=')) {
				if ((bool)$disable_non_native_modules && Hook::$native_module && count(Hook::$native_module) && !in_array($array['module'], self::$native_module))
					continue;
			}
			
			if (!($moduleInstance = Module::getInstanceByName($array['module'])))
				continue;

			// Check permissions
			if ($check_exceptions) {
				$exceptions = $moduleInstance->getExceptions($array['id_hook']);
				
				if (version_compare(_PS_VERSION_, '1.6.0.6', '>=')) {
					$controller_obj = Context::getContext()->controller;
					//check if current controller is a module controller
					if (isset($controller_obj->module) && Validate::isLoadedObject($controller_obj->module))
						self::$_DispatcherControllerInstance = 'module-'.$controller_obj->module->name.'-'.self::$_DispatcherControllerInstance;
				}
				
				if (self::$_DispatcherControllerInstance != null && in_array(self::$_DispatcherControllerInstance, $exceptions)) continue;
				
				//retro compat of controller names
				if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
					$matching_name = array(
						'authentication' => 'auth',
						'productscomparison' => 'compare',
					);
				} else {
					$matching_name = array(
						'authentication' => 'auth',
						'compare' => 'products-comparison',
					);
				}
				if (isset($matching_name[$controller]) && in_array($matching_name[$controller], $exceptions))
					continue;
				if (Validate::isLoadedObject($context->employee) && !$moduleInstance->getPermission('view', $context->employee))
					continue;
			}
			
			if (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && $use_push && !$moduleInstance->allow_push)
				continue;
			
			$retro_hook_name = Hook::getRetroHookName($hook_name);

			// Check which / if method is callable
			$hook_callable = is_callable(array($moduleInstance, 'hook'.$hook_name));
			$hook_retro_callable = is_callable(array($moduleInstance, 'hook'.$retro_hook_name));
			if (($hook_callable || $hook_retro_callable) && Module::preCall($moduleInstance->name))
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
				if (isset(self::$hookModuleToIncludeFromCache[$hookNameLowerCase][$moduleInstance->name]) && !self::$hookModuleToIncludeFromCache[$hookNameLowerCase][$moduleInstance->name]['use_global'])
					$cache_lifetime = (int) self::$hookModuleToIncludeFromCache[$hookNameLowerCase][$moduleInstance->name]['lifetime'] * 60;
				else $cache_lifetime = (int) $global_cache_configuration['modulecache_lifetime'] * 60;

				$hook_args['altern'] = ++$altern;
				
				if (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && $use_push && isset($moduleInstance->push_filename) && file_exists($moduleInstance->push_filename))
					Tools::waitUntilFileIsModified($moduleInstance->push_filename, $moduleInstance->push_time_limit);

				// Module in cache or excluded ?
				if (isset(self::$hookModuleToIncludeFromCache[$hookNameLowerCase][$moduleInstance->name]) && ($dbCacheContent = pm_cachemanager::getDBCacheContentNotExpired($cache_id,true)) !== false)
				{
					//Output DB cache
					if ($array_return) $output[] = $dbCacheContent['content'];
					else $output .= $dbCacheContent['content'];
					if (pm_cachemanager::_isFilledArray($dbCacheContent['css_diff']))
						$context->controller->css_files = array_merge($context->controller->css_files, $dbCacheContent['css_diff']);
					if (pm_cachemanager::_isFilledArray($dbCacheContent['js_diff']))
						$context->controller->js_files = array_merge($context->controller->js_files, $dbCacheContent['js_diff']);
					if (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && pm_cachemanager::_isFilledArray($dbCacheContent['jsdef_diff']))
						Media::addJsDef($dbCacheContent['jsdef_diff']);
				} else {
					if (isset(self::$hookModuleToIncludeFromCache[$hookNameLowerCase][$moduleInstance->name])) {
						$css_diff = $js_diff = $jsdef_diff = array();
						if (isset($context->controller->css_files))
							$css_diff = $context->controller->css_files;
						if (isset($context->controller->js_files))
							$js_diff = $context->controller->js_files;
						if (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && is_array(Media::getJsDef()) && sizeof(Media::getJsDef()))
							$jsdef_diff = Media::getJsDef();
					}
					
					// Call hook method
					if ($hook_callable)
						$display = $moduleInstance->{'hook'.$hook_name}($hook_args);
					else if ($hook_retro_callable)
						$display = $moduleInstance->{'hook'.$retro_hook_name}($hook_args);

					if (isset(self::$hookModuleToIncludeFromCache[$hookNameLowerCase][$moduleInstance->name])) {
						if (isset($context->controller->css_files))
							$css_diff = array_diff_assoc((array)$context->controller->css_files,(array)$css_diff);
						if (isset($context->controller->js_files))
							$js_diff = array_diff_assoc((array)$context->controller->js_files,(array)$js_diff);
						if (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && is_array(Media::getJsDef()) && sizeof(Media::getJsDef())) {
							$jsdef_diff = pm_cachemanager::array_diff_assoc_recursive((array)Media::getJsDef(), (array)$jsdef_diff);
						}
					}
					
					if ($array_return)
						if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
							$output[$moduleInstance->name] = $display;
						else
							$output[] = $display;
					else
						$output .= $display;
					//Save cache if necessary
					if (isset(self::$hookModuleToIncludeFromCache[$hookNameLowerCase][$moduleInstance->name])) {
						pm_cachemanager::setDBCacheContentNotExpired($cache_id, $display, $id_hook, $moduleInstance->id, $css_diff, $js_diff, $jsdef_diff, $cache_lifetime);
					}
				}
			}
		}
		
		if (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && $different_shop) {
			$context->shop = $old_shop;
			$context->shop->setContext($old_context, $shop->id);
		}
		
		if (self::hookNeedComment($hook_name)) {
			if ($array_return) $output[] = '<!-- PM_CM END '.$hook_name.' -->';
			else $output .= '<!-- PM_CM END '.$hook_name.' -->';
		}
		return $output;
	}

	private static $_isLiveEditCache = null;
	private static function _pmIsLiveEdit() {
		if (self::$_isLiveEditCache == null)
			self::$_isLiveEditCache = (Tools::isSubmit('live_edit') && Tools::getValue('ad') && Tools::getValue('liveToken') == Tools::getAdminToken('AdminModulesPositions'.(int)Tab::getIdFromClassName('AdminModulesPositions').(int)Tools::getValue('id_employee')));
		return self::$_isLiveEditCache;
	}

	private static $_loadCacheManagerClassCache = null;
	protected static function loadCacheManagerClass() {
		if (self::$_loadCacheManagerClassCache == null) 
			self::$_loadCacheManagerClassCache = (file_exists(_PS_ROOT_DIR_ . '/modules/pm_cachemanager/pm_cachemanager.php') && require_once(_PS_ROOT_DIR_ . '/modules/pm_cachemanager/pm_cachemanager.php'));
		return self::$_loadCacheManagerClassCache;
	}

	public static function exec($hook_name, $hook_args = array(), $id_module = null, $array_return = false, $check_exceptions = true, $use_push = false, $id_shop = null)
	{
		if (
			!self::_pmIsLiveEdit() && !Validate::isLoadedObject(Context::getContext()->employee)
			&& self::loadCacheManagerClass()
			&& pm_cachemanager::isActivated()
			&& pm_cachemanager::getSpecificGlobalConfiguration('modulecache_active')
			&& !pm_cachemanager::hookIsExcluded($hook_name)
		) {
			// Cache Manager - Activated
			$result = self::execCache($hook_name, $hook_args, $id_module, $array_return, $check_exceptions, $use_push, $id_shop);
			return $result;
		} else {
			return parent::exec($hook_name, $hook_args, $id_module, $array_return, $check_exceptions ,$use_push, $id_shop);
		}
	}
}
?>