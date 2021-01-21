<?php
if (!defined('_PS_VERSION_'))
  exit;
  
  class egamerica extends Module
  {
  	
    public function __construct()
    {
	    $this->name = 'egamerica';
	    $this->tab = 'front_office_features';
	    $this->version = '0.1.1';
	    $this->author = 'Evgeny Grishin';
	    $this->need_instance = 0;
	    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
	    $this->bootstrap = true;
		 
	    parent::__construct();
	 
	    $this->displayName = $this->l('marketing module');
	    $this->description = $this->l('America marketing module.');
	 
	    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	 
  	}	
  	
	public function hookHeader($params)
	{		
		$this->context->controller->addCSS($this->_path.'views/css/egmultishop.css', 'all');
	}
		  	
  	public function hookDisplayTop($params)
	{
		return $this->display(__FILE__, 'mess.tpl', $this->getCacheId());
	}

  	public function hookDisplayFooter($params)
	{
		
		return $this->display(__FILE__, 'popup.tpl', $this->getCacheId());
		
		//return false;
	}	

	public function install($keep = true)
	{
			
	  if (!parent::install()|| 
		//!Configuration::updateValue('EGAMERICA_', 0)||
		!$this->registerHook('displayBackOfficeHeader')||
		!$this->registerHook('displayTop')||
		!$this->registerHook('displayFooter')
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
	
	public function deleteTables()
	{
		return true;
	}
	
	public function uninstall($keep = true)
	{
	  if (!parent::uninstall() || 
	  		//!Configuration::deleteByName('EGAMERICA_') ||
	  		!$this->unregisterHook('displayFooter')||
	  		($keep && !$this->deleteTables())
			)
	    return false;
	  return true;
	}		
	
  }
  
  