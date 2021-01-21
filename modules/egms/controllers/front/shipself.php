<?php
require_once(_PS_MODULE_DIR_.'egms/classes/page.php');
class egmsshipselfModuleFrontController extends ModuleFrontController
{

	public function initContent()
	{
		parent::initContent();
		
		$page = egmspage::getSitePage('shipself');
		
		$this->context->smarty->assign(array(
			'page' => $page
		));	
		
		$this->setTemplate('page.tpl');	

	}


}

?>