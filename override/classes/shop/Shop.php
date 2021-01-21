<?php


class Shop extends ShopCore

{
	public $id_shop_url;
		
	public static function isFeatureActive()
	{
		static $feature_active = null;

		/*if ($feature_active === null)
			$feature_active = (bool)Db::getInstance()->getValue('SELECT value FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE "PS_MULTISHOP_FEATURE_ACTIVE"')
				&& (Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'shop') > 1);
		*/
		$feature_active = true;
		return $feature_active;
	}

	public function getUrlsSharedCart()
	{
		if (!$this->getGroup()->share_order)
			return false;

		$query = new DbQuery();
		$query->select('domain');
		$query->from('shop_url');
				$query->where('active = 1');
		$query .= $this->addSqlRestriction(Shop::SHARE_ORDER);
		$domains = array();
		foreach (Db::getInstance()->executeS($query) as $row)
			$domains[] = $row['domain'];

		return $domains;
	}
	
	/**
	 * Find the shop from current domain / uri and get an instance of this shop
	 * if INSTALL_VERSION is defined, will return an empty shop object
	 *
	 * @return Shop
	 */

	public static function initialize()
	{
				if (!($id_shop = Tools::getValue('id_shop')) || defined('_PS_ADMIN_DIR_'))
		{
			$found_uri = '';
			$is_main_uri = false;
			$host = Tools::getHttpHost();
			$request_uri = rawurldecode($_SERVER['REQUEST_URI']);

            $cache_id = 'Shop::initialize_'.$host;


            if (!Cache::isStored($cache_id)) {
                $sql = 'SELECT s.id_shop, CONCAT(su.physical_uri, su.virtual_uri) AS uri, su.domain, su.main
					FROM ' . _DB_PREFIX_ . 'shop_url su
					INNER JOIN ' . _DB_PREFIX_ . 'shop s ON (s.id_shop = su.id_shop)
					WHERE (su.domain = \'' . pSQL($host) . '\' OR su.domain_ssl = \'' . pSQL($host) . '\')
						AND s.active = 1
						AND s.deleted = 0
						AND su.active = 1
					ORDER BY LENGTH(CONCAT(su.physical_uri, su.virtual_uri)) DESC';

                $result = Db::getInstance()->executeS($sql);
                Cache::store($cache_id, $result);
            }
            $result = Cache::retrieve($cache_id);

			$through = false;
			foreach ($result as $row)
			{
								if (preg_match('#^'.preg_quote($row['uri'], '#').'#i', $request_uri))
				{
					$through = true;
					$id_shop = $row['id_shop'];
					$found_uri = $row['uri'];
					if ($row['main'])
						$is_main_uri = true;
					break;
				}
			}
			//$id_shop = 1;

			if ($through && $id_shop && !$is_main_uri)
			{

				foreach ($result as $row)
				{
					if ($row['id_shop'] == $id_shop && $row['main'])
					{
						$request_uri = substr($request_uri, strlen($found_uri));
						$url = str_replace('/'.'/', '/', $row['domain'].$row['uri'].$request_uri);
						$redirect_type = Configuration::get('PS_CANONICAL_REDIRECT') == 2 ? '301' : '302';
						header('HTTP/1.0 '.$redirect_type.' Moved');
						header('Cache-Control: no-cache');
						header('location: '.Tools::getShopProtocol().$url);
						exit;
					}
				}
			}
		}

		$http_host = Tools::getHttpHost();
				$all_media = array();

		if ((!$id_shop && defined('_PS_ADMIN_DIR_')) || Tools::isPHPCLI() || in_array($http_host, $all_media))
		{
						if ((!$id_shop && Tools::isPHPCLI()) || defined('_PS_ADMIN_DIR_'))
				$id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');

			$shop = new Shop((int)$id_shop);
			if (!Validate::isLoadedObject($shop))
				$shop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));

			$shop->virtual_uri = '';

						if (Tools::isPHPCLI())
			{
				if (!isset($_SERVER['HTTP_HOST']) || empty($_SERVER['HTTP_HOST']))
					$_SERVER['HTTP_HOST'] = $shop->domain;
				if (!isset($_SERVER['SERVER_NAME']) || empty($_SERVER['SERVER_NAME']))
					$_SERVER['SERVER_NAME'] = $shop->domain;
				if (!isset($_SERVER['REMOTE_ADDR']) || empty($_SERVER['REMOTE_ADDR']))
					$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
			}
		}
		else
		{
			if($id_shop==3)
		        $_SERVER['HTTPS'] = 'on';
			$shop = new Shop($id_shop);
			if (!Validate::isLoadedObject($shop) || !$shop->active)
			{
								$default_shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));

