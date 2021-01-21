<?php


class AdminEGMSAjaxController extends ModuleAdminController
{
	

	public function __construct()
	{
		$this->bootstrap = true;
		$this->display = 'view';
		$this->meta_title = $this->l('Your Merchant Expertise');
		
		parent::__construct();
        if (!$this->module->active)
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
	}


	
	public function ajaxProcessgetUrl()
	{
        $id_url = Tools::getValue('id_url');

        $ch = curl_init($id_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $output = curl_exec($ch);
        curl_close($ch);
		die ($output);
	}
	
}
