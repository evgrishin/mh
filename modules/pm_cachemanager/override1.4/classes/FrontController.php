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

class FrontController extends FrontControllerCore
{
	public $dbCacheContent;
	protected static $cacheManagerCentralStatus = array();
	protected static $pmCM_getCacheIdCache;

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
		
		global $cookie;
		if(isset ( $cookie ) && isset ( $cookie->id_customer ) && $cookie->id_customer) {
			$cookie->id_group = Customer::getDefaultGroupId($cookie->id_customer);
		}
		self::$pmCM_getCacheIdCache = md5(sprintf('%s|%s|%s|%s|%d|%d|%d|%d|%d|%s',
			(isset($cookie->iso_code_country) ? $cookie->iso_code_country : 0),
			str_replace ( '.', '', $_SERVER ['HTTP_HOST'] ),
			$_SERVER ['PHP_SELF'],
			_THEME_NAME_,
			(isset ( $cookie ) ? $cookie->id_lang : 0),
			(isset ( $cookie ) ? $cookie->id_group : 0),
			(isset ( $cookie ) ? $cookie->id_country : 0),
			(isset ( $cookie ) ? $cookie->id_customer : 0),
			(isset ( $cookie ) ? $cookie->id_currency : 0),
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
			self::$_isLiveEditCache = ((Tools::isSubmit('live_edit') AND Tools::getValue('ad') AND (Tools::getValue('liveToken') == sha1(Tools::getValue('ad')._COOKIE_KEY_))));
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
		$this->replaceHook('extraLeft',Module::hookExec('extraLeft'));
		$this->replaceHook('extraRight',Module::hookExec('extraRight'));
		$this->replaceHook('productOutOfStock',Hook::productOutOfStock($this->product));

		/* Category */
		$category = false;
		if (isset($_SERVER['HTTP_REFERER']) && preg_match('!^(.*)\/([0-9]+)\-(.*[^\.])|(.*)id_category=([0-9]+)(.*)$!', $_SERVER['HTTP_REFERER'], $regs) && !strstr($_SERVER['HTTP_REFERER'], '.html'))
		{
			if (isset($regs[2]) && is_numeric($regs[2]))
			{
				if (Product::idIsOnCategoryId((int)($this->product->id), array('0' => array('id_category' => (int)($regs[2])))))
					$category = new Category((int)($regs[2]), (int)(self::$cookie->id_lang));
			}
			elseif (isset($regs[5]) && is_numeric($regs[5]))
			{
				if (Product::idIsOnCategoryId((int)($this->product->id), array('0' => array('id_category' => (int)($regs[5])))))
					$category = new Category((int)($regs[5]), (int)(self::$cookie->id_lang));
			}
		}
		if (!$category)
			$category = new Category($this->product->id_category_default, (int)(self::$cookie->id_lang));

		$this->replaceHook('productFooter',Hook::productFooter($this->product, $category));
		$this->replaceHook('productActions',Module::hookExec('productActions'));
		$this->replaceHook('productTab',Module::hookExec('productTab'));
		$this->replaceHook('productTabContent',Module::hookExec('productTabContent'));
	}

	public function run() {
		$this->init();
		$this->preProcess();
		$this->displayHeader();

		if (
			!self::_pmIsLiveEdit()
			&& self::loadCacheManagerClass()
			&& pm_cachemanager::isActivated()
			&& (int)pm_cachemanager::getSpecificGlobalConfiguration('centralcache_active')
			&& pm_cachemanager::hasCentralCacheActivatedFor($this, (isset($this->product) ? $this->product : NULL))
		) {
			// Cache Manager - Activated

			//Get cache content if exists
			$this->dbCacheContent = pm_cachemanager::getDBCacheContentNotExpired(self::pmCM_getCacheId());

			//If cache not exists execute normal process
			if ($this->dbCacheContent === false) {
				$this->process();
			}else self::process();
			$this->displayContentPM();
		} else {
			$this->process();
			$this->displayContent();
		}
		$this->displayFooter();
	}

	public function displayContentPM() {
		// Cache Manager - Activated
		Tools::safePostVars();
		self::$smarty->assign('errors', $this->errors);

		//If cache exists display it
		if($this->dbCacheContent !== false) {
			// does hooks need to be replaced ?
			if (method_exists($this, 'hookReplacement_'.get_class($this))) $this->{'hookReplacement_'.get_class($this)}();
			echo $this->dbCacheContent;
		} else {
			// Get output
			$output = pm_cachemanager::fetchSmartyContent($this, self::$smarty, (isset($this->product) ? $this->product : NULL), (isset($this->manufacturer) ? $this->manufacturer : NULL), (isset($this->supplier) ? $this->supplier : NULL));
			if ($output !== false) {
				//Set cache for next display
				pm_cachemanager::setDBCacheContentNotExpired(self::pmCM_getCacheId(), '<!-- PM_CM CENTRAL CACHE START - '.get_class($this).' -->'.$output.'<!-- PM_CM CENTRAL CACHE END - '.get_class($this).' -->');
				//Output content
				echo $output;
			} else {
				self::displayContent();
			}
		}
	}
}
