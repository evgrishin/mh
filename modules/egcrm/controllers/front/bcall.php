<?php

//require_once(_PS_MODULE_DIR_.'egms/classes/city.php');

class egcrmbcallModuleFrontController extends ModuleFrontController
{



	
	public function initContent()
	{
	
		parent::initContent();
        $this->ajax = true;

        $postdata = file_get_contents("php://input");

        die($postdata);
	}


}


?>