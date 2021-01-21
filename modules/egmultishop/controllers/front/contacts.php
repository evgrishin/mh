<?php

class egmultishopcontactsModuleFrontController extends ModuleFrontController
{

	public function initContent()
	{
		parent::initContent();

		$egmultishop = new egmultishop();
		
		$context = Context::getContext();
		
		$id_url = egmultishop::getUrlId($context->shop->id);
		
		$page = $egmultishop->getMultishopPage('contacts', null, false);
		
		$page = $egmultishop->replaceCeoContact($page);
		
		$add1 = $egmultishop->replaceCeoContact('%addr1');
		
		$add2 = $egmultishop->replaceCeoContact('%chema');
 
		$this->context->smarty->assign(array(
			'page' => $page,
			'add1' => $add1,
			'add2' => $add2
		));	
		
		$this->setTemplate('contacts.tpl');
				
	}

}


?>