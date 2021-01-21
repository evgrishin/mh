<?php
require_once(_PS_MODULE_DIR_.'egploader/classes/ploader.php');

class AdminEGPLOADERController extends ModuleAdminControllerCore //ModuleAdminController
{



    protected $position_identifier = 'id_load';

    protected $provider = array();
    protected $products;

    protected $categorys;
    protected $categorys_array = array();

    protected $manufacturers;
    protected $manufacturers_array = array();

	public function __construct()
	{
        $this->provider = ploader::getProvidersDir();
        $this->bootstrap = true;
		$this->table = 'egploader';
        $this->list_id = $this->position_identifier;
        $this->identifier = $this->position_identifier;
        $this->className = 'ploader';
		$this->meta_title = $this->l('Product Loader PANEL');
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));
        //$this->context = Context::getContext();

        $context = Context::getContext();

        $manufacturers = ManufacturerCore::getManufacturers();

        $this->manufacturers_array[0] = 'not defined';
        foreach ($manufacturers as $manufacturer)
            $this->manufacturers_array[$manufacturer['id_manufacturer']] = $manufacturer['name'];

        $this->categorys_array[0] = 'not defined';
        $categorys = Category::getSimpleCategories($context->language->id);
        foreach ($categorys as $category)
            $this->categorys_array[$category['id_category']] = $category['id_category'].'-'.$category['name'];

                $this->fields_list = array(
                    'id_load' => array(
                        'title' => $this->l('ID'),
                        'align' => 'center',
                        'width' => 30,
                        'class' => 'fixed-width-xs'
                    ),
                    'id_load' => array('title' => $this->l('ID'), 'filter_key' => 'id_load', 'width' => 30),
                    /*'a!provider' => array('title' => $this->l('Provider'), 'filter_key' => 'a!provider', 'width' => 40),*/
                    'provider' => array('title' => $this->l('Provider'), 'type' => 'select','width' => 40,
                        'list' => $this->provider,
                        'filter_key' => 'provider',
                        'filter_type' => 'string',
                        'order_key' => 'a!provider'),
                    'url' => array('title' => $this->l('URL'), 'filter_key' => 'a!url', 'width' => 50),
                    'page_type' => array('title' => $this->l('Page type'), 'filter_key' => 'a!page_type', 'width' => 20),
                    'url_product_name' => array('title' => $this->l('URL Product Name'), 'filter_key' => 'a!url_product_name', 'width' => 50),
                    'cname' => array('title' => $this->l('Category'), 'type' => 'select',
                        'list' => $this->categorys_array,
                        'filter_key' => 'a!id_category',
                        'filter_type' => 'int',
                        'order_key' => 'cname'),
                    /* 'id_manufacturer' => array('title' => $this->l('ID manufacturer'), 'filter_key' => 'a!id_manufacturer', 'width' => 50),*/
                    'mname' => array('title' => $this->l('Manufacturer'), 'type' => 'select',
                        'list' => $this->manufacturers_array,
                        'filter_key' => 'a!id_manufacturer',
                        'filter_type' => 'int',
                        'order_key' => 'mname'),
                    'id_pproduct' => array('title' => $this->l('Id Prodict'),  'type' => 'int', 'filter_key' => 'a!id_pproduct', 'width' => 50),
                    'load_datetime' => array('title' => $this->l('Load datetime'), 'type' => 'datetime', 'filter_key' => 'a!load_datetime'),
                    'active' => array('title' => $this->l('Active'),
                        'filter_key' => 'a!active',
                        'align' => 'center',
                        'active' => 'status',
                        'class' => 'fixed-width-sm',
                        'type' => 'bool')
                );

                $this->_select .= 'a.id_load, a.provider, a.`url`, a.id_pproduct, a.page_type, a.url_product_name, a.load_datetime,a.id_category, a.id_manufacturer, a.active, concat(c.id_category," - ",c.name) as cname, concat(m.id_manufacturer," - ",m.name) as mname'; /*a.id_category, */
                $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'category_lang c ON a.id_category = c.id_category and c.id_shop = 1';
                $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'manufacturer m ON a.id_manufacturer = m.id_manufacturer';
                $this->_where .= ' and 0 = 0';

                $this->_orderBy = 'a.id_load';
                $this->_orderWay = 'DESC';

                $this->_theme_dir = Context::getContext()->shop->getTheme();

        parent::__construct();
        $this->getManufacturers();
        $this->getCategorys();
	}

    private function getCategorys()
    {
        $context = Context::getContext();
        $items[] =  array(
            'id' => 0,
            'name' => '-',
        );
        $categorys = Category::getSimpleCategories($context->language->id);

        foreach ($categorys as $category)
        {
            $items[] = array('id' => $category['id_category'], 'name' => $category['id_category'].' - '.$category['name']);
        }
        $this->categorys = $items;
    }

    private function getManufacturers()
    {
        $manufacturers = ManufacturerCore::getManufacturers();
        $items = array();
        $items[] =  array(
            'id' => 0,
            'name' => '-',
        );
        foreach ($manufacturers as $manufacturer)
        {
            $items[]= array(
                'id' => $manufacturer['id_manufacturer'],
                'name' => $manufacturer['name'],
            );
        }

        $this->manufacturers = $items;
    }

    public function getOptions($type)
    {
        $r = '';
            foreach ($this->$type as $m)
                $r .= '<option value="'.$m['id'].'">'.$m['name'].'</option>';
        return $r;
    }


    public function renderView()
    {


        $tpl = $this->createTemplate('orderview.tpl');
        $tpl->assign(array(
            'order' => ''
        ));

        $this->content .= $tpl->fetch();

        return parent::renderView();
    }

    private function getProviders()
    {
        $items = array();
        foreach ($this->provider as $k => $p)
        {
            $items[]= array(
                'id' => $k,
                'name' => $p,
            );
        }

        return $items;
    }




	public function renderList()
	{

	    $this->content = '<select name="load_delay" id="load_delay" style="width:100px;display: inline;"><option value="120000">120 sek</option><option value="60000">60 sek</option><option value="30000">30 sek</option><option value="5000" selected>5 sek</option></select> 
                        <label><input type="checkbox" name="load_image_ajax" id="load_image_ajax">download images</label>
                        <!-- label><input type="checkbox" name="load_features_ajax" id="load_features_ajax">update features</label>
                        <label><input type="checkbox" name="load_consist_ajax" id="load_consist_ajax">update consists</label>
                        <label><input type="checkbox" name="load_price_ajax" id="load_price_ajax">update price</label-->
                        
                            <input type="button" name="create_cache" id="create_cache" value="Create cache">

                            <div>
                            <select name="id_category_selected" id="id_category_selected" style="width: 100px; display: inline;">
                            '.$this->getOptions('categorys').'
                            </select>
                        <input type="button" name="update_category" id="update_category" value="Update Category" style="display: inline;">
                        </div>
                        <div>
                            <select name="id_manufacturer_selected" id="id_manufacturer_selected" style="width: 100px; display: inline;">
                            '.$this->getOptions('manufacturers').'
                            </select>
                        <input type="button" name="update_manufacturer" id="update_manufacturer" value="Update Manufacturer">
                        </div>   
                        <input type="button" name="create_product" id="create_product" value="Create Product">
                        <input type="button" name="clear_log" id="clear_log" value="Clear Log">                         
                            <!-- <input type="button" name="create_from_cache" id="create_from_cache" value="CREATE OR UPDATE FROM CACHE" disabled-->
                            <div id="message_display" style="overflow: auto; width:100%; height: 50px;border: 1px solid #C1C1C1;background: #fff;"></div>';
        $this->initToolbar();
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
	}

    public function postProcess()
    {
        parent::postProcess(true);
    }

    public function getPproduts($fiels){

        $url_product_name = $fiels['url_product_name'];

        $sql = "SELECT * FROM " . _DB_PREFIX_ . "egploader_product pl WHERE pl.name like '%".$url_product_name."%'";
        if($fiels['id_category']>0){
            $sql .= " and pl.id_category = ".$fiels['id_category'];
        }
        if($fiels['id_manufacturer']>0){
            $sql .= " and pl.id_manufacturer = ".$fiels['id_manufacturer'];
        }
        $products = Db::getInstance()->executeS($sql);
        $items = array();
        $items[] = array('id' => 0, 'name' => '-');
        foreach ($products as $product)
        {
            $items[]= array(
                'id' => $product['id_pproduct'],
                'name' => $product['id_pproduct'].' - '.$product['name'].' | (id_product-'.$product['id_product'].')',
            );
        }

        $sql = "SELECT * FROM " . _DB_PREFIX_ . "egploader_product pl where 1=1";
        if($fiels['id_category']>0){
            $sql .= " and pl.id_category = ".$fiels['id_category'];
        }
        if($fiels['id_manufacturer']>0){
            $sql .= " and pl.id_manufacturer = ".$fiels['id_manufacturer'];
        }
        $products = Db::getInstance()->executeS($sql);
        $items[] = array('id' => 0, 'name' => '--------------');
        foreach ($products as $product)
        {
            $items[]= array(
                'id' => $product['id_pproduct'],
                'name' => $product['id_pproduct'].' - '.$product['name'].' | ('.$product['id'].'-'.$product['id_product'].')',
            );
        }

        $this->products = $items;
    }

	public function renderForm()
	{
        //$this->content = '<input type="text" value="" name="id_load" id="id_load"><input type="button" name="download" id="download" value="download">';
        if (!$this->loadObject(true))
            if (Validate::isLoadedObject($this->object))
                $this->display = 'edit';
            else
                $this->display = 'add';

        $this->initToolbar();
        $this->initPageHeaderToolbar();
        $fiels = $this->getFieldsValues();

        $this->getPproduts($fiels);


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

        $this->multiple_fieldsets = true;

                $this->fields_form[0]['form'] = array(
                    'tinymce' => true,
                    'legend' => array(
                        'title' => $this->l('Load'),
                        'icon' => 'icon-folder-close'
                    ),
                    'input' => array(
                        array(
                            'type' => 'hidden',
                            'label' => $this->l('id_load'),
                            'name' => 'id_load',
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Active'),
                            'name' => 'active',
                            'values' => $soption,
                            'default' => '1',
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Provider'),
                            'name' => 'provider',
                            'required' => true,
                            'readonly' => true,
                            'options' => array('query' => $this->getProviders(),
                                'id' => 'id',
                                'name' => 'name')
                        ),

                        array(
                            'type' => 'text',
                            'label' => $this->l('Page type'),
                            'name' => 'page_type',
                            'cols' => 8,
                            'readonly' => true,
                            'hint' => $this->l('Page type')
                        ),
                        (!$fiels['id_load'])?array(
                            'type' => 'textarea',
                            'label' => $this->l('URL'),
                            'name' => 'url',
                            'autoload_rte' => false,
                            'lang' => false,
                            'rows' => 8,
                            'hint' => $this->l('url or urls, eache url on new line'),
                            'cols' => 40
                        ):array(
                            'type' => 'text',
                            'label' => $this->l('URL'),
                            'name' => 'url',
                            'cols' => 8,
                            'readonly' => true,
                            'hint' => $this->l('url or urls, eache url on new line')
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('URL Product Name'),
                            'name' => 'url_product_name',
                            'cols' => 8,
                            'readonly' => true,
                            'hint' => $this->l('URL Product Name')
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Category'),
                            'name' => 'id_category',
                            'required' => true,
                            'readonly' => true,
                            'options' => array('query' => $this->categorys,
                                'id' => 'id',
                                'name' => 'name')
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Manufacturer'),
                            'name' => 'id_manufacturer',
                            'required' => true,
                            'readonly' => true,
                            'options' => array('query' => $this->manufacturers,
                                'id' => 'id',
                                'name' => 'name')
                        ),

                        ($fiels['page_type']=='PRODUCT'&&$fiels['active']==1)?array(
                            'type' => 'select',
                            'label' => $this->l('Product Difinition'),
                            'name' => 'id_pproduct',
                            'readonly' => ($fiels['id_pproduct']>0)?true:false,
                            'required' => true,
                            'options' => array('query' => $this->products,
                                'id' => 'id',
                                'name' => 'name')
                        ):array(),
                        /*
                        array(
                            'type' => 'text',
                            'label' => $this->l('ID Product Difinition'),
                            'name' => 'id_pproduct',
                            'required' => false,
                            'cols' => 8,
                            'readonly' => ($fiels['id_pproduct'])?true:false,
                            'hint' => $this->l('ID of Product')
                        ),*/
                        ($fiels['page_type']=='PRODUCT'&&$fiels['active']==1&&$fiels['id_category']>0&&$fiels['id_manufacturer']>0&&($fiels['id_pproduct']==''||$fiels['id_pproduct']==0))?array(
                            'type' => 'switch',
                            'label' => $this->l('Create product difinition'),
                            'name' => 'create_pproduct',
                            'values' => $soption,
                            'default' => '1',
                        ):array(),
                        ($fiels['page_type']=='NOPROD'&&$fiels['active']==1&&$fiels['id_pproduct']=='')?array(
                            'type' => 'switch',
                            'label' => $this->l('Create filter page'),
                            'name' => 'create_filter',
                            'values' => $soption,
                            'default' => '1',
                        ):array(),
                         array(
                            'type' => 'text',
                            'label' => $this->l('Load Datetime'),
                            'name' => 'load_datetime',
                            'cols' => 8,
                            'readonly' => true,
                            'hint' => $this->l('Load Datetime')
                        ),
                        (!$fiels['id_load']&&$fiels['active']==1)?array(
                            'type' => 'switch',
                            'label' => $this->l('SITEMAP URL LOAD'),
                            'name' => 'sitemap_url',
                            'values' => $soption,
                            'default' => '1',
                        ):array()
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

    public function getFieldsValues()
    {
        $id_load = Tools::getValue('id_load');
        if ($id_load != false)
            $row = ploader::getPloader($id_load);
        return array(
            'id_load' => $id_load,
            'url' => $row[0]['url'],
            'page_type' => $row[0]['page_type'],
            'id_pproduct' => $row[0]['id_pproduct'],
            'url_product_name' => $row[0]['url_product_name'],
            'provider' => $row[0]['provider'],
            'load_datetime' => $row[0]['load_datetime'],
            'active' => $row[0]['active'],
            'id_category' => $row[0]['id_category'],
            'id_manufacturer' => $row[0]['id_manufacturer'],

        );
    }

    public function ajaxProcessCreatecache()
    {
        $ploader = new ploader();
        $id_load = Tools::getValue('id_load');

        die($ploader->create_cache($id_load));
    }

    public function ajaxProcessCreateproduct()
    {
        $ploader = new ploader();
        $id_load = Tools::getValue('id_load');

        die($ploader->create_product($id_load));
    }

    public function ajaxProcessUpdateManufacturer()
    {
        $ploader = new ploader();
        $id_load = Tools::getValue('id_load');
        $value = Tools::getValue('action_type');

        $ploader->updateLoadsField($id_load, 'id_manufacturer', $value, 'int');

        die("Manufacturer updated:".$id_load.' - '.$value.'<br>');
    }

    public function ajaxProcessUpdateCategory()
    {
        $ploader = new ploader();
        $id_load = Tools::getValue('id_load');
        $value = Tools::getValue('action_type');

        $ploader->updateLoadsField($id_load, 'id_category', $value, 'int');

        die("Category updated:".$id_load.' - '.$value.'<br>');
    }

    /*******************************************************************/

}

