<?php
/**

 */



class city extends ObjectModel
{
	/** @var string Name */
	public $id_egms_city;
	public $cityname1;
	public $cityname2;
	public $cityname3;
	public $psyname;
	public $alias;
	
	/**
	 * @see ObjectModel::$definition
	 */
	//TODO: add check fot length of fields
	public static $definition = array(
		'table' => 'egms_city',
		'primary' => 'id_egms_city',
		'fields' => array(
			'id_egms_city' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'cityname1' => array('type' => self::TYPE_STRING, 'required' => true),
			'cityname2' => array('type' => self::TYPE_STRING, 'required' => true),
			'cityname3' => array('type' => self::TYPE_STRING, 'required' => true),
			'psyname' => array('type' => self::TYPE_STRING),
			'alias' => array('type' => self::TYPE_STRING),
		),
	);
/*
	public function add($autodate = true, $null_values = false)
	{

		return parent::add($autodate, $null_values);
	}
*/
	public function delete()
	{
		$sql = 'SELECT * FROM '._DB_PREFIX_.'egms_city_url
		WHERE id_city ='.(int)$this->id_egms_city;
		
		if (!Db::getInstance()->executeS($sql))
			return(parent::delete());
		
		return false;
	}
	
	public static function getCity($id_city=null)
	{
		$sql = 'SELECT * FROM '._DB_PREFIX_.'egms_city';
		if ($id_city != null)
			$sql.= ' WHERE id_egms_city='.(int)$id_city;
		$sql .= ' ORDER BY cityname1';
			
		return (Db::getInstance()->executeS($sql));
	}  

	public static function getCityByShop($id=null)
	{
        $host = Tools::getHttpHost();

        $context = Context::getContext();
        $url = $_SERVER["HTTP_REFERER"];
        $base = $context->shop->getBaseURL();

        $subdomains = Configuration::get('EGMS_SUBDOMAIN');

		$sql = 'SELECT cu.`id_shop_url` as id, su.`domain`, cu.visible,
				c.`cityname1` as `city_name`, s.`name` as shop_name,
				REPLACE(su.`domain`, "'.".".$host.'", "") as alias, 
				concat("'.$host.'/", REPLACE(su.`domain`, "'.".".$host.'", "")) as host_dir,';

            if ($subdomains)
                $sql.='replace("'.$url.'", "'.$base.'", concat("//", su.`domain`,"/")) as url';
            else
                $sql.='replace("'.$url.'", "'.$base.'", concat("//'.$host.'/", REPLACE(su.`domain`, "'.".".$host.'", ""),"/")) as url';

		    $sql.=' FROM '._DB_PREFIX_.'egms_city c
				INNER JOIN '._DB_PREFIX_.'egms_city_url cu ON cu.id_city = c.id_egms_city
				INNER JOIN '._DB_PREFIX_.'shop_url su ON cu.id_shop_url = su.id_shop_url
				INNER JOIN `'._DB_PREFIX_.'shop` s ON s.`id_shop`= su.`id_shop`
				WHERE cu.active=1
				and	su.active=1 
				and cu.visible = 1 ';
				if ($id != null)
					$sql .= ' and cu.`id_shop_url` = '.$id;
				$sql .=' AND su.id_shop IN ('.implode(', ', Shop::getContextListShopID()).')
				ORDER BY c.cityname1';
			
		return (Db::getInstance()->executeS($sql));
	}	
	
}