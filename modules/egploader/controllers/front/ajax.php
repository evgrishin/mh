<?php

require_once(_PS_MODULE_DIR_.'egploader/classes/ploader.php');

class egploaderajaxModuleFrontController extends ModuleFrontController
{

	
	public function initContent()
	{
	
		//parent::initContent();

		$this->ajax = true;

        $ploader = new ploader();

        $id_load = Tools::getValue('id_load');

        die($ploader->create_cache($id_load));

	}


}

