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
class FrontController extends FrontControllerCore
{

	public $dbCacheContent;
	protected static $cacheManagerCentralStatus = array();
	protected static $pmCM_getCacheIdCache;

	public function run()
	{
		$this->init();
		if ($this->checkAccess())
		{
			// setMedia MUST be called before postProcess
			if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className)))
				$this->setMedia();

			// postProcess handles ajaxProcess
			$this->postProcess();

			if (!empty($this->redirect_after))
				$this->redirect();

			if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className)))
				$this->initHeader();

			if ($this->viewAccess()) {
				if (
					!self::_pmIsLiveEdit() && !Validate::isLoadedObject(Context::getContext()->employee)
					&& self::loadCacheManagerClass()
					&& pm_cachemanager::isActivated()
					&& (int)pm_cachemanager::getSpecificGlobalConfiguration('centralcache_active')
					&& pm_cachemanager::hasCentralCacheActivatedFor($this, (isset($this->product) ? $this->product : NULL))
				) {
					// get data from cache
					if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
						$this->dbCacheContent = pm_cachemanager::getDBCacheContentNotExpired(self::pmCM_getCacheId(), true);
						if ($this->dbCacheContent !== false) {
							if (pm_cachemanager::_isFilledArray($this->dbCacheContent['jsdef_diff']))
								Media::addJsDef($this->dbCacheContent['jsdef_diff']);
							$this->dbCacheContent = $this->dbCacheContent['content'];
						}
					} else {
						$this->dbCacheContent = pm_cachemanager::getDBCacheContentNotExpired(self::pmCM_getCacheId());
					}
					if ($this->dbCacheContent === false)
						$this->initContent();
					else
						self::initContent();
				} else {
					$this->initContent();
				}
			} else {
				$this->errors[] = Tools::displayError('Access denied.');
			}
			
			if (!$this->content_only && ($this->display_footer || (isset($this->className) && $this->className)))
				$this->initFooter();

			// default behavior for ajax process is to use $_POST[action] or $_GET[action]
			// then using displayAjax[action]
			if ($this->ajax)
			{
				$action = Tools::getValue('action');
				if (!empty($action) && method_exists($this, 'displayAjax'.Tools::toCamelCase($action, true)))
					$this->{'displayAjax'.$action}();
				elseif (method_exists($this, 'displayAjax'))
					$this->displayAjax();
			}
			else
				$this->display();
		}
		else
		{
			$this->initCursedPage();
			$this->display();
		}
	}
	
	public static function pmCM_getCacheId() {
		if (isset(self::$pmCM_getCacheIdCache))
			return self::$pmCM_getCacheIdCache;
		
		if (isset($_GET) && is_array($_GET) && sizeof($_GET)) {
			$httpParameters = $_GET;
			foreach (array('gclid', 'x', 'y', 'utm_source', 'utm_medium', 'utm_term', 'utm_content', 'utm_campaign') as $httpParamKey)
				if (isset($httpParameters[$httpParamKey]))
					unset($httpParameters[$httpParamKey]);
			ksort($httpParameters);
		} else {
			$httpParameters = array();
		}
		
		$context = Context::getContext();
		
		$compareProducts = array();
		if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
			$compareProducts = CompareProduct::getCompareProducts($context->cookie->id_compare);
			if (is_array($compareProducts) && sizeof($compareProducts))
				sort($compareProducts);
			else
				$compareProducts = array();
		}
		
		self::$pmCM_getCacheIdCache = md5(sprintf('%d|%s|%s|%s|%s|%d|%d|%d|%d|%d|%d|%s|%s',
			((version_compare(_PS_VERSION_, '1.6.0.0', '>=') ? $context->controller->useMobileTheme() : $context->getMobileDevice()) ? 1 : 0),
			(isset($context->cookie->iso_code_country) ? $context->cookie->iso_code_country : 0),
			$_SERVER['HTTP_HOST'],
			$_SERVER['PHP_SELF'],
			_THEME_NAME_,
			(isset($context->cookie->id_lang) ? $context->cookie->id_lang : 0),
			(Validate::isLoadedObject($context->customer) ? $context->customer->id : 0),
			(Validate::isLoadedObject($context->customer) ? $context->customer->id_default_group : 0),
			(isset($context->cookie->id_country) ? $context->cookie->id_country : 0),
			(isset($context->cookie->id_currency) ? $context->cookie->id_currency : 0),
			$context->shop->id,
			(version_compare(_PS_VERSION_, '1.6.0.0', '>=') ? implode(',', $compareProducts) : ''),
			(sizeof($httpParameters) ? self::r_implode ('|', $httpParameters) : '')
		));
		return self::$pmCM_getCacheIdCache;
	}

	public static function r_implode($glue, $pieces) {
		foreach ( $pieces as $key=>$r_pieces ) {
			if (is_array ( $r_pieces )) {
				$retVal [] = self::r_implode ( $glue, $r_pieces );
			} else {
				$retVal [] = $key.'-'.$r_pieces;
			}
		}
		return implode ( $glue, $retVal );
	}

	private function _pmReplaceContent($startIdentifier, $endIdentifier, $newContent) {
		$length_start = strlen($startIdentifier);
		$length_end = strlen($endIdentifier);
		$pos_offset = 0;
		$pos_start = strpos($this->dbCacheContent, $startIdentifier, $pos_offset);
		while ($pos_start !== false) {
			$pos_end = strpos($this->dbCacheContent, $endIdentifier, $pos_start);
			if ($pos_end !== false) {
				$to_replace = substr($this->dbCacheContent, $pos_start, $pos_end-$pos_start + $length_end);
				$this->dbCacheContent = str_replace($to_replace, $newContent, $this->dbCacheContent);
				unset($to_replace);
			}
			$pos_offset = $pos_end + $length_end;
			if ($pos_offset > strlen($this->dbCacheContent)) {
				$post_start = false;
			} else {
				$pos_start = strpos($this->dbCacheContent, $startIdentifier, $pos_offset);
			}
		}
	}

	protected function replaceHook($hook_name,$content_replace) {
		$this->_pmReplaceContent('<!-- PM_CM START '.strtolower($hook_name).' -->', '<!-- PM_CM END '.strtolower($hook_name).' -->', $content_replace);
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

	// ProductController - hookReplacement
	protected function hookReplacement_ProductController() {
		$this->replaceHook('displayLeftColumnProduct',Hook::exec('displayLeftColumnProduct'));
		$this->replaceHook('displayRightColumnProduct',Hook::exec('displayRightColumnProduct'));
		$this->replaceHook('actionProductOutOfStock',Hook::exec('actionProductOutOfStock', array('product' => $this->product)));

		// Load category
		if (isset($_SERVER['HTTP_REFERER'])
			&& !strstr($_SERVER['HTTP_REFERER'], Tools::getHttpHost()) // Assure us the previous page was one of the shop
			&& preg_match('!^(.*)\/([0-9]+)\-(.*[^\.])|(.*)id_category=([0-9]+)(.*)$!', $_SERVER['HTTP_REFERER'], $regs))
		{
			// If the previous page was a category and is a parent category of the product use this category as parent category
			if (isset($regs[2]) && is_numeric($regs[2]))
			{
				if (Product::idIsOnCategoryId((int)$this->product->id, array('0' => array('id_category' => (int)$regs[2]))))
					$this->category = new Category($regs[2], (int)$this->context->cookie->id_lang);
			}
			else if (isset($regs[5]) && is_numeric($regs[5]))
			{
				if (Product::idIsOnCategoryId((int)$this->product->id, array('0' => array('id_category' => (int)$regs[5]))))
					$this->category = new Category($regs[5], (int)$this->context->cookie->id_lang);
			}
		}
		else
			// Set default product category
			$this->category = new Category($this->product->id_category_default, (int)$this->context->cookie->id_lang);

		if (Validate::isLoadedObject($this->category) && Validate::isLoadedObject($this->product)) {
			$this->context->smarty->assign(array(
				'path' => Tools::getPath($this->category->id, $this->product->name, true),
				'product' => $this->product,
				'body_classes' => array(
					$this->php_self.'-'.$this->product->id, 
					$this->php_self.'-'.$this->product->link_rewrite,
					'category-'.(isset($this->category) ? $this->category->id : ''),
					'category-'.(isset($this->category) ? $this->category->getFieldByLang('link_rewrite') : '')
				),
			));
		}
		
		$this->replaceHook('displayFooterProduct',Hook::exec('displayFooterProduct', array('product' => $this->product, 'category' => $this->category)));
		$this->replaceHook('displayProductButtons',Hook::exec('displayProductButtons'));
		$this->replaceHook('displayProductTab',Hook::exec('displayProductTab'));
		$this->replaceHook('displayProductTabContent',Hook::exec('displayProductTabContent'));
	}
	
	// CategoryController - hookReplacement
	protected function hookReplacement_CategoryController() {
		if (Validate::isLoadedObject($this->category)) {
			$this->context->smarty->assign(array(
				'path' => Tools::getPath($this->category->id),
				'category' => $this->category,
				'body_classes' => array($this->php_self.'-'.$this->category->id, $this->php_self.'-'.$this->category->link_rewrite),
			));
		}
		
		if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
			$compared_products = array();
			if (Configuration::get('PS_COMPARATOR_MAX_ITEM') && isset($this->context->cookie->id_compare))
				$compared_products = CompareProduct::getCompareProducts($this->context->cookie->id_compare);
			Media::addJsDef(array('comparedProductsIds' => $compared_products));
		}
	}
	
	// ManufacturerController - hookReplacement
	protected function hookReplacement_ManufacturerController() {
		global $_LANG;
		if (Validate::isLoadedObject($this->manufacturer)) {
			$this->context->smarty->assign(array(
				'path' => ($this->manufacturer->active ? Tools::safeOutput($this->manufacturer->name) : $_LANG['manufacturer-list_'.md5('Manufacturers:')]),
				'manufacturer' => $this->manufacturer,
				'body_classes' => ($this->manufacturer->active ? array($this->php_self.'-'.$this->manufacturer->id, $this->php_self.'-'.$this->manufacturer->link_rewrite) : array())
			));
		} else {
			$this->context->smarty->assign(array(
				'path' => $_LANG['manufacturer-list_'.md5('Manufacturers:')],
			));
		}
	}
	
	// SupplierController - hookReplacement
	protected function hookReplacement_SupplierController() {
		global $_LANG;
		if (Validate::isLoadedObject($this->supplier)) {
			$this->context->smarty->assign(array(
				'path' => ($this->supplier->active ? Tools::safeOutput($this->supplier->name) : $_LANG['supplier-list_'.md5('Suppliers:')]),
				'supplier' => $this->supplier,
				'body_classes' => ($this->supplier->active ? array($this->php_self.'-'.$this->supplier->id, $this->php_self.'-'.$this->supplier->link_rewrite) : array())
			));
		} else {
			$this->context->smarty->assign(array(
				'path' => $_LANG['supplier-list_'.md5('Suppliers:')],
			));
		}
	}
	
	// CmsController - hookReplacement
	protected function hookReplacement_CmsController() {
		if (Validate::isLoadedObject($this->cms)) {
			if (isset($this->cms->id_cms_category) && $this->cms->id_cms_category)
				$this->context->smarty->assign('path', Tools::getFullPath($this->cms->id_cms_category, $this->cms->meta_title, 'CMS'));
			else if (isset($this->cms_category->meta_title))
				$this->context->smarty->assign('path', Tools::getFullPath(1, $this->cms_category->meta_title, 'CMS'));
			$this->context->smarty->assign(array(
				'body_classes' => array($this->php_self.'-'.$this->cms->id, $this->php_self.'-'.$this->cms->link_rewrite),
				'cms' => $this->cms,
			));
			if (isset($this->cms->indexation) && $this->cms->indexation == 0)
				$this->context->smarty->assign('nobots', true);
		} else if (Validate::isLoadedObject($this->cms_category)) {
			$this->context->smarty->assign(array(
				'path' => ($this->cms_category->id !== 1) ? Tools::getPath($this->cms_category->id, $this->cms_category->name, false, 'CMS') : '',
				'body_classes' => array($this->php_self.'-'.$this->cms_category->id, $this->php_self.'-'.$this->cms_category->link_rewrite),
				'cms_category' => $this->cms_category,
			));
		}
	}
	
	// SitemapController - hookReplacement
	protected function hookReplacement_SitemapController() {
		global $_LANG;
		if (isset($_LANG['sitemap_'.md5('Sitemap')]))
			$this->context->smarty->assign('path', $_LANG['sitemap_'.md5('Sitemap')]);
	}
	// NewProductsController - hookReplacement
	protected function hookReplacement_NewProductsController() {
		global $_LANG;
		if (isset($_LANG['new-products_'.md5('New products')]))
			$this->context->smarty->assign('path', $_LANG['new-products_'.md5('New products')]);
	}
	
	// BestSalesController - hookReplacement
	protected function hookReplacement_BestSalesController() {
		global $_LANG;
		if (isset($_LANG['best-sales_'.md5('Top sellers')]))
			$this->context->smarty->assign('path', $_LANG['best-sales_'.md5('Top sellers')]);
	}
	
	// PricesDropController - hookReplacement
	protected function hookReplacement_PricesDropController() {
		global $_LANG;
		if (isset($_LANG['prices-drop_'.md5('Price drop')]))
			$this->context->smarty->assign('path', $_LANG['prices-drop_'.md5('Price drop')]);
	}
	
	public function display()
	{
		if (
			!self::_pmIsLiveEdit() && !Validate::isLoadedObject(Context::getContext()->employee)
			&& self::loadCacheManagerClass()
			&& pm_cachemanager::isActivated()
			&& (int)pm_cachemanager::getSpecificGlobalConfiguration('centralcache_active')
			&& pm_cachemanager::hasCentralCacheActivatedFor($this, (isset($this->product) ? $this->product : NULL))
		) {
			// Cache Manager - Activated
			Tools::safePostVars();

			// assign css_files and js_files at the very last time
			if ((Configuration::get('PS_CSS_THEME_CACHE') || Configuration::get('PS_JS_THEME_CACHE')) && is_writable(_PS_THEME_DIR_.'cache'))
			{
				// CSS compressor management
				if (Configuration::get('PS_CSS_THEME_CACHE'))
					$this->css_files = Media::cccCSS($this->css_files);
				//JS compressor management
				if (Configuration::get('PS_JS_THEME_CACHE') && !(version_compare(_PS_VERSION_, '1.6.0.0', '>=') ? $this->useMobileTheme() : Context::getContext()->getMobileDevice()))
					$this->js_files = Media::cccJs($this->js_files);
			}

			$this->context->smarty->assign(array(
				'js_defer' => (bool)Configuration::get('PS_JS_DEFER'),
				'css_files' => $this->css_files,
				'js_files' => array_unique($this->js_files),
				'errors' => $this->errors,
				'display_header' => $this->display_header,
				'display_footer' => $this->display_footer,
			));
			
			$jsdef_diff = array();
			if (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && is_array(Media::getJsDef()) && sizeof(Media::getJsDef()))
				$jsdef_diff = Media::getJsDef();

			$layout = $this->getLayout();
			if ($layout)
			{
				if (isset($this->dbCacheContent) && $this->dbCacheContent !== false) {
					// does hooks need to be replaced ?
					if (method_exists($this, 'hookReplacement_'.get_class($this))) $this->{'hookReplacement_'.get_class($this)}();
					$this->context->smarty->assign('template', $this->dbCacheContent);
				}
				elseif ($this->template) {
					$template = $this->context->smarty->fetch($this->template);
					$this->context->smarty->assign('template', $template);
					//Set cache for next display
					if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
						$jsdef_diff = pm_cachemanager::array_diff_assoc_recursive((array)Media::getJsDef(),(array)$jsdef_diff);
					pm_cachemanager::setDBCacheContentNotExpired(self::pmCM_getCacheId(),'<!-- PM_CM CENTRAL CACHE START - '.get_class($this).' -->'.$template.'<!-- PM_CM CENTRAL CACHE END - '.get_class($this).' -->', false, false, array(), array(), $jsdef_diff);
				}
				else // For retrocompatibility with 1.4 controller
				{
					ob_start();
					$this->displayContent();
					$template = ob_get_contents();
					ob_clean();
					//Set cache for next display
					if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
						$jsdef_diff = pm_cachemanager::array_diff_assoc_recursive((array)Media::getJsDef(),(array)$jsdef_diff);
					pm_cachemanager::setDBCacheContentNotExpired(self::pmCM_getCacheId(),'<!-- PM_CM CENTRAL CACHE START - '.get_class($this).' -->'.$template.'<!-- PM_CM CENTRAL CACHE END - '.get_class($this).' -->', false, false, array(), array(), $jsdef_diff);
					$this->context->smarty->assign('template', $template);
				}
				$this->smartyOutputContent($layout);
			}
			else
			{
				// BEGIN - 1.4 retrocompatibility - will be removed in 1.6
				Tools::displayAsDeprecated('layout.tpl is missing in your theme directory');
				if (isset($this->dbCacheContent) && $this->dbCacheContent !== false) {
					if (method_exists($this, 'hookReplacement_'.get_class($this)))
						$this->{'hookReplacement_'.get_class($this)}();
					if ($this->display_header)
						$this->smartyOutputContent(_PS_THEME_DIR_.'header.tpl');
					echo $this->dbCacheContent;
				} elseif ($this->template) {
					if ($this->display_header)
						$this->smartyOutputContent(_PS_THEME_DIR_.'header.tpl');
					ob_start();
					$this->smartyOutputContent($this->template);
					$template = ob_get_contents();
					ob_flush();
					//Set cache for next display
					if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
						$jsdef_diff = pm_cachemanager::array_diff_assoc_recursive((array)Media::getJsDef(),(array)$jsdef_diff);
					pm_cachemanager::setDBCacheContentNotExpired(self::pmCM_getCacheId(),'<!-- PM_CM CENTRAL CACHE START - '.get_class($this).' -->'.$template.'<!-- PM_CM CENTRAL CACHE END - '.get_class($this).' -->', false, false, array(), array(), $jsdef_diff);
				}
				else {
					if ($this->display_header)
						$this->smartyOutputContent(_PS_THEME_DIR_.'header.tpl');
					// For retrocompatibility with 1.4 controller
					ob_start();
					$this->displayContent();
					$template = ob_get_contents();
					ob_flush();
					//Set cache for next display
					if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
						$jsdef_diff = pm_cachemanager::array_diff_assoc_recursive((array)Media::getJsDef(),(array)$jsdef_diff);
					pm_cachemanager::setDBCacheContentNotExpired(self::pmCM_getCacheId(),'<!-- PM_CM CENTRAL CACHE START - '.get_class($this).' -->'.$template.'<!-- PM_CM CENTRAL CACHE END - '.get_class($this).' -->', false, false, array(), array(), $jsdef_diff);
				}

				if ($this->display_footer)
					$this->smartyOutputContent(_PS_THEME_DIR_.'footer.tpl');
				// END - 1.4 retrocompatibility - will be removed in 1.6
			}
			return true;
		} else {
			return parent::display();
		}
	}

	protected function smartyOutputContent($content) {
		if (method_exists('Controller', 'smartyOutputContent')) {
			parent::smartyOutputContent($content);
		} else {
			$this->context->cookie->write();
			$this->context->smarty->display($content);
		}
	}
}
?>