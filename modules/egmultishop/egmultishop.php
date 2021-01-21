<?php
if (!defined('_PS_VERSION_'))
  exit;
  
class egmultishop extends Module
{
	protected	$row;
	const INSTALL_SQL_FILE = 'install.sql';
	private		$host;
	
  public function __construct()
  {
    $this->name = 'egmultishop';
    $this->tab = 'front_office_features';
    $this->version = '0.1.1';
    $this->author = 'Evgeny Grishin';
    $this->need_instance = 0;
    $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 
    $this->bootstrap = true;
 
    parent::__construct();
 
    $this->displayName = $this->l('Mutishop addon');
    $this->description = $this->l('addon for multishop.');
 
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    $this->host = Tools::getHttpHost();
 
  }
  
  
	public function getContent()
	{
		$this->registerHook('displayNav');
		//TODO: !!!
	    $output = null;
	 
	    if (Tools::isSubmit('submit'.$this->name))
	    {
	        $my_module_name = strval(Tools::getValue('MYMODULE_NAME'));
	        if (!$my_module_name
	          || empty($my_module_name)
	          || !Validate::isGenericName($my_module_name))
	            $output .= $this->displayError($this->l('Invalid Configuration value'));
	        else
	        {
	            Configuration::updateValue('MYMODULE_NAME', $my_module_name);
	            $output .= $this->displayConfirmation($this->l('Settings updated'));
	        }
	    }
	    $output.=$this->displayForm();
	    $output.=$this->renderList();
	    
	    return $output;
	}  
	
	public function renderList()
	{
					
		$fields_list = array(
			'id' => array(
				'title' => $this->l('Link ID'),
				'type' => 'text',
			),
			'shop_name' => array(
				'title' => $this->l('Shop'),
				'type' => 'text',
			),			
			'city_name' => array(
				'title' => $this->l('City'),
				'type' => 'text',
			),
			'domain' => array(
				'title' => $this->l('URL'),
				'type' => 'text',
			),	
			'yandex' => array(
				'title' => $this->l('Yandex'),
				'type' => 'bool',
			),
			'google' => array(
				'title' => $this->l('Google'),
				'type' => 'bool',
			),								
		);
		
		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = false;
		$helper->identifier = 'id';
		$helper->actions = array('edit', 'delete');
		$helper->show_toolbar = false;

		$helper->title = $this->l('Link list');
		$helper->table = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$links = $this->getLinks();
		if (is_array($links) && count($links))
			return $helper->generateList($links,  $fields_list);
		else
			return false;
	}
	
	public function getRow($row, $name)
	{
		return $this->row[$row][$name];
	}
	
	
	public function getMultishopDateById($id_url=0)
	{
		
		if (!$this->row)
		{		
			$sql = 'SELECT mu.*
					FROM `'._DB_PREFIX_.'shop_url` su ';
			if (!Configuration::get('BLOCK_EGMULTSOP_CITY'))
				$sql.=' INNER JOIN `'._DB_PREFIX_.'egmultishop_url` mu ON
							mu.`id_url` = su.`id_shop_url`
							WHERE su.`active` = 1
							and su.`id_shop`='.(int)$this->context->shop->id;
			else {
				if ($id_url==0)
				{
						$sql.=' INNER JOIN `'._DB_PREFIX_.'egmultishop_url` mu ON
							mu.`id_url` = su.`id_shop_url`
							WHERE su.`domain` = \''.Tools::getHttpHost().'\'';
				}else				
						$sql.=' WHERE mu.`id_url`='.$id_url;
			}
			if (!$this->row = Db::getInstance()->executeS($sql))
				return false;
		}
	}	
	
