<?php

class egmultishopdeliveryModuleFrontController extends ModuleFrontController
{

	public function initContent()
	{
		parent::initContent();

		$egmultishop = new egmultishop();
		
		$context = Context::getContext();
		
		$id_url = egmultishop::getUrlId($context->shop->id);
 
		//$page = $egmultishop->getMultishopPage('deliverycondition',$id_url);
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
				 
	}


}


?>