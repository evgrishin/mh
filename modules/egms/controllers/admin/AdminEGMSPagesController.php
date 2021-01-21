<?php

require_once(_PS_MODULE_DIR_.'egms/classes/page.php');

class AdminEGMSPagesController extends ModuleAdminControllerCore
{

	protected $position_identifier = 'id_page';
	
	public function __construct()
	{
		parent::__construct();
		$this->bootstrap = true;
		$this->table = 'egms_pages';
		$this->list_id = $this->position_identifier;
		$this->identifier = $this->position_identifier;	
		$this->className = 'egmspage';	
		$this->meta_title = $this->l('Pages');
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));
		$this->fields_list = array(
			'id_page' => array('title' => $this->l('id'), 'filter_key' => 'id_page'),	
			'page_name' => array('title' => $this->l('Page name'), 'filter_key' => 'page_name'),
			'page_type' => array('title' => $this->l('Page type'), 'filter_key' => 'page_type'),
			'title' => array('title' => $this->l('Title'), 'filter_key' => 'title'),
		);	
		
		$this->_orderBy = 'a.id_page';
	
		$this->_theme_dir = Context::getContext()->shop->getTheme();
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

		$this->multiple_fieldsets = true;
		$rte_type = true;
		switch ($fiels['page_type']){
			case 'robotstxt':
				$rte_type = false;
				break;
			case 'sitemap':
				$rte_type = false;
				break;
		}
		
		$this->fields_form[0]['form'] = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Pages'),
				'icon' => 'icon-folder-close'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'label' => $this->l('id'),
					'name' => 'id_page',
				),			
				array(
					'type' => 'text',
					'label' => $this->l('Page name'),
					'name' => 'page_name',
					'required' => true,
					'hint' => $this->l('Page name')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Page type'),
					'name' => 'page_type',
					'required' => true,  
					'options' => array('query' => $this->getPageTypes(),
						'id' => 'id',
						'name' => 'name')
				),					
				array(
					'type' => 'text',
					'label' => $this->l('Title'),
					'name' => 'title',
					'required' => true,
					'hint' => $this->l('Title')
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Meta'),
					'name' => 'meta',
					'required' => true,
					'hint' => $this->l('Meta'),
					'rows' => 2,
					'cols' => 40
				),	
				array(
					'type' => 'text',
					'label' => $this->l('Keywords'),
					'name' => 'keywords',
					'required' => true,
					'hint' => $this->l('Keywords')
				),	
				array(
					'type' => 'textarea',
					'label' => $this->l('Content'),
					'name' => 'body',
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
	
	public function getpageTypes()
	{
		return array(
				array('id' => 'index',	'name' => $this->l('index')),
				array('id' => 'contact', 'name' => $this->l('Contact')), 
				array('id' => 'delivery', 'name' => $this->l('Delivery')),
				array('id' => 'shipself', 'name' => $this->l('Shipself')),
				array('id' => 'category', 'name' => $this->l('Category')),
				array('id' => 'freetext', 'name' => $this->l('Freetext')),
				array('id' => 'robotstxt', 'name' => $this->l('robots.txt')),				
				array('id' => 'sitemap', 'name' => $this->l('sitemap.xml')),
				);
	}
	
    public function getFieldsValues()
    {
    	$id_page= Tools::getValue('id_page');
    	if ($id_page!=false)
    		$row = egmspage::getPage($id_page);
        return array(
            'id_page' => $id_page,
        	'page_name' => $row[0]['page_name'],
        	'page_type' => $row[0]['page_type'],
			'title' => $row[0]['title'],
			'meta' => $row[0]['meta'],
			'keywords' => $row[0]['keywords'],
			'body' => $row[0]['body']
        );
    }
   
/*******************************************************************/

	
}
