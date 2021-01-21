<?php
require_once(_PS_MODULE_DIR_.'egploader/classes/pproduct.php');
require_once(_PS_MODULE_DIR_.'egploader/classes/ploader.php');
//require_once(_PS_MODULE_DIR_.'egploader/classes/pconnecter.php');

class AdminEGPPRODUCTController extends ModuleAdminControllerCore //ModuleAdminController
{



    protected $position_identifier = 'id_pproduct';
    protected $category_type_array = array();
    protected $category_type;
    protected $categorys;
    protected $categorys_array = array();
    protected $loads;
    protected $attribute_groups;
    protected $manufacturers;
    protected $manufacturers_array = array();
   // protected $provider = array();



    public function __construct()
    {
        //$this->provider = $this->getProvidersDir();

        $context = Context::getContext();

        $this->bootstrap = true;
        $this->table = 'egploader_product';
        $this->list_id = $this->position_identifier;
        $this->identifier = $this->position_identifier;
        $this->className = 'pproduct';
        $this->meta_title = $this->l('Products PANEL');
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));

        $manufacturers = ManufacturerCore::getManufacturers();

        $this->manufacturers_array['0'] = 'not defined';
        foreach ($manufacturers as $manufacturer)
            $this->manufacturers_array[$manufacturer['id_manufacturer']] = $manufacturer['name'];

        $this->categorys_array['0'] = 'not defined';
        $categorys = Category::getSimpleCategories($context->language->id);
        foreach ($categorys as $category)
            $this->categorys_array[$category['id_category']] = $category['id_category'].'-'.$category['name'];

        $this->fields_list = array(
            'id_pproduct' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 20,
                'class' => 'fixed-width-xs'
            ),
            'id_product' => array('title' => $this->l('ID product'), 'type' => 'int', 'filter_key' => 'a!id_product', 'width' => 50),
            'name' => array('title' => $this->l('Product Name'), 'filter_key' => 'a!name', 'width' => 150),
            /*'id_category' => array('title' => $this->l('ID category'), 'filter_key' => 'a!id_category', 'width' => 50),*/
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
            'load_datetime' => array('title' => $this->l('Load datetime'), 'type' => 'datetime', 'filter_key' => 'a!load_datetime')
        );



        $this->_select .= 'a.id_pproduct, a.name, a.id_product, a.id_category, a.id_manufacturer, load_datetime, concat(c.id_category," - ",c.name) as cname, concat(m.id_manufacturer," - ",m.name) as mname';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'category_lang c ON a.id_category = c.id_category and c.id_shop = 1';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'manufacturer m ON a.id_manufacturer = m.id_manufacturer';
        $this->_where .= ' and 0 = 0';
        $this->_orderBy = 'a.id_pproduct';
        $this->_orderWay = 'DESC';

        $this->_theme_dir = Context::getContext()->shop->getTheme();
        $this->getManufacturers();
        $this->getCategorys();


        parent::__construct();

    }

    private function getCategorys()
    {
        $context = Context::getContext();
        $categorys = Category::getSimpleCategories($context->language->id);
        foreach ($categorys as $category)
        {
            $this->categorys[] = array('id' => $category['id_category'], 'name' => $category['id_category'].' - '.$category['name']);
        }
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

    private function getLoads($id_pproduct)
    {
        $loads = ploader::getPloadersByProduct($id_pproduct);
        $items = array();
        $items[] = array('id' => 0, 'name' => '-');
        foreach ($loads as $load)
        {
            $items[]= array(
                'id' => $load['id_load'],
                'name' => $load['id_load'].'-'.$load['provider'].'-'.$load['url_product_name'],
            );
        }

        $this->loads = $items;
    }

    private function getAttributeGroups()
    {
        $attribute_groups = AttributeGroupCore::getAttributesGroups(1);//ploader::getPloadersByProduct();
        $items = array();
        $items[] = array('id' => 0, 'name' => '-');
        foreach ($attribute_groups as $attribute_group)
        {
            $items[]= array(
                'id' => $attribute_group['id_attribute_group'],
                'name' => $attribute_group['name'],
            );
        }

        $this->attribute_groups = $items;
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





    public function renderList()
    {
        $this->content = '<div class="row">
                    <div class="col-lg-12">
                    <select name="load_delay" id="load_delay" style="width:100px;"><option value="120000">120 sek</option><option value="60000">60 sek</option><option value="30000">30 sek</option><option value="5000" selected>5 sek</option></select>
                            <label><input type="checkbox" name="no_cache" id="no_cache">no use cache</label>
                            <label><input type="checkbox" name="product_meta_load" id="product_meta_load">Update META</label>
                            <label><input type="checkbox" name="product_name_load" id="product_name_load">Update NAME</label>
                            <label><input type="checkbox" name="description_load" id="description_load">Update DESCRIPTION</label>
                            <label><input type="checkbox" name="price_load" id="price_load">Update PRICE</label>
                            <label><input type="checkbox" name="images_load" id="images_load">Update IMEAGE</label>
                            <label><input type="checkbox" name="features_load" id="features_load">Update Features</label>
                            <label><input type="checkbox" name="consistions_load" id="consistions_load">Update Consistions</label>
                            <label><input type="checkbox" name="rewiews_load" id="rewiews_load">Update Reviews</label>
                            <input type="button" name="update_data" id="update_data" value="Update Data">
                            <br><input type="button" name="product_create" id="product_create" value="Create product">
                            <input type="button" name="none" id="none" value="-" disabled>
                            <!-- <input type="button" name="product_meta_load" id="product_meta_load" value="Update META">
                            <input type="button" name="product_name_load" id="product_name_load" value="Update NAME">
                            <input type="button" name="description_load" id="description_load" value="Update DESCRIPTION">
                            <input type="button" name="price_load" id="price_load" value="Update PRICE">
                            <input type="button" name="images_load" id="images_load" value="Update IMEAGE">
                            <input type="button" name="features_load" id="features_load" value="Update Features">
                            <input type="button" name="consistions_load" id="consistions_load" value="Update Consistions">
                            <input type="button" name="rewiews_load" id="rewiews_load" value="Update Reviews"> -->
                            <input type="button" name="clear_log" id="clear_log" value="Clear Log">
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-lg-12">
                    <div id="message_display" style="overflow: auto; width:100%; height: 50px;border: 1px solid #C1C1C1;background: #fff;"></div>    
                    </div>
            </div>';

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
        $fiels = $this->getFieldsValues();

        $this->getAttributeGroups();

        $this->getLoads($fiels['id_pproduct']);

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
                    'label' => $this->l('id_pproduct'),
                    'name' => 'id_pproduct',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product name'),
                    'name' => 'name',
                    'cols' => 8,
                    'hint' => $this->l('')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Category default'),
                    'name' => 'id_category',
                    'required' => true,
                    'options' => array('query' => $this->categorys,
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
                    'type' => 'select',
                    'label' => $this->l('Attribut 1'),
                    'name' => 'id_attr_group1',
                    'options' => array('query' => $this->attribute_groups,
                        'id' => 'id',
                        'name' => 'name')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Attribut 2'),
                    'name' => 'id_attr_group2',
                    'options' => array('query' => $this->attribute_groups,
                        'id' => 'id',
                        'name' => 'name')
                ),
                (!$fiels['id_product']||$fiels['id_product']==0)?array(
                    'type' => 'text',
                    'label' => $this->l('ID product'),
                    'name' => 'id_product',
                    'cols' => 8,
                    'hint' => $this->l('')
                ):array(
                    'type' => 'text',
                    'label' => $this->l('ID product'),
                    'name' => 'id_product',
                    'cols' => 8,
                    'readonly' => true,
                    'hint' => $this->l('')
                ),
                ($fiels['id_product']>0)?array(
                    'type' => 'switch',
                    'label' => $this->l('Update META'),
                    'name' => 'product_meta_load',
                    'values' => $soption,
                    'default' => '1',
                ):array(),
                array(
                    'type' => 'select',
                    'label' => $this->l('Product name'),
                    'name' => 'product_name',
                    'options' => array('query' => $this->loads,
                        'id' => 'id',
                        'name' => 'name')
                ),($fiels['id_product']>0)?array(
                    'type' => 'switch',
                    'label' => $this->l('Product name load'),
                    'name' => 'product_name_load',
                    'values' => $soption,
                    'default' => '1',
                ):array(),

                array(
                    'type' => 'select',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'options' => array('query' => $this->loads,
                        'id' => 'id',
                        'name' => 'name')
                ),($fiels['id_product']>0)?array(
                    'type' => 'switch',
                    'label' => $this->l('Description load'),
                    'name' => 'description_load',
                    'values' => $soption,
                    'default' => '1',
                ):array(),

                array(
                    'type' => 'select',
                    'label' => $this->l('Price'),
                    'name' => 'price',
                    'options' => array('query' => $this->loads,
                        'id' => 'id',
                        'name' => 'name')
                ),($fiels['id_product']>0)?array(
                    'type' => 'switch',
                    'label' => $this->l('Price load'),
                    'name' => 'price_load',
                    'values' => $soption,
                    'default' => '1',
                ):array(),

                array(
                    'type' => 'select',
                    'label' => $this->l('Images'),
                    'name' => 'images',
                    'options' => array('query' => $this->loads,
                        'id' => 'id',
                        'name' => 'name')
                ),($fiels['id_product']>0)?array(
                    'type' => 'switch',
                    'label' => $this->l('Images load'),
                    'name' => 'images_load',
                    'values' => $soption,
                    'default' => '1',
                ):array(),

                array(
                    'type' => 'select',
                    'label' => $this->l('Features'),
                    'name' => 'features',
                    'options' => array('query' => $this->loads,
                        'id' => 'id',
                        'name' => 'name')
                ),($fiels['id_product']>0)?array(
                    'type' => 'switch',
                    'label' => $this->l('Features load'),
                    'name' => 'features_load',
                    'values' => $soption,
                    'default' => '1',
                ):array(),

                array(
                    'type' => 'select',
                    'label' => $this->l('Consistions'),
                    'name' => 'consistions',
                    'options' => array('query' => $this->loads,
                        'id' => 'id',
                        'name' => 'name')
                ),($fiels['id_product']>0)?array(
                    'type' => 'switch',
                    'label' => $this->l('Consistions load'),
                    'name' => 'consistions_load',
                    'values' => $soption,
                    'default' => '1',
                ):array(),

                array(
                    'type' => 'select',
                    'label' => $this->l('Rewiews'),
                    'name' => 'rewiews',
                    'options' => array('query' => $this->loads,
                        'id' => 'id',
                        'name' => 'name')
                ),($fiels['id_product']>0)?array(
                    'type' => 'switch',
                    'label' => $this->l('Rewiews load'),
                    'name' => 'rewiews_load',
                    'values' => $soption,
                    'default' => '1',
                ):array(),

                (!$fiels['id_product']||$fiels['id_product']==0)?array(
                    'type' => 'switch',
                    'label' => $this->l('Create produt'),
                    'name' => 'create_product',
                    'values' => $soption,
                    'default' => '1',
                ):array(),
                ($fiels['id_product']>0)?array(
                    'type' => 'switch',
                    'label' => $this->l('Update product'),
                    'name' => 'update_product',
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
        $id_pproduct = Tools::getValue('id_pproduct');
        if ($id_pproduct != false)
            $row = pproduct::getPproduct($id_pproduct);
        return array(
            'id_pproduct' => $id_pproduct,
            'name' => $row[0]['name'],
            'id_product' => $row[0]['id_product'],
            'id_category' => $row[0]['id_category'],
            'id_manufacturer' => $row[0]['id_manufacturer'],
            'id_attr_group1' => $row[0]['id_attr_group1'],
            'id_attr_group2' => $row[0]['id_attr_group2'],
            'load_datetime' => $row[0]['load_datetime'],
            'product_name' => $row[0]['product_name'],
            'description' => $row[0]['description'],
            'price' => $row[0]['price'],
            'images' => $row[0]['images'],
            'features' => $row[0]['features'],
            'consistions' => $row[0]['consistions'],
            'rewiews' => $row[0]['rewiews'],
        );
    }

    public function ajaxProcessCreateproduct()
    {
        $id_connecter = Tools::getValue('id_load');
        $pproduct = new pproduct($id_connecter);
        $no_cache = Tools::getValue('no_cache');

        die($pproduct->create_product($no_cache));

    }

    public function ajaxProcessUpdateData()
    {
        $id_connecter = Tools::getValue('id_load');
        $pproduct = new pproduct($id_connecter);

        die($pproduct->update_field());
    }

/*
    public function ajaxProcessupdatefeatures()
    {
        $id_connecter = Tools::getValue('id_load');
        $pproduct = new pproduct($id_connecter);

        //$params['product_name_load'] = 1;//Tools::getValue('product_name_load');
        //$params['description_load'] = Tools::getValue('description_load');
        //$params['price_load'] = 1;//Tools::getValue('price_load');
        //$params['images_load'] = 1;//Tools::getValue('images_load');
        //$params['features_load'] = 1;//Tools::getValue('features_load');
        //$params['consistions_load'] = 1;//Tools::getValue('consistions_load');
        //$params['product_meta_load'] = 1;//Tools::getValue('product_meta_load');
        //$params['rewiews_load']

        die($pproduct->update_field('features_load'));

    }
*/
    /*******************************************************************/

}

