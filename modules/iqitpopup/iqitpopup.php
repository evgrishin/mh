<?php
/**
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class Iqitpopup extends Module
{
	protected $config_form = false;

	public function __construct()
	{
		$this->name = 'iqitpopup';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'IQIT-COMMERCE.COM';
		$this->need_instance = 0;

		/**
		 * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
		 */
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Pop-up window module with newsletter subscription');
		$this->description = $this->l('Show custom popup in your prestashop store. ');

		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

		$this->config_name = 'IQITPOPUP';
		$this->defaults = array(
			'width' => 650,
			'height' => 450,
			'pages' => 1,
			'newval' => 1,
			'cookie' => 10,
			'txt_color' => '#777777',
			'bg_color' => '#ffffff',
			'newsletter' => 0,
			'bg_image' => '',
			'bg_repeat' => 0,
			'new_txt_color' => '#ffffff',
			'new_bg_color' => '#777777',
			'input_txt_color' => '#ffffff',
			'input_bg_color' => '#777777',
			'new_bg_image' => '',
			'new_bg_repeat' => 0,
			'content' => 'Content of newsletter popup',
			);
	}

	/**
	 * Don't forget to create update methods if needed:
	 * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
	 */
	public function install()
	{
		if (parent::install() &&
			$this->registerHook('header') &&
			$this->registerHook('displayFooter'))
		{

			$this->setDefaults();
			$this->generateCss();
			return true;
		}
		else return false;
	}

	public function uninstall()
	{
		foreach ($this->defaults as $default => $value)
				Configuration::deleteByName($this->config_name.'_'.$default);

		return parent::uninstall();
	}

	public function setDefaults()
	{
		foreach ($this->defaults as $default => $value)
		{
			if ($default == 'content')
			{
					$message_trads = array();
					foreach (Language::getLanguages(false) as $lang)
							$message_trads[(int)$lang['id_lang']] = '<p>The best effect you will get if you remove text and put background image</p>';
			Configuration::updateValue($this->config_name.'_'.$default, $message_trads, true);
			}
			else
			Configuration::updateValue($this->config_name.'_'.$default, $value);
		}
	}

	/**
	 * Load the configuration form
	 */
	public function getContent()
	{
		if (Tools::isSubmit('submitIqitpopupModule'))
			$this->_postProcess();
		$this->context->smarty->assign('module_dir', $this->_path);
		$output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

		return $output.$this->renderForm();
	}

	/**
	 * Create the form that will be displayed in the configuration of your module.
	 */
	protected function renderForm()
	{
		$helper = new HelperForm();

		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->module = $this;
		$helper->default_form_language = $this->context->language->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitIqitpopupModule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
			.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
		);

		return $helper->generateForm(array($this->getConfigForm()));
	}

	/**
	 * Create the structure of your form.
	 */
	protected function getConfigForm()
	{
		return array(
			'form' => array(
				'legend' => array(
				'title' => $this->l('Settings'),
				'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'type' => 'select',
						'label' => $this->l('Pages'),
						'name' => 'pages',
						'options' => array(
							'query' => array(array(
								'id_option' => 1,
								'name' => $this->l('Index only(homepage)')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('All pages')
								)
							),
							'id' => 'id_option',
							'name' => 'name'
							)
						),
					array(
						'type' => 'switch',
						'label' => $this->l('Show newsletter form'),
						'name' => 'newsletter',
						'is_bool' => true,
						'desc' => $this->l('Make shure that you have instaled blocknewsletter module. Voucher code you can set in blocknewsletter module'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->l('Disabled')
							)
						),
					),
					array(
						'type' => 'text',
						'label' => $this->l('Width'),
						'name' => 'width',
						'suffix' => 'px',
						'desc' => $this->l('Popup window width. Below this width module will be hidden.'),
						'size' => 20,
						),
					array(
						'type' => 'text',
						'label' => $this->l('Height of main content'),
						'name' => 'height',
						'suffix' => 'px',
						'desc' => $this->l('Popup window height. Below this height module will be hidden.'),
						'size' => 20,
						),
					array(
						'type' => 'color',
						'label' => $this->l('Content background color'),
						'name' => 'bg_color',
						'size' => 30,
					),
					array(
						'type' => 'background_image',
						'label' => $this->l('Content  background image'),
						'name' => 'bg_image',
						'size' => 30,
					),
					array(
						'type' => 'select',
						'label' => $this->l('Content background repeat'),
						'name' => 'bg_repeat',
						'options' => array(
							'query' => array(array(
								'id_option' => 3,
								'name' => $this->l('Repeat XY')
								),
							array(
								'id_option' => 2,
								'name' => $this->l('Repeat X')
								),
							array(
								'id_option' => 1,
								'name' => $this->l('Repeat Y')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('No repeat')
								)
							),
							'id' => 'id_option',
							'name' => 'name'
							)
					),
					array(
						'type' => 'color',
						'label' => $this->l('Content text color'),
						'name' => 'txt_color',
						'desc' => $this->l('Default text color. Can be modified in wysiwyg editor too.'),
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Newsletter background color'),
						'name' => 'new_bg_color',
						'size' => 30,
					),
					array(
						'type' => 'background_image',
						'label' => $this->l('Newsletter background image'),
						'name' => 'new_bg_image',
						'size' => 30,
					),
					array(
						'type' => 'select',
						'label' => $this->l('Newsletter background repeat'),
						'name' => 'new_bg_repeat',
						'options' => array(
							'query' => array(array(
								'id_option' => 3,
								'name' => $this->l('Repeat XY')
								),
							array(
								'id_option' => 2,
								'name' => $this->l('Repeat X')
								),
							array(
								'id_option' => 1,
								'name' => $this->l('Repeat Y')
								),
							array(
								'id_option' => 0,
								'name' => $this->l('No repeat')
								)
							),
							'id' => 'id_option',
							'name' => 'name'
							)
					),
					array(
						'type' => 'color',
						'label' => $this->l('Newsletter text color'),
						'name' => 'new_txt_color',
						'desc' => $this->l('Default text color. Text can be modified by modules translation'),
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Newsletter input text color'),
						'name' => 'input_txt_color',
						'desc' => $this->l('Text color of input field'),
						'size' => 30,
					),
					array(
						'type' => 'color',
						'label' => $this->l('Newsletter input bg color'),
						'name' => 'input_bg_color',
						'desc' => $this->l('Background color on input field'),
						'size' => 30,
					),
					array(
						'type' => 'text',
						'label' => $this->l('Cookie time'),
						'name' => 'cookie',
						'suffix' => 'days',
						'desc' => $this->l('Time in days of storing cookie. After that time windows will be showed again'),
						'size' => 20,
						),
					array(
						'type' => 'textarea',
						'label' => $this->l('Content of popup module'),
						'name' => 'content',
						'autoload_rte' => true,
						'lang' => true,
						'cols' => 60,
						'rows' => 30
						),
						array(
						'type' => 'switch',
						'label' => $this->l('Generate new cookie variable'),
						'name' => 'newval',
						'is_bool' => true,
						'desc' => $this->l('If enabled cookie variable name will be generated, so after modification popup will be showed to users even if old cookie life time do not end yet'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => true,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => false,
								'label' => $this->l('Disabled')
							)
						),
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				),
			),
		);
	}

	/**
	 * Set values for the inputs.
	 */
	protected function getConfigFormValues()
	{
		$var = array();

		foreach ($this->defaults as $default => $value)
		{

		if ($default == 'content')
		{
			foreach (Language::getLanguages(false) as $lang)
			$var[$default][(int)$lang['id_lang']] = Configuration::get($this->config_name.'_'.$default, (int)$lang['id_lang']);
		}
		elseif ($default == 'newval')
			$var[$default] = 1;
		else
			$var[$default] = Configuration::get($this->config_name.'_'.$default);
		}
		return $var;

	}

	/**
	 * Save form data.
	 */
	protected function _postProcess()
	{
		foreach ($this->defaults as $default => $value)
		{
				if ($default == 'content')
				{
					$message_trads = array();
					foreach ($_POST as $key => $value)
						if (preg_match('/content_/i', $key))
						{
							$id_lang = preg_split('/content_/i', $key);
							$message_trads[(int)$id_lang[1]] = $value;
						}
						Configuration::updateValue($this->config_name.'_'.$default, $message_trads, true);
				}
				elseif ($default == 'newval')
				{
					if (Tools::getValue($default))
					Configuration::updateValue($this->config_name.'_'.$default, mt_rand(1, 40000));
				}
				else
				Configuration::updateValue($this->config_name.'_'.$default, (Tools::getValue($default)));
		}
		$this->_clearCache('iqitpopup.tpl');
		$this->generateCss();
	}

	public function generateCss()
	{
		$css = '';
		$add_val = 30;
		if (Configuration::get($this->config_name.'_newsletter'))
			$add_val = 95;
		$css .= '
		@media (max-width: '.((int)Configuration::get($this->config_name.'_width') + 40).'px) {#iqitpopup, #iqitpopup-overlay{ display: none !important;}} 
		@media (max-height: '.((int)Configuration::get($this->config_name.'_height') + 80).'px) {#iqitpopup, #iqitpopup-overlay{ display: none !important;}} 
		#iqitpopup{ width: '.(int)Configuration::get($this->config_name.'_width').'px; height: '.((int)Configuration::get($this->config_name.'_height') + $add_val).'px; }

		#iqitpopup .iqitpopup-content{ color: '.Configuration::get($this->config_name.'_txt_color').'; background-color: '.(Configuration::get($this->config_name.'_bg_color')).'; 
		background-image: url('.Configuration::get($this->config_name.'_bg_image').') !important;
		background-repeat: '.$this->convertBgRepeat(Configuration::get($this->config_name.'_bg_repeat')).' !important;
		}

		#iqitpopup .iqitpopup-newsletter-form{ color: '.Configuration::get($this->config_name.'_new_txt_color').'; background-color: '.(Configuration::get($this->config_name.'_new_bg_color')).'; 
		background-image: url('.Configuration::get($this->config_name.'_new_bg_image').') !important;
		background-repeat: '.$this->convertBgRepeat(Configuration::get($this->config_name.'_new_bg_repeat')).' !important;
		}

		#iqitpopup .iqitpopup-newsletter-form .newsletter-input{
		color: '.Configuration::get($this->config_name.'_input_txt_color').'; background-color: '.(Configuration::get($this->config_name.'_input_bg_color')).'; 
		}

		#iqitpopup .iqitpopup-newsletter-form .newsletter-inputl:-moz-placeholder {
  color: '.Configuration::get($this->config_name.'_input_txt_color').' !important;}
