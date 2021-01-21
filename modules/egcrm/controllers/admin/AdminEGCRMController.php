<?php
require_once(_PS_MODULE_DIR_.'egcrm/classes/orders.php');

class AdminEGCRMController extends ModuleAdminController //ModuleAdminController
{
    protected $position_identifier = 'id_order';
    protected $manufacturers;
    protected $shops;
    protected $statuses_array = array();
    protected $shops_array = array();
    protected $manufacturers_array = array();


	public function __construct()
	{
        parent::__construct();
        $this->bootstrap = true;
		$this->bootstrap = true;
		$this->table = 'egms_orders';
        $this->identifier = $this->position_identifier;
        $this->className = 'orders';
        $this->addRowAction('view');
        $this->deleted = false;
        $this->list_id = $this->position_identifier;
		$this->meta_title = $this->l('CRM PANEL');
        $this->context = Context::getContext();


        $this->_select .= 'a.id_order, os.`color`, a.man_order, a.bayer_name, a.id_manufacturer, a.id_shop, a.phone, a.city, s.name shopname, m.name, osl.name as stname, a.id_order_state, a.date_delivery, a.date_add'; //, s.name shopname, c.cityname1, m.name, su.domain, su.active urlstatus, cu.active custatus, a.active deliverystatus';
        $this->_join .= ' INNER JOIN '._DB_PREFIX_.'shop s ON a.id_shop = s.id_shop ';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'manufacturer m ON m.id_manufacturer = a.id_manufacturer ';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`id_order_state`) ';
		$this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$this->context->language->id.')';
        $this->_where .= ' and 0 = 0';
        $this->_orderBy = 'a.id_order';
        $this->_orderWay = 'DESC';


        //$this->getManufacturers();
        //$this->getShops();

        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($statuses as $status)
            $this->statuses_array[$status['id_order_state']] = $status['name'];

        $shops = Shop::getShops();
        foreach ($shops as $shop)
            $this->shops_array[$shop['id_shop']] = $shop['name'];

        $manufacturers = ManufacturerCore::getManufacturers();
        foreach ($manufacturers as $manufacturer)
            $this->manufacturers_array[$manufacturer['id_manufacturer']] = $manufacturer['name'];


        $this->fields_list = array(
            'id_order' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 50,
                'class' => 'fixed-width-xs'
            ),
            'id_order' => array('title' => $this->l('order'), 'filter_key' => 'id_order', 'width' => 50),
            'man_order' => array('title' => $this->l('morder'), 'filter_key' => 'a!man_order', 'width' => 70),
            'stname' => array('title' => $this->l('Status'), 'type' => 'select',
                'color' => 'color',
                'list' => $this->statuses_array,
                'filter_key' => 'a!id_order_state',
                'filter_type' => 'int',
                'order_key' => 'stname'),
            'phone' => array('title' => $this->l('Phone'), 'filter_key' => 'a!phone'),
            'bayer_name' => array('title' => $this->l('FIO'), 'filter_key' => 'a!bayer_name'),
            'city' => array('title' => $this->l('City'), 'filter_key' => 'a!city'),
            'date_add' => array('title' => $this->l('date_add'), 'type' => 'datetime', 'filter_key' => 'a!date_add'),
            'date_delivery' => array('title' => $this->l('date_delivery'), 'type' => 'datetime', 'filter_key' => 'a!date_delivery'),
            'name' => array('title' => $this->l('Manufacturer'), 'filter_key' => 'm!name', 'width' => 50),
            'name' => array('title' => $this->l('Manufacturer'), 'type' => 'select',
                'list' => $this->manufacturers_array,
                'filter_key' => 'a!id_manufacturer',
                'filter_type' => 'int',
                'order_key' => 'name'),
            'shopname' => array('title' => $this->l('Shop name'), 'type' => 'select',
                'list' => $this->shops_array,
                'filter_key' => 'a!id_shop',
                'filter_type' => 'int',
                'order_key' => 'shopname'),
        );

        $this->_theme_dir = Context::getContext()->shop->getTheme();
	}

    public function renderView()
    {
        $order = new Orders(Tools::getValue('id_order'));
        if (!Validate::isLoadedObject($order))
            $this->errors[] = Tools::displayError('The order cannot be found within your database.');

        // $customer = new Customer($order->id_customer);
        // $carrier = new Carrier($order->id_carrier);


        $tpl = $this->createTemplate('orderview.tpl');
        $tpl->assign(array(
            'order' => $order
        ));
        $this->content .= $tpl->fetch();

        return parent::renderView();
    }



