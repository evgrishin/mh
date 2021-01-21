<?php

require_once(_PS_MODULE_DIR_.'egms/classes/city.php');

class AdminEGMSCitysController extends ModuleAdminControllerCore
{

	protected $position_identifier = 'id_egms_city';
	
	public function __construct()
	{
		parent::__construct();
		$this->bootstrap = true;
		$this->table = 'egms_city';
		$this->list_id = $this->position_identifier;
		$this->identifier = $this->position_identifier;	
		$this->className = 'city';	
		$this->meta_title = $this->l('Citys');
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?'), 'icon' => 'icon-trash'));
		$this->fields_list = array(
			'id_'.$this->table => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'id_egms_city' => array('title' => $this->l('id'), 'filter_key' => 'id_egms_city'),	
			'cityname1' => array('title' => $this->l('Cityname'), 'filter_key' => 'cityname1'),
			'cityname2' => array('title' => $this->l('Cityname'), 'filter_key' => 'cityname2'),
			'cityname3' => array('title' => $this->l('Cityname'), 'filter_key' => 'cityname3'),
			'alias' => array('title' => $this->l('alias'), 'filter_key' => 'a!alias'),
		);	
		
		$this->_orderBy = 'a.cityname1';
	
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
	/*
	public function initPageHeaderToolbar()
	{
		$link = $this->context->link;

		if (Tools::getValue('id_egms_city'))
		{
			$this->page_header_toolbar_btn['back-blog'] = array(
				'href' => $link->getAdminLink('AdminLeoblogBlogs').'&updateleoblog_blog&id_leoblog_blog='.Tools::getValue('id_leoblog_blog'),
				'desc' => $this->l('Back To The Blog'),
				'icon' => 'icon-blog icon-3x process-icon-blog'
			);
		}
		if (Tools::getValue('id_egms_city'))
		{
			$this->page_header_toolbar_btn['save-and-stay'] = array(
				'short' => 'SaveAndStay',
				'href' => '#',
				'desc' => $this->l('Save and stay'),
			);
		}
		return parent::initPageHeaderToolbar();
	}
		*/
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
		
		$this->fields_form[0]['form'] = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Citys'),
				'icon' => 'icon-folder-close'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'label' => $this->l('id'),
					'name' => 'id_egms_city',
				),			
				array(
					'type' => 'text',
					'label' => $this->l('cityname1'),
					'name' => 'cityname1',
					'required' => true,
					'hint' => $this->l('cityname1')
				),
				array(
					'type' => 'text',
					'label' => $this->l('cityname2'),
					'name' => 'cityname2',
					'required' => true,
					'hint' => $this->l('cityname2')
				),
				array(
					'type' => 'text',
					'label' => $this->l('cityname3'),
					'name' => 'cityname3',
					'required' => true,
					'hint' => $this->l('cityname3')
				),
				array(
					'type' => 'text',
					'label' => $this->l('psyname'),
					'name' => 'psyname',
					'hint' => $this->l('psyname')
				),	
				array(
					'type' => 'text',
					'label' => $this->l('alias'),
					'name' => 'alias',
					'hint' => $this->l('alias')
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
    	$id_egms_city = Tools::getValue('id_egms_city');
    	if ($id_egms_city!=false)
    		$row = city::getCity($id_egms_city);
        return array(
            'id_egms_city' => $id_egms_city,
        	'cityname1' => $row[0]['cityname1'],
        	'cityname2' => $row[0]['cityname2'],
			'cityname3' => $row[0]['cityname3'],
        	'psyname' => $row[0]['psyname'],
        	'alias' => $row[0]['alias']
        );
    }
   
/*******************************************************************/
/*
	public function ajaxProcessInsertProductInfo()
	{
		$this->ormprod = new egormprod();

		$id_page = Tools::getValue('id_page');		
		$attrgroup = Tools::getValue('attrgroup');
		$url_page = Tools::getValue('url_page');
		
		$this->ormprod->insertProductInfo($id_page, $attrgroup, $url_page);
		
	}
*/		
	
	
}
