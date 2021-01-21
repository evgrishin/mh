<?php

if (!defined('_PS_VERSION_'))
  exit;
  
class egrobotstxt extends Module
{

  public function __construct()
  {
    $this->name = 'egrobotstxt';
    $this->tab = 'front_office_features';
    $this->version = '0.1.1';
    $this->author = 'Evgeny Grishin';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
    $this->bootstrap = true;

    //$this->registerHook('header');
    
    parent::__construct();
 
    $this->displayName = $this->l('Robots.txt file');
    $this->description = $this->l('Generation of robots.txt file.');
 
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
 
  }


}
?>