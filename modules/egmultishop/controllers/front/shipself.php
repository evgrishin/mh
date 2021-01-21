<?php

class egmultishopshipselfModuleFrontController extends ModuleFrontController
{

	public function initContent()
	{
		parent::initContent();

				$egmultishop = new egmultishop();
		
		$context = Context::getContext();
		
		$id_url = egmultishop::getUrlId($context->shop->id);
		
		//$page = $egmultishop->getMultishopPage('shipself',$id_url);
		$sub_domain = egmultishop::getSubdomain();
		$page = $egmultishop->getMultishopPageDomain('shipself',$sub_domain, false);

		if ($page =="")
		{
			$page = $egmultishop->getMultishopPage('shipself_df', null, false);
			
			$shipselfPage = $egmultishop->getShipselfCwarNearInfo($id_url);
			
			$page = $egmultishop->replaceCeoContact($page);
			
		}else{	

	 		$page = $egmultishop->replaceCeoContact($page);
		
			$map = $egmultishop->replaceCeoContact('%chema');
		}
 		
		$this->context->smarty->assign(array(
			'page' => $page,
			'map' => $map,
			'shipselfPage' => $shipselfPage
		));	
		  
		$this->setTemplate('shipself.tpl');
		

	}


}

?>