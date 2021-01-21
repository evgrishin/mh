<?php
/**
*  @author Evgeny Grishin <e.v.grishin@yandex.ru>
*  @copyright  2015 Evgeny grishin
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
class egcrm extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';

    const ADMIN_TAB = 'AdminEGCRM';
    private $html = '';

    /**
     * 1) translations
     * 2) tab for maintanence
     * Enter description here ...
     */
    public function __construct()
    {
        $this->name = 'egcrm';
        $this->tab = 'front_office_features';
        $this->version = '0.1.1';
        $this->author = 'Evgeny Grishin';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;
        $this->html = '';

        parent::__construct();

        $this->displayName = $this->l('CRM');
        $this->description = $this->l('Addon CRM.');
        $this->registerHook('displayAdminProductsExtra') ||
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->registerHook('displayAdminProductsExtra');

    }

    public function getContent()
    {

        $this->html .= $this->renderForm();
        return $this->html;

    }

    private function renderForm()
    {
        return 'hello crm';
    }

    public function getConfigFieldsValues()
    {

    }
    
  	public function installAdminTab()
	{
		$tab = new Tab();
		$tab->active = 1;
		$languages = Language::getLanguages(false);
		if (is_array($languages))
			foreach ($languages as $language)
				$tab->name[$language['id_lang']] = 'CRM panel';
		$tab->class_name = self::ADMIN_TAB;
		$tab->module = $this->name;
		$tab->id_parent = 10;
		return (bool)$tab->add();
	}    
	
	public static function uninstallAdminTab()
	{
		$idTab = Tab::getIdFromClassName(self::ADMIN_TAB);
		if ($idTab != 0)
		{
			$tab = new Tab($idTab);
			$tab->delete();
			return true;
		}
		return false;
	}	



    public function install($keep = true)
    {

        if (!parent::install() ||
            !$this->registerHook('displayAdminProductsExtra') ||
        !$this->installAdminTab()
        )
        return false;
      return true;
    }

    public function uninstall($keep = true)
    {
      if (!parent::uninstall() || ($keep && !$this->deleteTables()) ||
      		!$this->uninstallAdminTab())
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
}
