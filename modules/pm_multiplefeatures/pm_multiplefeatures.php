<?php
/**
 *
 *	PM_MultipleFeatures Front Office Feature
 *
 *	@category front_office_features
 *	@author Presta-Module.com <support@presta-module.com>
 *	@copyright Presta-Module 2015
 *	@version 1.3.4
 *
 * 		 	 ____     __  __
 * 			|  _ \   |  \/  |
 * 			| |_) |  | |\/| |
 * 			|  __/   | |  | |
 * 			|_|      |_|  |_|
 *
 *
 *************************************
 **        Multiple Features         *
 **   http://www.presta-module.com   *
 *************************************
 * +
 * + Languages: EN, FR
 * + PS version: 1.4, 1.5, 1.6
 *
 ****/
if (!defined('_PS_VERSION_'))
	exit;
class PM_MultipleFeatures extends Module
{
	public static $_module_prefix = 'MF';
	private $_languages;
	private $_defaultFormLanguage;
	public $_errors = array();
	protected $submitted_tabs;
	private $_cookie;
	private $_smarty;
	private $_iso_lang;
	protected $_copyright_link = array(
		'link'	=> '',
		'img'	=> 'logo-module.JPG'
	);
	protected $_support_link = false;
	protected $_getting_started = false;
	protected $_defaultConfiguration = array(
		'featureSeparator' => ', ',
	);
	public function __construct()
	{
		$this->name = 'pm_multiplefeatures';
		$this->tab = 'front_office_features';
		$this->version = '1.3.4';
		$this->author = 'Presta-Module';
		$this->module_key = 'ba224a2fe8a2bd0c86589860fab0decb';
		$this->need_instance = 0;
		parent::__construct();
		$this->initClassVar();
		$this->displayName = 'Multiple Features';
		if ($this->_onBackOffice()) {
			$this->description = $this->l('Allow to define multiple features values per features');
			$doc_url_tab['fr'] = '//fr/multiplefeatures/';
			$doc_url_tab['en'] = '//docs/en/multiplefeatures/';
			$doc_url = $doc_url_tab['en'];
			if ($this->_iso_lang == 'fr') $doc_url = $doc_url_tab['fr'];
			$this->_support_link = array(
				
				array('link' => '//contact-community.php?id_product=6356', 'target' => '_blank', 'label' => $this->l('Support contact')),
			);
			$oldModuleVersion = Configuration::get('PM_'.self::$_module_prefix.'_LAST_VERSION', false);
			if ($oldModuleVersion != false && version_compare($oldModuleVersion, '1.3.0', '<=') || $oldModuleVersion != false && $oldModuleVersion != $this->version) {
				$this->_installDatabase();
			}
			Configuration::updateValue('PM_'.self::$_module_prefix.'_LAST_VERSION', $this->version);
		}
	}
	private function _installDatabase() {
		$result = Db::getInstance()->ExecuteS('SHOW INDEX FROM `' . _DB_PREFIX_ . 'feature_product` WHERE `Key_name` = "PRIMARY"');
		if ($result) {
			Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . 'feature_product` DROP PRIMARY KEY');
		}
		$result = Db::getInstance()->ExecuteS('SHOW INDEX FROM `' . _DB_PREFIX_ . 'feature_product` WHERE `Key_name` = "mf_feature_product"');
		if (!$result || !Db::getInstance()->numRows()) {
			Db::getInstance()->Execute('ALTER TABLE  `' . _DB_PREFIX_ . 'feature_product` ADD INDEX `mf_feature_product` (`id_feature`, `id_product`)');
		}
		$result = Db::getInstance()->ExecuteS('SHOW INDEX FROM `' . _DB_PREFIX_ . 'feature_product` WHERE `Key_name` = "mf_unique"');
		if (!$result || !Db::getInstance()->numRows()) {
			$duplicateRows = Db::getInstance()->ExecuteS('SELECT *, COUNT(*) as count from `' . _DB_PREFIX_ . 'feature_product` GROUP BY id_feature, id_product, id_feature_value HAVING COUNT(*) > 1');
			if ($duplicateRows && self::_isFilledArray($duplicateRows)) {
				foreach ($duplicateRows as $duplicateRow) {
					Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'feature_product` WHERE `id_feature` = ' . (int)$duplicateRow['id_feature'] . ' AND `id_product` = ' . (int)$duplicateRow['id_product'] . '	AND `id_feature_value` = ' . (int)$duplicateRow['id_feature_value'] . ' LIMIT ' . ((int)$duplicateRow['count'] - 1));
				}
			}
			Db::getInstance()->Execute('ALTER TABLE  `' . _DB_PREFIX_ . 'feature_product` ADD UNIQUE INDEX `mf_unique` (`id_feature`, `id_product`, `id_feature_value`)');
		}
		$this->_columnExists('feature_product', 'position', true, 'tinyint(3) unsigned NOT NULL DEFAULT "0"', 'id_feature_value');
		$result = Db::getInstance()->ExecuteS('SHOW INDEX FROM `' . _DB_PREFIX_ . 'feature_product` WHERE `Key_name` = "position"');
		if (!$result || !Db::getInstance()->numRows()) {
			Db::getInstance()->Execute('ALTER TABLE  `' . _DB_PREFIX_ . 'feature_product` ADD INDEX `position` (`position`)');
		}
	}
	private function _columnExists($table, $column, $createIfNotExist = false, $type = false, $insertAfter = false) {
		$resultset = Db::getInstance()->ExecuteS("SHOW COLUMNS FROM `" . _DB_PREFIX_ . $table . "`", true, false);
		foreach ($resultset as $row )
			if ($row['Field'] == $column)
				return true;
		if ($createIfNotExist && Db::getInstance()->Execute('ALTER TABLE `' . _DB_PREFIX_ . $table . '` ADD `' . $column . '` ' . $type . ' ' . ($insertAfter ? ' AFTER `' . $insertAfter . '`' : '') . ''))
			return true;
		return false;
	}
	public function install() {
		$this->_installDatabase();
		return (parent::install() AND $this->registerHook('backOfficeTop')  AND $this->registerHook('backOfficeHeader') AND ((version_compare(_PS_VERSION_, '1.5.0.0', '>=') && $this->registerHook('displayOverrideTemplate')) || version_compare(_PS_VERSION_, '1.5.0.0', '<')));
	}
	private function checkFeatures($languages, $feature_id) {
		$rules = call_user_func(array('FeatureValue', 'getValidationRules'), 'FeatureValue');
		$feature = Feature::getFeature(Configuration::get('PS_LANG_DEFAULT'), $feature_id);
		$val = 0;
		foreach ($languages AS $language)
			if ($val = Tools::getValue('custom_'.$feature_id.'_'.$language['id_lang']))
			{
				$currentLanguage = new Language($language['id_lang']);
				if (Tools::strlen($val) > $rules['sizeLang']['value'])
					$this->_errors[] = Tools::displayError('name for feature').' <b>'.$feature['name'].'</b> '.Tools::displayError('is too long in').' '.$currentLanguage->name;
				elseif (!call_user_func(array('Validate', $rules['validateLang']['value']), $val))
					$this->_errors[] = Tools::displayError('Valid name required for feature.').' <b>'.$feature['name'].'</b> '.Tools::displayError('in').' '.$currentLanguage->name;
				if (sizeof($this->_errors))
					return (0);
				if ($language['id_lang'] == Configuration::get('PS_LANG_DEFAULT'))
					return ($val);
			}
		return (0);
	}
	public static function getProductFeaturesStatic($id_product, $id_lang, $custom = true) {
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT fp.id_feature, fp.id_product, fp.id_feature_value, vl.value, v.custom
		FROM `'._DB_PREFIX_.'feature_product` fp
		LEFT JOIN `'._DB_PREFIX_.'feature_value` v ON (fp.`id_feature_value` = v.`id_feature_value`)
		LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` vl ON (v.`id_feature_value` = vl.`id_feature_value` AND vl.`id_lang` = '.(int)$id_lang.')
		WHERE fp.`id_product` = '.(int)$id_product
		. (!$custom ? ' AND v.`custom` = 0' : '')
		. ' ORDER BY fp.`position` ASC'
		);
	}
	public function getFrontFeatures($id_product, $separator = null, $id_feature = null) {
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			$id_lang = (int)Context::getContext()->cookie->id_lang;
		} else {
			global $cookie;
			$id_lang = $cookie->id_lang;
		}
		if ($separator == null) {
			$config = $this->_getModuleConfiguration();
			$separator = $config['featureSeparator'];
		}
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT fp.id_feature, vl.value, fl.name
		FROM `'._DB_PREFIX_.'feature_product` fp
		LEFT JOIN `'._DB_PREFIX_.'feature_value` v ON (fp.`id_feature_value` = v.`id_feature_value`)
		LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` vl ON (v.`id_feature_value` = vl.`id_feature_value` AND vl.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'feature` f ON (f.`id_feature` = v.`id_feature`)
		'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP ? Shop::addSqlAssociation('feature', 'f') : '').'
		LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl ON (fl.`id_feature` = f.`id_feature` AND fl.`id_lang` = '.(int)$id_lang.')
		WHERE fp.`id_product` = '.(int)$id_product
		. ($id_feature != null && $id_feature ? ' AND f.`id_feature` = '.(int)$id_feature : '')
		. ' ORDER BY ' 
		. (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'f.`position` ASC, ' : '')
		. 'fp.`position` ASC');
		$return = array();
		if ($result && is_array($result) && sizeof($result)) {
			foreach ($result as $row) {
				$return[$row['id_feature']]['values'][] = $row['value'];
				$return[$row['id_feature']]['name'] = $row['name'];
			}
			foreach ($return as $key=>$row) $return[$key]['value'] = implode($separator, $row['values']);
		}
		if ($id_feature != null && $id_feature && isset($return[$id_feature])) {
			return $return[$id_feature]['value'];
		} else {
			return $return;
		}
	}
	protected function getCurrentProductId() {
		if (is_object($this->context->controller) && preg_match('/^ProductController/i', get_class($this->context->controller))) {
			if (method_exists($this->context->controller, 'getProduct'))
				$product = $this->context->controller->getProduct();
			else {
				$id_product = (int)Tools::getValue('id_product');
				if (Validate::isUnsignedId($id_product) && $id_product > 0)
					return $id_product;
			}
			if (Validate::isLoadedObject($product))
				return $product->id;
		}
		return false;
	}
	public function hookDisplayOverrideTemplate($params) {
		$id_product = $this->getCurrentProductId();
		if ($id_product !== false) {
			$this->context->smarty->assign('features', $this->getFrontFeatures($id_product));
		} else if (isset($this->context->controller) && is_object($this->context->controller) && isset($this->context->controller->php_self) && $this->context->controller->php_self == 'products-comparison') {
			$ids = null;
			if (($product_list = Tools::getValue('compare_product_list')) && ($postProducts = (isset($product_list) ? rtrim($product_list, '|') : ''))) {
				$ids = array_unique(explode('|', $postProducts));
			} elseif (isset($this->context->cookie->id_compare)) {
				$ids = CompareProduct::getCompareProducts($this->context->cookie->id_compare);
			}
			$listFeatures = array();
			foreach ($ids as $id) {
				$curProduct = new Product((int)$id, true, $this->context->language->id);
				if (Validate::isLoadedObject($curProduct) && $curProduct->active && $curProduct->isAssociatedToShop()) {
					foreach ($curProduct->getFrontFeatures($this->context->language->id) as $feature)
						$listFeatures[$curProduct->id][$feature['id_feature']] = $this->getFrontFeatures($curProduct->id, null, $feature['id_feature']);
				}
			}
			if (sizeof($listFeatures)) {
				$this->context->smarty->assign(array(
					'product_features' => $listFeatures,
				));
			}
		}
	}
	public function getContent() {
		$this->_html = '';
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$this->_html .= '<div id="pm_backoffice_wrapper" class="pm_bo_ps_'.substr(str_replace('.', '', _PS_VERSION_), 0, 2).'">';
		$this->_displayTitle($this->displayName);
		if (file_exists(dirname(__FILE__) . '/css/admin.css'))
			$this->_html .= '<link type="text/css" rel="stylesheet" href="' . $this->_path . 'css/admin.css" />';
		if (file_exists(dirname(__FILE__) . '/js/admin.js'))
			$this->_html .= '<script type="text/javascript" src="' . $this->_path . 'js/admin.js"></script>';
		$config = $this->_getModuleConfiguration();
		$this->_postProcess();
		$this->_showRating(true);
		if (version_compare(_PS_VERSION_, '1.5.0.0', '<')) {
			$this->_html .= '<p>' . $this->l('In order to show grouped features, you have edit the product.tpl file of your theme and add the code below, just before "{foreach from=$features item=feature}"') . '<p>';
			$this->_html .= '<pre>';
			$this->_html .= '
{* Multiple Features *}
{assign var="features" value=Module::getInstanceByName(\'pm_multiplefeatures\')->getFrontFeatures($product->id)}
{* /Multiple Features *}';
			$this->_html .= '</pre>';
		}
		$this->_html .= '<h3>' . $this->l('Configuration') . '</h3>';
		$this->_startForm(array('id' => 'formGlobalOptions', 'iframetarget' => false, 'target' => '_self'));
		$this->_displayInputText(array(
			'obj' => $config,
			'maxlength' => 10,
			'size' => '70px',
			'required' => true,
			'key' => 'featureSeparator',
			'defaultvalue' => $this->_defaultConfiguration['featureSeparator'],
			'label' => $this->l('Feature separator')));
		$this->_displaySubmit($this->l('   Save   '), 'submitModuleConfiguration');
		$this->_endForm(array('id' => 'formGlobalOptions', 'includehtmlatend' => true));
		$this->_displaySupport();
		$this->_html .= '</div>';
		return $this->_html;
	}
	private function _postProcess() {
		if (Tools::getIsset('submitModuleConfiguration') && Tools::isSubmit('submitModuleConfiguration')) {
			$config = $this->_getModuleConfiguration();
			foreach (array('featureSeparator') as $configKey)
				$config[$configKey] = Tools::getValue($configKey);
			$this->_setModuleConfiguration($config);
			$this->_html .= $this->displayConfirmation($this->l('Module configuration successfully saved'));
		} else if (Tools::getIsset('dismissRating')) {
			$this->_html = '';
			self::_cleanBuffer();
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				Configuration::updateGlobalValue('PM_'.self::$_module_prefix.'_DISMISS_RATING', 1);
			else
				Configuration::updateValue('PM_'.self::$_module_prefix.'_DISMISS_RATING', 1);
			die;
		}
	}
	protected function isTabSubmitted($tab_name)
	{
		if (!is_array($this->submitted_tabs))
			$this->submitted_tabs = Tools::getValue('submitted_tabs');
		if (is_array($this->submitted_tabs) && in_array($tab_name, $this->submitted_tabs))
			return true;
		return false;
	}
	protected function deleteTabSubmitted($tab_name)
	{
		if (!is_array($this->submitted_tabs))
			$this->submitted_tabs = Tools::getValue('submitted_tabs');
		if (is_array($this->submitted_tabs) && in_array($tab_name, $this->submitted_tabs)) {
			unset($_POST['submitted_tabs'][array_search($tab_name, $_POST['submitted_tabs'])]);
		}
		return;
	}
	private function _initBackOfficeAssets($idProduct) {
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			$idLang = (int)$this->context->cookie->id_lang;
		} else {
			$idLang = (int)$this->_cookie->id_lang;
		}
		$obj = new Product($idProduct);
		if (Validate::isLoadedObject($obj)) {
			$selectedFeaturesList = array();
			$selectedFeatures = self::getProductFeaturesStatic($obj->id, (int)$idLang, false);
			foreach ($selectedFeatures AS $feature) {
				if (!isset($selectedFeaturesList[(int)$feature['id_feature']]))
					$selectedFeaturesList[(int)$feature['id_feature']] = array();
				$selectedFeaturesList[(int)$feature['id_feature']][] = (int)$feature['id_feature_value'];
			}
			echo '
			<script type="text/javascript">
				var pm_FeatureList = '. Tools::jsonEncode($selectedFeaturesList) .';
				var pm_FeatureAvailableListTitle = '. Tools::jsonEncode($this->l('Available features:')) .';
				var pm_FeatureSelectedListTitle = '. Tools::jsonEncode($this->l('Selected features:')) .';
				var pm_FeatureAddAllButtonLabel = '. Tools::jsonEncode($this->l('Add all')) .';
				var pm_FeatureRemoveAllButtonLabel = '. Tools::jsonEncode($this->l('Remove all')) .';
			</script>
			';
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
				echo '
				<script type="text/javascript">
					$(document).ready(function() {
						// Init select
						pmTransformSelect();
					});
				</script>
				';
			}	
		}
	}
	public function hookBackOfficeHeader($params) {
		if (!$this->active)
			return;
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && (Tools::getValue('controller') == 'adminproducts' || Tools::getValue('controller') == 'AdminProducts') && Tools::getValue('id_product')) {
			$this->context->controller->addJquery();
			$this->context->controller->addJqueryUI('ui.core');
			$this->context->controller->addJqueryUI('ui.widget');
			$this->context->controller->addJqueryUI('ui.mouse');
			$this->context->controller->addJqueryUI('ui.draggable');
			$this->context->controller->addJqueryUI('ui.sortable');
			$this->context->controller->addJqueryUI('ui.droppable');
			$this->context->controller->addCSS($this->_path . 'js/connected-list/connected-list.min.css', 'all');
			$this->context->controller->addJS($this->_path . 'js/connected-list/connected-list.min.js');
			$this->context->controller->addJS($this->_path . 'js/product-tab.js');
		}
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && ((Tools::getValue('controller') == 'adminproducts' || Tools::getValue('controller') == 'AdminProducts') && Tools::getValue('id_product') && (Tools::getIsset('updateproduct') || Tools::getIsset('addproduct')) && Tools::getIsset('ajax') && Tools::getValue('action') == 'Features')) {
			$this->_initBackOfficeAssets(Tools::getValue('id_product'));
		} else if ((version_compare(_PS_VERSION_, '1.5.0.0', '<') && Tools::isSubmit('submitProductFeature')) || (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && $this->isTabSubmitted('Features'))) {
			if (Validate::isLoadedObject($product = new Product((int)(Tools::getValue('id_product'))))) {
				$product->deleteFeatures();
				$languages = Language::getLanguages(false);
				foreach ($_POST AS $key => $val) {
					if (preg_match('/^(?:feature|custom)_([0-9]+)_(value|[0-9]+)/i', $key, $match)) {
						if (preg_match('/^feature/i', $key) && ((is_array($val) && sizeof($val)) || (!is_array($val) && $val))) {
							if (is_array($val)) {
								foreach ($val as $pos => $val2) {
									$id_feature = (int)$match[1];
									$id_feature_value = (int)$val2;
									$row = array(
										'id_feature' => (int)$id_feature,
										'id_product' => (int)$product->id,
										'id_feature_value' => (int)$id_feature_value,
										'position' => (int)$pos,
									);
									if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
										Db::getInstance()->insert('feature_product', $row);
									else
										Db::getInstance()->autoExecute(_DB_PREFIX_.'feature_product', $row, 'INSERT');
								}
								if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
									SpecificPriceRule::applyAllRules(array((int)$this->id));
							} else {
								$product->addFeaturesToDB($match[1], $val);
							}
						} else if(preg_match('/^custom/i', $key) && $match[2] == Configuration::get('PS_LANG_DEFAULT') && $val) {
							if ($default_value = $this->checkFeatures($languages, $match[1])) {
								$id_value = $product->addFeaturesToDB($match[1], 0, 1);
								foreach ($languages AS $language)
									if ($cust = Tools::getValue('custom_'.$match[1].'_'.(int)$language['id_lang']))
										$product->addFeaturesCustomToDB($id_value, (int)$language['id_lang'], $cust);
									else
										$product->addFeaturesCustomToDB($id_value, (int)$language['id_lang'], $default_value);
							}
						}
					}
				}
				if (version_compare(_PS_VERSION_, '1.5.0.0', '<')) {
					global $currentIndex;
					Tools::redirectAdmin($currentIndex.'&id_product='.(int)$product->id.'&id_category='.(!empty($_REQUEST['id_category'])?$_REQUEST['id_category']:'1').'&addproduct&tabs=4&conf=4&token='.Tools::getValue('token'));
					die;
				} else {
					$this->deleteTabSubmitted('Features');
				}
			}
		} else if (Tools::getValue('key_tab') == 'Features') {
			$_GET['key_tab_onload'] = 'Features';
			unset($_GET['key_tab']);
		}
	}
	public function hookBackOfficeTop($params) {
		if ((Tools::getValue('tab') == 'AdminCatalog' && Tools::getValue('id_product') && (Tools::getIsset('updateproduct') || Tools::getIsset('addproduct')))
			|| (Tools::getValue('controller') == 'adminproducts' || Tools::getValue('controller') == 'AdminProducts') && Tools::getValue('id_product') && (Tools::getIsset('updateproduct') || Tools::getIsset('addproduct')) && !Tools::getIsset('ajax')) {
			if (version_compare(_PS_VERSION_, '1.5.0.0', '<')) {
				$this->_initBackOfficeAssets(Tools::getValue('id_product'));
				echo '
				<link type="text/css" rel="stylesheet" href="' . $this->_path.'js/connected-list/connected-list.min.css" />
				<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js"></script>
				<script type="text/javascript" src="' . $this->_path.'js/connected-list/connected-list.min.js"></script>
				<script type="text/javascript" src="' . $this->_path.'js/product-tab.js"></script>
				<script type="text/javascript">
					$(document).ajaxComplete(function(event, xhr, settings) {
						if (typeof(settings.data) != "undefined" && settings.data.indexOf("ajaxProductTab=5") != -1) {
							// Change <td> width
							$("div#step5 table tr td:eq(2)").css("width", "45%");
							// Init select
							pmTransformSelect();
						}
					});
				</script>';
			} else {
				if (Tools::getValue('key_tab_onload') == 'Features') {
					return '
					<script type="text/javascript">
						$(window).load(function() {
							$(".productTabs #link-Features").trigger("click");
						});
					</script>
					';
				}
			}
		}
	}
	private static function _cleanBuffer() {
		if (ob_get_length() > 0)
			ob_clean();
	}
	private function initClassVar() {
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
			$this->_context = Context::getContext();
			$this->_cookie = $this->_context->cookie;
			$this->_smarty = $this->_context->smarty;
		} else {
			global $smarty, $cookie;
			$this->_cookie = $cookie;
			$this->_smarty = $smarty;
		}
		$this->_iso_lang = Language::getIsoById($this->_cookie->id_lang);
	}
	protected function _displayTitle($title) {
		$this->_html .= '<h2>' . $title . '</h2>';
	}
	public static function _isFilledArray($array) {
		return ($array && is_array($array) && sizeof($array));
	}
	
	private function _getPMdata() {
		$param = array();
		$param[] = 'ver-'._PS_VERSION_;
		$param[] = 'current-'.$this->name;
		
		$result = Db::getInstance()->ExecuteS('SELECT DISTINCT name FROM '._DB_PREFIX_.'module WHERE name LIKE "pm_%"');
		if ($result && self::_isFilledArray($result)) {
			foreach ($result as $module) {
				$instance = Module::getInstanceByName($module['name']);
				if ($instance && isset($instance->version)) $param[] = $module['name'].'-'.$instance->version;
			}
		}
		return urlencode(base64_encode(implode('|', $param)));
	}
	protected function _displayCS() {
		$this->_html .= '<div id="pm_panel_cs_modules_bottom" class="pm_panel_cs_modules_bottom"><br />';
		$this->_displayTitle($this->l('Check all our modules'));
		$this->_html .= '<iframe src="//cross-selling-addons-modules-footer?pm='.$this->_getPMdata().'" scrolling="no"></iframe></div>';
	}
	protected function _displaySupport() {
		$this->_html .= '<div id="pm_footer_container" class="ui-corner-all ui-tabs ui-tabs-panel">';
		$this->_displayCS();
		$this->_html .= '<div id="pm_support_informations" class="pm_panel_bottom"><br />';
		if (method_exists($this, '_displayTitle'))
			$this->_displayTitle($this->l('Information & Support', (isset($this->_coreClassName) ? $this->_coreClassName : false)));
		else
			$this->_html .= '<h2>' . $this->l('Information & Support', (isset($this->_coreClassName) ? $this->_coreClassName : false)) . '</h2>';
		$this->_html .= '<ul class="pm_links_block">';
		$this->_html .= '<li class="pm_module_version"><strong>' . $this->l('Module Version: ', (isset($this->_coreClassName) ? $this->_coreClassName : false)) . '</strong> ' . $this->version . '</li>';
		if (isset($this->_getting_started) && self::_isFilledArray($this->_getting_started))
			$this->_html .= '<li class="pm_get_started_link"><a href="javascript:;" class="pm_link">'. $this->l('Getting started', (isset($this->_coreClassName) ? $this->_coreClassName : false)) .'</a></li>';
		if (self::_isFilledArray($this->_support_link))
			foreach($this->_support_link as $infos)
				$this->_html .= '<li class="pm_useful_link"><a href="'.$infos['link'].'" target="_blank" class="pm_link">'.$infos['label'].'</a></li>';
		$this->_html .= '</ul>';
		if (isset($this->_copyright_link) && $this->_copyright_link) {
			$this->_html .= '<div class="pm_copy_block">';
			if (isset($this->_copyright_link['link']) && !empty($this->_copyright_link['link'])) $this->_html .= '<a href="'.$this->_copyright_link['link'].'"'.((isset($this->_copyright_link['target']) AND $this->_copyright_link['target']) ? ' target="'.$this->_copyright_link['target'].'"':'').''.((isset($this->_copyright_link['style']) AND $this->_copyright_link['style']) ? ' style="'.$this->_copyright_link['style'].'"':'').'>';
			$this->_html .= '<img src="'.str_replace('_PATH_',$this->_path,$this->_copyright_link['img']).'" />';
			if (isset($this->_copyright_link['link']) && !empty($this->_copyright_link['link'])) $this->_html .= '</a>';
			$this->_html .= '</div>';
		}
		$this->_html .= '</div>';
		$this->_html .= '</div>';
		if (isset($this->_getting_started) && self::_isFilledArray($this->_getting_started)) {
			$this->_html .= "<script type=\"text/javascript\">
			$('.pm_get_started_link a').click(function() { $.fancybox([";
			$get_started_image_list = array();
			foreach ($this->_getting_started as $get_started_image)
				$get_started_image_list[] = "{ 'href': '".$get_started_image['href']."', 'title': '".htmlentities($get_started_image['title'], ENT_QUOTES, 'UTF-8')."' }";
			$this->_html .= implode(',', $get_started_image_list);
			$this->_html .= "
					], {
					'padding'			: 0,
					'transitionIn'		: 'none',
					'transitionOut'		: 'none',
					'type'				: 'image',
					'changeFade'		: 0
				}); });
			</script>";
		}
		if (method_exists($this, '_includeHTMLAtEnd')) $this->_includeHTMLAtEnd();
	}
	protected function _showRating($show = false) {
		$dismiss = (int)(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? Configuration::getGlobalValue('PM_'.self::$_module_prefix.'_DISMISS_RATING') : Configuration::get('PM_'.self::$_module_prefix.'_DISMISS_RATING'));
		if ($show && $dismiss != 1 && self::_getNbDaysModuleUsage() >= 3) {
			$this->_html .= '
			<div id="addons-rating-container" class="ui-widget note">
				<div style="margin-top: 20px; margin-bottom: 20px; padding: 0 .7em; text-align: center;" class="ui-state-highlight ui-corner-all">
					<p class="invite">'
						. $this->l('You are satisfied with our module and want to encourage us to add new features ?')
						. '<br/>'
						. '<a href="http://addons.prestashop.com/ratings.php" target="_blank"><strong>'
						. $this->l('Please rate it on Prestashop Addons, and give us 5 stars !')
						. '</strong></a>
					</p>
					<p class="dismiss">'
						. '[<a href="javascript:void(0);">'
						. $this->l('No thanks, I don\'t want to help you. Close this dialog.')
						. '</a>]
					 </p>
				</div>
			</div>';
		}
	}
	protected static function _getNbDaysModuleUsage() {
		$sql = 'SELECT DATEDIFF(NOW(),date_add)
				FROM '._DB_PREFIX_.'configuration
				WHERE name = \''.pSQL('PM_'.self::$_module_prefix.'_LAST_VERSION').'\'
				ORDER BY date_add ASC';
		return (int)Db::getInstance()->getValue($sql);
	}
	protected function _onBackOffice() {
		if (isset($this->_cookie->id_employee) && Validate::isUnsignedId($this->_cookie->id_employee)) return true;
		return false;
	}
	protected function _getModuleConfiguration() {
		$conf = Configuration::get('PM_' . self::$_module_prefix . '_CONF');
		if (!empty($conf))
			return Tools::jsonDecode($conf, true);
		else
			return $this->_defaultConfiguration;
	}
	public static function getModuleConfigurationStatic() {
		$conf = Configuration::get('PM_' . self::$_module_prefix . '_CONF');
		if (!empty($conf))
			return Tools::jsonDecode($conf, true);
		else
			return array();
	}
	protected function _setModuleConfiguration($newConf) {
		Configuration::updateValue('PM_' . self::$_module_prefix . '_CONF', Tools::jsonEncode($newConf));
	}
	protected function _setDefaultConfiguration() {
		if (!is_array($this->_getModuleConfiguration()) || !sizeof($this->_getModuleConfiguration()))
			Configuration::updateValue('PM_' . self::$_module_prefix . '_CONF', Tools::jsonEncode($this->_defaultConfiguration));
		return true;
	}
	protected function _pmClear(){
		$this->_html .= '<div class="clear"></div>';
	}
	public function _retrieveFormValue($type, $fieldName, $fieldDbName = false, $obj, $defaultValue = '', $compareValue = false, $key = false) {
		if (! $fieldDbName) $fieldDbName = $fieldName;
		switch ($type) {
			case 'text' :
				if ($key)
					return htmlentities(Tools::stripslashes(Tools::getValue($fieldName, (self::_isFilledArray($obj) && isset($obj[$fieldDbName] [$key]) ? $obj[$fieldDbName] [$key] : $defaultValue))), ENT_COMPAT, 'UTF-8');
				else
					return htmlentities(Tools::stripslashes(Tools::getValue($fieldName, ( self::_isFilledArray($obj) && isset($obj[$fieldDbName]) ? $obj[$fieldDbName] : $defaultValue))), ENT_COMPAT, 'UTF-8');
			case 'textpx' :
				if ($key)
					return (int)preg_replace('#px#', '', Tools::getValue($fieldName, ( self::_isFilledArray($obj) && isset($obj[$fieldDbName]) ? $obj[$fieldDbName] [$key] : $defaultValue)));
				else
					return (int)preg_replace('#px#', '', Tools::getValue($fieldName, ( self::_isFilledArray($obj) && isset($obj[$fieldDbName]) ? $obj[$fieldDbName] : $defaultValue)));
			case 'select' :
				return ((Tools::getValue($fieldName, ( self::_isFilledArray($obj) && isset($obj[$fieldDbName]) ? $obj[$fieldDbName] : $defaultValue)) == $compareValue) ? ' selected="selected"' : '');
			case 'radio' :
			case 'checkbox' :
				if( isset($obj[$fieldName]) && is_array($obj[$fieldName]) && sizeof($obj[$fieldName]) && isset($obj[$fieldDbName])  )
					return (( in_array($compareValue,$obj[$fieldName]) ) ? ' checked="checked"' : '');
				return ((Tools::getValue($fieldName, (self::_isFilledArray($obj) && isset($obj[$fieldDbName]) ? $obj[$fieldDbName] : $defaultValue)) == $compareValue) ? ' checked="checked"' : '');
		}
	}
	private function _parseOptions($defaultOptions = array(), $options = array()) {
		if (self::_isFilledArray($options))
			$options = array_change_key_case($options, CASE_LOWER);
		if (isset($options['tips']) && !empty($options['tips']))
			$options['tips'] = htmlentities($options['tips'], ENT_QUOTES, 'UTF-8');
		if (self::_isFilledArray($defaultOptions)) {
			$defaultOptions = array_change_key_case($defaultOptions, CASE_LOWER);
			foreach (array_keys($defaultOptions) as $option_name)
				if (!isset($options[$option_name])) $options[$option_name] = $defaultOptions[$option_name];
		}
		return $options;
	}
	protected function _displaySubmit($value, $name) {
		$this->_pmClear();
		$this->_html .= '<center><input type="submit" value="' . $value . '" name="' . $name . '" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" /></center><br />';
	}
	protected function _startForm($configOptions) {
		$defaultOptions = array(
			'action' => false,
			'target' => 'dialogIframePostForm'
		);
		$configOptions = $this->_parseOptions($defaultOptions, $configOptions);
		$this->_base_config_url = ((version_compare(_PS_VERSION_, '1.5.0.0', '<')) ? $currentIndex : $_SERVER['SCRIPT_NAME'].(($controller = Tools::getValue('controller')) ? '?controller='.$controller: '')) . '&configure=' . $this->name . '&token=' . Tools::getValue('token');
		$this->_html .= '<form action="' . ($configOptions['action'] ? $configOptions['action'] : $this->_base_config_url) . '" method="post" id="' . $configOptions['id'] . '" target="' . $configOptions['target'] . '">';
	}
	protected function _endForm($configOptions) {
		$defaultOptions = array('id' => NULL);
		$configOptions = $this->_parseOptions($defaultOptions, $configOptions);
		$this->_html .= '</form>';
	}
	protected function _displayInputText($configOptions) {
		$defaultOptions = array(
			'type' => 'text',
			'size' => '150px',
			'defaultvalue' => false,
			'min' => false,
			'max' => false,
			'maxlength' => false,
			'onkeyup' => false,
			'onchange' => false,
			'required' => false,
			'tips' => false
		);
		$configOptions = $this->_parseOptions($defaultOptions, $configOptions);
		$this->_html .= '<label>' . $configOptions['label'] . '</label>
		    <div class="margin-form">
		      <input style="width:' . $configOptions['size'] . '" type="'. $configOptions['type'] .'" name="' . $configOptions['key'] . '" id="' . $configOptions['key'] . '" value="' . $this->_retrieveFormValue('text', $configOptions['key'], false, $configOptions['obj'], $configOptions['defaultvalue'], false, false) . '" class="ui-corner-all ui-input-pm" '.(($configOptions['required'] == true) ? 'required="required" ' : '') . ($configOptions['onkeyup'] ? ' onkeyup="' . $configOptions['onkeyup'] . '"' : '') . ($configOptions['onchange'] ? ' onchange="' . $configOptions['onchange'] . '"' : '') . (($configOptions['min'] !== false) ? 'min="'.(int)$configOptions['min'].'" ' : '').(($configOptions['max'] !== false) ? 'max="'.(int)$configOptions['max'].'" ' : '').(($configOptions['maxlength'] != false) ? 'maxlength="'.(int)$configOptions['maxlength'].'" ' : '').'/>'.((isset($configOptions['suffix']) && !empty($configOptions['suffix'])) ? '<span class="input-suffix">&nbsp;&nbsp;'.$configOptions['suffix'].'</span>' : '');
		if (isset($configOptions['tips']) && $configOptions['tips']) {
			$this->_html .= '<img title="'.$configOptions['tips'].'" id="' . $configOptions['key'] . '-tips" class="pm_tips" src="' . $this->_path . 'img/question.png" width="16px" height="16px" />';
			$this->_html .= '<script type="text/javascript">initTips("#' . $configOptions['key'] . '")</script>';
		}
		$this->_pmClear();
		$this->_html .= '</div>';
	}
}
