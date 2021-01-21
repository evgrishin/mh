<?php

class egrobotstxtrobotstxtModuleFrontController extends ModuleFrontController
{

	public function initContent()
	{

		parent::initContent();

		$this->ajax = true;	
		
		$this->context->smarty->assign(array(
			'hello' => 'hello'			
		));	
		
		$this->smartyOutputContent(
			$this->getTemplatePath('default.tpl')
		);
		
	}


}


?>