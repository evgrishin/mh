<?php
if (!defined('_PS_VERSION_'))
  exit;
  
  class eggtm extends Module
  {
  	private $gtmcode;
  	private $dataLayer;
  	
    public function __construct()
    {
	    $this->name = 'eggtm';
	    $this->tab = 'front_office_features';
	    $this->version = '0.1.1';
	    $this->author = 'Evgeny Grishin';
	    $this->need_instance = 0;
	    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
	    $this->bootstrap = true;
		 
	    parent::__construct();
	 
	    $this->displayName = $this->l('Google Tag Manager');
	    $this->description = $this->l('Manage your tag by youself.');
	 
	    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	    
	    $this->gtmcode = Configuration::get('EGGTM_CODE');
	    $this->dataLayer = Configuration::get('EGGTM_DATALAYER');
	 
  	}	
  	public function hookDisplayStartHeader($params)
	{
		$this->smarty->assign(array(
			'gtmcode' => 	$this->gtmcode,
			'dataLayer' => 	$this->dataLayer
		));	
		
		return $this->display(__FILE__, 'displayStartHeader.tpl');
	}
	
	public function hookDisplayAfterBody($params)
	{
		$this->smarty->assign(array(
			'gtmcode' => 	$this->gtmcode,
			'dataLayer' => 	$this->dataLayer
		));	
		
		return $this->display(__FILE__, 'displayAfterBody.tpl');
	}		
	
	public function install($keep = true)
	{
			
	  if (!parent::install()|| 
		!Configuration::updateValue('EGGTM_CODE', 'your code here')||//GTM-563QCR
		!Configuration::updateValue('EGGTM_DATALAYER', 'dataLayer')||
		!$this->registerHook('displayAfterBody')||
		!$this->registerHook('displayStartHeader')
		)
	    return false;
	  return true;
	}  	
	
  	public function reset()
	{
		if (!$this->uninstall(false))
			return false;
		if (!$this->install(false))
			return false;
		return true;
	}	
	
	
	public function uninstall($keep = true)
	{
	  if (!parent::uninstall() || 
	  		!Configuration::deleteByName('EGGTM_CODE') ||
	  		!Configuration::deleteByName('EGGTM_DATALAYER') ||
	  		!$this->unregisterHook('displayAfterBody') ||
	  		!$this->unregisterHook('displayStartHeader')
			)
	    return false;
	  return true;
	}		
	
  }
  
  