								if (!Validate::isLoadedObject($default_shop))
					throw new PrestaShopException('Shop not found');

				$params = $_GET;
				unset($params['id_shop']);
				$url = $default_shop->domain;
				if (!Configuration::get('PS_REWRITING_SETTINGS'))
					$url .= $default_shop->getBaseURI().'index.php?'.http_build_query($params);
				else
				{
										if (strpos($url, 'www.') === 0 && 'www.'.$_SERVER['HTTP_HOST'] === $url || $_SERVER['HTTP_HOST'] === 'www.'.$url)
						$url .= $_SERVER['REQUEST_URI'];
					else
						$url .= $default_shop->getBaseURI();

					if (count($params))
						$url .= '?'.http_build_query($params);
				}
				$redirect_type = Configuration::get('PS_CANONICAL_REDIRECT') == 2 ? '301' : '302';
				header('HTTP/1.0 '.$redirect_type.' Moved');
				header('location: '.Tools::getShopProtocol().$url);
				exit;
			}
			elseif (defined('_PS_ADMIN_DIR_') && empty($shop->physical_uri))
			{
				$shop_default = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));
				$shop->physical_uri = $shop_default->physical_uri;
				$shop->virtual_uri = $shop_default->virtual_uri;
			}
		}

		self::$context_id_shop = $shop->id;
		self::$context_id_shop_group = $shop->id_shop_group;
		self::$context = self::CONTEXT_SHOP;

		return $shop;
	}

	public function setUrl()
	{	
		$host = Tools::getHttpHost();
		$main_domain = Tools::getMaindomain();

			if ($main_domain == $host){
					$request_uri = rawurldecode($_SERVER['REQUEST_URI']);
					$parts = explode('/',$request_uri);
					//if(is_array($parts) && $parts[1]){
						$part = $parts[1];
						$host_test = $part.'.'.$host;							
						$row = Db::getInstance()->getRow('
						SELECT su.physical_uri, \''.$part.'/'.'\' as virtual_uri, \''.$main_domain.'\' as domain, \''.$main_domain.'\' as domain_ssl, su.id_shop_url+10000 as id_shop_url, 
						true as isVirtual, \''.$part.'\' as part, \''.$part.'.'.$host.'\'  as host,
						\''.$main_domain.'\' as main_domain, su.active,t.id_theme, t.name, t.directory
						FROM '._DB_PREFIX_.'shop s
						LEFT JOIN '._DB_PREFIX_.'shop_url su ON (s.id_shop = su.id_shop)
						LEFT JOIN '._DB_PREFIX_.'theme t ON (t.id_theme = s.id_theme)
						WHERE s.active = 1 AND su.active = 1 AND s.deleted = 0 AND su.main = 0 AND su.domain = "'.$host_test.'"');
						if(!$row){
							$row = Db::getInstance()->getRow('
							SELECT su.physical_uri, su.virtual_uri, su.domain, su.domain_ssl, t.id_theme, t.name, t.directory
							, su.id_shop_url, su.active
							FROM '._DB_PREFIX_.'shop s
							LEFT JOIN '._DB_PREFIX_.'shop_url su ON (s.id_shop = su.id_shop)
							LEFT JOIN '._DB_PREFIX_.'theme t ON (t.id_theme = s.id_theme)
							WHERE s.id_shop = '.(int)$this->id.'
							AND s.active = 1 AND s.deleted = 0 AND su.domain = "'.$host.'"');
							$context = Context::getContext();		
							if (isset($context->cookie)){
								if ($context->cookie->__isset('url_id'))
									$iy = $context->cookie->__get('url_id');
								else
									$iy = $row['id_shop_url'];
								$row['id_shop_url']	= 20000 + $iy;						
							}
						}
				}else{								
				$row = Db::getInstance()->getRow('
				SELECT su.physical_uri, su.virtual_uri, su.domain, su.domain_ssl, t.id_theme, t.name, t.directory
				, su.id_shop_url, su.active
				FROM '._DB_PREFIX_.'shop s
				LEFT JOIN (select * from '._DB_PREFIX_.'shop_url sub where sub.domain = "'.$host.'") su ON (s.id_shop = su.id_shop)
				LEFT JOIN '._DB_PREFIX_.'theme t ON (t.id_theme = s.id_theme)
				WHERE s.id_shop = '.(int)$this->id.'
				AND s.active = 1 AND s.deleted = 0 ');
			}			


		$this->id_shop_url = $row['id_shop_url'];
		$this->theme_id = $row['id_theme'];
		$this->theme_name = $row['name'];
		$this->theme_directory = $row['directory'];
		$this->physical_uri = $row['physical_uri'];
		$this->virtual_uri = $row['virtual_uri'];
		$this->domain = $row['domain'];
		$this->domain_ssl = $row['domain_ssl'];	

		//if (!$row['active'])
		//	$this->SiteNotFound();
	
		//if (!$this->shopIsActive())
		//	$this->SiteNotFound();			
		return true;
	}	
	
	public function SiteNotFound()
	{
		header('HTTP/1.0 404 Not Found');
        echo '<h1>URL not exist</h1>';
        exit;
	}
	
	public function shopIsActive()
	{
		return(Db::getInstance()->getValue('
			SELECT cu.active
			FROM '._DB_PREFIX_.'egms_city_url cu
			WHERE cu.id_shop_url = '.(int)$this->id_shop_url));
	}

	public static function getIdUrl($host = null)
	{
		if(!$host)
			$host = Tools::getHttpHost();
        $cache_id = 'Shop::getIdUrl_'.$host;

        if (!Cache::isStored($cache_id)) {
            $s = 'SELECT su.id_shop_url
			FROM ' . _DB_PREFIX_ . 'shop_url su
			WHERE su.domain = \'' . $host . '\'';
            $r = Db::getInstance()->getValue($s);
            Cache::store($cache_id, $r);
        }
        $result = Cache::retrieve($cache_id);
        return($result);
	}	
	
    // check carriaer
    public static function getCarriers()
    {
    	$carriers = Shop::getCEOData();
    	return ($carriers['carriers']);
    }	
    
    //payment check
    public static function paymentModuleIsAvaliable($module)
    {
    	$payments = Shop::getCEOData();
    	$my_payments =  explode(',', $payments['payments']);
    	foreach ($my_payments as $payment)
    	{
    		if($payment == $module)
    			return true;
    	}
    	return false;    
    }    
	
	public static function getCEOData($id_manufacturer = null)
	{
		$id_url = Shop::getUtlId();
		$main_domain = Tools::getMaindomain();
		$domain = Tools::getHttpHost();
		// host, id_egms_cu, phone, id_city, city_name, city1_name, city2_name, psyname, alias
		// , email, address, chema, shipselfinfo
		// domain data
        $cache_id = 'Shop::getCEOData_1'.$id_url;
        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT su.domain host
				FROM `' . _DB_PREFIX_ . 'shop_url` su
				WHERE su.`id_shop_url` = ' . (int)$id_url;
            $r = Db::getInstance()->getRow($sql);
            Cache::store($cache_id, $r);
        }
        $r = Cache::retrieve($cache_id);
		$rets[] = $r;
		if ($main_domain==$domain)
			if(Shop::isRegionalURL())
			{
				$sub = Tools::getSubdomain($rets[0]['host']);
				$rets[0]['host'] = $main_domain.'/'.$sub[2];
			}else
			{
				$rets[0]['host'] = $main_domain;
			}
		/*
		if ($rets[0]['host']!=$domain)
		{
			$sub = Tools::getSubdomain($rets[0]['host']);
			$rets[0]['host'] = $main_domain.'/'.$sub[2];
		}*/
        $cache_id = 'Shop::getCEOData_2'.(int)$id_url;
        if (!Cache::isStored($cache_id)) {
            $sql = "SELECT cu.id_egms_cu, cu.phone, cu.id_city, cu.address, cu.fake_address, cu.chema, cu.shipselfinfo,
				cu.veryf_yandex, cu.veryf_google, cu.veryf_mail, cu.del_pay, cu.free_pay, cu.dlex, cu.carriers, cu.payments, '" . Configuration::get('PS_SHOP_NAME') . "' as shop_name 
				FROM `" . _DB_PREFIX_ . "egms_city_url` cu
				WHERE cu.`id_shop_url` = " . (int)$id_url;
            $r = Db::getInstance()->getRow($sql);
            Cache::store($cache_id, $r);
        }
		$rets[] = Cache::retrieve($cache_id);
		// , concat(engname,\'@'.$subdomain.'\') email
		
		//city data
        $cache_id = 'Shop::getCEOData_3'.(int)$rets[1]['id_city'];
        if (!Cache::isStored($cache_id)) {
            $sql = 'SELECT c.`cityname1` as city_name, c.`cityname2` as city1_name, 
                    c.`cityname3` as city2_name, c.psyname, c.alias, concat(c.alias,\'@' . $main_domain . '\') as email,
                    \'' . $main_domain . '\' as main_host 
                    FROM `' . _DB_PREFIX_ . 'egms_city` c
                    WHERE c.`id_egms_city` = ' . (int)$rets[1]['id_city'];
            $r = Db::getInstance()->getRow($sql);
            Cache::store($cache_id, $r);
        }
		$rets[] = Cache::retrieve($cache_id);
		
		// delivery 
		// and delivery by id
		//, del_pay, free_pay, dlex 
		if (!is_null($id_manufacturer)){
            $cache_id = 'Shop::getCEOData_4'.(int)$rets[1]['id_city'];
            if (!Cache::isStored($cache_id)) {
                $sql = 'SELECT address, chema, shipselfinfo 
				FROM `' . _DB_PREFIX_ . 'egms_delivery`
				WHERE `id_egms_cu` = ' . (int)$rets[1]['id_egms_cu'] . ' 
				AND `id_manufacturer` = ' . $id_manufacturer;
                $r = Db::getInstance()->getRow($sql);
                Cache::store($cache_id, $r);
            }
            $rets[] = Cache::retrieve($cache_id);
		}
		foreach ($rets as $ret)
		{
			foreach ($ret as $key => $val)
				$result[$key] = $val;
		}
		
		return $result;
	}	
	
	public static function getUtlId()
	{
		$domain = Tools::getHttpHost();
		$main_domain = Tools::getMaindomain();
		$url_id = null;
		if ($main_domain == $domain)
		{
			//if region
			$url_id = Shop::isRegionalURL();
			if(!$url_id)
			{
				$context = Context::getContext();
				//if region isset
				if ($context->cookie->__isset('url_id'))
					//id from coockies
					$url_id = $context->cookie->__get('url_id');
				else
					// id main domain
					$url_id = Shop::getIdUrl($main_domain);				
			}
			else
			{
				Shop::setUtlId($url_id);
				return $url_id;
			}
		}
		else
		{
			//regional id
			$url_id = Shop::getIdUrl();
			Shop::setUtlId($url_id);
		}	
		return $url_id;
	}
	
	public static function isRegionalURL()
	{
		$url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$uri_parts = explode('/', trim($url_path, ' /'));
		$subdomain = array_shift($uri_parts);
		
		$main_domain = Tools::getMaindomain();
		
		return (Shop::getIdUrl($subdomain.'.'.$main_domain));
	}
	
	public static function setUtlId($url_id)
	{
		// set coockies id domain
		$context = Context::getContext();
		$context->cookie->__set('url_id', $url_id);
	}

    public function getBaseURI2()
    {
        return $this->physical_uri;
    }
		
}