	public function getLinks($main_url=true)
	{

		$sql = 'SELECT su.`id_shop_url` as id, su.`domain`, mu.`city_name`, s.`name` as shop_name
				,CASE WHEN(TRIM(yandex_metr)!=\'\') THEN true ELSE false END AS yandex
				,CASE WHEN(TRIM(google_anal)!=\'\') THEN true ELSE false END AS google
				FROM `'._DB_PREFIX_.'shop_url` su
				INNER JOIN `'._DB_PREFIX_.'shop` s ON
					s.`id_shop`=su.`id_shop`
				LEFT JOIN `'._DB_PREFIX_.'egmultishop_url` mu ON
					mu.`id_url`=su.`id_shop_url`
				WHERE su.id_shop IN ('.implode(', ', Shop::getContextListShopID()).') ';
		if(!$main_url)
			$sql.= ' and su.`main`= 0';

		if (!$links = Db::getInstance()->executeS($sql))
			return false;

		return $links;
	}	
	/*
	public static function getUrlId($id_shop)
	{
		if (!isset($id_shop))
			$id_shop = $this->context->shop->id;
		
		$sql = 'select id_shop_url
				from ps_shop_url 
				where domain =\''.Tools::getHttpHost().'\'
				and id_shop='.(int)$id_shop;
		
		if (!$row = Db::getInstance()->executeS($sql))
			return 0;
		else 
			return $row[0]['id_shop_url'];
	}
	*/
	//@depricated
	public function getMultishopPage($ptype, $id_url=null, $shop=true)
	{
		$page = "";
		
		$sql = 'select content, title, meta, keywords
			from ps_egmultishop_pages
			where  ptype = \''.$ptype.'\'';
		if ($shop==true)
			$sql .= ' and id_shop='.(int)$this->context->shop->id.'';
		if ($id_url!=null)
			$sql.=' and id_url='.$id_url;
		
		if (!$row = Db::getInstance()->executeS($sql))
			return "";
		
		return $row[0]['content'];
	}
	
	public function getMultishopPageDomain($ptype, $domain=null, $shop=true)
	{
		$page = "";
		
		$sql = 'select content, title, meta, keywords
			from ps_egmultishop_pages
			where  ptype = \''.$ptype.'\'';
		if ($shop==true)
			$sql .= ' and id_shop='.(int)$this->context->shop->id.'';
		if ($domain!=null)
			$sql.=' and trim(sub_domain)=\''.$domain.'\'';
		
		if (!$row = Db::getInstance()->executeS($sql))
			return "";
		
		return $row[0]['content'];
	}	
	
	public function replaceCeoWords($page)
	{
		$ceo_word = Meta::getCitys();
		$host = array('host' => $this->host);
		$ceo_word = array_merge($ceo_word, $host);
		return Meta::sprintf2($page,$ceo_word);
	}
	
	public function getShipselfCwarNearInfo($id_url)
	{
		
		$sql = 'select maincwar
			from '._DB_PREFIX_.'egmultishop_url
			where  id_url = '.(int) $id_url.'';
		$row = Db::getInstance()->getRow($sql);
		
		$maincwar = $row['maincwar'];
		
		$sql = 'select addr1, chema, maincwar
			from '._DB_PREFIX_.'egmultishop_url
			where  id_url = '.(int) $maincwar.'';
		
		$row = Db::getInstance()->getRow($sql);
		
		return $row;
	}
	
	public function replaceCeoContact($page)
	{
		$ceo_word = Meta::getCitysAddr();
		$host = array('host' => $this->host);
		list($x1,$x2)=array_reverse(explode('.',$this->host));	
		$xdomain=$x2.'.'.$x1;	
		$ceo_word = array_merge($ceo_word, $host,array('ehost' => $xdomain));
		return Meta::sprintf2($page,$ceo_word);
	}	
	
	public static function getSubdomain()
	{
		$d = Tools::getHttpHost();
		list($x1,$x2,$x3)=array_reverse(explode('.',$d));	
		return $x3;
	}
	

	public function displayForm()
	{
		//TODO: !!!
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		
			$fields_form1[0] = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Dynamic'),
						'name' => 'BLOCK_EGMULTSOP_CITY',
						'desc' => $this->l('Activate dynamic (animated) mode for category sublevels.'),
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->l('Enabled')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->l('Disabled')
									)
								),
					)
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			)
		);	
		
    	$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		//$helper->submit_action = 'submitBlockCategories';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
    	
    	
    	return $helper->generateForm($fields_form1);
	}
	public static function isLiveSite()
	{
		if (!substr_count($_SERVER['DOCUMENT_ROOT'], "home/matras-house.ru/www"))
			return TRUE;
		else
			return false;
	}
	
	public static function isMarketingSite()
	{
		$context = Context::getContext();
		$id_shop = $context->shop->id;
		
		$sql = 'select mu.actual
				from `'._DB_PREFIX_.'shop_url` su
				INNER JOIN `'._DB_PREFIX_.'egmultishop_url` mu ON
					mu.`id_url`=su.`id_shop_url`
				where su.domain =\''.Tools::getHttpHost().'\'
				and su.id_shop='.(int)$id_shop;
		
		if (!$row = Db::getInstance()->executeS($sql))
			return "";
		 
		return $row[0]['actual'];
		
	}

	public function hookDisplayProductDeliveryTime()
	{
		$page = $this->getMultishopPage("freedeliv");
			
		$page = $this->replaceCeoWords($page);

	    $sql = 'SELECT fdelivery, delivery_price, dlex FROM `'._DB_PREFIX_.'shop_url` su
		INNER JOIN `'._DB_PREFIX_.'egmultishop_url` mu ON
			mu.`id_url`=su.`id_shop_url`
		WHERE su.domain =\''.Tools::getHttpHost().'\'';
	    $id_product = (int)Tools::getValue('id_product'); 
	    $links = Db::getInstance()->executeS($sql);

			if (in_array($id_product,explode(',', $links[0]['dlex'])))
				$links[0]['fdelivery'] = 0; 
	    
		$this->smarty->assign(array(
			'free_price' => 	$links[0]['fdelivery'],
			'delivery_price' => $links[0]['delivery_price'] 	
		));	
			
		return $this->display(__FILE__, 'egmultishop_delivery.tpl');
	}
	
	public function hookDisplayNav($params)
	{
		//$this->context->controller->addCSS($this->_path.'views/css/egmultishop.css', 'all');
		//$this->context->controller->addJS($this->_path.'views/js/modal.js', 'all');
		
		$this->getMultishopDateById();
		
		
 		$city_list = Configuration::get('BLOCK_EGMULTSOP_CITY');
 		if ($this->row)
 		{
			$this->smarty->assign(array(
				'phone' => (string)$this->row[0]['phone'],
				'city_name' => (string)$this->row[0]['city_name'],
				'city_lists' => (bool)$city_list,
				'city_link' => $this->context->link->getModuleLink('egmultishop', 'citys')//,
			));
	
			return $this->display(__FILE__, 'egmultishop_nav.tpl');
 		}
	}	
	
	public function hookDisplayTop($params)
	{
		//$this->context->controller->addCSS($this->_path.'views/css/egmultishop.css', 'all');
		//$this->context->controller->addJS($this->_path.'views/js/modal.js', 'all');
		
		$this->getMultishopDateById();
		
		
 		$city_list = Configuration::get('BLOCK_EGMULTSOP_CITY');
 		if ($this->row)
 		{
			$this->smarty->assign(array(
				'phone' => (string)$this->row[0]['phone'],
				'city_name' => (string)$this->row[0]['city_name'],
				'city_lists' => (bool)$city_list,
				'city_link' => $this->context->link->getModuleLink('egmultishop', 'citys')//,
 				//'ajaxcontroller' => $this->context->link->getModuleLink('egmultishop', 'ajax')
			));
	
			return $this->display(__FILE__, 'egmultishop_top.tpl');
 		}
	}	
	
	public function hookHeader($params)
	{
	if (!Configuration::get('PS_CSS_THEME_CACHE')){
		//$this->context->controller->addJS($this->_path.'views/js/callme.js', 'all');
		$this->context->controller->addJS('http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU', 'all');		
		$this->context->controller->addCSS($this->_path.'views/css/egmultishop.css', 'all');
		$this->context->controller->addJS($this->_path.'views/js/modal.js', 'all');
	}
		$this->getMultishopDateById();
		if (egmultishop::isLiveSite()){
	 		if ($this->row)
	 		{
				$this->smarty->assign(array(
					'yandex_verify' => (string)$this->row[0]['yandex_verify'],
					'google_verify' => (string)$this->row[0]['google_verify'],
					'mail_verify' => (string)$this->row[0]['mail_verify'],
					'google_anal' => (string)$this->row[0]['google_anal'],
					'yandex_metr' => (string)$this->row[0]['yandex_metr'],
					'city_link' => $this->context->link->getModuleLink('egmultishop', 'citys')
				));
	 		}
		}else{
				$this->smarty->assign(array(
					'yandex_verify' => (string)$this->row[0]['yandex_verify'],
					'google_verify' => (string)$this->row[0]['google_verify'],
					'mail_verify' => (string)$this->row[0]['mail_verify'],
					'google_anal' => Configuration::get('BLOCK_EGMULTSOP_TESTGOOGL'),
					'yandex_metr' => Configuration::get('BLOCK_EGMULTSOP_TESTMETR'),
					'city_link' => $this->context->link->getModuleLink('egmultishop', 'citys')
				));			
		}
		
		return $this->display(__FILE__, 'egmultishop_header.tpl');
	}
	

	
	public function hookDisplayHome($params)
	{
		
		$row = Meta::getEgCEOWords('home');
		$page = $row['description'];
		$this->smarty->assign(array(
			'title' => $row['meta_title'],
			'page' => $page
		));
				
		return $this->display(__FILE__, 'egmultishop_home.tpl');

	}
	
	public function hookLeftColumn($params)
	{
		//return $this->getMultishopPage('social', null, true);	
	}	
	
	public function hookDisplayFooter($params)
	{
			$page = $this->getMultishopPage("home_futer");
			
			$page = $this->replaceCeoWords($page);
					
			$this->smarty->assign(array(
				'page' => $page
			));		
		return $this->display(__FILE__, 'egmultishop_page.tpl');		
	}
	
	public function hookDisplayFooterBottom($params)
	{
		$this->hookDisplayFooter($params);	
	}
	
	public function hookDisplayLeftColumn($params)
	{	
		return $this->getMultishopPage('social', null, false);	
		/*
		$this->getMultishopDateById();
		
		
		$metr = Configuration::get('BLOCK_EGMULTSOP_METRDEF');
		
		if(trim($metr) == "") 
			(string)$this->row[0]['yandex_metr'];
		
		if ($this->row)
		{
			$this->smarty->assign(array(
				'yandex_metr' => $metr,
				'google_anal' => (string)$this->row[0]['google_anal']
			));		
			return $this->display(__FILE__, 'egmultishop_fbottom.tpl');
		}
		*/
	}
	public static function getCityURL($id)
	{
		$sql = 'select su.domain, mu.`city_name`
				from `'._DB_PREFIX_.'shop_url` su
				INNER JOIN `'._DB_PREFIX_.'egmultishop_url` mu ON
					mu.`id_url`=su.`id_shop_url`
				where su.id_shop_url='.(int)$id;
		
		if (!$row = Db::getInstance()->executeS($sql))
			return false;
		
		return array(
			'url' => $row[0]['domain'],
			'city_name' => $row[0]['city_name']		
					);
	}
	public function hookDisplayRightColumn($params)
	{
		$page = $this->getMultishopPage("home1");
		
		$page = $this->replaceCeoWords($page);
		
		$this->smarty->assign(array(
			'page' => 	$page	
		));	
		
		return $this->display(__FILE__, 'egmultishop_page.tpl');
	}
	
	public function hookActionValidateOrder($params)
	{
		$order = $params['order'];
		
		
		$total = $order->total_paid;
		$host = Tools::getHttpHost();
		$order_id = $order->id;
		
		$param = array(
				'{total_paid}'	=> $total, 
				'{shop_url}'	=> $host
			);

		Mail::Send(
				(int)$order->id_lang,
				'event_order',
				Mail::l('new order', (int)$order->id_lang),
				$param,
				Configuration::get('SMS_ORDER_NEW_EMAIL'),
				'Site Admin',
				null,
				null,
				null,
				null,
				_PS_MAIL_DIR_,
				false,
				(int)$order->id_shop
			);	
			
		Mail::Send(
				(int)$order->id_lang,
				'event_order',
				Mail::l('new order', (int)$order->id_lang),
				$param,
				Configuration::get('PS_SHOP_EMAIL'),
				'Site Admin',
				null,
				null,
				null,
				null,
				_PS_MAIL_DIR_,
				false,
				(int)$order->id_shop
			);		
		// notify to me
		$message = Configuration::get('SMS_ORDER_NEW');
		$address = new Address((int) $order->id_address_delivery);
		$message = Meta::sprintf2($message, array(
			'order' => $order_id,
			'host' => $host
			));
		$phone_client = (trim($address->phone)=="")?$address->phone_mobile:$address->phone;
		$phone_client = preg_replace('#\D+#', '', $phone_client);			
		if (egmultishop::isLiveSite()) {
			//if (Configuration::get('BLOCK_EGMULTSOP_SNON')){
				// send to client if marketing site
				//if(egmultishop::isMarketingSite()>0){
					egmultishop::SendSMS($phone_client, $message);
				//}
		
				
				// send to me in any way
		$phone = Configuration::get('SMS_ORDER_NEW_PHONE');
		$message = " ".$total." RUB ";
		//egmultishop::SendSMS($phone, "new order".$message.$host);
		}

		$param = array(
                '{phone}'    => str_replace('+','',$phone_client),
                '{message}'    => $message,
                '{fname}' => '',
            	'{type}'	=> 'NewOrder',
            	'{host}' => $host,
            	'{shost}' => str_replace('.','-',$host)
            );
		$this->sendTelegramm($param, $sms_message);		
			//}	
		// client notify	
	}
	
	private function sendTelegramm($param, $sms_message)
	{
		$request = Configuration::get('EGCALLME_HTTPNOT_3');
		$text = Configuration::get('EGCALLME_HTTPNOT_3_TXT');
		$request = $this->replaceKeywords($param, $request, $text);
        $result = file_get_contents($request);
	}	
	
    private function replaceKeywords($params, $request, $text)
    {
      	foreach ($params as $key => $value) {
    		$text = str_replace($key,$value,$text);
    	}
    	
    	foreach ($params as $key => $value) {
    		$request = str_replace($key,$value,$request);
    	}
    	$request = str_replace('{text}',urlencode($text),$request);
    	return $request;
    }
    	
	public static function SendSMS($phone, $message)
	{
		$context = Context::getContext();
		$id_shop = $context->shop->id;
		$id_shop_group = $context->shop->id_shop_group;
		
		$max_sms = 6;
		$sms = Configuration::get('EGCALLME_SMS_REQUEST',null, $id_shop_group, $id_shop);
		$phone = preg_replace('#\D+#', '', $phone);
		$sms = Meta::sprintf2($sms, array(
				'sendto' => $phone,
				'message' => substr($message, 0, 70*$max_sms)
				));
					
		if(trim($sms)!=""){		
			$result = file_get_contents($sms);
		}
	}
	
	
	public function install($keep = true)
	{
		if ($keep)
			{
				if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
					return false;
				else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
					return false;
				$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
				$sql = preg_split("/;\s*[\r\n]+/", trim($sql));
	
				foreach ($sql as $query)
					if (!Db::getInstance()->execute(trim($query)))
						return false;
	
			}
			
	  if (!parent::install() ||
	  	!$this->registerHook('displayTop') ||
		!$this->registerHook('header') ||
		!$this->registerHook('displayHome') ||
		!$this->registerHook('displayNav') ||
		!$this->registerHook('displayFooter') ||
		!$this->registerHook('displayBottom') ||
		!$this->registerHook('displayFooterBottom') ||
		!Configuration::updateValue('BLOCK_EGMULTSOP_CITY', 0)||
		!Configuration::updateValue('BLOCK_EGMULTSOP_SMNON', 0)
		)
	    return false;
	  return true;
	}  

	public function reset()
	{
		if (!$this->uninstall(false))
			return false;
		if (!$this->install(false))
			return false;
		return true;
	}	
	
	public function deleteTables()
	{
		return Db::getInstance()->execute('
			DROP TABLE IF EXISTS
			`'._DB_PREFIX_.'egmultishop_url`');
	}
	
	public function uninstall($keep = true)
	{
	  if (!parent::uninstall() || ($keep && !$this->deleteTables()) ||
			!Configuration::deleteByName('BLOCK_EGMULTSOP_CITY') ||
			!Configuration::deleteByName('BLOCK_EGMULTSOP_SMNON') ||
			!$this->unregisterHook('displayTop') ||
			!$this->unregisterHook('header') ||
			!$this->unregisterHook('displayHome') ||
			!$this->unregisterHook('displayNav') ||
			!$this->unregisterHook('displayFooter') ||
			!$this->unregisterHook('displayBottom') ||
			!$this->unregisterHook('displayFooterBottom'))
	    return false;
	  return true;
	}	
}
  
  ?>