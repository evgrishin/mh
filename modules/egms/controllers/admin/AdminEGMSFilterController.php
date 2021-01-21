<?php

require_once(_PS_MODULE_DIR_.'egms/classes/filter.php');

class AdminEGMSFilterController extends ModuleAdminControllerCore
{

    protected $position_identifier = 'id_filter';
    protected $shops;
    protected $shops_array = array();
    protected $categorys;
    protected $groups;
    protected $categorys_array = array();
    protected $fiels;

    public function __construct()
    {
        $context = Context::getContext();

        $this->bootstrap = true;
        $this->table = 'egms_filter';
        $this->list_id = $this->position_identifier;
        $this->identifier = $this->position_identifier;
        $this->className = 'egmsfilter';
        $this->meta_title = $this->l('Filters');
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));

        $this->_select .= 'a.id_filter, a.id_shop, s.name as shopname, a.id_category, a.url, a.index, concat(c.id_category," - ",c.name) as cname, c.link_rewrite';
        $this->_join .= ' INNER JOIN '._DB_PREFIX_.'shop s ON a.id_shop = s.id_shop ';
        $this->_join .= ' INNER JOIN '._DB_PREFIX_.'category_lang c ON a.id_category = c.id_category and c.id_shop = a.id_shop';
        $this->_orderBy = 'a.url';

        $shops = Shop::getShops();
        foreach ($shops as $shop)
            $this->shops_array[$shop['id_shop']] = $shop['name'];

        $categorys = Category::getSimpleCategories($context->language->id);
        foreach ($categorys as $category)
            $this->categorys_array[$category['id_category']] = $category['id_category'].'-'.$category['name'];


        $this->fields_list = array(
            'id_filter' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),

            'shopname' => array('title' => $this->l('Shop name'), 'type' => 'select',
                'list' => $this->shops_array,
                'filter_key' => 's!id_shop',
                'filter_type' => 'int',
                'order_key' => 'shopname'),
            //'id_category' => array('title' => $this->l('Category'), 'filter_key' => 'a!id_category'),
            'cname' => array('title' => $this->l('Category'), 'type' => 'select',
                'list' => $this->categorys_array,
                'filter_key' => 'c!id_category',
                'filter_type' => 'int',
                'order_key' => 'cname'),
            'link_rewrite' => array('title' => $this->l('Rewrite'), 'filter_key' => 'c!link_rewrite'),
            'url' => array('title' => $this->l('URL'), 'filter_key' => 'a!url'),
            'index' => array('title' => $this->l('in index'), 'type' => 'bool',
                'filter_key' => 'a!index',
                'filter_type' => 'int',
                'align' => 'center',
                'active' => 'status',
                'order_key' => 'active',
                'class' => 'fixed-width-sm'),
        );

        $this->_theme_dir = Context::getContext()->shop->getTheme();
        //$this->id_egms_cu = Tools::getValue('id_egms_cu');
        //$s = Shop::getContextListShopID();
        //$this->getAllManufacturers();
        //$this->getCitys();
        //$this->getUrls();
        $this->getShops();

        $this->getCategorys();

        parent::__construct();
    }

    private function getShops()
    {
        $shops = Shop::getShops();
        foreach ($shops as $shop)
        {
            $this->shops[] = array('id' => $shop['id_shop'], 'name' => $shop['name']);
        }
    }

    private function getGroups()
    {
        $groups = egmsfilter::getFilter();
        $this->groups[] = array('id' => 0, 'name' => '-');
        $this->groups[] = array('id' => $this->fiels['id_filter'], 'name' => 'create menu');
        foreach ($groups as $group)
        {
            $this->groups[] = array('id' => $group['id_filter'], 'name' => $group['link_name']);
        }
    }

    private function getCategorys()
    {
        $context = Context::getContext();
        $categorys = Category::getSimpleCategories($context->language->id);
        $this->categorys[] = array('id' => 0, 'name' => '-');
        foreach ($categorys as $category)
        {
            $this->categorys[] = array('id' => $category['id_category'], 'name' => $category['id_category'].' - '.$category['name']);
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
        $fiels = $this->getFieldsValues();
        $this->fiels = $fiels;
        $this->getGroups();

        $this->multiple_fieldsets = true;
        $rte_type = true;
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
                'title' => $this->l('Filter'),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'label' => $this->l('id'),
                    'name' => 'id_filter',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Shop'),
                    'name' => 'id_shop',
                    'required' => true,
                    'options' => array('query' => $this->shops,
                        'id' => 'id',
                        'name' => 'name')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Category'),
                    'name' => 'id_category',
                    'required' => true,
                    'options' => array('query' => $this->categorys,
                        'id' => 'id',
                        'name' => 'name')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('URL'),
                    'name' => 'url',
                    'hint' => $this->l('URL')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Link name'),
                    'name' => 'link_name',
                    'hint' => $this->l('Link name')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Redirect from category'),
                    'name' => 'id_category_redirect',
                    'required' => true,
                    'options' => array('query' => $this->categorys,
                        'id' => 'id',
                        'name' => 'name')
                ),
                ($fiels['id_filter']!='')?array(
                    'type' => 'select',
                    'label' => $this->l('Group for menu'),
                    'name' => 'id_group',
                    'required' => true,
                    'options' => array('query' => $this->groups,
                        'id' => 'id',
                        'name' => 'name')
                ):array(
                    'type' => 'hidden',
                    'name' => 'id_group'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Bread'),
                    'name' => 'bread',
                    'hint' => $this->l('Bread')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('In Index'),
                    'name' => 'index',
                    'values' => $soption,
                    'default' => '1',
                ),
                array(
                    'type' => 'date',
                    'label' => $this->l('Filter Datetime'),
                    'name' => 'f_date',
                    'cols' => 8,
                    'hint' => $this->l('Filter Datetime')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta Title'),
                    'name' => 'meta_title',
                    'hint' => $this->l('Meta Title')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta Description'),
                    'name' => 'meta_description',
                    'hint' => $this->l('Meta Description')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Meta Keywords'),
                    'name' => 'meta_keywords',
                    'hint' => $this->l('Meta Keywords')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'title',
                    'hint' => $this->l('Title')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'autoload_rte' => $rte_type,
                    'lang' => false,
                    'rows' => 5,
                    'hint' => $this->l('Keywords: %city_name, %city1_name, %city2_name, %psyname, %alias, %email, 
					%phone, %address, %chema, %shipselfinfo, %del_pay, %free_pay'),
                    'cols' => 40
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );


        $this->tpl_form_vars = array(
            'fields_value' => $fiels
        );

        return parent::renderForm();
    }

    public function getFieldsValues()
    {
        $id_filter= Tools::getValue('id_filter');
        if ($id_filter!=false)
            $row = egmsfilter::getFilter($id_filter);
        return array(
            'id_filter' => $id_filter,
            'id_shop' => $row[0]['id_shop'],
            'id_category' => $row[0]['id_category'],
            'id_group' => $row[0]['id_group'],
            'id_category_redirect' => $row[0]['id_category_redirect'],
            'url' => $row[0]['url'],
            'link_name' => $row[0]['link_name'],
            'f_date' => $row[0]['f_date'],
            'bread' => $row[0]['bread'],
            'index' => $row[0]['index'],
            'meta_title' => $row[0]['meta_title'],
            'meta_description' => $row[0]['meta_description'],
            'meta_keywords' => $row[0]['meta_keywords'],
            'title' => $row[0]['title'],
            'description' => $row[0]['description']
        );
    }

    /*******************************************************************/


}
