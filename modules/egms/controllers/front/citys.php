<?php

require_once(_PS_MODULE_DIR_.'egms/classes/city.php');  

class egmscitysModuleFrontController extends ModuleFrontController
{

	public function getRegionURL($region)
	{
		$url = $this->getCityURL($region);
		if (!$url)
			return false;
		return $url;
	}
	
	public function getCityURL($id)
	{		
		if (!$row = city::getCityByShop($id))
			return false;
		
		return array(
			'url' => $row[0]['domain'],
			'city_name' => $row[0]['city_name']		
					);
	}
	
	public function initContent()
	{
	
		parent::initContent();

		$this->ajax = true;	

		$region = Tools::getValue('set_region',-1);
		
		if ($region >-1) {
			$url = $this->getRegionURL($region);
			if ($url){
				$this->context->cookie->__set('yandex_region', $region);
			}else {
				$this->context->cookie->__unset('yandex_region');
			}

			die(json_encode($url));
		}


		$citys = city::getCityByShop();
		
		
		if (Tools::getValue('json',false)!==false) {
			die(json_encode($citys));
		}	
			
		$host = Tools::getHttpHost();
		$current_page = str_replace($host,"", $_SERVER["HTTP_REFERER"]);
		$question = Tools::getValue('question');
		$region = Tools::getValue('region');
        $region_link = city::getCityByShop($region);//$this->getRegionURL($region);//"//nn.matras-house.ru";//TODO: add value
		
		$this->context->smarty->assign(array(
					'citys' => $citys,
					'host'	=> $host,
					//'subdomains' => Configuration::get('EGMS_SUBDOMAIN'),
					'question' => $question,
					'region' => $region,
                    'region_link' => $region_link[0]['url']
				));
		
		$this->smartyOutputContent($this->getTemplatePath('citys.tpl'));

	}


}


?>