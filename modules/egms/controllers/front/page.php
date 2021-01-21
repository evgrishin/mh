<?php
require_once(_PS_MODULE_DIR_.'egms/classes/page.php');
class egmspageModuleFrontController extends ModuleFrontController
{

	public function initContent()
	{
		//TODO: grishin 
		// show content only
		// shop page by id
		parent::initContent();
		
		$page = egmspage::getSitePage('page');
		
		$this->context->smarty->assign(array(
			'page' => $page
		));	
		
		$this->setTemplate('page.tpl');	
	}


}


?>