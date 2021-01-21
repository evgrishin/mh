<?php


class AdminEGCallBackController extends ModuleAdminController
{

	public function __construct()
	{
		$this->bootstrap = true;
		//$this->display = 'view';
		$this->table = 'egcallme';
		$this->meta_title = $this->l('Your Merchant Expertise');

		$this->fields_list = array(
			'id_'.$this->table => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 50,
				'class' => 'fixed-width-xs'
			),	
			'host' => array(
				'title' => $this->l('Host'),
				'filter_key' => 'a!host',
				'type' => 'text',
				'width' => 70,
			),	
			'type' => array(
				'title' => $this->l('Type'),
				'filter_key' => 'a!type',
				'type' => 'text',
				'width' => 50,
			),
			'date_call' => array(
				'title' => $this->l('Date'),
				'filter_key' => 'a!date_call',
				'type' => 'datetime',
				'width' => 100,
			),	
			'message' => array(
				'title' => $this->l('Message'),
				'filter_key' => 'a!message',
				'type' => 'text',
				'width' => 150,
			),
			'status' => array(
				'title' => $this->l('Status'),
				'filter_key' => 'a!status',
				'type' => 'text',
				'width' => 50,
			),																						
			);	

		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'confirm' => $this->l('Delete selected items?'),
				'icon' => 'icon-trash'
			),
			'correctlink' => array(
				'text' => $this->l('Correct Image Link'),
				'confirm' => $this->l('Are you sure you want to change image url from old theme to new theme?'),
				'icon' => 'icon-edit'
			),
			'insertLang' => array(
				'text' => $this->l('Auto Input Data for New Lang'),
				'confirm' => $this->l('Auto insert data for new language?'),
				'icon' => 'icon-edit'
			),
			'correctContent' => array(
				'text' => $this->l('Correct Content use basecode64(Just for developer)'),
				'confirm' => $this->l('Are you sure?'),
				'icon' => 'icon-edit'
			),			
			);			
		parent::__construct();
		//$this->_where = ' AND id_shop='.(int)($this->context->shop->id);
		$this->_theme_dir = Context::getContext()->shop->getTheme();
	}
	
	public function renderList()
	{
		$this->initToolbar();
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		return parent::renderList();
	}
	
	public function renderForm()
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
