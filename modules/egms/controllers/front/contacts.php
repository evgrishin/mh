<?php
require_once(_PS_MODULE_DIR_.'egms/classes/page.php');
class egmscontactsModuleFrontController extends ModuleFrontController
{

	public function initContent()
	{
		
		parent::initContent();

		$page = egmspage::getSitePage('contact');
		
		$this->context->smarty->assign(array(
			'page' => $page
		));	
		
		$this->setTemplate('page.tpl');
	}

}


?>