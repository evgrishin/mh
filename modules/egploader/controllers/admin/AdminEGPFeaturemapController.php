<?php
require_once(_PS_MODULE_DIR_.'egploader/classes/pfeaturemap.php');
require_once(_PS_MODULE_DIR_.'egploader/classes/ploader.php');
//require_once(_PS_MODULE_DIR_.'egploader/classes/pconnecter.php');

class AdminEGPFeaturemapController extends ModuleAdminControllerCore //ModuleAdminController
{



    protected $position_identifier = 'id_load_feature_map';
    protected $features;
    protected $provider;
    //protected $category_type_array = array();
    //protected $category_type;

    // protected $provider = array();



    public function __construct()
    {
        $this->provider = ploader::getProvidersDir();

        $context = Context::getContext();

        $this->bootstrap = true;
        $this->table = 'egploader_product_feature_map';
        $this->list_id = $this->position_identifier;
        $this->identifier = $this->position_identifier;
        $this->className = 'pfeaturemap';
        $this->meta_title = $this->l('Fature MAP PANEL');
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));
/*
        $manufacturers = ManufacturerCore::getManufacturers();
        foreach ($manufacturers as $manufacturer)
            $this->manufacturers_array[$manufacturer['id_manufacturer']] = $manufacturer['name'];

        $categorys = Category::getSimpleCategories($context->language->id);
        foreach ($categorys as $category)
            $this->categorys_array[$category['id_category']] = $category['id_category'].'-'.$category['name'];
*/
        $this->fields_list = array(
            'id_load_feature_map' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 20,
                'class' => 'fixed-width-xs'
            ),
            'provider' => array('title' => $this->l('Provider'), 'type' => 'select','width' => 40,
                'list' => $this->provider,
                'filter_key' => 'provider',
                'filter_type' => 'string',
                'order_key' => 'a!provider'),
            'feature_load_name' => array('title' => $this->l('Feature Name'), 'filter_key' => 'a!feature_load_name', 'width' => 150),
            'feature_load_value' => array('title' => $this->l('Feature Value'), 'filter_key' => 'a!feature_load_value', 'width' => 150),
            'fname' => array('title' => $this->l('Feature Name'), 'type' => 'select',
                'list' => $this->features_array,
                'filter_key' => 'a!id_feature',
                'filter_type' => 'int',
                'order_key' => 'fname'),
            'fvalue' => array('title' => $this->l('Feature Value'),
                'filter_key' => 'fvalue',
                'filter_type' => 'int',
                'order_key' => 'fvalue'),
            'active' => array('title' => $this->l('Active'),
                'filter_key' => 'a!active',
                'align' => 'center',
                'active' => 'status',
                'class' => 'fixed-width-sm',
                'type' => 'bool')
        );



        $this->_select .= 'a.id_load_feature_map, a.provider, a.feature_load_name, a.feature_load_value, a.id_feature, f.name as fname, a.id_feature_value, fv.value as fvalue, active';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'feature_lang f ON a.id_feature = f.id_feature and f.id_lang = 1';
        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'feature_value_lang fv ON a.id_feature_value = fv.id_feature_value and fv.id_lang = 1';
        $this->_where .= ' and 0 = 0';
        $this->_orderBy = 'a.feature_load_name';
        //$this->_orderBy = 'a.feature_load_value';
        $this->_orderWay = 'DESC';

        $this->_theme_dir = Context::getContext()->shop->getTheme();
//        $this->getManufacturers();
//        $this->getCategorys();


        parent::__construct();

    }
/*
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

*/
    private function getFeatures()
    {
        $sql = "SELECT fl.*, fvl.* FROM " . _DB_PREFIX_ . "feature_value  fv
                    INNER JOIN " . _DB_PREFIX_ . "feature_lang fl ON fl.id_feature = fv.id_feature
                    INNER JOIN " . _DB_PREFIX_ . "feature_value_lang fvl ON fvl.id_feature_value = fv.id_feature_value
                where custom = 0
                order by fl.name, fvl.value";

        $features = Db::getInstance()->executeS($sql);
        $items = array();
        $items[] = array('id' => 0, 'name' => '-');
        foreach ($features as $feature)
        {
            $items[]= array(
                'id' => $feature['id_feature_value'],
                'name' => $feature['id_feature'].'-'.$feature['name'].'| '.$feature['id_feature_value'].'-'.$feature['value'],
            );
        }

        $this->features = $items;
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
                            <!--input type="button" name="update_meta" id="update_meta" value="Update META"--><input type="button" name="clear_log" id="clear_log" value="Clear Log">
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

        $this->getFeatures();

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
                    'label' => $this->l('id_load_feature_map'),
                    'name' => 'id_load_feature_map',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Provider'),
                    'name' => 'provider',
                    'cols' => 8,
                    'readonly' => true,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Dublicate'),
                    'name' => 'dublicate',
                    'hint' => $this->l(''),
                    'required' => true,
                    'values' => $soption,
                    'default' => '0',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Feature Name'),
                    'name' => 'feature_load_name',
                    'cols' => 8,
                    'readonly' => true,
                    'hint' => $this->l('')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Feature Value'),
                    'name' => 'feature_load_value',
                    'cols' => 8,
                    'readonly' => true,
                    'hint' => $this->l('')
                ),

                array(
                    'type' => 'select',
                    'label' => $this->l('Shop Feature Value'),
                    'name' => 'id_feature_value',
                    'required' => true,
                    'options' => array('query' => $this->features,
                        'id' => 'id',
                        'name' => 'name')
                ),
                ($fiels['id_feature_value']==0)?array(
                    'type' => 'text',
                    'label' => $this->l('NEW Value'),
                    'name' => 'new_value',
                    'hint' => $this->l('add NEW value'),
                ):array(),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active'),
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

    public function getFieldsValues()
    {
        $id_load_feature_map = Tools::getValue('id_load_feature_map');
        if ($id_load_feature_map != false)
            $row = pfeaturemap::getFeatureMap($id_load_feature_map);
        return array(
            'id_load_feature_map' => $id_load_feature_map,
            'provider' => $row[0]['provider'],
            'feature_load_name' => $row[0]['feature_load_name'],
            'feature_load_value' => $row[0]['feature_load_value'],
            'id_feature_value' => $row[0]['id_feature_value'],
            'active' => $row[0]['active'],
        );
    }

    /*
        public function ajaxProcessDownload()
        {
            $pconnecter = new pconnecter();
            $id_connecter = Tools::getValue('id_pproduct');
            die($pconnecter->download($id_connecter));
        }
    */
    /*******************************************************************/

}

