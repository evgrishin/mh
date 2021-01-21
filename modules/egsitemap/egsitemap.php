<?php

if (!defined('_PS_VERSION_'))
  exit;
  
class egsitemap extends Module
{

  public function __construct()
  {
    $this->name = 'egsitemap';
    $this->tab = 'front_office_features';
    $this->version = '0.1.1';
    $this->author = 'Evgeny Grishin';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
    $this->bootstrap = true;
    
    parent::__construct();
 
    $this->displayName = $this->l('Sitemap xml');
    $this->description = $this->l('Generation of sitemap.xml file.');
 
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
  	 
  }
  
  public function getContent()
  {
  	//TODO: action
  	
  	$output = null;
  	$output .= '<a href="'.$this->context->link->getModuleLink('egsitemap', 'sitemap').'">Sitemap</a>';
  	$output.=$this->displayForm();
  	/*

		* карта с основными категрориями глубина 2
			* Плавующая дата главной страницы EGSITEMAP_MAIN_FDAT 
			* периуд обновления EGSITEMAP_MAIN_PERIOD
			* приоритет основных разделов EGSITEMAP_MAIN_PRIOR

		* карта с основными категрориями глубина > 2 
			* периуд обновления EGSITEMAP_CAT_PERIOD
			* приоритет основных разделов EGSITEMAP_CAT_PRIOR
			 
		* карта с товарами > 2 
			* периуд обновления товаров EGSITEMAP_MAP_PERIOD
			* приоритет товаров EGSITEMAP_MAP_PRIOR	

		* отдельна группа настроек
			* список страниц в EGSITEMAP_INF разделенных запятыми
			* дата обновления разделов EGSITEMAP_INF_DATI
			* периуд обновления EGSITEMAP_INF_PERIOD
			* приоритет EGSITEMAP_INF_PRIOR
  	 */
  	return $output;
  }
  
  public function displayForm()
  {
  	$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
  	
  				$fields_form1[0] = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Dynamic'),
						'name' => 'EGSITEMAP_MAIN_FDAT',
						'desc' => $this->l('Activate dynamic (animated) mode for category sublevels.'),
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->l('Enabled')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->l('Disabled')
									)
								),
					)
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			)
		);	
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		//$helper->submit_action = 'submitBlockCategories';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
    	$helper->tpl_vars = array(
			'fields_value' => array('EGSITEMAP_MAIN_FDAT' => (bool)Configuration::get('EGSITEMAP_MAIN_FDAT')),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
    	
    	return $helper->generateForm($fields_form1);
  }
  
	public function install($keep = true)
	{
			
	  if (!parent::install() ||
		!Configuration::updateValue('EGSITEMAP_MAP_PERIOD','weekly')||
		!Configuration::updateValue('EGSITEMAP_MAP_PRIOR', 0.6)||
		!Configuration::updateValue('EGSITEMAP_CAT_PERIOD', 'weekly')||
		!Configuration::updateValue('EGSITEMAP_CAT_PRIOR', '0.8')||		
		!Configuration::updateValue('EGSITEMAP_MAIN_PERIOD', 'daily')||
		!Configuration::updateValue('EGSITEMAP_MAIN_PRIOR', '1')||		
		!Configuration::updateValue('EGSITEMAP_MAIN_FDAT', true)||
		!Configuration::updateValue('EGSITEMAP_INF_DATI', '')||
		!Configuration::updateValue('EGSITEMAP_INF_PERIOD', 'weekly')||
		!Configuration::updateValue('EGSITEMAP_INF_PRIOR', '0.5')||
		!Configuration::updateValue('EGSITEMAP_INF', 'deliverycondition,content/6-sertifikaty,
			shipself,content/7-garantiya-na-tovary,
			content/4-about-us,1_ormatek,
			2_rajton,3_verda')
		)
	    return false;
	  return true;
	}  

	public function uninstall($keep = true)
	{
	  if (!parent::uninstall() || 
			!Configuration::deleteByName('EGSITEMAP_MAP_PERIOD') ||
			!Configuration::deleteByName('EGSITEMAP_MAP_PRIOR') ||	  
			!Configuration::deleteByName('EGSITEMAP_CAT_PERIOD') ||
			!Configuration::deleteByName('EGSITEMAP_CAT_PRIOR') ||
			!Configuration::deleteByName('EGSITEMAP_MAIN_PERIOD') ||
			!Configuration::deleteByName('EGSITEMAP_MAIN_FDAT') ||
			!Configuration::deleteByName('EGSITEMAP_MAIN_PRIOR') ||
			!Configuration::deleteByName('EGSITEMAP_INF_DATI') ||
			!Configuration::deleteByName('EGSITEMAP_INF_PERIOD') ||
			!Configuration::deleteByName('EGSITEMAP_INF_PRIOR') ||
			!Configuration::deleteByName('EGSITEMAP_INF')
			)
	    return false;
	  return true;
	}  
  
}
?>