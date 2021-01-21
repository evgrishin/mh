<?php

class egmsrobotstxtModuleFrontController extends ModuleFrontController
{

	public function initContent()
	{

		parent::initContent();

		$this->ajax = true;	
		
		$page = egmspage::getSitePage('robotstxt');
		
		die($page);	
		
	}


}


?>