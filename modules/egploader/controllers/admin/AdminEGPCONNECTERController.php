<?php
require_once(_PS_MODULE_DIR_.'egploader/classes/ploader.php');
require_once(_PS_MODULE_DIR_.'egploader/classes/pconnecter.php');

class AdminEGPCONNECTERController extends ModuleAdminControllerCore //ModuleAdminController
{



    protected $position_identifier = 'id_connecter';
    protected $category_type_array = array();
    protected $category_type;
    protected $provider = array();



    public function __construct()
    {
        $this->provider = $this->getProvidersDir();
        $this->bootstrap = true;
        $this->table = 'egploader_connecter';
        $this->list_id = $this->position_identifier;
        $this->identifier = $this->position_identifier;
        $this->className = 'pconnecter';
        $this->meta_title = $this->l('Connecters PANEL');
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));

        $this->fields_list = array(
            'id_connecter' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 20,
                'class' => 'fixed-width-xs'
            ),
            'id_connecter' => array('title' => $this->l('ID'), 'filter_key' => 'id_load', 'width' => 20),
            /*'a!provider' => array('title' => $this->l('Provider'), 'filter_key' => 'a!provider', 'width' => 40),*/
            'provider' => array('title' => $this->l('Provider'), 'type' => 'select','width' => 40,
                'list' => $this->provider,
                'filter_key' => 'provider',
                'filter_type' => 'string',
                'order_key' => 'a!provider'),
            'type_name' => array('title' => $this->l('Type'), 'filter_key' => 'type_name', 'width' => 40),
            'type_name' => array('title' => $this->l('Type'), 'type' => 'select',
                'list' => $this->category_type_array,
                'filter_key' => 'a!connection_type',
                'filter_type' => 'int',
                'order_key' => 'type_name'),
            'url_sitemap' => array('title' => $this->l('URL Conncet'), 'filter_key' => 'a!url_sitemap'),
            'load_datetime' => array('title' => $this->l('Load datetime'), 'type' => 'datetime', 'filter_key' => 'a!load_datetime', 'width' => 40),
        );

        $this->_select .= 'a.id_connecter, a.provider, a.`connection_type`, a.url_sitemap, m.type_name as type_name, a.load_datetime, a.log'; /*a.id_category, */
        $this->_join .= ' LEFT JOIN (select 1 as id, "sitemap" as type_name union select 2 as id, "category" as type_name) m ON m.id = a.connection_type ';
        $this->_where .= ' and 0 = 0';
        $this->_orderBy = 'a.id_connecter';
        $this->_orderWay = 'DESC';

        $this->_theme_dir = Context::getContext()->shop->getTheme();

        parent::__construct();

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



    private function getConnectionType()
    {
        return $items = array(
                            array('id' => '1', 'name' => 'sitemap'),
                            array('id' => '2', 'name' => 'category')
                            );
    }

    private function getConnectionTypeList()
    {
        $items = array();
        foreach ($this->getConnectionType() as $c)
        {
            $items[$c['connection_type']] = $c['type_name'];
        }

        return $items;
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

    private function getProvidersDir()
    {
        $res = array();

        $dir  = _PS_MODULE_DIR_.'egploader/classes/providers/';
        $files = scandir($dir,1 );
        foreach ($files as $file) {
            if (strpos($file, ".php")) {
                $file = str_replace(".php", "", $file);
                $res[$file] = $file;
            }
        }
        return $res;
    }


    public function renderList()
    {
        $this->content = '<div class="row">
                    <div class="col-lg-12">
                            <input type="button" name="download_connecters" id="download_connecters" value="download"><input type="button" name="clear_log" id="clear_log" value="Clear Log">
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
                    'label' => $this->l('id_connecter'),
                    'name' => 'id_connecter',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Provider'),
                    'name' => 'provider',
                    'required' => true,
                    'options' => array('query' => $this->getProviders(),
                        'id' => 'id',
                        'name' => 'name')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Connection Type'),
                    'name' => 'connection_type',
                    'required' => true,
                    'options' => array('query' => $this->getConnectionType(),
                        'id' => 'id',
                        'name' => 'name')
                ),
                (!$fiels['id_connecter'])?array(
                    'type' => 'textarea',
                    'label' => $this->l('URL connceter'),
                    'name' => 'url_sitemap',
                    'autoload_rte' => false,
                    'lang' => false,
                    'rows' => 8,
                    'hint' => $this->l('url or urls of connecter, eache url on new line'),
                    'cols' => 40
                ):array(
                    'type' => 'text',
                    'label' => $this->l('URL connceter'),
                    'name' => 'url_sitemap',
                    'cols' => 8,
                    'readonly' => true,
                    'hint' => $this->l('url or urls of connecter, eache url on new line')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Load Datetime'),
                    'name' => 'load_datetime',
                    'cols' => 8,
                    'readonly' => true,
                    'hint' => $this->l('Load Datetime')
                ),
                array(
                    'type' => 'free',
                    'label' => $this->l('Log'),
                    'name' => 'log',
                    'autoload_rte' => true,
                    'readonly' => true,
                    'lang' => false,
                    'rows' => 8,
                    'hint' => $this->l('log of loadings'),
                )
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
        $id_connecter = Tools::getValue('id_connecter');
        if ($id_connecter != false)
            $row = pconnecter::getConnecter($id_connecter);
        return array(
            'id_connecter' => $id_connecter,
            'provider' => $row[0]['provider'],
            'connection_type' => $row[0]['connection_type'],
            'url_sitemap' => $row[0]['url_sitemap'],
            'provider' => $row[0]['provider'],
            'load_datetime' => $row[0]['load_datetime'],
            'log' => '<div id="message_display" style="overflow: auto; width:100%; height: 200px;border: 1px solid #C1C1C1;background: #eee;">'.$row[0]['log'].'</div>',
        );
    }

    public function ajaxProcessDownload()
    {
        $pconnecter = new pconnecter();
        $id_connecter = Tools::getValue('id_connecter');
        die($pconnecter->download($id_connecter));
    }

    /*******************************************************************/

}
