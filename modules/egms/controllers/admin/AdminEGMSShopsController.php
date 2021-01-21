<?php

require_once(_PS_MODULE_DIR_.'egms/classes/egms_shop.php');
require_once(_PS_MODULE_DIR_.'egms/classes/city.php');
require_once ('AdminEGMSDeliveryController.php');

class AdminEGMSShopsController extends ModuleAdminControllerCore
{

	protected $position_identifier = 'id_egms_cu';
	protected $manufacturers;
	protected $citys;
	protected $urls;
	protected $id_egms_cu;
    protected $shops_array = array();
	
	public function __construct()
	{

		$this->bootstrap = true;
		$this->list_id = 'id_egms_cu';
		$this->identifier = 'id_egms_cu';
		$this->table = 'egms_city_url';	
		$this->className = 'egms_shop';	
		$this->meta_title = $this->l('Citys by Shops');
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));
		
		$this->_select .= 'a.id_egms_cu, s.name as shopname, c.cityname1, a.phone, su.domain, a.active, su.active as active_url, a.visible, 
				(select count(d.id_egms_delivery) from '._DB_PREFIX_.'egms_delivery d where d.id_egms_cu = a.id_egms_cu and deleted=0) manufacturer';
		$this->_join .= ' INNER JOIN '._DB_PREFIX_.'shop_url su ON a.id_shop_url = su.id_shop_url ';
		$this->_join .= ' INNER JOIN '._DB_PREFIX_.'egms_city c ON a.id_city = c.id_egms_city';
		$this->_join .= ' INNER JOIN '._DB_PREFIX_.'shop s ON su.id_shop = s.id_shop ';
		//$this->_join .= ' LEFT JOIN '._DB_PREFIX_.'egms_city_manuf cm ON cm.id_egms_city = a.id_egms_cu ';
		//$this->_join .= ' LEFT JOIN '._DB_PREFIX_.'egms_delivery d ON d.id_egms_cu = a.id_egms_cu ';
		//if (Shop::getContext() == Shop::CONTEXT_SHOP)
		//	$this->_where .= ' and s.id_shop in ('.(int)Context::getContext()->shop->id.')';
		//$sss = Shop::getContextListShopID();
		//$this->_where .= '  and su.id_shop IN ('.implode(', ', $sss).')';
        //$this->_where .= ' and 1 = 1  ';
		$this->_orderBy = 'c.cityname1';

        $shops = Shop::getShops();
        foreach ($shops as $shop)
            $this->shops_array[$shop['id_shop']] = $shop['name'];


        $this->fields_list = array(
            'id_egms_cu' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'shopname' => array('title' => $this->l('Shop name'), 'type' => 'select',
                'list' => $this->shops_array,
                'filter_key' => 's!id_shop',
                'filter_type' => 'int',
                'order_key' => 'shopname'),
            'cityname1' => array('title' => $this->l('Cityname'), 'filter_key' => 'c!cityname1'),
            'domain' => array('title' => $this->l('domain'), 'filter_key' => 'su!domain'),
            //TODO: error with wilter by activeurl
            'active_url' => array('title' => $this->l('Displayed url'), 'type' => 'bool',
                'filter_key' => 'su!active',
                'filter_type' => 'int',
                'align' => 'center',
                'search' => false,
                'active' => 'status',
                'class' => 'fixed-width-sm'),
            'phone' => array('title' => $this->l('Phone'), 'filter_key' => 'a!phone'),
            'manufacturer' => array('title' => $this->l('manufact'), 'orderby' => false, 'search' => false,),
            'active' => array('title' => $this->l('Displayed shop'), 'type' => 'bool',
                'filter_key' => 'a!active',
                'filter_type' => 'int',
                'align' => 'center',
                'active' => 'status',
                'order_key' => 'active',
                'class' => 'fixed-width-sm'),
        );

        $this->_theme_dir = Context::getContext()->shop->getTheme();
		$this->id_egms_cu = Tools::getValue('id_egms_cu');
		//$s = Shop::getContextListShopID();
		$this->getAllManufacturers();
		$this->getCitys();
		$this->getUrls();
		
		parent::__construct();
	}
	
	public function renderList()
	{
		$this->initToolbar();
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		return parent::renderList();
	}

	public function postProcess()
	{
		parent::postProcess(true);
	}
	
	public function getAllManufacturers()
	{
		$manufacturers = ManufacturerCore::getManufacturers();
		$items = array();
		foreach ($manufacturers as $manufacturer)
		{
			$items[]= array(
						'id' => $manufacturer['id_manufacturer'], 
						'name' => $manufacturer['name'], 
			);
		}
		
         $this->manufacturers = $items;
	}
	
	public function getCitys()
	{
		$citys = city::getCity(Tools::getValue());
		foreach ($citys as $city)
		{
			$this->citys[] = array('id' => $city['id_egms_city'], 'name' => $city['cityname1']);
		}
	}
	
	public function getUrls()
	{
		$id_shop = null;
		//if (Shop::getContext() == Shop::CONTEXT_SHOP)
		//	$id_shop = $this->context->shop->id;//Context::getContext()->shop->id;
			
		$shops = egms_shop::getShopUrls($id_shop);
		foreach ($shops as $shop)
		{
			$sql ='SELECT id_egms_cu, id_shop_url FROM '._DB_PREFIX_.'egms_city_url cu 
					WHERE cu.id_shop_url='.(int)$shop['id_shop_url'];
			$row = Db::getInstance()->getRow($sql);
			
			if(!isset($row['id_shop_url']) 
				|| ($row['id_egms_cu'] == $this->id_egms_cu 
					&& $row['id_shop_url']==$shop['id_shop_url']))
			
				$this->urls[] = array(
		                'id' => $shop['id_shop_url'], 
		                'name' => $shop['domain']      
		        );
		}		
	}
	

	public function renderForm()
	{
		
		if (!$this->loadObject(true))
			if (Validate::isLoadedObject($this->object))
				$this->display = 'edit';
			else
				$this->display = 'add';

		$this->initToolbar();
		$this->initPageHeaderToolbar();

		$this->multiple_fieldsets = true;

		$soption = array(
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
		);
		
		
		
		$this->fields_form[0]['form'] = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Shops by Citys'),
				'icon' => 'icon-folder-close'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'label' => $this->l('id'),
					'name' => 'id_egms_cu',
				),			
				array(
					'type' => 'select',
					'label' => $this->l('Citys'),
					'name' => 'id_city',
					'required' => true,  
					'options' => array('query' => $this->citys,
						'id' => 'id',
						'name' => 'name')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Shop URL'),
					'name' => 'id_shop_url',
					'required' => true,  
					'options' => array('query' => $this->urls,
						'id' => 'id',
						'name' => 'name'),
				),				
				array(
					'type' => 'text',
					'label' => $this->l('veryf_yandex'),
					'name' => 'veryf_yandex',
					'hint' => $this->l('veryf_yandex')
				),				
				array(
					'type' => 'text',
					'label' => $this->l('veryf_google'),
					'name' => 'veryf_google',
					'hint' => $this->l('veryf_google')
				),	
				array(
					'type' => 'text',
					'label' => $this->l('veryf_mail'),
					'name' => 'veryf_mail',
					'hint' => $this->l('veryf_mail')
				),
				array(
					'type' => 'text',
					'label' => $this->l('phone'),
					'name' => 'phone',
					'hint' => $this->l('phone')
				),
				array(
					'type' => 'text',
					'label' => $this->l('address'),
					'name' => 'address',
					'hint' => $this->l('address')
				),	
				array(
					'type' => 'text',
					'label' => $this->l('chema'),
					'name' => 'chema',
					'hint' => $this->l('chema')
				),	
				array(
					'type' => 'textarea',
					'label' => $this->l('shipself info'),
					'name' => 'shipselfinfo',
					'hint' => $this->l('shipself info')
				),
				array(
					'type' => 'text',
					'label' => $this->l('del_pay'),
					'name' => 'del_pay',
					'hint' => $this->l('del_pay')
				),	
				array(
					'type' => 'text',
					'label' => $this->l('free_pay'),
					'name' => 'free_pay',
					'hint' => $this->l('free_pay')
				),		
				array(
					'type' => 'text',
					'label' => $this->l('dlex'),
					'name' => 'dlex',
					'hint' => $this->l('dlex')
				),
                array(
                    'type' => 'switch',
                    'label' => $this->l('visible in city list'),
                    'name' => 'visible',
                    'values' => $soption,
                    'default' => '1',
                ),
                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Carriers'),
                	'multiple' => true,
                    'name' => 'carriers',
                	'hint' => $this->l('Carriers hint'),
                    'desc' => $this->l('Carriers'),
                    'values' => array(
		                            'query' => AdminEGMSDeliveryController::getCarriers(),
		                    		'id' => 'id',
		                    		'name' => 'name'
                					),
             	),	
				array(
                    'type' => 'checkbox',
                    'label' => $this->l('Payments'),
                	'multiple' => true,
                    'name' => 'payments',
                	'hint' => $this->l('Payments hint'),
                    'desc' => $this->l('Payments'),
                    'values' => array(
		                            'query' => AdminEGMSDeliveryController::getPayments(),
		                    		'id' => 'id',
		                    		'name' => 'name'
                					),
             	),															
				array(
					'type' => 'select',
					'label' => $this->l('Index Page'),
					'name' => 'page_index',
					'required' => true,  
					'options' => array('query' => $this->getPages('index'),
						'id' => 'id',
						'name' => 'name')
				),	
				array(
					'type' => 'select',
					'label' => $this->l('Contact Page'),
					'name' => 'page_contact',
					'required' => true,  
					'options' => array('query' => $this->getPages('contact'),
						'id' => 'id',
						'name' => 'name')
				),	
				array(
					'type' => 'select',
					'label' => $this->l('Delivery Page'),
					'name' => 'page_delivery',
					'required' => true,  
					'options' => array('query' => $this->getPages('delivery'),
						'id' => 'id',
						'name' => 'name')
				),	
				array(
					'type' => 'select',
					'label' => $this->l('Shipself Page'),
					'name' => 'page_shipself',
					'required' => true,  
					'options' => array('query' => $this->getPages('shipself'),
						'id' => 'id',
						'name' => 'name')
				),
				array(
					'type' => 'select',
					'label' => $this->l('robots.txt'),
					'name' => 'page_robotstxt',
					'required' => true,  
					'options' => array('query' => $this->getPages('robotstxt'),
						'id' => 'id',
						'name' => 'name')
				),		
				array(
					'type' => 'select',
					'label' => $this->l('sitemap.txt'),
					'name' => 'page_sitemap',
					'required' => true,  
					'options' => array('query' => $this->getPages('sitemap'),
						'id' => 'id',
						'name' => 'name')
				),																				
				array(
					'type' => 'switch',
					'label' => $this->l('Is Active'),
					'name' => 'active',
					'values' => $soption,
					'default' => '1',
				),				
                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Manufacturers'),
                	'multiple' => true,
                    'name' => 'manufacturer',
                	'hint' => $this->l('Manufacturers hint'),
                    'desc' => $this->l('Manufacturers'),
                    'values' => array(
		                            'query' => $this->manufacturers,
		                    		'id' => 'id',
		                    		'name' => 'name'
                					),
             		 ),																				
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
			)
		);	
		
		
       $this->tpl_form_vars = array(
            'fields_value' => $this->getFieldsValues()
       );	
		
		return parent::renderForm();
	}	
	
	public function getPages($type)
	{
		$sql = 'SELECT id_page as page_'.$type.', page_name as name FROM '._DB_PREFIX_.'egms_pages 
				WHERE page_type=\''.$type.'\'';
		$rows = Db::getInstance()->executeS($sql);
		$items = array();
		foreach ($rows as $row)
		{
			$items[]= array('id' => $row['page_'.$type],	'name' => $row['name']);
		}
		
		return $items;
	}
	
    public function getFieldsValues()
    {
    	$id_egms_cu = Tools::getValue('id_egms_cu');
    	$row = $this->getCityUrl(Tools::getValue('id_egms_cu'));
        $vals = array(
            'id_egms_cu' => $id_egms_cu,
        	'id_city' => $row[0]['id_city'],
        	'id_shop_url' => $row[0]['id_shop_url'],
			'veryf_yandex' => $row[0]['veryf_yandex'],
        	'veryf_google' => $row[0]['veryf_google'],
        	'veryf_mail' => $row[0]['veryf_mail'],
        	'phone' => $row[0]['phone'],
            'address' => $row[0]['address'],
        	'chema' => $row[0]['chema'],
        	'shipselfinfo' => $row[0]['shipselfinfo'],
        	'page_index' => $row[0]['page_index'],
        	'page_contact' => $row[0]['page_contact'],
        	'page_delivery' => $row[0]['page_delivery'],
        	'page_shipself' => $row[0]['page_shipself'],
        	'page_robotstxt' => $row[0]['page_robotstxt'],
        	'page_sitemap' => $row[0]['page_sitemap'],
            'visible' => $row[0]['visible'],
        	'del_pay' => $row[0]['del_pay'],
        	'free_pay' => $row[0]['free_pay'],
        	'dlex' => $row[0]['dlex'],
        	'active' => $row[0]['active']
        );
  
        foreach ($this->manufacturers as $i => $manufacturer)
        {
        	$vals['manufacturer_'.$manufacturer['id']]+= $this->getManufacturerByShop($id_egms_cu, $manufacturer['id']);
        }
        
        foreach (Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::PS_CARRIERS_ONLY, true) as $carrier)
        {
        	$vals['carriers_'.$carrier['id_carrier']]+= AdminEGMSDeliveryController::getFlagChecked($carrier['id_carrier'],$row[0]['carriers']);
        }
                
        foreach (Module::getPaymentModules() as $module)
        {
        	$vals['payments_'.$module['id_module']]+= AdminEGMSDeliveryController::getFlagChecked($module['id_module'],$row[0]['payments']);
        }
        
        return $vals;
    }
    
    public function getManufacturerByShop($id_shop, $id_manufacturer)
    {
    	$sql = 'SELECT * FROM '._DB_PREFIX_.'egms_delivery 
    			WHERE id_egms_cu='.(int)$id_shop.' 
    			AND id_manufacturer='.$id_manufacturer.' 
    			AND deleted = 0';
    	if (Db::getInstance()->getRow($sql))
    		return true;
    	else
    		return false;
    }
    
	public function getCityUrl($id_city_url)
	{
		$sql = 'SELECT * FROM '._DB_PREFIX_.'egms_city_url WHERE id_egms_cu='.(int)$id_city_url;
		return (Db::getInstance()->executeS($sql));
	}
		
}
