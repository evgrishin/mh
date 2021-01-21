<?php
/**

 */


class egms_shop extends ObjectModel
{
	/** @var string Name */
	public $id_egms_cu;
	public $id_city;
	public $id_shop_url;
	public $veryf_yandex;
	public $veryf_google;
	public $veryf_mail;
	public $phone;
	public $address;
	public $chema;
	public $shipselfinfo;	
	public $carriers;
	public $payments;	
	public $page_index;
	public $page_contact;
	public $page_delivery;
	public $page_shipself;
	public $page_robotstxt;
	public $page_sitemap;
	public $del_pay;
	public $free_pay;
	public $dlex;
    public $visible;
	public $active;
	public $manufacturer = array();
	
	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'egms_city_url',
		'primary' => 'id_egms_cu',
		'fields' => array(
			'id_egms_cu' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'id_city' => array('type' => self::TYPE_INT),
			'id_shop_url' => array('type' => self::TYPE_INT),
			'veryf_yandex' => array('type' => self::TYPE_STRING),
			'veryf_google' => array('type' => self::TYPE_STRING),
			'veryf_mail' => array('type' => self::TYPE_STRING),
			'phone' => array('type' => self::TYPE_STRING),
			'address' => array('type' => self::TYPE_STRING),
			'chema' => array('type' => self::TYPE_HTML),
			'shipselfinfo' => array('type' => self::TYPE_STRING),	
			'carriers' => array('type' => self::TYPE_STRING),
			'payments' => array('type' => self::TYPE_STRING),
			'page_index' => array('type' => self::TYPE_INT),
			'page_contact' => array('type' => self::TYPE_INT),
			'page_delivery' => array('type' => self::TYPE_INT),
			'page_shipself' => array('type' => self::TYPE_INT),
			'page_robotstxt' => array('type' => self::TYPE_INT),
			'page_sitemap' => array('type' => self::TYPE_INT),
			'del_pay' => array('type' => self::TYPE_INT),
			'free_pay' => array('type' => self::TYPE_INT),
			'dlex' => array('type' => self::TYPE_STRING),
            'visible' => array('type' => self::TYPE_BOOL),
			'active' => array('type' => self::TYPE_BOOL),
		),
	);
	
	public function delete()
	{
		Tools::generateHtaccess();
		return false;
	}	
	
	public static function getEgmsAccess($id_manufacturer = null)
	{	
		$url_id = Shop::getUtlId();

        $cache_id = 'egms_shop::getEgmsAccess_'.$url_id.'-'.$id_manufacturer;
        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT * FROM '._DB_PREFIX_.'shop_url su
                    INNER JOIN '._DB_PREFIX_.'egms_city_url c ON c.id_shop_url = su.id_shop_url
                    INNER JOIN '._DB_PREFIX_.'egms_delivery d ON d.id_egms_cu = c.id_egms_cu
                    WHERE c.id_shop_url = '.$url_id.' 
                    AND d.deleted = 0  
                    AND su.active = 1
                    AND c.active = 1
                    AND d.active = 1';
            if ($id_manufacturer!=null)
                $sql.=' AND d.id_manufacturer = '.(int)$id_manufacturer;
            $result = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $result);
        }
        $result = Cache::retrieve($cache_id);
		return($result);
			
		//return isset($row['id_shop_url']);
	}
	
  	public static function getManufacturerByShop($type_result='array')
	{
		$rows = egms_shop::getEgmsAccess();
		foreach ($rows as $row)
		{
			$result[] = $row['id_manufacturer'];
		}
		
		if ($type_result == 'in')
			if (count($result))
				return ' IN ('.implode(', ', $result).')';
			else 
				return '0';
		
		return $result;				

	}  		
	
	public function update($null_values = false)
	{
		
		$this->getFieldsValues();
		$this->updateShopManuf();
		Tools::generateHtaccess();
		return parent::update($null_values);	
	}
	
	public function add($autodate = true, $null_values = false)
	{
		$this->getFieldsValues();
		$result = parent::add($autodate, $null_values);
		$this->updateShopManuf();
		Tools::generateHtaccess();
		return $result;
	}
	
	public function getFieldsValues()
	{
		$items = ManufacturerCore::getManufacturers();
		
		foreach ($items as $item)
		{
			if (Tools::getValue('manufacturer_'.(int)$item['id_manufacturer']))
				$this->manufacturer[] = $item['id_manufacturer'];
		}
		
		$carriers = array();
	    foreach (Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::PS_CARRIERS_ONLY, true) as $item)
        {
        	if (Tools::getValue('carriers_'.(int)$item['id_carrier']))
				$carriers[] = $item['id_carrier'];
        }
        $this->carriers = implode(',', $carriers);

        $payments = array();
        foreach (Module::getPaymentModules() as $item)
        {
        	if (Tools::getValue('payments_'.(int)$item['id_module']))
				$payments[] = $item['id_module'];
        }		
        $this->payments = implode(',', $payments);		
	}
	
	private function updateShopManuf()
	{	
		$sql = 'UPDATE '._DB_PREFIX_.'egms_delivery
				SET
				deleted = 1
				WHERE id_egms_cu='.(int)$this->id;
		$res = Db::getInstance()->execute($sql);
		
		foreach($this->manufacturer as $manufacturer)
		{
			$sql = '';
			if(!$this->deliveryExist($this->id, $manufacturer)){
				$sql = 'INSERT INTO `'._DB_PREFIX_.'egms_delivery` (`id_egms_cu`, `id_manufacturer`,active)
                            VALUES('.(int)$this->id.', '.(int)$manufacturer.', 0)';
			} else {	
				
					$sql='	UPDATE '._DB_PREFIX_.'egms_delivery
							SET
							deleted = 0
							WHERE id_egms_cu='.(int)$this->id.'
							AND id_manufacturer='.(int)$manufacturer;
			}
			$res = Db::getInstance()->execute($sql);
		}
	}
	
	private function deliveryExist($id_egms_cu, $id_manufacturer)
	{
		$sql ='SELECT id_egms_delivery, active, deleted FROM '._DB_PREFIX_.'egms_delivery d 
				WHERE d.id_egms_cu='.(int)$id_egms_cu.'
				AND id_manufacturer='.(int)$id_manufacturer;
		
		return(Db::getInstance()->getRow($sql));
	}
	
	public static function getShopUrls($id_shop=null)
	{
		$sql = 'SELECT * FROM '._DB_PREFIX_.'shop_url';
		if ($id_shop != null)
			$sql.= ' WHERE id_shop='.(int)$id_shop;
			
		$sql.= ' order by id_shop, domain';
			
		return (Db::getInstance()->executeS($sql));		
		
	}
	
	public static function getShopData()
	{
		$id_url = Shop::getUtlId();
		
		$sql ='SELECT * FROM '._DB_PREFIX_.'egms_city_url 
				WHERE id_shop_url='.(int)$id_url;
		
		return(Db::getInstance()->getRow($sql));
	}
		
}