/*
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
*/

/*
    private function getShops()
    {
        $shops = delivery::getShops();
        foreach ($shops as $shop)
        {
            $this->shops[] = array('id' => $shop['id_egms_cu'], 'name' => $shop['name'].' - '.$shop['cityname1'].' - '.$shop['domain']);
        }
    }
	*/
	public function renderList()
	{
		$this->initToolbar();
		$this->addRowAction('edit');
		//$this->addRowAction('delete');
		return parent::renderList();
	}
	
/*	public function renderForm()
	{
		if (!$this->loadObject(true))
			return;
		if (Validate::isLoadedObject($this->object))
			$this->display = 'edit';
		else
			$this->display = 'add';
		$this->initToolbar();
		$this->context->controller->addJqueryUI('ui.sortable');
		//return $this->_showWidgetsSetting();
	}
	*/
/*******************************************************************/
	public function ajaxProcessInsertProductInfo()
	{
		$this->ormprod = new egormprod();

		$id_page = Tools::getValue('id_page');		
		$attrgroup = Tools::getValue('attrgroup');
		$url_page = Tools::getValue('url_page');
		
		$this->ormprod->insertProductInfo($id_page, $attrgroup, $url_page);
		
	}
		

	public function ajaxProcessUpdateProductInfo()
	{
		$this->ormprod = new egormprod();
		
		$t_tmp = Tools::getValue('uc');
		if ($t_tmp == 'true')
			$action = "load,update";
		else
			$action = "update";
		
		$type = array();	
		if (Tools::getValue('up')=='true')
			$type[] = "price";
		if (Tools::getValue('comment')=='true')
			$type[] = "comment";
		if (Tools::getValue('upname')=='true')
			$type[] = "upname";
		
		$id_page = Tools::getValue('id_page');
		
		$this->ormprod->updateProductInfo($type, $id_page,$action);
		
	}
	
	public function ajaxProcessGetTable()
	{
		
		$this->ormprod = new egormprod();
		
		$list = $this->ormprod->getProductList(0,false,false);
		
		$link = new Link();

		$output = "";		
		foreach($list as $row)
		{

			$output .= "<tr><td>-</td>";
			$output .= "<td>".$row['id_product']."</td>";
			$output .= "<td><span>".$row['product_sname']."</span><br><span style='font-size: 8px;'><a href='".$row['product_url']."'>".$row['product_url']."</a></span></td>";
			$output .= "<td><span>".$row['name']."</span><br><span style='font-size: 8px;'><a href='".$link->getProductLink($row['id_product'])."'>".$link->getProductLink($row['id_product'])."</a></span></td>";
			$output .= "<td>".$row['product_attrgroup']."</td>";
			$output .= "<td>".$row['price_discount']."</td>";
			$output .= "<td>".$row['dname']."</td>";			
			$output .= "<td><a href='#' onclick='UpdateRow(this);return(false);'>R</a>";
			$output .= " <a href='".Context::getContext()->link->getAdminLink('AdminProducts')."&updateproduct&id_product=".(int)$row['id_product']."' target='new'>E</a>";
			$output .= " <a href='#' onclick='InsertRow(this);return(false);'>I</a></td>";
			$output.="</tr>";

		}
		
		die ($output);
	}
	
}
