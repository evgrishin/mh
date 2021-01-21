<?php
if (!defined('_PS_VERSION_'))
  exit;
  
  class egsearche extends Module
  {
  	
    public function __construct()
    {
	    $this->name = 'egsearche';
	    $this->tab = 'front_office_features';
	    $this->version = '0.1.1';
	    $this->author = 'Evgeny Grishin';
	    $this->need_instance = 0;
	    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
	    $this->bootstrap = true;
		 
	    parent::__construct();
	 
	    $this->displayName = $this->l('searche product module');
	    $this->description = $this->l('searche product module.');
	 
	    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	 
  	}	
  	public function hookDisplayTop($params)
	{
		
	}
	
	public function hookDisplayTopColumn($params)
	{
		
		$host = Tools::getHttpHost();
			
		$this->smarty->assign(array(
			'host' => 	$host
		));	
		
		return $this->display(__FILE__, 'displaytop.tpl');
	}
	
	public function hookDisplayFooter($params)
	{
		
		//return $this->display(__FILE__, 'iqitpopup.tpl', $this->getCacheId());
		
		//return false;
	}
	

	public function install($keep = true)
	{
			
	  if (!parent::install()|| 
		!Configuration::updateValue('EGORMATEKPROD_MANUF', 0)||
		!$this->registerHook('displayTopColumn') ||
		!$this->registerHook('displayBackOfficeHeader')
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
	
	}		
	
	public function uninstall($keep = true)
	{
	  if (!parent::uninstall() || 
	  		!Configuration::deleteByName('EGORMATEKPROD_MANUF') ||
	  		!$this->unregisterHook('displayTopColumn') ||
	  		($keep && !$this->deleteTables())
			)
	    return false;
	  return true;
	}		
	
  }
  
  