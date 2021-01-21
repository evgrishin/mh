<?php

require_once(_PS_MODULE_DIR_.'egms/classes/delivery.php');

class AdminEGMSDeliveryController extends ModuleAdminController
{

	protected $position_identifier = 'id_egms_delivery';
	protected $manufacturers;
	protected $shops;
	protected $id_egms_delivery;
    protected $shops_array = array();
    protected $manufacturers_array = array();
	
	public function __construct()
	{
		parent::__construct();
		$this->bootstrap = true;
		$this->table = 'egms_delivery';
		$this->list_id = $this->position_identifier;
		$this->identifier = $this->position_identifier;	
		$this->className = 'delivery';	
		$this->meta_title = $this->l('Delivery by manyfacturer');
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));

		$this->_select .= 'a.id_egms_delivery, s.name as shopname, c.cityname1, m.name, su.domain, su.active urlstatus, cu.active custatus, a.active';
		$this->_join .= ' INNER JOIN '._DB_PREFIX_.'egms_city_url cu ON a.id_egms_cu = cu.id_egms_cu ';
		$this->_join .= ' INNER JOIN '._DB_PREFIX_.'egms_city c ON c.id_egms_city = cu.id_city ';
		$this->_join .= ' INNER JOIN '._DB_PREFIX_.'shop_url su ON cu.id_shop_url = su.id_shop_url ';
		$this->_join .= ' INNER JOIN '._DB_PREFIX_.'shop s ON su.id_shop = s.id_shop ';
		$this->_join .= ' INNER JOIN '._DB_PREFIX_.'manufacturer m ON m.id_manufacturer = a.id_manufacturer ';
		$this->_where .= ' and a.deleted = 0';		
		$this->_orderBy = 'a.id_egms_delivery';

        $shops = Shop::getShops();
        foreach ($shops as $shop)
            $this->shops_array[$shop['id_shop']] = $shop['name'];

        $manufacturers = ManufacturerCore::getManufacturers();
        foreach ($manufacturers as $manufacturer)
            $this->manufacturers_array[$manufacturer['id_manufacturer']] = $manufacturer['name'];

        $this->fields_list = array(
            'id_'.$this->table => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'id_egms_delivery' => array('title' => $this->l('id'), 'filter_key' => 'id_egms_delivery'),
            'shopname' => array('title' => $this->l('Shop name'), 'type' => 'select',
                'list' => $this->shops_array,
                'filter_key' => 's!id_shop',
                'filter_type' => 'int',
                'order_key' => 'shopname'),
            'cityname1' => array('title' => $this->l('Cityname'), 'filter_key' => 'c!cityname1'),
            'name' => array('title' => $this->l('Manufacturer'), 'type' => 'select',
                'list' => $this->manufacturers_array,
                'filter_key' => 'm!id_manufacturer',
                'filter_type' => 'int',
                'order_key' => 'name'),
            'domain' => array('title' => $this->l('Domain'), 'filter_key' => 'su!domain'),
            'urlstatus' => array('title' => $this->l('url status'),
                'filter_key' => 'su.active',
                'search' => false,
                //'filter_key' => 'su.active',
                'align' => 'center',
                'active' => 'status',
                'class' => 'fixed-width-sm',
                'order_key' => 'urlstatus',
                'type' => 'bool'),
            'custatus' => array('title' => $this->l('shop url status'),
                'filter_key' => 'cu!custatus',
                'search' => false,
                'align' => 'center',
                'active' => 'status',
                'class' => 'fixed-width-sm',
                'order_key' => 'custatus',
                'type' => 'bool'),
            'active' => array('title' => $this->l('Manufacturer status'),
                'filter_key' => 'a!active',
                'align' => 'center',
                'active' => 'status',
                'class' => 'fixed-width-sm',
                'type' => 'bool')

        );

        $this->_theme_dir = Context::getContext()->shop->getTheme();
		$this->getManufacturers();
		$this->getShops();
	}
	
	private function getManufacturers()
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

	private function getShops()
	{
		$shops = delivery::getShops();
		foreach ($shops as $shop)
		{
			$this->shops[] = array('id' => $shop['id_egms_cu'], 'name' => $shop['name'].' - '.$shop['cityname1'].' - '.$shop['domain']);
		}		
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
				'title' => $this->l('Delivery by manufacturer'),
				'icon' => 'icon-folder-close'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'label' => $this->l('id'),
					'name' => 'id_egms_delivery',
				),
				array(
					'type' => 'select',
					'label' => $this->l('Shop'),
					'name' => 'id_egms_cu',
					'required' => true,  
					'options' => array('query' => $this->shops,
						'id' => 'id',
						'name' => 'name')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Manufacturer'),
					'name' => 'id_manufacturer',
					'required' => true,  
					'options' => array('query' => $this->manufacturers,
						'id' => 'id',
						'name' => 'name')
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
					'type' => 'text',
					'label' => $this->l('Free delivery amount'),
					'name' => 'free_pay',
					'hint' => $this->l('Free delivery amount')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Delivery Amount'),
					'name' => 'del_pay',
					'hint' => $this->l('Delivery Amount')
				),												
				array(
					'type' => 'text',
					'label' => $this->l('Free delivery for'),
					'name' => 'dlex',
					'hint' => $this->l('exception products for free delivery')
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
					'type' => 'textarea',
					'label' => $this->l('Comment'),
					'name' => 'comment',
					'hint' => $this->l('Comment')
				),
                array(
                    'type' => 'switch',
                    'label' => $this->l('In Stock'),
                    'name' => 'quantity',
                    'values' => $soption,
                    'default' => '0',
                ),
                array(
					'type' => 'switch',
					'label' => $this->l('Is Active'),
					'name' => 'active',
					'values' => $soption,
					'default' => '1',
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

	public static function getPayments()
	{
		$paym = array();
		foreach (Module::getPaymentModules() as $module)
		{
			$paym[]= array('id' => $module['id_module'],	'name' => $module['name']);
		}
		return $paym;
	}
	
	public static function getFlagChecked($id, $values)
	{
		$items = explode(',',$values);
		foreach ($items as $item){
			if ($item == $id)
				return true;
		}
		return false;
	}
	
	public static function getCarriers()
	{
		$carr = array();
		$context = Context::getContext();
		foreach (Carrier::getCarriers($context->language->id, true, false, false, null, Carrier::PS_CARRIERS_ONLY, true) as $carrier)
		{
			$carr[]= array('id' => $carrier['id_carrier'],	'name' => $carrier['name']);
		}
		return $carr;
	}
	
    public function getFieldsValues()
    {
    	
    	$id_egms_delivery = Tools::getValue('id_egms_delivery');
    	if ($id_egms_delivery!=false)
    		$row = delivery::getDelivery($id_egms_delivery);
        $vals = array(
            'id_egms_delivery' => $id_egms_delivery,
        	'id_egms_cu' => $row[0]['id_egms_cu'],
        	'id_manufacturer' => $row[0]['id_manufacturer'],
			'del_pay' => $row[0]['del_pay'],
        	'free_pay' => $row[0]['free_pay'],
        	'dlex' => $row[0]['dlex'],
        	//'carriers' => $row[0]['carriers'],
        	//'payments' => $row[0]['payments'],
        	'address' => $row[0]['address'],
        	'chema' => $row[0]['chema'],
        	'shipselfinfo' => $row[0]['shipselfinfo'],
        	'comment' => $row[0]['comment'],
            'quantity' => $row[0]['quantity'],
        	'active' => $row[0]['active'],
        );

        foreach (Carrier::getCarriers($this->context->language->id, true) as $carrier)
        {
        	$vals['carriers_'.$carrier['id_carrier']]+= AdminEGMSDeliveryController::getFlagChecked($carrier['id_carrier'],$row[0]['carriers']);
        }
                
        foreach (Module::getPaymentModules() as $module)
        {
        	$vals['payments_'.$module['id_module']]+= AdminEGMSDeliveryController::getFlagChecked($module['id_module'],$row[0]['payments']);
        }
        
        return ($vals);
        
    }
 	
	
}
