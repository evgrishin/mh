<?php

class egmarketingcprModuleFrontController extends ModuleFrontController
{

	public function initContent()
	{
		parent::initContent();
		//if (Tools::getValue('utm_medium')=='cpr')
			
/*
		$sub_domain = egmultishop::getSubdomain();
		$page = $egmultishop->getMultishopPageDomain('deliverycondition',$sub_domain, false);
		if ($page =="")
		{
			$page = $egmultishop->getMultishopPage('deliverycondition_df', null, false);
		}	

		$page = $egmultishop->replaceCeoContact($page);
		
		$this->context->smarty->assign(array(
			'page' => $page
		));	
		
		$this->setTemplate('delivery.tpl');
	*/			 
	}


}


?>