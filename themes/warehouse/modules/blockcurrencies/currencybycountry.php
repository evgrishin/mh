<?php
/**
 * currencyByCountry module
 *
 * @category Prestashop
 * @category Module
 * @author Samdha <contact@samdha.net>
 * @copyright Samdha
 * @license commercial license see license.txt
 * @version 2.0.1.0
 * @author logo.png http://www.webdesignerdepot.com/
 * @author logo.png http://www.kde-look.org/usermanager/search.php?username=InFeRnODeMoN
 * @author logo.gif http://www.famfamfam.com/lab/icons/silk/
**/

if (!defined('_PS_VERSION_'))
	exit;

if (!class_exists('CurrencyByCountry', false))
{
	if (!defined('_PS_MODULE_DIR_')) /* PS 1.0 */
		define('_PS_MODULE_DIR_', _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR);
	if (!defined('_PS_ADMIN_IMG_')) /* PS 1.0 */
		define('_PS_ADMIN_IMG_', _PS_IMG_.'admin/');
	if (!defined('_PS_USE_SQL_SLAVE_')) /* PS 1.3 */
		define('_PS_USE_SQL_SLAVE_', false);

	/**
	 * Autoloader for this module classes
	 */
	function currencyByCountryAutoload($class_name)
	{
		$module_name = 'currencybycountry';
		$class_name = ltrim($class_name, '\\');
		$file_name  = '';
		$namespace = '';
		if ($last_ns_post = strrpos($class_name, '\\'))
		{
			$namespace = Tools::substr($class_name, 0, $last_ns_post);
			$class_name = Tools::substr($class_name, $last_ns_post + 1);
			$file_name  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
		}
		$file_name .= str_replace('_', DIRECTORY_SEPARATOR, $class_name).'.php';
		$file_name = _PS_MODULE_DIR_.$module_name.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.$file_name;
		if (file_exists($file_name))
			return require_once($file_name);
		elseif (version_compare(_PS_VERSION_, '1.4.0.0', '>=') && function_exists('__autoload'))
			return __autoload($class_name);
		elseif (version_compare(_PS_VERSION_, '1.4.0.0', '<') && is_readable(_PS_ROOT_DIR_.'/classes/'.$class_name.'.php'))
			require_once _PS_ROOT_DIR_.'/classes/'.$class_name.'.php';
	}
	spl_autoload_register('currencyByCountryAutoload');

	class CurrencyByCountry extends Samdha_Module_Module
	{
		public $short_name = 'currencybycountry';

		public function __construct()
		{
			$this->name = 'currencybycountry';
			$this->tab = version_compare(_PS_VERSION_, '1.4.0.0', '<')?'Tools':'pricing_promotion';
			$this->version = '2.0.1.0';
			$this->module_key = '7cd12f3b00aabc17d7d4113dadb7d58c';

			/* Validator compatibility *//*
			$this->author = 'Samdha';
			if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
				require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
			if (function_exist(function_exists('curl_init'))
				require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
			*/
			parent::__construct();

			$this->page = basename(__FILE__, '.php');
			$this->displayName = $this->l('Currency by Country');
			$this->description = $this->l('Allow you to force a currency for a country.');

			$this->tools = new Samdha_CurrencyByCountry_Tools($this);
		}

		public function install()
		{
			if (!$this->samdha_tools->executeSQLFile(self::INSTALL_SQL_FILE)
				|| !parent::install())
				return false;

			if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
			{
				if (!$this->registerHook('header'))
					return false;
			} elseif (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
			{
				if (!$this->registerHook('header')
					|| !$this->registerHook('beforeCarrier'))
					return false;
			} else {
				if (!$this->registerHook('displayHeader')
					|| !$this->registerHook('displayMobileHeader')
					|| !$this->registerHook('displayBeforeCarrier'))
					return false;
			}

			return true;
		}

		public function uninstall()
		{
			return ($this->samdha_tools->executeSQLFile(self::UNINSTALL_SQL_FILE)
					&& parent::uninstall());
		}

		public function postUpdateModule()
		{
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				$this->registerHook('actionObjectCartUpdateBefore');
				$this->registerHook('displayBeforeCarrier');
			}
			elseif (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
			{
				$this->registerHook('cart');
				$this->registerHook('beforeCarrier');
			}
		}

		public function displayForm($token, $big = true, $space = false)
		{
			$cookie = $this->context->cookie;
			$smarty = $this->context->smarty;
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				$currentIndex = AdminController::$currentIndex;
			else
				global $currentIndex;

			$currencies = array();
			$tmp = Currency::getCurrencies();
			foreach ($tmp as $currency)
				$currencies[$currency['id_currency']] = $currency['name'];

			$countries = Country::getCountries($cookie->id_lang);
			$id_currency_default = (int)Configuration::get('PS_CURRENCY_DEFAULT');
			$countries_currencies = $this->tools->getCountriesCurrencies();
			// if a countrie was added since the last save
			foreach ($countries as $country)
				if (!isset($countries_currencies[$country['id_country']]))
					$countries_currencies[$country['id_country']] = $id_currency_default;

			$smarty->assign(
				array(
					'about_form'        => parent::displayForm($token, false),
					'module'            => $this,
					'module_config'     => $this->config->getAsArray(),
					'module_short_name' => $this->short_name,
					'module_url'        => $currentIndex.'&configure='.urlencode($this->name).'&module_name='.urlencode($this->name).'&token='.$token,
					'module_path'       => $this->samdha_tools->getHttpHost(true).$this->_path,
					'module_directory'  => dirname(__FILE__),
					'active_tab'        => Tools::getValue('active_tab'),
					'support_url'       => $this->licence->licence_number?$this->licence->getSupportURL():$this->licence->getLicenceURL(),
					'version_15'        => version_compare(_PS_VERSION_, '1.5.0.0', '>='),
					'version_14'        => version_compare(_PS_VERSION_, '1.4.0.0', '>='),
					'module_version'    => $this->version,

					'currencies'        => $currencies,
					'countries'         => $countries,
					'countries_currencies' => $countries_currencies,
				)
			);
			// Display Form

			$output = $this->display(__FILE__, (version_compare(_PS_VERSION_, '1.5.0.0', '<')?'/views/templates/hook/':'').'admin.tpl');
			return $output;
		}

		/**
		 * update countries/currencies associations
		 *
		 * @return void;
		 */
		public function postProcess($token)
		{
			if (Tools::isSubmit('saveSettings'))
				$this->tools->saveCountriesCurrencies(Tools::getValue('country'));

			if ($this->config->_use_IP && !ini_get('allow_url_fopen'))
				$this->errors[] = $this->l('allow_url_fopen must be enabled to guess country from IP.');

			return parent::postProcess($token);
		}

		/**
		* set default config
		**/
		public function getDefaultConfig()
		{
			return array(
				'_address' => 'id_address_delivery',
				'_use_IP' => true,
			);
			return $result;
		}

		public function hookdisplayMobileHeader($params)
		{
			return $this->hookHeader($params);
		}

		public function hookdisplayHeader($params)
		{
			return $this->hookHeader($params);
		}

		public function hookHeader($params)
		{
			$cookie = $this->context->cookie;
			$currency = $this->context->currency;
			$cart = $this->context->cart;
			$smarty = $this->context->smarty;

			if ($currency instanceof Currency)
			{
				$id_currency = $this->tools->getCurrentCurrency($cart);
				if ($id_currency
					&& ($currency->id != $id_currency))
				{
					$tmp_currency = new Currency($id_currency);
					if (Validate::isLoadedObject($tmp_currency)
						&& !$tmp_currency->deleted)
					{
						$currency = $tmp_currency;
						$cookie->id_currency = (int)$currency->id;
						$cart->id_currency = (int)$currency->id;
						$cart->update();
						if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
							$smarty->ps_currency = $currency;

						$smarty->assign('id_currency_cookie', (int)$currency->id);
						if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
							Tools::redirectLink($_SERVER['REQUEST_URI']);
					}
				}
			}
		}

		public function hookBeforeCarrier($params)
		{
			return $this->hookDisplayBeforeCarrier($params);
		}

		public function hookDisplayBeforeCarrier($params)
		{
			$cart = $this->context->cart;
			$currency = $this->context->currency;
			if ($currency instanceof Currency)
			{
				$id_currency = $this->tools->getCurrentCurrency($cart);

				if ($id_currency
					&& (
						$currency->id != $id_currency
						|| $cart->id_currency != $id_currency))
				{
					$tmp_currency = new Currency($id_currency);
					if (Validate::isLoadedObject($tmp_currency)
						&& !$tmp_currency->deleted)
					{
						$currency = $tmp_currency;
						if ($cart->id_currency != $id_currency) {
							$cart->id_currency = $id_currency;
							$cart->update();
						}
					}
				}
			}

			$this->context->smarty->assign(array(
				'currencySign' => $currency->sign,
				'currencyRate' => $currency->conversion_rate,
				'currencyFormat' => $currency->format,
				'currencyBlank' => $currency->blank,
			));
			return $this->display(__FILE__, (version_compare(_PS_VERSION_, '1.5.0.0', '<')?'views/templates/hook/':'').'beforecarrier.tpl');
		}

	}
}