#iqitpopup .iqitpopup-newsletter-form .newsletter-input::-moz-placeholder {
  color: '.Configuration::get($this->config_name.'_input_txt_color').' !important;}
#iqitpopup .iqitpopup-newsletter-form .newsletter-input:-ms-input-placeholder {
  color: '.Configuration::get($this->config_name.'_input_txt_color').' !important; }
#iqitpopup .iqitpopup-newsletter-form .newsletter-input::-webkit-input-placeholder {
  color: '.Configuration::get($this->config_name.'_input_txt_color').' !important; }

		#iqitpopup .iqitpopup-content{
		height: '.((int)Configuration::get($this->config_name.'_height')).'px;
		}
		';
		$my_file = "";
		if (Shop::getContext() == Shop::CONTEXT_GROUP)
			$my_file = $this->local_path.'css/iqitpopup_g_'.(int)$this->context->shop->getContextShopGroupID().'.css';
		elseif (Shop::getContext() == Shop::CONTEXT_SHOP)
			$my_file = $this->local_path.'css/iqitpopup_s_'.(int)$this->context->shop->getContextShopID().'.css';
		
		$fh = fopen($my_file, 'w') or die("can't open file");
		fwrite($fh, $css);
		fclose($fh);
	}

	public function convertBgRepeat($value)
	{
			switch ($value)
			{
				case 3 :
					$repeat_option = 'repeat';
					break;
				case 2 :
					$repeat_option = 'repeat-x';
					break;
				case 1 :
					$repeat_option = 'repeat-y';
					break;
				default :
					$repeat_option = 'no-repeat';
			}
			return $repeat_option;
	}

	/**
	 * Add the CSS & JavaScript files you want to be added on the FO.
	 */
	public function hookHeader()
	{
		if (Configuration::get($this->config_name.'_pages') && $this->context->controller->php_self != 'index')
			return;
		$this->context->controller->addJS($this->_path.'/js/front.js');
		$this->context->controller->addCSS($this->_path.'/css/front.css');
		if (Shop::getContext() == Shop::CONTEXT_GROUP)
		$this->context->controller->addCSS(($this->_path).'css/iqitpopup_g_'.(int)$this->context->shop->getContextShopGroupID().'.css', 'all');
		elseif (Shop::getContext() == Shop::CONTEXT_SHOP)
		$this->context->controller->addCSS(($this->_path).'css/iqitpopup_s_'.(int)$this->context->shop->getContextShopID().'.css', 'all');
	}

	public function hookDisplayFooter($params)
	{
		if (Configuration::get($this->config_name.'_pages') && $this->context->controller->php_self != 'index')
			return;

		if (!isset($_COOKIE['iqitpopup_'.(int)Configuration::get($this->config_name.'_newval')]))
		{

		if (!$this->isCached('iqitpopup.tpl', $this->getCacheId()))
		{

			$this->smarty->assign(
				array(
					'txt' => Configuration::get($this->config_name.'_content', $this->context->language->id),
					'newsletter' => Configuration::get($this->config_name.'_newsletter'),
				)
			);

		}
		Media::addJsDef(array('iqitpopup_time' => (int)Configuration::get($this->config_name.'_cookie')));
		Media::addJsDef(array('iqitpopup_name' => 'iqitpopup_'.(int)Configuration::get($this->config_name.'_newval')));
		return $this->display(__FILE__, 'iqitpopup.tpl', $this->getCacheId());
		}
		return false;
	}
}
