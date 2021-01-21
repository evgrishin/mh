<?php
/**
 *
 * PM_CacheManagement
 *
 * @category  Front Office Features
 * @author    Presta-Module.com <support@presta-module.com>
 * @copyright Presta-Module 2014
 * @version   1.2.12
 * 		_______  ____    ____
 * 	   |_   __ \|_   \  /   _|
 * 		 | |__) | |   \/   |
 * 		 |  ___/  | |\  /| |
 * 		_| |_    _| |_\/_| |_
 * 	   |_____|  |_____||_____|
 *
 *
 *************************************
 **           Cache Manager          *
 **   http://www.presta-module.com   *
 *************************************
 **   PS: 1.4, 1.5, 1.6              *
 *************************************
 */
if (!defined('_PS_VERSION_')) exit;
require_once (_PS_ROOT_DIR_ . '/modules/pm_cachemanager/cacheManagerCoreClass.php');
class pm_cachemanager extends cacheManagerCoreClass {
	public static $globalCacheConfig;
	protected static $cacheModuleHook;
	public static $_module_prefix = 'CM';
	protected $_css_js_to_load = array(
									'jquery',
									'jquerytiptip',
									'admincore',
									'adminmodule',
									'jgrowl',
									'datatables');
	public static $module_default_cache_time = 60;
	protected $_registerOnHooks = array('backOfficeTop','backOfficeHeader','afterSaveAttribute','afterSaveFeatureValue','afterSaveProduct','afterSaveFeature','afterSaveAttributeGroup','categoryUpdate','newOrder');
	public static $hook_list = array();
	public static $module_list = array();
	public static $hookExclude = array('payment','neworder','paymentconfirm','paymentreturn','updatequantity','cart','authentication','addproduct','updateproduct','deleteproduct','invoice','updateorderstatus','adminorder','pdfinvoice','admincustomers','orderconfirmation','createaccount','customeraccount','orderslip','createaccountform','adminstatsmodules','graphengine','orderreturn','backofficehome','gridengine','watermark','cancelproduct','updateproductattribute','search','backbeforepayment','updatecarrier','postupdateorderstatus','deleteproductattribute','processcarrier','orderdetail','beforecarrier','paymentccadded','categoryaddition','categoryupdate','categorydeletion','beforeauthentication','aftercreatehtaccess','aftersaveadminmeta','attributegroupform','aftersaveattributegroup','afterdeleteattributegroup','aftersavefeature','afterdeletefeature','aftersaveproduct','productlistassign','postprocessattributegroup','postprocessfeature','postprocessfeaturevalue','afterdeletefeaturevalue','aftersavefeaturevalue','postprocessattribute','afterdeleteattribute','aftersaveattribute','frontcanonicalredirect','actiontaxmanager','actiontaxmanager','actioncustomeraccountadd', 'displayorderconfirmation', 'displaybeforepayment','featureform','featurevalueform','attributeform','header','urlprocessing','orderdetaildisplayed','backofficefooter','backofficetop','backofficeheader', 'moduleroutes', 'mcbelow', 'mcabove', 'displayoverridetemplate');
	public $moduleNameExclude = array(
		'advganalytics',
		'blockcart',
		'blockcart2',
		'blockmyaccount',
		'blockmyaccountfooter',
		'blocknewsletter',
		'blockuserinfo',
		'blockviewed',
		'carriercompare',
		'cron',
		'crossselling',
		'ganalytics',
		'gsnippetsreviews',
		'mc360',
		'mobile_theme',
		'pm_advancedblockcart',
		'pm_advancedtrackingwizard',
		'pm_crosssellingoncart',
		'pm_abfighting',
		'pm_livepreview',
		'pm_bskin',
		'popcart',
		'shoppingfluxmodule',
		'shopymind',
		'statsdata',
	);
	public static $module_default_in_cache = array(
		array('extraleft', 'pm_adsandslideshow', 300, 1),
		array('extraleft', 'pm_advancedfeedback', 300, 1),
		array('extraleft', 'sendtoafriend', 300, 1),
		array('extraright', 'pm_adsandslideshow', 300, 1),
		array('extraright', 'pm_advancedfeedback', 300, 1),
		array('extraright', 'pm_advancedpack', 300, 1),
		array('footer', 'blockcms', 300, 1),
		array('footer', 'blocklayered', 300, 1),
		array('footer', 'blockcategories', 300, 1),
		array('footer', 'blockreinsurance', 300, 1),
		array('footer', 'blockcontactinfos', 300, 1),
		array('footer', 'blockmyaccountfooter', 300, 1),
		array('footer', 'blocksocial', 300, 1),
		array('footer', 'pm_adsandslideshow', 300, 1),
		array('footer', 'themeconfigurator', 300, 1),
		array('home', 'editorial', 300, 1),
		array('home', 'homefeatured', 300, 1),
		array('home', 'pm_adsandslideshow', 300, 1),
		array('home', 'pm_advancedfeedback', 300, 1),
		array('home', 'pm_advancedsearch4', 300, 1),
		array('home', 'homeslider', 300, 1),
		array('home', 'themeconfigurator', 300, 1),
		array('home', 'blockcmsinfo', 300, 1),
		array('home', 'blockfacebook', 300, 1),
		array('leftcolumn', 'blockadvertising', 300, 1),
		array('leftcolumn', 'blockcategories', 300, 1),
		array('leftcolumn', 'blockcms', 300, 1),
		array('leftcolumn', 'blocklayered', 300, 1),
		array('leftcolumn', 'blockmyaccount', 300, 1),
		array('leftcolumn', 'blocklink', 300, 1),
		array('leftcolumn', 'blockbestsellers', 300, 1),
		array('leftcolumn', 'blocknewproducts', 300, 1),
		array('leftcolumn', 'blockspecials', 300, 1),
		array('leftcolumn', 'blockstore', 300, 1),
		array('leftcolumn', 'blockmanufacturer', 300, 1),
		array('leftcolumn', 'blockpaymentlogo', 300, 1),
		array('leftcolumn', 'blocksupplier', 300, 1),
		array('leftcolumn', 'blocktags', 300, 1),
		array('leftcolumn', 'blockviewed', 300, 1),
		array('leftcolumn', 'blockcontact', 300, 1),
		array('leftcolumn', 'themeconfigurator', 300, 1),
		array('leftcolumn', 'pm_adsandslideshow', 300, 1),
		array('leftcolumn', 'pm_advancedfeedback', 300, 1),
		array('leftcolumn', 'pm_advancedsearch4', 300, 1),
		array('rightcolumn', 'blockadvertising', 300, 1),
		array('rightcolumn', 'blockcategories', 300, 1),
		array('rightcolumn', 'blockcms', 300, 1),
		array('rightcolumn', 'blocklayered', 300, 1),
		array('rightcolumn', 'blockmyaccount', 300, 1),
		array('rightcolumn', 'blocklink', 300, 1),
		array('rightcolumn', 'blockbestsellers', 300, 1),
		array('rightcolumn', 'blocknewproducts', 300, 1),
		array('rightcolumn', 'blockspecials', 300, 1),
		array('rightcolumn', 'blockstore', 300, 1),
		array('rightcolumn', 'blockmanufacturer', 300, 1),
		array('rightcolumn', 'blockpaymentlogo', 300, 1),
		array('rightcolumn', 'blocksupplier', 300, 1),
		array('rightcolumn', 'blocktags', 300, 1),
		array('rightcolumn', 'blockviewed', 300, 1),
		array('rightcolumn', 'blockcontact', 300, 1),
		array('rightcolumn', 'themeconfigurator', 300, 1),
		array('rightcolumn', 'pm_adsandslideshow', 300, 1),
		array('rightcolumn', 'pm_advancedfeedback', 300, 1),
		array('rightcolumn', 'pm_advancedpack', 300, 1),
		array('rightcolumn', 'pm_advancedsearch4', 300, 1),
		array('productfooter', 'crossselling', 300, 1),
		array('productfooter', 'pm_adsandslideshow', 300, 1),
		array('productfooter', 'pm_advancedfeedback', 300, 1),
		array('productfooter', 'pm_advancedpack', 300, 1),
		array('productfooter', 'productscategory', 300, 1),
		array('top', 'blockcurrencies', 300, 1),
		array('top', 'blocklanguages', 300, 1),
		array('top', 'blockpermanentlinks', 300, 1),
		array('top', 'blocksearch', 300, 1),
		array('top', 'pagesnotfound', 300, 1),
		array('top', 'pm_adsandslideshow', 300, 1),
		array('top', 'pm_advancedsearch4', 300, 1),
		array('top', 'pm_advancedtopmenu', 300, 1),
		array('top', 'blocktopmenu', 300, 1),
		array('top', 'sekeywords', 300, 1),
		array('displayHomeTab', 'blockbestsellers', 300, 1),
		array('displayHomeTab', 'blocknewproducts', 300, 1),
		array('displayHomeTab', 'homefeatured', 300, 1),
		array('displayHomeTabContent', 'blockbestsellers', 300, 1),
		array('displayHomeTabContent', 'blocknewproducts', 300, 1),
		array('displayHomeTabContent', 'homefeatured', 300, 1),
		array('displayTopColumn', 'themeconfigurator', 300, 1),
		array('displayTopColumn', 'homeslider', 300, 1),
		array('displayBanner', 'blockbanner', 300, 1),
		array('displayCompareExtraInformation', 'socialsharing', 300, 1),
		array('displayRightColumnProduct', 'socialsharing', 300, 1),
		array('displayNav', 'blockcurrencies', 300, 1),
		array('displayNav', 'blocklanguages', 300, 1),
		array('displayNav', 'blockcontact', 300, 1)
	);
	public static $hook_list_excluded = array();
	public static $isActivated = NULL;
	private static $cacheManagerCentralCacheStatus = array();
	private $listTabModules;
	protected $_copyright_link = array(
		'link'	=> '',
		'img'	=> '//www.presta-module.com/img/logo-module.JPG'
	);
	protected $_support_link = false;
	const INSTALL_SQL_FILE = 'install.sql';
	const UNINSTALL_SQL_FILE = 'uninstall.sql';
	public function __construct() {
		$this->name = 'pm_cachemanager';
		$this->author = 'Presta-Module';
		$this->tab = 'front_office_features';
		//$this->module_key = '2582eea28eac6111bac388c4fb5e1684';
		$this->version = '1.2.12';
		$this->need_instance = 0;
		parent::__construct();
		if ($this->_onBackOffice()) {
			$this->displayName = $this->l('Cache Manager');
			$this->description = $this->l('Increases the performance of your site through a cache system');
			$doc_url_tab['fr'] = 'http://presta-module.com/docs/fr/cachemanager/';
			$doc_url_tab['en'] = 'http://presta-module.com/docs/en/cachemanager/';
			$doc_url = $doc_url_tab['en'];
			if ($this->_iso_lang == 'fr') $doc_url = $doc_url_tab['fr'];
			$forum_url_tab['fr'] = 'http://www.prestashop.com/forums/topic/198498-module-pm-cache-manager-boostez-votre-prestashop/';
			$forum_url_tab['en'] = 'http://www.prestashop.com/forums/topic/198497-module-pm-cache-manager-boost-your-prestashop/';
			$forum_url = $forum_url_tab['en'];
			if ($this->_iso_lang == 'fr') $forum_url = $forum_url_tab['fr'];
			$this->_support_link = array(
				array('link' => $forum_url, 'target' => '_blank', 'label' => $this->l('Forum topic')),
				
				array('link' => 'http://addons.prestashop.com/contact-community.php?id_product=6413', 'target' => '_blank', 'label' => $this->l('Support contact')),
			);
			$this->listTabModules['administration'] = $this->l('Administration');
			$this->listTabModules['advertising_marketing'] = $this->l('Advertising & Marketing');
			$this->listTabModules['analytics_stats'] = $this->l('Analytics & Stats');
			$this->listTabModules['billing_invoicing'] = $this->l('Billing & Invoicing');
			$this->listTabModules['checkout'] = $this->l('Checkout');
			$this->listTabModules['content_management'] = $this->l('Content Management');
			$this->listTabModules['export'] = $this->l('Export');
			$this->listTabModules['front_office_features'] = $this->l('Front Office Features');
			$this->listTabModules['i18n_localization'] = $this->l('I18n & Localization');
			$this->listTabModules['merchandizing'] = $this->l('Merchandizing');
			$this->listTabModules['migration_tools'] = $this->l('Migration Tools');
			$this->listTabModules['mobile'] = $this->l('Mobile');
			$this->listTabModules['payments_gateways'] = $this->l('Payments & Gateways');
			$this->listTabModules['payment_security'] = $this->l('Payment Security');
			$this->listTabModules['pricing_promotion'] = $this->l('Pricing & Promotion');
			$this->listTabModules['quick_bulk_update'] = $this->l('Quick / Bulk update');
			$this->listTabModules['search_filter'] = $this->l('Search & Filter');
			$this->listTabModules['seo'] = $this->l('SEO');
			$this->listTabModules['shipping_logistics'] = $this->l('Shipping & Logistics');
			$this->listTabModules['slideshows'] = $this->l('Slideshows');
			$this->listTabModules['smart_shopping'] = $this->l('Smart Shopping');
			$this->listTabModules['market_place'] = $this->l('Market Place');
			$this->listTabModules['social_networks'] = $this->l('Social Networks');
			$this->listTabModules['others'] = $this->l('Other Modules');
		}
	}
	public function install() {
		if (parent::install() == false) return false;
		$this->checkIfModuleIsUpdate(true, false);
		return true;
	}
	private function _upgradeOldConfiguration() {
		if (!self::_isFilledArray(self::getGlobalConfiguration(true))) {
			if (Configuration::get('PM_' . self::$_module_prefix . '_LAST_VERSION', false) != false && version_compare(Configuration::get('PM_' . self::$_module_prefix . '_LAST_VERSION', false), '1.2.8', '<=')) {
				$conf = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `'._DB_PREFIX_.'pm_cachemanager_configuration`');
				$newConf = self::getGlobalConfiguration();
				if (self::_isFilledArray($conf))
					foreach ($conf as $key=>$val)
						$newConf[$key] = (is_numeric($val) ? (int)$val : $val);
				self::setModuleConfiguration($newConf);
				if (self::_isFilledArray($newConf))
					Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'pm_cachemanager_configuration`');
			}
		}
		$newConf = self::getGlobalConfiguration();
		if (!isset($newConf['centralcache_active']))
			self::_setDefaultConfiguration();
	}
	public function checkIfModuleIsUpdate($updateDb = false, $displayConfirm = true) {
		parent::checkIfModuleIsUpdate($updateDb, $displayConfirm);
		$isUpdate = true;
		if (!$updateDb && $this->version != Configuration::get('PM_' . self::$_module_prefix . '_LAST_VERSION', false))
			return false;
		if ($updateDb) {
			$this->_upgradeOldConfiguration();
			unset($_GET['makeUpdate']);
			Configuration::updateValue('PM_' . self::$_module_prefix . '_LAST_VERSION', $this->version);
			$this->installDb();
			$this->updateDb();
			if ($isUpdate && $displayConfirm)
				$this->_html .= $this->displayConfirmation($this->l('Module updated successfully'));
			else
				$this->_html .= $this->displayError($this->l('Module update fail'));
		}
		return $isUpdate;
	}
	public function updateDb() {
		$fields = array();
		foreach(self::$module_default_in_cache as $row) {
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
				if (Hook::getRetroHookName($row[0]) != '') {
					if (self::getHookIdByLowerName(Hook::getRetroHookName($row[0]))) {
						$row[0] = Hook::getRetroHookName($row[0]);
					} else if (!self::getHookIdByLowerName($row[0])) {
						continue;
					}
				}
			}
			if($this->moduleNeedCache($row[1], $row[0])) {
				$fields['hook_name'] = strtolower($row[0]);
				$fields['module_name'] = strtolower($row[1]);
				$fields['lifetime'] = $row[2];
				$fields['use_global'] = $row[3];
				Db::getInstance()->AutoExecute(_DB_PREFIX_.'pm_cachemanager_hooks', $fields, 'insert');
			}
		}
		$toAdd = array (
			array ('pm_cachemanager_cache_content', 'expire', 'int(10) UNSIGNED NOT NULL', 'content'),
			array ('pm_cachemanager_cache_content', 'jsdef_diff', 'mediumtext NULL', 'js_diff'),
		);
		foreach ( $toAdd as $table => $infos ) {
			$this->columnExists($infos[0], $infos[1], true, $infos[2], $infos[3]);
		}
	}
	private function columnExists($table, $column, $createIfNotExist = false, $type = false, $insertAfter = false) {
		$resultset = Db::getInstance()->ExecuteS("SHOW COLUMNS FROM `" . _DB_PREFIX_ . $table . "`");
		foreach ( $resultset as $row )
			if ($row ['Field'] == $column)
				return true;
		if ($createIfNotExist && Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . $table . '` ADD `' . $column . '` ' . $type . ' ' . ($insertAfter ? ' AFTER `' . $insertAfter . '`' : '') . ''))
			return true;
		return false;
	}
	function installDB() {
		if (!file_exists(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE))
			return (false);
		else if (!$sql = file_get_contents(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE))
			return (false);
		$sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
		if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
			$sql = str_replace('MYSQL_ENGINE', _MYSQL_ENGINE_, $sql);
		else
			$sql = str_replace('MYSQL_ENGINE', 'MyISAM', $sql);
		$sql = preg_split("/;\s*[\r\n]+/", $sql);
		foreach ($sql as $query)
			if (!self::Db_Execute(trim($query)))
				return (false);
		return true;
	}
	public static $_hookIsExcludedCache = array();
	public static function hookIsExcluded($hookName) {
		if (isset($_hookIsExcludedCache[$hookName])) return $_hookIsExcludedCache[$hookName];
		else {
			$_hookIsExcludedCache[$hookName] = ((version_compare(_PS_VERSION_, '1.5.0.0', '>=') && (preg_match('#^(dashboard|displayMyAccount|displayMobile|displayCustomer|action|displayBackOffice|displayAdmin|hookActionObject|displayPayment)#',$hookName) || preg_match('#PostProcess$#',$hookName))
			) || in_array(strtolower($hookName),self::$hookExclude));
			return $_hookIsExcludedCache[$hookName];
		}
		$_hookIsExcludedCache[$hookName] = false;
		return false;
	}
	protected function _postProcess() {
		parent::_postProcess();
		if (Tools::getIsset('submit_globalConfiguration') && Tools::getValue('submit_globalConfiguration')) {
			$newConf = self::getGlobalConfiguration();
			foreach (array('centralcache_active','centralcache_lifetime','centralcache_product_active','centralcache_category_active','centralcache_cms_active','centralcache_manufacturer_active','centralcache_supplier_active','centralcache_sitemap_active','centralcache_pricesdrop_active','centralcache_newproducts_active','centralcache_bestsales_active','modulecache_active','modulecache_lifetime') as $confKey)
				$newConf[$confKey] = (int)Tools::getValue($confKey);
			self::setModuleConfiguration($newConf);
			$this->_html .= '<script type="text/javascript">$(document).ready(function() {show_info("' . $this->l('Saved') . '");});</script>';
		}
	}
	protected function _initVar() {
		$conf = self::getGlobalConfiguration();
		self::$module_default_cache_time = $conf['modulecache_lifetime'];
		if(!self::_isFilledArray(self::$hook_list))
				$hooks = Hook::getHooks();
				foreach($hooks as $hook)
					if(in_array($hook['name'], self::$hook_list_excluded))
						continue;
					else
						self::$hook_list[] = $hook;
	}
	protected function checkOverride() {
		$errors = array();
		if (version_compare(_PS_VERSION_, '1.5.0.0', '<')) {
			$files_to_check = array(
				array('file'=>_PS_ROOT_DIR_.'/override/classes/FrontController.php','match'=>'pmCM_getCacheId'),
				array('file'=>_PS_ROOT_DIR_.'/override/classes/Module.php','match'=>'hookExecCache')
			);
		}else {
			$files_to_check = array(
				array('file'=>_PS_ROOT_DIR_.'/override/classes/controller/FrontController.php','match'=>'pmCM_getCacheId'),
				array('file'=>_PS_ROOT_DIR_.'/override/classes/Hook.php','match'=>'execCache'),
			);
		}
		foreach ($files_to_check as $file_to_check) {
			if(file_exists($file_to_check['file'])) {
				$controller_content = file_get_contents($file_to_check['file']);
				if(!preg_match('#'.preg_quote($file_to_check['match']).'#im',$controller_content)) {
					$errors[] = $this->l('Override incomplete:').$file_to_check['file'];
				}
			}else {
				$errors[] = $this->l('Override missing:').$file_to_check['file'];
			}
		}
		if(sizeof($errors)) {
			$this->_showWarning('<b>'.$this->l('Please correct the following errors').'</b><br /><br />'.implode('<br />',$errors));
			return false;
		}
		else
			return true;
	}
	public function getContent() {
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) $this->_html .= '<div id="pm_backoffice_wrapper" class="pm_bo_ps_'.substr(str_replace('.', '', _PS_VERSION_), 0, 2).'">';
		$this->_displayTitle($this->displayName);
		if ($this->checkOverride()) {
			if (Tools::getValue('makeUpdate'))
				$this->checkIfModuleIsUpdate(true);
			if (!$this->checkIfModuleIsUpdate(false)) {
				$this->_html .= '
					<div class="warning warn clear"><p>' . $this->l('We have detected that you installed a new version of the module on your shop') . '</p>
						<p style="text-align: center"><a href="' . $this->_base_config_url . '&makeUpdate=1" class="button">' . $this->l('Please click here in order to finish the installation process') . '</a></p>
					</div>';
				$this->_loadCssJsLibraries();
			} else {
				if((version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? (Configuration::get('PS_SMARTY_FORCE_COMPILE') == 2) : (bool)Configuration::get('PS_SMARTY_FORCE_COMPILE')) || !(bool)Configuration::get('PS_SMARTY_CACHE'))
					$this->_showWarning($this->l('Please activate the smarty cache and desactivate smarty compilation for the Cache Manager module work properly.'));
				if((bool)Configuration::get('PS_HTML_THEME_COMPRESSION'))
					$this->_showWarning($this->l('Please disable "Minify HTML" and set "Keep HTML as original" in order to make Cache Manager module work properly.'));
				$this->_preProcess();
				$this->_postProcess();
				$this->_loadCssJsLibraries();
				$this->_html .= '<script type="text/javascript">var msgUnHookedModules = "'.$this->l('All module from hook will be uncached. Continue?').'";</script>';
				if(Tools::getValue('pm_conf') == 1) {
					$this->_html .= '<script type="text/javascript">$(document).ready(function() {show_info("' . $this->l('Cache cleared') . '");});</script>';
				}
				$this->_showRating(true);
				parent::getContent();
				$this->displayTabsForm();
				$this->_displaySupport();
			}
		}
		$this->_pmClear();
		$this->_html .= '</div>';
		return $this-> _html;
	}
	public function displayModuleHook() {
		$id_hook = Tools::getValue('id_hook');
		$first_init = Tools::getValue('first_init');
		$modules = self::getModuleHook($id_hook);
		$hook_name = self::getHookNameById($id_hook);
		$modules_hooked = array();
		if($first_init) {
			$fields = array();
			$fields['hook_name']	= strtolower($hook_name);
			$fields['lifetime']		= (int)self::$module_default_cache_time;
			$fields['use_global']	= 1;
			if(self::_isFilledArray($modules)) {
				foreach($modules as $mod){
					$fields['module_name'] = pSQL($mod['name']);
					$modules_hooked[$mod['name']] = array('name' => $mod['name'], 'lifetime'=>$fields['lifetime'], 'use_global' => $fields['use_global']);
					Db::getInstance()->AutoExecute(_DB_PREFIX_.'pm_cachemanager_hooks', $fields, 'INSERT');
				}
			}
		}
		else {
			$results =  Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'pm_cachemanager_hooks` WHERE `hook_name` = "'.pSQL($hook_name).'"');
			if(self::_isFilledArray($results)) {
				foreach($results as $row) {
					$modules_hooked[$row['module_name']] = array('name' => $row['module_name'], 'lifetime'=>$row['lifetime'], 'use_global' => $row['use_global']);
				}
			}
		}
		$this->_html .= '<table id="modulesTable" cellspacing="0" cellpadding="0" class="display"  style="width:100%;">
							<thead>
			    				<tr class="ui-state-default ui-corner-top" style="height:20px;">
									<th style="text-align:center;"></th>
									<th><b>' . $this->l('Module Name') . '</b></th>
									<th><b>'.$this->l('Module Category').' :</b></th>
									<th style="text-align:center;"><b>' . $this->l('Cache Life time') . '</b></th>
								</tr>
							</thead>
							<tbody>';
		if (self::_isFilledArray($modules)) {
			foreach($modules as $module_key=>$module) {
				$objModule = Module::getInstanceByName($module['name']);
				if (!is_object($objModule) || in_array($module['name'],$this->moduleNameExclude)) {
					Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'pm_cachemanager_hooks` WHERE `hook_name` = "'.pSQL($hook_name).'" AND `module_name` = "'.pSQL($module['name']).'"');
					self::deleteCacheFromModule($module['name']);
					unset($modules[$module_key]);
				}
			}
		}
		if(self::_isFilledArray($modules)) {
			foreach($modules as $module) {
				$objModule = Module::getInstanceByName($module['name']);
				if(is_object($objModule)) {
					$this->_html .= '<tr>
						<td style="text-align:center;">
							<input class="module_select module_select_'.$module['id_module'].'" type="checkbox" name="'. $module['name'] .'" rel="'. $hook_name .'" value="' . $module['id_module'] . '" '.(array_key_exists($module['name'],$modules_hooked) ? 'checked' : '').'/>
						</td>
						<td>
							<span class="pm_module_name"><b> ' . Module::getInstanceByName($module['name'])->displayName . '</b></span><br/>
							<div class="pm_module_detail" ><b>'.$this->l('Author').' :</b>'.Module::getInstanceByName($module['name'])->author.'<div class="pm_module_detail" >
						</td>
						<td>
							<div><b>'.(isset($this->listTabModules[Module::getInstanceByName($module['name'])->tab]) ? Module::getInstanceByName($module['name'])->tab : $this->listTabModules['others']).'</b></div>
						</td>
						<td>
							<div class="pm_time_input" style="float:none;">
								<input
									 class="module_lifetime_'.$hook_name.'_'.$module['name'].' module_lifetime ui-corner-all ui-input-pm"
									 size="15"
									 type="text"
									'.(array_key_exists($module['name'],$modules_hooked) ? ($modules_hooked[$module['name']]['use_global'] ? 'disabled="disabled"' : '') : 'disabled="disabled"').'"
									 name="' . $module['name'] . '"
									 rel="'.$hook_name . '"
									 value="'.(array_key_exists($module['name'],$modules_hooked) ? $modules_hooked[$module['name']]['lifetime'] : self::$module_default_cache_time).'"
									 style="width:30px"
								/>
								<span style="margin-left:5px;"> ' . $this->l('minutes') . '</span>
							</div>
							<div class="module_time_div_'.$module['name'].'" style="float:none;display:'.(array_key_exists($module['name'],$modules_hooked) ? 'block' : 'none').'">
								<input
									class="module_setTime module_setTime_'.$module['name'].'"
									type="checkbox"
									name="'. $module['name'] .'"
									rel="'. $hook_name .'"
									value="' . $module['id_module'] . '"
									'.(array_key_exists($module['name'],$modules_hooked) ? ($modules_hooked[$module['name']]['use_global'] ? '' :'checked' ) : '').'
								/>
								<span>'. $this->l('Set lifetime manually ') .'</span>
							</div>
						</td>';
				}
			}
		} else {
			$this->_html .= '<tr>
									<td colspan="4"><b>'.$this->l('No modules available in this hook').'</b></td>
								</tr>';
		}
					$this->_html .= '</tbody></table>';
			$this->_echoOutput(true);
	}
	protected function unHookedModulesFromHooks() {
		$id_hook = Tools::getValue('id_hook');
		$hook_name = self::getHookNameById($id_hook);
		if($hook_name) {
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'pm_cachemanager_hooks` WHERE `hook_name` = "'.pSQL(strtolower($hook_name)).'"');
		}
	}
	protected function hookModuleFromHook() {
		$hook_name		= Tools::getValue('hook_name');
		$module_name	= Tools::getValue('module_name');
		$lifetime		= Tools::getValue('lifetime');
		$use_global		= Tools::getValue('use_global');
		if((!$use_global && !$lifetime) || (empty($lifetime) || $lifetime == 0))
			$this->_html .= 'show_error("' . $this->l('Set a valid time') . '");';
		else
			if($hook_name && $module_name){
				$fields['hook_name']	= strtolower(pSQL($hook_name));
				$fields['module_name']	= pSQL($module_name);
				$fields['lifetime']		= (int)$lifetime;
				$fields['use_global']	= (int)$use_global;
					Db::getInstance()->Execute('
						DELETE FROM `'._DB_PREFIX_.'pm_cachemanager_hooks`
						WHERE `hook_name` = "'.pSQL($hook_name).'"
						AND `module_name` = "'.pSQL($module_name).'"');
					Db::getInstance()->AutoExecute(_DB_PREFIX_.'pm_cachemanager_hooks', $fields, 'INSERT');
					$this->_html .= 'show_info("' . $this->l('Saved') . '");';
			}
		$this->_echoOutput(true);
	}
	protected function unHookModuleFromHook() {
		$hook_name		= Tools::getValue('hook_name');
		$module_name	= Tools::getValue('module_name');
		if($hook_name && $module_name){
			$delete = Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'pm_cachemanager_hooks` WHERE `hook_name` = "'.pSQL($hook_name).'" AND `module_name` = "'.pSQL($module_name).'"');
			if($delete)
				$this->_html .= 'show_info("' . $this->l('Saved') . '");';
		}
		if(!isset($delete) || !$delete)
			$this->_html .= 'show_error("' . $this->l('Error while saving') . '");';
		$this->_echoOutput(true);
	}
	protected function displayTabsForm() {
		$tabsPanelOptions = array(
			'id_panel' => 'CM_panel',
			'tabs' => array(
				array('url' => $this->_base_config_url . '&pm_load_function=globalConfigPanel',
				'label' => $this->l('Global Configuration'))
			));
		if(self::getSpecificGlobalConfiguration('modulecache_active',true))
			$tabsPanelOptions['tabs'][] = array('url' => $this->_base_config_url . '&pm_load_function=hookListPanel',
				'label' => $this->l('Manage Modules Cache'));
		$tabsPanelOptions['tabs'][] = array('url' => $this->_base_config_url . '&pm_load_function=displayMaintenancePanel',
			'label' => $this->l('Maintenance'));
		$tabsPanelOptions['tabs'][] = array('url' => $this->_base_config_url . '&pm_load_function=displayCrontabPanel',
			'label' => $this->l('Crontab'));
		$this->_displayTabsPanel($tabsPanelOptions);
	}
	protected function globalConfigPanel() {
		$conf = self::getGlobalConfiguration();
		$this->_displayTitle($this->l('Global configuration'));
		$this->_startForm(array('id' => 'formGlobalConf','iframetarget' => false,'target' => '_self'));
		$this->_displayInputActive(array(
				'obj' => false,
				'defaultvalue' => $conf['centralcache_active'],
				'key_active' => 'centralcache_active',
				'key_db' => 'centralcache_active',
				'label' => $this->l('Activate Central Cache'),
				'onclick' => 'showRelatedItems($jqPm(this))'
		));
		$this->_html .= '<div class="centralcache pm_option_group">';
		$this->_displayInputActive(array(
				'obj' => false,
				'defaultvalue' => $conf['centralcache_product_active'],
				'key_active' => 'centralcache_product_active',
				'key_db' => 'centralcache_product_active',
				'label' => $this->l('Central cache for product pages')
		));
		$this->_displayInputActive(array(
				'obj' => false,
				'defaultvalue' => $conf['centralcache_category_active'],
				'key_active' => 'centralcache_category_active',
				'key_db' => 'centralcache_category_active',
				'label' => $this->l('Central cache for category pages')
		));
		$this->_displayInputActive(array(
				'obj' => false,
				'defaultvalue' => $conf['centralcache_manufacturer_active'],
				'key_active' => 'centralcache_manufacturer_active',
				'key_db' => 'centralcache_manufacturer_active',
				'label' => $this->l('Central cache for manufacturer pages')
		));
		$this->_displayInputActive(array(
				'obj' => false,
				'defaultvalue' => $conf['centralcache_supplier_active'],
				'key_active' => 'centralcache_supplier_active',
				'key_db' => 'centralcache_supplier_active',
				'label' => $this->l('Central cache for supplier pages')
		));
		$this->_displayInputActive(array(
				'obj' => false,
				'defaultvalue' => $conf['centralcache_cms_active'],
				'key_active' => 'centralcache_cms_active',
				'key_db' => 'centralcache_cms_active',
				'label' => $this->l('Central cache for CMS pages')
		));
		$this->_displayInputActive(array(
				'obj' => false,
				'defaultvalue' => $conf['centralcache_sitemap_active'],
				'key_active' => 'centralcache_sitemap_active',
				'key_db' => 'centralcache_sitemap_active',
				'label' => $this->l('Central cache for sitemap page')
		));
		$this->_displayInputActive(array(
				'obj' => false,
				'defaultvalue' => $conf['centralcache_pricesdrop_active'],
				'key_active' => 'centralcache_pricesdrop_active',
				'key_db' => 'centralcache_pricesdrop_active',
				'label' => $this->l('Central cache for prices drop page')
		));
		$this->_displayInputActive(array(
				'obj' => false,
				'defaultvalue' => $conf['centralcache_newproducts_active'],
				'key_active' => 'centralcache_newproducts_active',
				'key_db' => 'centralcache_newproducts_active',
				'label' => $this->l('Central cache for new products page')
		));
		$this->_displayInputActive(array(
				'obj' => false,
				'defaultvalue' => $conf['centralcache_bestsales_active'],
				'key_active' => 'centralcache_bestsales_active',
				'key_db' => 'centralcache_bestsales_active',
				'label' => $this->l('Central cache for best sales page')
		));
		$this->_displayInputText(array(
				'obj' => false,
				'defaultvalue' => $conf['centralcache_lifetime'],
				'key' => 'centralcache_lifetime',
				'label' => $this->l('Central cache lifetime (in minutes)'),
				'size' => '30px',
				'required' => true,
		));
		$this->_html .= '</div><hr>';
		$this->_displayInputActive(array(
				'obj' => false,
				'defaultvalue' => $conf['modulecache_active'],
				'key_active' => 'modulecache_active',
				'key_db' => 'modulecache_active',
				'label' => $this->l('Activate Module Cache'),
				'onclick' => 'showRelatedItems($jqPm(this))'
		));
		$this->_html .= '<div class="modulecache pm_option_group">';
		$this->_displayInputText(array(
				'obj' => false,
				'defaultvalue' => $conf['modulecache_lifetime'],
				'key' => 'modulecache_lifetime',
				'label' => $this->l('Cache lifetime (in minutes)'),
				'size' => '30px',
				'required' => true,
		));
		$this->_html .= '</div>';
		$this->_displaySubmit($this->l('Save'), 'submit_globalConfiguration');
		$this->_endForm(array('iframetarget' => false));
		$this->_html .='	<script type="text/javascript">
								$jqPm(document).ready(function() {
									$jqPm(".modulecache").'.($conf['modulecache_active'] ? 'show' : 'hide') . '("fast");
									$jqPm(".centralcache").'.($conf['centralcache_active'] ? 'show' : 'hide') . '("fast");
								});
							</script>';
	}
	protected function clearCache($redirect = true) {
		self::clearAllCache();
		if ($redirect)
			header('Location:'.$this -> _base_config_url.'&pm_conf=1#ui-tabs-3');
	}
	public static function clearAllCache() {
		Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'pm_cachemanager_cache`');
		Db::getInstance()->Execute('TRUNCATE TABLE `'._DB_PREFIX_.'pm_cachemanager_cache_content`');
	}
	protected function displayMaintenancePanel() {
		$this->_displayTitle($this->l('Cache Maintenance'));
		$this->_addButton(array(
			'text'=> $this->l('Clear cache',$this->_coreClassName),
			'href'=>$this -> _base_config_url . '&pm_load_function=clearCache',
			'icon_class'=>'ui-icon ui-icon-trash'
		));
	}
	protected function displayCrontabPanel() {
		$this->_displayTitle($this->l('Crontab'));
		if (Configuration::get('PM_'.self::$_module_prefix.'_CRON_SECURE_KEY') === false) {
			Configuration::updateValue('PM_'.self::$_module_prefix.'_CRON_SECURE_KEY', Tools::passwdGen(16));
			Configuration::updateValue('PM_'.self::$_module_prefix.'_CRON_LAST_RUN', '0');
		}
		$this->_html .= '<p>'.$this->l('Last crontab usage').' : <strong>'. ((Configuration::get('PM_'.self::$_module_prefix.'_CRON_LAST_RUN', '0') != '0') ? date('r', Configuration::get('PM_'.self::$_module_prefix.'_CRON_LAST_RUN')) : 'N/A') .'</strong></p>';
		$this->_html .= '<div class="conf pm_confirm">' . $this->l('If you want to automatically clear the whole cache by running a scheduled task, you can use the URL below :') .
		'<br /><br />' . self::getHttpHost(true, true) . __PS_BASE_URI__ . 'modules/pm_cachemanager/cron.php?secure_key=' . Configuration::get('PM_'.self::$_module_prefix.'_CRON_SECURE_KEY') .
		'</div>';
	}
	protected function hookListPanel() {
		$this->_displayTitle($this->l('Manage Modules Cache'));
		if(!self::_isFilledArray(self::$hook_list))
			$this->_initVar('hook');
		$checked = $this->getActiveHooks();
		$hookModulesToLoad = array();
		$this->_html .= '<input type="hidden" value="'.self::$module_default_cache_time.'" class="module_default_cache_time">';
		$this -> _html .= '<div id="displayHooks" rel="' . $this -> _base_config_url . '&pm_load_function=moduleListPanel">
							<table id="hooksTable" cellspacing="0" cellpadding="0" style="width:100%;">
	       					 <thead>
			    					<th style="text-align:center;"></th>
									<th>' . $this->l('Hook Name') . '</th>
								</tr>
							</thead>
							<tbody>';
		foreach(self::$hook_list as $hook) {
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				$retro_hook_name = Hook::getRetroHookName($hook['name']);
			else $retro_hook_name = $hook['name'];
			$hookBlacklisted = false;
			if(self::hookIsExcluded($retro_hook_name) || self::hookIsExcluded($hook['name'])) $hookBlacklisted = true;
			$haveModuleHooked = $this->haveModuleHooked($hook['name']);
			if ($hookBlacklisted) {
				Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'pm_cachemanager_hooks` WHERE `hook_name` = "'.pSQL($hook['name']).'"');
				continue;
			}
			if ($haveModuleHooked) $hookModulesToLoad[] = $hook['id_hook'];
			else continue;
			$this->_html .= '<tr>
								<td style="text-align:center;vertical-align:top;">
									<input class="hook_select hook_select_'.$hook['id_hook'].'" type="checkbox" name="'. $hook['name'] .'" rel="'. $hook['id_hook'] .'" value="' . $hook['id_hook'] . '"'.($haveModuleHooked ? ' checked="checked"' : '').' />
								</td>
								<td>
									<span class="pm_hook_name"><b> ' .$hook['title'].  ' <small>('.$hook['name'].')</small></b></span><br/>
									<div id="pm_hook_module_list_'.$hook['id_hook'].'" class="pm_hook_module_list" style="display:none;">
									</div>
								</td>';
		}
		$this->_html .= '</tbody></table>';
		if(self::_isFilledArray($hookModulesToLoad)) {
			$this->_html .= '<script type="text/javascript">$jqPm(document).ready(function() {';
			foreach($hookModulesToLoad as $id_hook) {
				$this->_html .= 'getModulesFromHooks('.(int)$id_hook.',false);';
			}
			$this->_html .= '});</script>';
		}
		$this->_initDataTable('hooksTable');
	}
	protected function haveModuleHooked($hook_name){
		$nb_hooks = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'module` m
				JOIN `'._DB_PREFIX_.'hook_module` hm ON (hm.id_module = m.id_module)
				JOIN `'._DB_PREFIX_.'hook` h ON (h.name="'.pSQL($hook_name).'" AND h.id_hook = hm.id_hook)');
		$modules = self::getModuleHook(self::getHookIdByLowerName($hook_name));
		if (self::_isFilledArray($modules)) {
			foreach($modules as $module_key=>$module) {
				$objModule = Module::getInstanceByName($module['name']);
				if (!is_object($objModule) || in_array($module['name'],$this->moduleNameExclude)) {
					Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'pm_cachemanager_hooks` WHERE `hook_name` = "'.pSQL($hook_name).'" AND `module_name` = "'.pSQL($module['name']).'"');
					self::deleteCacheFromModule($module['name']);
					unset($modules[$module_key]);
				}
			}
		} else {
			$modules = array();
		}
		if ($nb_hooks > 0 && sizeof($modules)) return true;
		return false;
	}
	protected function moduleNeedCache($module_name, $hook_name){
		if (self::getHookIdByLowerName($hook_name)) {
			$row = Db::getInstance()->getRow('SELECT m.`id_module` FROM `'._DB_PREFIX_.'module` m
			JOIN `'._DB_PREFIX_.'hook_module` hm ON (m.`id_module` = hm.`id_module`)
			LEFT JOIN `'._DB_PREFIX_.'pm_cachemanager_hooks` ch ON (m.`name` = ch.`module_name` AND ch.`hook_name` = "'.(pSQL($hook_name)).'")
			WHERE m.`name` = "'.pSQL($module_name).'" AND hm.`id_hook` = '.(self::getHookIdByLowerName($hook_name)).' AND ch.`module_name` IS NULL');
			return isset($row['id_module']);
		}
		return false;
	}
	protected function getActiveHooks() {
		$result = array();
		$return =  Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'pm_cachemanager_hooks`');
		if(self::_isFilledArray($return))
			foreach($return as $hook)
				$result[$hook['hook_name']] = array('hook_name' => $hook['hook_name'], 'lifetime'=>$hook['lifetime'],'use_global' => $hook['use_global']);
		return $result;
	}
	protected static function getModuleHook($id_hook) {
		if(!isset(self::$cacheModuleHook[$id_hook])) {
			$return = Db::getInstance()->ExecuteS('
					SELECT *
					FROM `'._DB_PREFIX_.'module` m
					LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON (hm.id_module = m.id_module)
					WHERE hm.id_hook = '.(int)($id_hook).'
					GROUP BY m.id_module');
			if(self::_isFilledArray($return)) {
				self::$cacheModuleHook[$id_hook] = $return;
			}else return false;
		}
		return self::$cacheModuleHook[$id_hook];
	}
	public static function isActivated() {
		if (self::$isActivated == NULL) {
			if (version_compare(_PS_VERSION_, '1.5.0.0', '<')) {
				$is_active_module = Module::getInstanceByName('pm_cachemanager');
				self::$isActivated = isset($is_active_module->active) && $is_active_module->active && Configuration::get('PS_SMARTY_CACHE');
			} else {
				self::$isActivated = Module::isEnabled('pm_cachemanager') && Configuration::get('PS_SMARTY_CACHE');
			}
		}
		return self::$isActivated;
	}
	private static function _setDefaultConfiguration() {
		self::$globalCacheConfig = array('centralcache_active' => 1,'centralcache_lifetime' => 1440,'centralcache_product_active' => 1,'centralcache_category_active' => 1,'centralcache_cms_active' => 1,'centralcache_manufacturer_active' => 1,'centralcache_supplier_active' => 1,'centralcache_sitemap_active' => 1,'centralcache_pricesdrop_active' => 1,'centralcache_newproducts_active' => 1,'centralcache_bestsales_active' => 1,'modulecache_active' => 1,'modulecache_lifetime' => 1440);
		self::setModuleConfiguration(self::$globalCacheConfig);
	}
	public static function getGlobalConfiguration($force = false) {
		if (!isset(self::$globalCacheConfig) || $force) {
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				$conf = Configuration::getGlobalValue('PM_' . self::$_module_prefix . '_CONF');
			else
				$conf = Configuration::get('PM_' . self::$_module_prefix . '_CONF');
			if (!empty($conf)) {
				self::$globalCacheConfig = json_decode($conf, true);
			} else {
				if (!Configuration::get('PM_' . self::$_module_prefix . '_LAST_VERSION', false)) {
					self::_setDefaultConfiguration();
				} else {
					self::$globalCacheConfig = array();
				}
			}
		}
		return self::$globalCacheConfig;
	}
	private static function setModuleConfiguration($newConf) {
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			Configuration::updateGlobalValue('PM_' . self::$_module_prefix . '_CONF', json_encode($newConf));
		else
			Configuration::updateValue('PM_' . self::$_module_prefix . '_CONF', json_encode($newConf));
	}
	public static function getSpecificGlobalConfiguration($key,$force=false){
		if(!isset(self::$globalCacheConfig) || $force) self::getGlobalConfiguration($force);
		if(isset(self::$globalCacheConfig[$key])) return self::$globalCacheConfig[$key];
		return false;
	}
	public static function getDBCacheContentNotExpired($cache_id, $with_css_js_diff = false){
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `content`'.($with_css_js_diff ? ', `js_diff`, `css_diff`, `jsdef_diff`':'').' FROM `'._DB_PREFIX_.'pm_cachemanager_cache_content` ccc LEFT JOIN `'._DB_PREFIX_.'pm_cachemanager_cache` cc ON(cc.`id_cache_content` = ccc.`id_cache_content`) WHERE `cache_id` = "'.pSQL($cache_id).'" AND `expire` > '.time());
		if(isset($row['content'])) {
			if($with_css_js_diff) {
				$row['css_diff'] = unserialize($row['css_diff']);
				$row['js_diff'] = unserialize($row['js_diff']);
				$row['jsdef_diff'] = unserialize($row['jsdef_diff']);
				return $row;
			}
			else
				return $row['content'];
		}
		return false;
	}
	public static function setDBCacheContentNotExpired($cache_id, $content, $id_hook = false, $id_module = false, $css_diff = array(), $js_diff = array(), $jsdef_diff = array(), $cacheLifetime = false){
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			$context = Context::getContext();
			$cookie = $context->cookie;
			$id_group = (int)(Validate::isLoadedObject($context->customer) ? $context->customer->id_default_group : 0);
			$id_customer = (int)(Validate::isLoadedObject($context->customer) ? $context->customer->id : 0);
		} else {
			global $cookie;
			$id_group = (int)$cookie->id_group;
			$id_customer = (int)$cookie->id_customer;
		}
		if (!$cacheLifetime) {
			if ($id_hook) $cacheLifetime = self::getSpecificGlobalConfiguration('modulecache_lifetime')*60;
			else $cacheLifetime = self::getSpecificGlobalConfiguration('centralcache_lifetime')*60;
		}
		$expire = time() + $cacheLifetime;
		$id_product = Tools::getValue('id_product',false);
		$id_category = Tools::getValue('id_category',false);
		$id_manufacturer = Tools::getValue('id_manufacturer',false);
		$id_supplier = Tools::getValue('id_supplier',false);
		$id_cms = Tools::getValue('id_cms',false);
		$content_md5 = md5($content.(serialize($js_diff).serialize($css_diff).serialize($jsdef_diff)));
		if (get_magic_quotes_gpc())
			$content = addslashes($content);
		if (!$id_hook) self::deleteExpiredCache();
		$return =  Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'pm_cachemanager_cache_content` (`content_md5`, `content`, `expire`'.(self::_isFilledArray($css_diff)  ? ',`css_diff`':'').(self::_isFilledArray($js_diff)  ? ',`js_diff`':'').(self::_isFilledArray($jsdef_diff)  ? ',`jsdef_diff`':'').') VALUES ("'.$content_md5.'","'.pSQL($content,true).'",'.$expire.''.(self::_isFilledArray($css_diff)  ? ',"'.pSQL(serialize($css_diff)).'"':'').(self::_isFilledArray($js_diff)  ? ',"'.pSQL(serialize($js_diff)).'"':'').(self::_isFilledArray($jsdef_diff)  ? ',"'.pSQL(serialize($jsdef_diff)).'"':'').') ON DUPLICATE KEY UPDATE `content` = "'.pSQL($content,true).'"'.(self::_isFilledArray($css_diff)  ? ', `css_diff` = "'.pSQL(serialize($css_diff)).'"':'').(self::_isFilledArray($js_diff)  ? ', `js_diff` = "'.pSQL(serialize($js_diff)).'"':'').(self::_isFilledArray($jsdef_diff)  ? ', `jsdef_diff` = "'.pSQL(serialize($jsdef_diff)).'"':'').', `id_cache_content`=LAST_INSERT_ID(`id_cache_content`);');
		$id_cache_content = Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
		$return =  Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'pm_cachemanager_cache` (`cache_id`,'.($id_group ? '`id_group`,':'').($id_customer ? '`id_customer`,':'').($id_product ? '`id_product`,':'').($id_category ? '`id_category`,':'').($id_manufacturer ? '`id_manufacturer`,':'').($id_supplier ? '`id_supplier`,':'').($id_cms ? '`id_cms`,':'').($id_hook ? '`id_hook`,':'').($id_module ? '`id_module`,':'').(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Context::getContext()->shop->id ? '`id_shop`,':'').'`id_cache_content`)
		VALUES ("'.pSQL($cache_id).'",'.($id_group ? '`id_group`,':'').($id_customer ? $id_customer.',':'').($id_product ? $id_product.',':'').($id_category ? $id_category.',':'').($id_manufacturer ? $id_manufacturer.',':'').($id_supplier ? $id_supplier.',':'').($id_cms ? $id_cms.',':'').($id_hook ? $id_hook.',':'').($id_module ? $id_module.',':'').(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Context::getContext()->shop->id ? (int)Context::getContext()->shop->id.',':'').(int)$id_cache_content.') ON DUPLICATE KEY UPDATE `id_cache_content` = '.$id_cache_content.';');
		return $return;
	}
	public static function getHookNameById($id_hook){
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `name`
		FROM `'._DB_PREFIX_.'hook`
		WHERE `id_hook` = \''.(int)$id_hook.'\'');
		return ($result ? $result['name'] : false);
	}
	protected static function getHookIdByLowerName($hookName) {
		if (empty($hookName))
			return false;
	 	if (!Validate::isHookName($hookName))
	 		die(Tools::displayError());
		$result = Db::getInstance()->getRow('
		SELECT `id_hook`
		FROM `'._DB_PREFIX_.'hook`
		WHERE LOWER(`name`) = \''.strtolower(pSQL($hookName)).'\'');
		return ($result ? $result['id_hook'] : false);
	}
	public static function deleteCacheFromIdProductOpenSi($ids_product) {
		self::deleteCacheFromIdProduct($ids_product);
	}
	public static function deleteCacheFromIdProduct($id_product) {
		if (!is_array($id_product)) $id_product = array($id_product);
		$productManufacturers = self::getProductManufacturer($id_product);
		$productCategories = self::getProductCategories($id_product);
		Db::getInstance()->Execute('DELETE cc.*, ccc.* FROM `'._DB_PREFIX_.'pm_cachemanager_cache` cc LEFT JOIN `'._DB_PREFIX_.'pm_cachemanager_cache_content` ccc ON (ccc.`id_cache_content` = cc.`id_cache_content`) WHERE cc.`id_product`  IN('.implode(', ',$id_product).')'.(self::_isFilledArray($productCategories) ? ' OR cc.`id_category` IN('.implode(', ',$productCategories).')' : '').(self::_isFilledArray($productManufacturers) ? ' OR cc.`id_manufacturer` IN('.implode(', ',$productManufacturers).')' : ''));
		self::deleteExpiredCache();
	}
	public static function deleteCacheFromIdCategory($id_category) {
		if (!is_array($id_category)) $id_category = array($id_category);
		Db::getInstance()->Execute('DELETE cc.*, ccc.* FROM `'._DB_PREFIX_.'pm_cachemanager_cache` cc LEFT JOIN `'._DB_PREFIX_.'pm_cachemanager_cache_content` ccc ON (ccc.`id_cache_content` = cc.`id_cache_content`) WHERE cc.`id_category` IN ('.implode(', ',$id_category).')');
		self::deleteExpiredCache();
	}
	public static function deleteExpiredCache() {
		Db::getInstance()->Execute('
			DELETE cc.*, ccc.* FROM `'._DB_PREFIX_.'pm_cachemanager_cache` cc
			LEFT JOIN `'._DB_PREFIX_.'pm_cachemanager_cache_content` ccc ON (cc.`id_cache_content` = ccc.`id_cache_content`)
			WHERE `expire` <= '.time() . ' OR ccc.`id_cache_content` IS NULL');
	}
	protected static function deleteCacheFromModule($module_name) {
		$row = Db::getInstance()->getRow('
		SELECT `id_module`
		FROM `'._DB_PREFIX_.'module`
		WHERE `name` = \''.pSQL($module_name).'\'');
		if (!$row)
			return false;
		$id_module = $row['id_module'];
		Db::getInstance()->Execute('DELETE cc.*, ccc.* FROM `'._DB_PREFIX_.'pm_cachemanager_cache` cc LEFT JOIN `'._DB_PREFIX_.'pm_cachemanager_cache_content` ccc ON (ccc.`id_cache_content` = cc.`id_cache_content`) WHERE cc.`id_module` = '.(int)$id_module);
		self::deleteExpiredCache();
	}
	protected static function deleteCacheFromIdCms($id_cms) {
		Db::getInstance()->Execute('DELETE cc.*, ccc.* FROM `'._DB_PREFIX_.'pm_cachemanager_cache` cc LEFT JOIN `'._DB_PREFIX_.'pm_cachemanager_cache_content` ccc ON (ccc.`id_cache_content` = cc.`id_cache_content`) WHERE cc.`id_cms` = '.(int)$id_cms);
		self::deleteExpiredCache();
	}
	public static function getProductCategories($ids_product) {
		$ret = array();
		if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT DISTINCT `id_category` FROM `'._DB_PREFIX_.'category_product`
		WHERE `id_product` IN ('.implode(', ',$ids_product).')'))
			foreach ($row as $val) {
				if (!is_numeric($val['id_category'])) continue;
				$ret[] = (int)$val['id_category'];
			}
		return $ret;
	}
	public static function getProductManufacturer($ids_product) {
		$ret = array();
		if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT DISTINCT `id_manufacturer` FROM `'._DB_PREFIX_.'product`
		WHERE `id_product` IN ('.implode(', ',$ids_product).')'))
			foreach ($row as $val) {
				if (!is_numeric($val['id_manufacturer'])) continue;
				$ret[] = (int)$val['id_manufacturer'];
			}
		return $ret;
	}
	public static function getIdsProductFromIdFeature($id_feature) {
		$ret = array();
		if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT DISTINCT `id_product` FROM `'._DB_PREFIX_.'feature_product`
		WHERE `id_feature` = '.(int)$id_feature))
			foreach ($row as $val) {
				if (!is_numeric($val['id_product'])) continue;
				$ret[] = (int)$val['id_product'];
			}
		return $ret;
	}
	public static function getIdsProductFromIdFeatureValue($id_feature_value) {
		$ret = array();
		if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT DISTINCT `id_product` FROM `'._DB_PREFIX_.'feature_product`
		WHERE `id_feature_value` = '.(int)$id_feature_value))
			foreach ($row as $val) {
				if (!is_numeric($val['id_product'])) continue;
				$ret[] = (int)$val['id_product'];
			}
		return $ret;
	}
	public static function getIdsProductFromIdAttributeGroup($id_attribute_group) {
		$ret = array();
		if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT DISTINCT `id_product`
		FROM `'._DB_PREFIX_.'product_attribute` pa
		JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
		JOIN `'._DB_PREFIX_.'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
		WHERE a.`id_attribute_group` = '.(int)$id_attribute_group))
			foreach ($row as $val) {
				if (!is_numeric($val['id_product'])) continue;
				$ret[] = (int)$val['id_product'];
			}
		return $ret;
	}
	public static function getIdsProductFromIdAttribute($id_attribute) {
		$ret = array();
		if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT DISTINCT `id_product`
		FROM `'._DB_PREFIX_.'product_attribute` pa
		JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
		WHERE pac.`id_attribute` = '.(int)$id_attribute))
			foreach ($row as $val) {
				if (!is_numeric($val['id_product'])) continue;
				$ret[] = (int)$val['id_product'];
			}
		return $ret;
	}
	public static function getIdsProductFromIdCategory($id_category) {
		$ret = array();
		if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT DISTINCT `id_product` FROM `'._DB_PREFIX_.'category_product`
		WHERE `id_category` = '.(int)$id_category))
			foreach ($row as $val) {
				if (!is_numeric($val['id_product'])) continue;
				$ret[] =(int) $val['id_product'];
			}
		return $ret;
	}
	public static function getIdsProductFromIdManufacturer($id_manufacturer) {
		$ret = array();
		if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT DISTINCT `id_product` FROM `'._DB_PREFIX_.'product` WHERE `id_manufacturer` = '.(int)$id_manufacturer))
			foreach ($row as $val) {
				if (!is_numeric($val['id_product'])) continue;
				$ret[] = (int)$val['id_product'];
			}
		return $ret;
	}
	public function hookDisplayBackOfficeHeader($params) {
		parent::hookDisplayBackOfficeHeader($params);
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) $this->hookBackOfficeHeader($params);
	}
	public function hookBackOfficeHeader() {
		if(Tools::getValue('pm_clear_all_cache')) {
			$this->_cleanOutput();
			self::clearAllCache();
			$this->_html = '$("#msg_page_loader").css("color","#29bb08").html("'.$this->l('Cache cleared').'");setTimeout("hideCachePageLoader()",1000);';
			$this->_echoOutput(true);
		}
		elseif ((strtolower(Tools::getValue('controller')) == 'adminimport' || strtolower(Tools::getValue('tab')) == 'adminimport') && Tools::getIsset('import')) {
			self::clearAllCache();
		}
		elseif ((Tools::getValue('tab') == 'AdminModules' && Tools::getIsset('configure') && ($module_name = Tools::getValue('configure')) && self::_isFilledArray($_POST))  
 || (strtolower(Tools::getValue('controller')) == 'adminmodules' && Tools::getIsset('configure') && ($module_name = Tools::getValue('configure')) && self::_isFilledArray($_POST))) {
			self::deleteCacheFromModule($module_name);
		}
		elseif (Tools::getValue('id_product') && Tools::getIsset('addproduct') && ((Tools::getIsset('tabs') && Tools::getValue('tabs') != 0) 
 || (Tools::getIsset('key_tab') && Tools::getValue('key_tab') != 'Informations'))) {
			self::deleteCacheFromIdProduct(array(Tools::getValue('id_product')));
		}
		elseif (strtolower(Tools::getValue('controller')) == 'adminproducts' && Tools::getValue('id_product') && Tools::getIsset('ajax') && Tools::getIsset('action')) {
			self::deleteCacheFromIdProduct(array(Tools::getValue('id_product')));
		}
		elseif(Tools::getIsset('deleteproduct') && ($id_product = Tools::getValue('id_product',false)) !== false) {
			self::deleteCacheFromIdProduct(array($id_product));
		}
		elseif(Tools::getIsset('deletefeature') && ($id_feature = Tools::getValue('id_feature',false)) !== false) {
			$ids_product = self::getIdsProductFromIdFeature($id_feature);
			if(self::_isFilledArray($ids_product))
				self::deleteCacheFromIdProduct($ids_product);
		}
		elseif(Tools::getIsset('deletefeature_value') && ($id_feature_value = Tools::getValue('id_feature_value',false)) !== false) {
			$ids_product = self::getIdsProductFromIdFeatureValue($id_feature_value);
			if(self::_isFilledArray($ids_product))
				self::deleteCacheFromIdProduct($ids_product);
		}
		elseif(Tools::getIsset('deleteattribute_group') && ($id_attribute_group = Tools::getValue('id_attribute_group',false)) !== false) {
			$ids_product = self::getIdsProductFromIdAttributeGroup($id_attribute_group);
			if(self::_isFilledArray($ids_product))
				self::deleteCacheFromIdProduct($ids_product);
		}
		elseif(Tools::getIsset('deleteattribute') && ($id_attribute = Tools::getValue('id_attribute',false)) !== false) {
			$ids_product = self::getIdsProductFromIdAttribute($id_attribute);
			if(self::_isFilledArray($ids_product))
				self::deleteCacheFromIdProduct($ids_product);
		}
		elseif(Tools::getIsset('deletecategory') && ($id_category = Tools::getValue('id_category',false)) !== false) {
			$ids_product = self::getIdsProductFromIdCategory($id_category);
			if(self::_isFilledArray($ids_product))
				self::deleteCacheFromIdProduct($ids_product);
		}
		elseif((Tools::getIsset('submitAddmanufacturer') || Tools::getIsset('deletemanufacturer')) && ($id_manufacturer = Tools::getValue('id_manufacturer',false)) !== false) {
			$ids_product = self::getIdsProductFromIdManufacturer($id_manufacturer);
			if(self::_isFilledArray($ids_product))
				self::deleteCacheFromIdProduct($ids_product);
		}
		elseif((Tools::getIsset('submitAddcms') || Tools::getIsset('deletecms')) && ($id_cms = Tools::getValue('id_cms',false)) !== false) {
			self::deleteCacheFromIdCms($id_cms);
		}
		if (version_compare(_PS_VERSION_, '1.4.5.0', '<')) {
			if( Tools::getIsset('submitAddattribute') && ($id_attribute = Tools::getValue('id_attribute',false)) !== false) {
				$ids_product = self::getIdsProductFromIdAttribute($id_attribute);
				if(self::_isFilledArray($ids_product))
					self::deleteCacheFromIdProduct($ids_product);
			}
			elseif(Tools::getIsset('submitAddfeature_value') && ($id_feature_value = Tools::getValue('id_feature_value',false)) !== false) {
				$ids_product = self::getIdsProductFromIdFeatureValue($id_feature_value);
				if(self::_isFilledArray($ids_product))
					self::deleteCacheFromIdProduct($ids_product);
			}
			elseif((Tools::getIsset('submitAddproduct') || Tools::getIsset('submitAddproductAndStay')) && ($id_product = Tools::getValue('id_product',false)) !== false) {
				self::deleteCacheFromIdProduct(array($id_product));
			}
			elseif(Tools::getIsset('submitAddfeature') && ($id_feature = Tools::getValue('id_feature',false)) !== false) {
				$ids_product = self::getIdsProductFromIdFeature($id_feature);
				if(self::_isFilledArray($ids_product))
					self::deleteCacheFromIdProduct($ids_product);
			}
			elseif(Tools::getIsset('submitAddattribute_group') && ($id_attribute_group = Tools::getValue('id_attribute_group',false)) !== false) {
				$ids_product = self::getIdsProductFromIdAttributeGroup($id_attribute_group);
				if(self::_isFilledArray($ids_product))
					self::deleteCacheFromIdProduct($ids_product);
			}
		}
	}
	public static function clearCacheFromSC() {
		if((($id_product = Tools::getValue('id_product')) || ($id_product = Tools::getValue('product_list')) || ($id_product = Tools::getValue('idlist')) || ($id_product = Tools::getValue('gr_id'))) &&
		(Tools::getValue('x') == 'cat_catalog_update' || Tools::getValue('x') == 'cat_description_update' || Tools::getValue('p') == 'cat_categ_update' || Tools::getValue('p') == 'cat_accessory_update' || Tools::getValue('obj') == 'attachment' || Tools::getValue('p') == 'cat_combi_del' || Tools::getValue('p') == 'cat_customization_update' || Tools::getValue('x') == 'cat_tag_update' || Tools::getValue('x') == 'cat_combi_update' || Tools::getValue('x') == 'cat_productfeature_update' || Tools::getValue('x') == 'cat_specificprice_update')) {
			self::deleteCacheFromIdProduct(explode(',',$id_product));
		}
		elseif(($id_product = Tools::getValue('id_product')) && (Tools::getValue('col') == 'legend' || Tools::getIsset('list_id_image'))) {
			self::deleteCacheFromIdProduct(explode(',',$id_product));
		}
		elseif(($id_product = Tools::getValue('id_product')) && (Tools::getValue('col') == 'legend' || Tools::getIsset('list_id_image'))) {
			self::deleteCacheFromIdProduct(explode(',',$id_product));
		}
		elseif((($id_category = Tools::getValue('id_category')) || ($id_category = Tools::getValue('id_parent'))) && (Tools::getValue('x') == 'cat_category_update')) {
			$ids_product = self::getIdsProductFromIdCategory($id_category);
			if(self::_isFilledArray($ids_product))
				self::deleteCacheFromIdProduct($ids_product);
		}
		elseif((($id_category1 = Tools::getValue('idNewParent')) && ($id_category2 = Tools::getValue('idCateg'))) && (Tools::getValue('x') == 'cat_category_update')) {
			$ids_product = self::getIdsProductFromIdCategory($id_category);
			$ids_product = array_merge((array)$ids_product,(array)self::getIdsProductFromIdCategory($id_category2));
			if(self::_isFilledArray($ids_product))
				self::deleteCacheFromIdProduct($ids_product);
		}
		elseif(($id_attribute_group = Tools::getValue('gr_id')) && Tools::getValue('x') == 'cat_group_update') {
			$ids_product = self::getIdsProductFromIdAttributeGroup($id_attribute_group);
			if(self::_isFilledArray($ids_product))
				self::deleteCacheFromIdProduct($ids_product);
		}
		elseif(($id_attribute = Tools::getValue('gr_id')) && Tools::getValue('x') == 'cat_attribute_update') {
			$ids_product = self::getIdsProductFromIdAttribute($id_attribute);
			if(self::_isFilledArray($ids_product))
				self::deleteCacheFromIdProduct($ids_product);
		}
		elseif(($id_feature = Tools::getValue('gr_id')) && Tools::getValue('x') == 'cat_feature_update') {
			$ids_product = self::getIdsProductFromIdFeature($id_feature);
			if(self::_isFilledArray($ids_product))
				self::deleteCacheFromIdProduct($ids_product);
		}
		elseif(($id_feature_value = Tools::getValue('gr_id')) && Tools::getValue('x') == 'cat_featurevalue_update') {
			$ids_product = self::getIdsProductFromIdFeatureValue($id_feature_value);
			if(self::_isFilledArray($ids_product))
				self::deleteCacheFromIdProduct($ids_product);
		}
		return;
	}
	public static function hasCentralCacheActivatedFor($obj, $obj_product = NULL) {
		if (!is_object($obj) || ($obj_product != NULL && !is_object($obj_product))) return false;
		global $cookie;
		$current_class = get_class($obj);
		if (isset(self::$cacheManagerCentralCacheStatus[$current_class])) return self::$cacheManagerCentralCacheStatus[$current_class];
		switch ($current_class) {
			case 'CmsController':
				if ((int)pm_cachemanager::getSpecificGlobalConfiguration('centralcache_cms_active')) self::$cacheManagerCentralCacheStatus[$current_class] = true;
				break;
			case 'ManufacturerController':
				if ((int)pm_cachemanager::getSpecificGlobalConfiguration('centralcache_manufacturer_active')) self::$cacheManagerCentralCacheStatus[$current_class] = true;
				break;
			case 'CategoryController':
				if ((int)pm_cachemanager::getSpecificGlobalConfiguration('centralcache_category_active')) self::$cacheManagerCentralCacheStatus[$current_class] = true;
				break;
			case 'SupplierController':
				if ((int)pm_cachemanager::getSpecificGlobalConfiguration('centralcache_supplier_active')) self::$cacheManagerCentralCacheStatus[$current_class] = true;
				break;
			case 'ProductController':
				if ((int)pm_cachemanager::getSpecificGlobalConfiguration('centralcache_product_active')
				&& !Tools::getValue('submitCustomizedDatas')
				&& !(Validate::isLoadedObject($obj_product) && $obj_product->customizable  && (sizeof($cookie->getFamily('pictures_'.(int)($obj_product->id))) || sizeof($cookie->getFamily('textFields_'.(int)($obj_product->id))))))
					self::$cacheManagerCentralCacheStatus[$current_class] = true;
				break;
			case 'SitemapController':
				if ((int)pm_cachemanager::getSpecificGlobalConfiguration('centralcache_sitemap_active')) self::$cacheManagerCentralCacheStatus[$current_class] = true;
				break;
			case 'PricesDropController':
				if ((int)pm_cachemanager::getSpecificGlobalConfiguration('centralcache_pricesdrop_active')) self::$cacheManagerCentralCacheStatus[$current_class] = true;
				break;
			case 'NewProductsController':
				if ((int)pm_cachemanager::getSpecificGlobalConfiguration('centralcache_newproducts_active')) self::$cacheManagerCentralCacheStatus[$current_class] = true;
				break;
			case 'BestSalesController':
				if ((int)pm_cachemanager::getSpecificGlobalConfiguration('centralcache_bestsales_active')) self::$cacheManagerCentralCacheStatus[$current_class] = true;
				break;
		}
		if (isset(self::$cacheManagerCentralCacheStatus[$current_class])) return self::$cacheManagerCentralCacheStatus[$current_class];
		return false;
	}
	public static function fetchSmartyContent($obj, $smarty, $obj_product = NULL, $obj_manufacturer = NULL, $obj_supplier = NULL) {
		global $cookie;
		$current_class = get_class($obj);
		switch ($current_class) {
			case 'CmsController':
				return $smarty->fetch(_PS_THEME_DIR_.'cms.tpl');
				break;
			case 'ManufacturerController':
				if ($obj_manufacturer) {
					return $smarty->fetch(_PS_THEME_DIR_.'manufacturer.tpl');
				} else {
					return $smarty->fetch(_PS_THEME_DIR_.'manufacturer-list.tpl');
				}
				break;
			case 'CategoryController':
				return $smarty->fetch(_PS_THEME_DIR_.'category.tpl');
				break;
			case 'SupplierController':
				if ($obj_supplier) {
					return $smarty->fetch(_PS_THEME_DIR_.'supplier.tpl');
				} else {
					return $smarty->fetch(_PS_THEME_DIR_.'supplier-list.tpl');
				}
				break;
			case 'ProductController':
				if (!Tools::getValue('submitCustomizedDatas')
				&& !(Validate::isLoadedObject($obj_product) && $obj_product->customizable  && (sizeof($cookie->getFamily('pictures_'.(int)($obj_product->id))) || sizeof($cookie->getFamily('textFields_'.(int)($obj_product->id))))))
					return $smarty->fetch(_PS_THEME_DIR_.'product.tpl');
				break;
			case 'SitemapController':
				return $smarty->fetch(_PS_THEME_DIR_.'sitemap.tpl');
				break;
			case 'PricesDropController':
				return $smarty->fetch(_PS_THEME_DIR_.'prices-drop.tpl');
				break;
			case 'NewProductsController':
				return $smarty->fetch(_PS_THEME_DIR_.'new-products.tpl');
				break;
			case 'BestSalesController':
				return $smarty->fetch(_PS_THEME_DIR_.'best-sales.tpl');
				break;
		}
		return false;
	}
	public function hookBackOfficeTop() {
		global $currentIndex;
		$return = '<link type="text/css" rel="stylesheet" href="' . $this->_path . 'css/backofficetop.css" />';
		$return .= '<script type="text/javascript">
			var pm_clearCacheLabel = "'.addcslashes($this->l('Clear cache'),'"').'";
			var pm_clearCacheImg = "'.$this->_path . 'img/1347360258_history_clear.png'.'";
			var pm_msgWaitClear = "'.addcslashes($this->l('Cache flush in progress! Please wait ....'),'"').'";
			var pm_clearCacheUrl = "'.((version_compare(_PS_VERSION_, '1.5.0.0', '<')) ? $currentIndex : $_SERVER['SCRIPT_NAME'].(($controller = Tools::getValue('controller')) ? '?controller='.$controller: '')) . '&pm_clear_all_cache=1&token=' . Tools::getValue('token').'";
		</script>';
		$return .= '<script type="text/javascript" src="' . $this->_path . 'js/backofficetop.js"></script>';
		return $return;
	}
	public function hookAfterSaveProduct($params) {
		if (strtolower(Tools::getValue('controller')) == 'adminimport' || strtolower(Tools::getValue('tab')) == 'adminimport') return;
		if (!$params['id_product'])
			return;
		self::deleteCacheFromIdProduct(array($params['id_product']));
	}
	public function hookAfterSaveFeature($params) {
		if (strtolower(Tools::getValue('controller')) == 'adminimport' || strtolower(Tools::getValue('tab')) == 'adminimport') return;
		if (!$params['id_feature'])
			return;
		$ids_product = self::getIdsProductFromIdFeature($params['id_feature']);
		if(self::_isFilledArray($ids_product))
			self::deleteCacheFromIdProduct($ids_product);
	}
	public function hookAfterSaveFeatureValue($params) {
		if (strtolower(Tools::getValue('controller')) == 'adminimport' || strtolower(Tools::getValue('tab')) == 'adminimport') return;
		if (!$params['id_feature_value'])
			return;
		$ids_product = self::getIdsProductFromIdFeatureValue($params['id_feature_value']);
		if(self::_isFilledArray($ids_product))
			self::deleteCacheFromIdProduct($ids_product);
	}
	public function hookAfterSaveAttributeGroup($params) {
		if (strtolower(Tools::getValue('controller')) == 'adminimport' || strtolower(Tools::getValue('tab')) == 'adminimport') return;
		if (!$params['id_attribute_group'])
			return;
		$ids_product = self::getIdsProductFromIdAttributeGroup($params['id_attribute_group']);
		if(self::_isFilledArray($ids_product))
			self::deleteCacheFromIdProduct($ids_product);
	}
	public function hookAfterSaveAttribute($params) {
		if (strtolower(Tools::getValue('controller')) == 'adminimport' || strtolower(Tools::getValue('tab')) == 'adminimport') return;
		if (!$params['id_attribute'])
			return;
		$ids_product = self::getIdsProductFromIdAttribute($params['id_attribute']);
		if(self::_isFilledArray($ids_product))
			self::deleteCacheFromIdProduct($ids_product);
	}
	public function hookCategoryUpdate($params) {
		if (strtolower(Tools::getValue('controller')) == 'adminimport' || strtolower(Tools::getValue('tab')) == 'adminimport') return;
		if (!$params['category'])
			return;
		$ids_product = self::getIdsProductFromIdCategory($params['category']->id);
		if(self::_isFilledArray($ids_product))
			self::deleteCacheFromIdProduct($ids_product);
	}
	public function hookNewOrder($params) {
		$order = $params['order'];
		$products_detail = $order->getProductsDetail();
		if(self::_isFilledArray($products_detail)) {
			$ids_product = array();
			foreach ($products_detail as $product_detail) {
				$ids_product[] = $product_detail['product_id'];
			}
			self::deleteCacheFromIdProduct($ids_product);
		}
	}
	public function runCrontab($secure_key) {
		$conf_secure_key = Configuration::get('PM_'.self::$_module_prefix.'_CRON_SECURE_KEY');
		if (!empty($conf_secure_key) && $conf_secure_key == $secure_key) {
			self::clearAllCache();
			Configuration::updateValue('PM_'.self::$_module_prefix.'_CRON_LAST_RUN', time());
			return true;
		}
		return false;
	}
	public static function array_diff_assoc_recursive($array1, $array2) {
		$difference = array();
		foreach ($array1 as $key => $value) {
			if (is_array($value)) {
				if (!array_key_exists($key, $array2) || !is_array($array2[$key]))
					$difference[$key] = $value;
				else {
					$new_diff = self::array_diff_assoc_recursive($value, $array2[$key]);
					if (!empty($new_diff))
						$difference[$key] = $new_diff;
				}
			} elseif (!array_key_exists($key, $array2) || $array2[$key] !== $value)
				$difference[$key] = $value;
		}
		return $difference;
	}
}
