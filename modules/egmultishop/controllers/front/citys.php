<?php


class egmultishopcitysModuleFrontController extends ModuleFrontController
{

	public function getRegionURL($region)
	{
		$url = egmultishop::getCityURL($region);
		if (!$url)
			return false;
		return $url;
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
		
		
		$sql = 'SELECT su.`id_shop_url` as id, su.`domain`, 
				mu.`city_name`, s.`name` as shop_name
				FROM `'._DB_PREFIX_.'shop_url` su
				INNER JOIN `'._DB_PREFIX_.'shop` s ON
					s.`id_shop`=su.`id_shop`
				INNER JOIN `'._DB_PREFIX_.'egmultishop_url` mu ON
					mu.`id_url`=su.`id_shop_url`
				WHERE su.id_shop IN ('.implode(', ', Shop::getContextListShopID()).') 
				and su.`main`= 0
				and su.`active` = 1
				and not mu.`city_name` = \'\'
				and	mu.`active` > 0
				order by 3';

		if (!$citys = Db::getInstance()->executeS($sql))
			return false;

		if (Tools::getValue('json',false)!==false) {
			die(json_encode($citys));
		}	
			
		$host = "http://".Tools::getHttpHost();
		$current_page = str_replace($host,"", $_SERVER["HTTP_REFERER"]);
		$question = Tools::getValue('question');
		$region = Tools::getValue('region');
		
		$this->context->smarty->assign(array(
					'citys' => $citys,
					'host'	=> $host,
					'question' => $question,
					'region' => $region
				));
		
		$this->smartyOutputContent($this->getTemplatePath('citys.tpl'));

	}


}


?>